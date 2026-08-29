<?php

$root = dirname(__DIR__);
$generator = file_get_contents($root . '/includes/classes/gpsfFeedGenerator.php');
$installer = file_get_contents($root . '/YOUR_ADMIN/includes/init_includes/init_gpsf_admin.php');
$menuLanguage = file_get_contents($root . '/YOUR_ADMIN/includes/languages/english/extra_definitions/gpsf_admin_extra_definitions.php');

$checks = [
    'included categories use category assignments' => str_contains($generator, 'gpsf_pc_include.categories_id IN'),
    'excluded categories use category assignments' => str_contains($generator, 'gpsf_pc_exclude.categories_id IN'),
    'master category is no longer the category filter' => !str_contains($generator, 'p.master_categories_id IN ('),
    'query exclusions are diagnosed' => str_contains($generator, 'Initial query exclusion - '),
    'release version is 1.0.14' => str_contains($installer, "RHS_GPSF_CURRENT_VERSION', '1.0.14"),
    'admin menus use the short label' => substr_count($menuLanguage, "'Google Product Feeder'") === 2,
];

$failed = false;
foreach ($checks as $description => $passed) {
    echo ($passed ? 'PASS' : 'FAIL') . ': ' . $description . PHP_EOL;
    $failed = $failed || !$passed;
}

exit($failed ? 1 : 0);
