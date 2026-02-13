<?php
/**
 * WPWay Setup Verification Script
 * Place this in WordPress root and access via browser
 * Then DELETE it
 */

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Load WordPress
require_once(ABSPATH . 'wp-load.php');

// Only run if user is admin
if (!current_user_can('manage_options')) {
    die('⛔ You must be logged in as Administrator');
}

echo '<h1>WPWay Setup Verification</h1>';
echo '<pre style="background:#f5f5f5; padding:20px; font-family:monospace;">';

// Check 1: Admin status
$user = wp_get_current_user();
echo "✓ User: " . $user->user_login . "\n";
echo "✓ Role: " . implode(', ', $user->roles) . "\n";
if (!in_array('administrator', $user->roles)) {
    echo "⚠️  WARNING: User is NOT administrator! WPWay menu won't show!\n";
}
echo "\n";

// Check 2: Plugin status
$plugins = get_option('active_plugins', []);
if (in_array('wpway/wpway.php', $plugins)) {
    echo "✓ WPWay Plugin: ACTIVE\n";
} else {
    echo "⚠️  WPWay Plugin: NOT ACTIVE\n";
}
echo "\n";

// Check 3: Class existence
if (class_exists('WPWay\Admin\AdminDashboard')) {
    echo "✓ AdminDashboard Class: LOADED\n";
} else {
    echo "⚠️  AdminDashboard Class: NOT FOUND\n";
}
echo "\n";

// Check 4: File existence
$files_to_check = [
    'wp-content/plugins/wpway/wpway.php' => 'Main Plugin File',
    'wp-content/plugins/wpway/includes/bootstrap-simple.php' => 'Bootstrap',
    'wp-content/plugins/wpway/includes/admin/dashboard.php' => 'Admin Dashboard',
    'wp-content/plugins/wpway/includes/admin/diagnostic.php' => 'Diagnostic',
    'wp-content/plugins/wpway/assets/admin.css' => 'Admin CSS',
    'wp-content/plugins/wpway/assets/admin.js' => 'Admin JS',
];

echo "File Status:\n";
foreach ($files_to_check as $file => $name) {
    $path = ABSPATH . $file;
    if (file_exists($path)) {
        $size = filesize($path);
        echo "  ✓ $name: EXISTS (" . $size . " bytes)\n";
    } else {
        echo "  ⚠️  $name: MISSING\n";
    }
}
echo "\n";

// Check 5: REST API
echo "REST API: ";
if (rest_api_init()) {
    echo "✓ ENABLED\n";
} else {
    echo "⚠️  Check if permalinks are configured\n";
}
echo "\n";

// Check 6: WordPress Version
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "PHP Version: " . phpversion() . "\n";
echo "\n";

// Check 7: Debug Log
$debug_log = ABSPATH . 'wp-content/debug.log';
if (file_exists($debug_log)) {
    $lines = file($debug_log);
    $last_lines = array_slice($lines, -5);
    echo "Latest Debug Log Entries:\n";
    foreach ($last_lines as $line) {
        if (strpos($line, 'WPWay') !== false) {
            echo "  " . trim($line) . "\n";
        }
    }
}

echo "\n";
echo "═══════════════════════════════════════════════════════════════\n";
echo "✅ If all items show ✓, WPWay should be working!\n";
echo "⚠️  If items show ⚠️, follow FIX_MENU_NOT_SHOWING.txt guide\n";
echo "\n";
echo "DELETE THIS FILE after checking!\n";
echo "═══════════════════════════════════════════════════════════════\n";

echo '</pre>';
