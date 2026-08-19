<?php
// -----
// Red Headed Stepchild of Zen Cart® Google Product Search Feeder II.  A one-time tool to copy the older google product
// search configuration settings to this plugin's settings.
//
// Copyright (C), 2023-2026.  https://vinosdefrutastropicales.com
// Modifications Copyright 2026 PRO-Webs, Inc. (Melanie Prough), https://PRO-Webs.net
//
// Last updated: Reimagined Release v1.0.8
//
// INSTRUCTIONS:
// - Copy this file to the root of your site's renamed admin directory.
// - Log into Zen Cart admin.
// - Enter {your-website-address}/{your-admin}/copy_google_search_configuration.php
// - The settings copied are displayed on screen and recorded in the
//   file /logs/copy_google_search_configuration_YYYYMMDDHHIISS.log.
// - The security-key value is redacted from both outputs.
// - Delete this file from your admin directory immediately after use.
//
require 'includes/application_top.php';

if (!defined('GPSF_VERSION') || !defined('RHS_GPSF_VERSION')) {
    exit('"Red Headed Stepchild of Zen Cart® Google Product Search Feeder II" must be installed before this script can be successfully run.');
}

if (!defined('GOOGLE_PRODUCTS_VERSION')) {
    exit('"Google Merchant Center Feeder" must be installed before its configuration can be copied.');
}

$settings_to_copy = [
    'GOOGLE_PRODUCTS_ACCESS_KEY' => 'GPSF_ACCESS_KEY',
    'GOOGLE_PRODUCTS_DEBUG' => 'GPSF_DEBUG',
    'GOOGLE_PRODUCTS_OUTPUT_FILENAME' => 'GPSF_OUTPUT_FILENAME',
    'GOOGLE_PRODUCTS_COMPRESS' => 'GPSF_COMPRESS',
    'GOOGLE_PRODUCTS_DIRECTORY' => 'GPSF_DIRECTORY',
    'GOOGLE_PRODUCTS_XML_SANITIZATION' => 'GPSF_XML_SANITIZATION',
    'GOOGLE_PRODUCTS_MAX_EXECUTION_TIME' => 'GPSF_MAX_EXECUTION_TIME',
    'GOOGLE_PRODUCTS_MEMORY_LIMIT' => 'GPSF_MEMORY_LIMIT',
    'GOOGLE_PRODUCTS_MAX_PRODUCTS' => 'GPSF_MAX_PRODUCTS',
    'GOOGLE_PRODUCTS_START_PRODUCTS' => 'GPSF_START_PRODUCTS',
    'GOOGLE_PRODUCTS_POS_CATEGORIES' => 'GPSF_POS_CATEGORIES',
    'GOOGLE_PRODUCTS_NEG_CATEGORIES' => 'GPSF_NEG_CATEGORIES',
    'GOOGLE_PRODUCTS_POS_MANUFACTURERS' => 'GPSF_POS_MANUFACTURERS',
    'GOOGLE_PRODUCTS_NEG_MANUFACTURERS' => 'GPSF_NEG_MANUFACTURERS',
    'GOOGLE_PRODUCTS_EXPIRATION_BASE' => 'GPSF_EXPIRATION_BASE',
    'GOOGLE_PRODUCTS_EXPIRATION_DAYS' => 'GPSF_EXPIRATION_DAYS',
    'GOOGLE_PRODUCTS_CURRENCY' => 'GPSF_CURRENCY',
    'GOOGLE_PRODUCTS_CONDITION' => 'GPSF_CONDITION',
    'GOOGLE_PRODUCTS_DEFAULT_PRODUCT_TYPE' => 'GPSF_DEFAULT_PRODUCT_TYPE',
    'GOOGLE_PRODUCTS_PRODUCT_TYPE' => 'GPSF_PRODUCT_TYPE',
    'GOOGLE_PRODUCTS_WEIGHT' => 'GPSF_WEIGHT',
    'GOOGLE_PRODUCTS_UNITS' => 'GPSF_UNITS',
    'GOOGLE_PRODUCTS_META_TITLE' => 'GPSF_META_TITLE',
    'GOOGLE_PRODUCTS_USE_CPATH' => 'GPSF_USE_CPATH',
    'GOOGLE_PRODUCTS_DEFAULT_PRODUCT_CATEGORY' => 'GPSF_DEFAULT_PRODUCT_CATEGORY',
    'GOOGLE_PRODUCTS_TAX_DISPLAY' => 'GPSF_TAX_DISPLAY',
    'GOOGLE_PRODUCTS_TAX_COUNTRY' => 'GPSF_TAX_COUNTRY',
    'GOOGLE_PRODUCTS_TAX_REGION' => 'GPSF_TAX_REGION',
    'GOOGLE_PRODUCTS_TAX_SHIPPING' => 'GPSF_TAX_SHIPPING',
    'GOOGLE_PRODUCTS_SHIPPING_METHOD' => 'GPSF_SHIPPING_METHOD',
    'GOOGLE_PRODUCTS_RATE_ZONE' => 'GPSF_RATE_ZONE',
    'GOOGLE_PRODUCTS_SHIPPING_COUNTRY' => 'GPSF_SHIPPING_COUNTRY',
    'GOOGLE_PRODUCTS_SHIPPING_REGION' => 'GPSF_SHIPPING_REGION',
    'GOOGLE_PRODUCTS_SHIPPING_SERVICE' => 'GPSF_SHIPPING_SERVICE',
    'GOOGLE_PRODUCTS_ALTERNATE_IMAGE_URL' => 'GPSF_ALTERNATE_IMAGE_URL',
    'GOOGLE_PRODUCTS_IMAGE_HANDLER' => 'GPSF_IMAGE_HANDLER',
    'GOOGLE_PRODUCTS_ENABLED' => 'GPSF_ENABLED',
    'GOOGLE_PRODUCTS_INCLUDE_MIN_QUANITY' => 'GPSF_INCLUDE_MIN_QUANTITY',
    'GOOGLE_PRODUCTS_SKIP_DUPLICATE_TITLES' => 'GPSF_SKIP_DUPLICATE_TITLES',
    'GOOGLE_PRODUCTS_INCLUDE_OUT_OF_STOCK' => 'GPSF_INCLUDE_OUT_OF_STOCK',
    'GOOGLE_PRODUCTS_INCLUDE_ADDITIONAL_IMAGES' => 'GPSF_INCLUDE_ADDITIONAL_IMAGES',
    'GOOGLE_PRODUCTS_SHIPPING_LABEL' => 'GPSF_SHIPPING_LABEL',
    'GOOGLE_PRODUCTS_OFFER_ID' => 'GPSF_OFFER_ID',
];

$copy_logfile = DIR_FS_LOGS . '/copy_google_search_configuration_' . date('YmdHis') . '.log';
$messages = [];
$google_products = $db->Execute(
    "SELECT configuration_key, configuration_value, configuration_title
       FROM " . TABLE_CONFIGURATION . "
      WHERE configuration_key IN ('" . implode("', '", array_keys($settings_to_copy)) . "')"
);
foreach ($google_products as $next_setting) {
    $source_key = $next_setting['configuration_key'];
    $target_key = $settings_to_copy[$source_key];
    $display_value = ($source_key === 'GOOGLE_PRODUCTS_ACCESS_KEY') ? '[redacted]' : $next_setting['configuration_value'];

    if ($next_setting['configuration_key'] === 'GOOGLE_PRODUCTS_OFFER_ID') {
        if ($next_setting['configuration_value'] !== 'id' && $next_setting['configuration_value'] !== 'model') {
            $messages[] = 'Not copied ' . $next_setting['configuration_title'] . ' (' . $display_value . '); value not supported by GPSF.';
            continue;
        }
    }

    $target_setting = $db->Execute(
        "SELECT configuration_id
           FROM " . TABLE_CONFIGURATION . "
          WHERE configuration_key = '" . zen_db_input($target_key) . "'
          LIMIT 1"
    );
    if ($target_setting->EOF) {
        $messages[] = 'Not copied ' . $next_setting['configuration_title'] . '; target setting ' . $target_key . ' does not exist.';
        continue;
    }

    $db->Execute(
        "UPDATE " . TABLE_CONFIGURATION . "
            SET configuration_value = '" . zen_db_input($next_setting['configuration_value']) . "'
          WHERE configuration_key = '" . zen_db_input($target_key) . "'"
    );
    $messages[] = 'Copied ' . $next_setting['configuration_title'] . ' (' . $display_value . ') to ' . $target_key . '.';
}

foreach ($messages as $message) {
    error_log($message . PHP_EOL, 3, $copy_logfile);
}

header('Content-Type: text/html; charset=' . CHARSET);
echo '<!doctype html><html><head><meta charset="' . htmlspecialchars(CHARSET, ENT_QUOTES, CHARSET) . '"><title>GPSF configuration conversion</title></head><body>';
echo '<h1>GPSF configuration conversion complete</h1>';
echo '<p>Review the results below, configure the Reimagined Release settings manually, and delete this script from the admin directory now.</p><ul>';
foreach ($messages as $message) {
    echo '<li>' . htmlspecialchars($message, ENT_QUOTES, CHARSET) . '</li>';
}
echo '</ul><p>Log file: <code>' . htmlspecialchars($copy_logfile, ENT_QUOTES, CHARSET) . '</code></p></body></html>';

require DIR_WS_INCLUDES . 'application_bottom.php';
