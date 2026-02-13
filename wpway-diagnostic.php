<?php
/**
 * WPWay Plugin Diagnostic Test
 * 
 * Upload this file to WordPress root folder
 * Access it in browser: yoursite.com/wpway-diagnostic.php
 * Then DELETE this file after getting results
 */

// Load WordPress
require_once('wp-load.php');

echo '<h1>WPWay Plugin Diagnostic Report</h1>';
echo '<pre style="background: #f5f5f5; padding: 20px; border-radius: 5px; font-family: monospace;">';

// TEST 1: PHP Syntax
echo "TEST 1: Checking PHP Files for Syntax Errors\n";
echo "═══════════════════════════════════════════════════════════════\n";

$files_to_check = [
    'wp-content/plugins/wpway/wpway.php',
    'wp-content/plugins/wpway/includes/bootstrap-simple.php',
    'wp-content/plugins/wpway/includes/admin/menu.php',
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        $output = shell_exec("php -l " . escapeshellarg($full_path) . " 2>&1");
        echo "✓ $file\n  → " . trim($output) . "\n\n";
    } else {
        echo "✗ $file\n  → FILE NOT FOUND!\n\n";
    }
}

// TEST 2: Plugin Active Status
echo "\nTEST 2: Plugin Status\n";
echo "═══════════════════════════════════════════════════════════════\n";

$active_plugins = get_option('active_plugins', []);
if (in_array('wpway/wpway.php', $active_plugins)) {
    echo "✓ WPWay Plugin: ACTIVE\n";
} else {
    echo "✗ WPWay Plugin: NOT ACTIVE\n";
    echo "  Active plugins: " . implode(', ', $active_plugins) . "\n";
}
echo "\n";

// TEST 3: Class Existence
echo "TEST 3: Class & Namespace Check\n";
echo "═══════════════════════════════════════════════════════════════\n";

if (class_exists('WPWay\BootstrapSimple')) {
    echo "✓ WPWay\\BootstrapSimple class EXISTS\n";
    if (method_exists('WPWay\BootstrapSimple', 'init')) {
        echo "✓ init() method EXISTS\n";
    } else {
        echo "✗ init() method NOT FOUND\n";
    }
} else {
    echo "✗ WPWay\\BootstrapSimple class NOT FOUND\n";
    echo "  Available classes: " . implode(', ', get_declared_classes()) . "\n";
}
echo "\n";

// TEST 4: Admin Menu Check
echo "TEST 4: Admin Menu Registration\n";
echo "═══════════════════════════════════════════════════════════════\n";

if (function_exists('wpway_dashboard_page')) {
    echo "✓ wpway_dashboard_page() function EXISTS\n";
} else {
    echo "✗ wpway_dashboard_page() function NOT FOUND\n";
}

if (function_exists('wpway_components_page')) {
    echo "✓ wpway_components_page() function EXISTS\n";
} else {
    echo "✗ wpway_components_page() function NOT FOUND\n";
}
echo "\n";

// TEST 5: Debug Log
echo "TEST 5: Debug Log (Last 20 Lines)\n";
echo "═══════════════════════════════════════════════════════════════\n";

$debug_log = __DIR__ . '/wp-content/debug.log';
if (file_exists($debug_log)) {
    $lines = file($debug_log, FILE_IGNORE_NEW_LINES);
    $last_lines = array_slice($lines, -20);
    foreach ($last_lines as $line) {
        echo $line . "\n";
    }
} else {
    echo "? Debug log not found or WordPress debug is not enabled\n";
    echo "  To enable: Edit wp-config.php and set WP_DEBUG = true\n";
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "REPORT END\n";
echo "═══════════════════════════════════════════════════════════════\n";

echo '</pre>';

echo '<hr>';
echo '<p><strong>Next Steps:</strong></p>';
echo '<ol>';
echo '<li>Review the tests above - look for ✗ marks</li>';
echo '<li>If you see ✗, that\'s the problem area</li>';
echo '<li>Take a screenshot and send to developer</li>';
echo '<li>Then DELETE this file (wpway-diagnostic.php)</li>';
echo '</ol>';
