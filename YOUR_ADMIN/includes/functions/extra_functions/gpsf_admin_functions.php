<?php
// -----
// Red Headed Stepchild of Zen Cart® Google Product Search Feeder II, admin tool.
// Copyright 2023-2026, https://vinosdefrutastropicales.com
// Modifications Copyright 2026 PRO-Webs, Inc. (Melanie Prough), https://PRO-Webs.net
//
// Last updated: Reimagined Release v1.0.5
//
/**
 * Based on:
 *
 * @package google product search feeder
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: google_product_search_functions.php 5 2011-11-17 11:19:31Z numinix $
 */
function gpsf_cfg_pull_down_currencies($currencies_id, $key = ''): string
{
    global $db;

    $name = (($key !== '') ? "configuration[$key]" : 'configuration_value');
    $currencies = $db->Execute(
        'SELECT code
           FROM '. TABLE_CURRENCIES . '
          ORDER BY code ASC'
    );
    $currencies_array = [];
    foreach ($currencies as $next_currency) {
        $currencies_array[] = [
            'id' => $next_currency['code'],
            'text' => $next_currency['code'],
        ];
    }
    return zen_draw_pull_down_menu($name, $currencies_array, $currencies_id);
}

function gpsf_cfg_pull_down_country_iso3_list($countries_id, $key = ''): string
{
    global $db;

    $name = (($key !== '') ? "configuration[$key]" : 'configuration_value');
    $countries = $db->Execute(
        'SELECT countries_id, countries_iso_code_3
           FROM ' . TABLE_COUNTRIES . '
          ORDER BY countries_iso_code_3 ASC'
    );
    $countries_array = [];
    foreach ($countries as $next_country) {
        $countries_array[] = [
            'id' => $next_country['countries_id'],
            'text' => $next_country['countries_iso_code_3'],
        ];
    }
    return zen_draw_pull_down_menu($name, $countries_array, $countries_id);
}

function gpsf_product_field_install_control($column, $key = ''): string
{
    global $sniffer;

    $allowedColumns = [
        'products_material' => 'material',
        'products_age_group' => 'age_group',
        'products_color' => 'color',
        'products_gender' => 'gender',
    ];
    $name = ($key !== '') ? "configuration[$key]" : 'configuration_value';
    $control = zen_draw_hidden_field($name, $column);
    if (!isset($allowedColumns[$column])) {
        return $control . '<span class="text-danger">Invalid field configuration</span>';
    }
    if ($sniffer->field_exists(TABLE_PRODUCTS, $column)) {
        return $control . '<span class="label label-success">Installed</span>';
    }

    $parameters = http_build_query(
        [
            'action' => 'install_product_field',
            'field' => $allowedColumns[$column],
            'gID' => (int)($_GET['gID'] ?? 0),
            'securityToken' => $_SESSION['securityToken'],
        ]
    );
    return $control . '<a class="btn btn-primary btn-sm" href="' . zen_href_link(FILENAME_GPSF_ADMIN, $parameters) . '">Install</a>';
}

function gpsf_custom_product_field_install_control($column, $key = ''): string
{
    global $sniffer;

    if (preg_match('/^GPSF_CUSTOM_PRODUCT_FIELD_([1-5])$/', $key, $matches) !== 1) {
        return '<span class="text-danger">Invalid custom-field slot</span>';
    }

    $slot = (int)$matches[1];
    $column = trim((string)$column);
    $name = "configuration[$key]";
    $inputId = 'gpsf-custom-product-field-' . $slot;
    $control = zen_draw_input_field(
        $name,
        $column,
        'id="' . $inputId . '" maxlength="64" pattern="[a-z][a-z0-9_]{0,63}" placeholder="e.g. vehicle_type"'
    );

    if ($column !== '' && preg_match('/^[a-z][a-z0-9_]{0,63}$/', $column) === 1 && stripos($column, 'xml') !== 0 && $sniffer->field_exists(TABLE_PRODUCTS, $column)) {
        $control .= ' <span class="label label-success">Installed</span>';
    }

    $parameters = http_build_query(
        [
            'action' => 'install_custom_product_field',
            'slot' => $slot,
            'gID' => (int)($_GET['gID'] ?? 0),
            'securityToken' => $_SESSION['securityToken'],
        ]
    );
    $installUrl = zen_href_link(FILENAME_GPSF_ADMIN, $parameters) . '&column=';
    $onclick = 'window.location.href=' . json_encode($installUrl) .
        '+encodeURIComponent(document.getElementById(' . json_encode($inputId) . ').value);';

    return $control . ' <button type="button" class="btn btn-primary btn-sm" onclick="' .
        htmlspecialchars($onclick, ENT_QUOTES, CHARSET) . '">Install</button>';
}
