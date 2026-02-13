<?php
/**
 * WPWay Menu Diagnostic Checker
 * Upload this to your WordPress root folder and visit:
 * http://thescriptly.in/wpway-menu-checker.php
 */

// Check if WordPress is loaded
if (!file_exists(__DIR__ . '/wp-load.php')) {
    die('❌ ERROR: WordPress not found in this directory');
}

// Load WordPress
require_once __DIR__ . '/wp-load.php';

echo "========== WPWay MENU DIAGNOSTIC CHECKER ==========\n\n";

// Check 1: Is plugin activated?
echo "TEST 1: Plugin Status\n";
echo "─────────────────────────\n";
if (is_plugin_active('wpway/wpway.php')) {
    echo "✅ WPWay Plugin is ACTIVE\n\n";
} else {
    echo "❌ WPWay Plugin is NOT ACTIVE\n";
    echo "   Please activate it first!\n\n";
}

// Check 2: Is the menu file loaded?
echo "TEST 2: Menu File Status\n";
echo "─────────────────────────\n";
$menu_file = __DIR__ . '/wp-content/plugins/wpway/includes/admin/menu.php';
if (file_exists($menu_file)) {
    echo "✅ Menu file exists: {$menu_file}\n";
} else {
    echo "❌ Menu file NOT found: {$menu_file}\n\n";
}

// Check 3: Are the callback functions defined?
echo "TEST 3: Callback Functions\n";
echo "──────────────────────────\n";
$functions = [
    'wpway_dashboard_page',
    'wpway_components_page',
    'wpway_pages_page',
    'wpway_code_editor_page',
    'wpway_settings_page',
    'wpway_documentation_page',
];

foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "✅ {$func} - DEFINED\n";
    } else {
        echo "❌ {$func} - NOT DEFINED\n";
    }
}

echo "\n";

// Check 4: Current user permissions
echo "TEST 4: User Permissions\n";
echo "────────────────────────\n";
$current_user = wp_get_current_user();
echo "Current User: {$current_user->user_login}\n";

if (current_user_can('manage_options')) {
    echo "✅ User can manage_options (ADMIN)\n\n";
} else {
    echo "❌ User CANNOT manage_options\n";
    echo "   You must be an admin to see WPWay menu\n\n";
}

// Check 5: Debug log for WPWay messages
echo "TEST 5: Debug Log Messages\n";
echo "──────────────────────────\n";
if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
    $debug_log = __DIR__ . '/wp-content/debug.log';
    if (file_exists($debug_log)) {
        $lines = file($debug_log, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $wpway_lines = array_filter($lines, function($line) {
            return strpos($line, 'WPWay') !== false;
        });
        
        // Get last 20 WPWay messages
        $recent = array_slice(array_values($wpway_lines), -20);
        
        if (!empty($recent)) {
            echo "📋 Last WPWay log messages:\n";
            foreach ($recent as $msg) {
                echo "   {$msg}\n";
            }
        } else {
            echo "ℹ️  No WPWay messages in debug.log\n";
        }
    } else {
        echo "ℹ️  Debug log not created yet (visit WordPress page first)\n";
    }
} else {
    echo "⚠️  WordPress debug logging not enabled\n";
    echo "   Edit wp-config.php and add:\n";
    echo "   define('WP_DEBUG', true);\n";
    echo "   define('WP_DEBUG_LOG', true);\n";
    echo "   define('WP_DEBUG_DISPLAY', false);\n";
}

echo "\n";

// Check 6: Check registered menus
echo "TEST 6: Global Menu Structure\n";
echo "──────────────────────────────\n";
global $menu, $submenu;

// Look for WPWay menu
$wpway_found = false;
foreach ($menu as $item) {
    if (strpos($item[0], 'WPWay') !== false) {
        echo "✅ WPWay menu item FOUND\n";
        echo "   Menu Slug: {$item[2]}\n";
        echo "   Menu Title: {$item[0]}\n";
        $wpway_found = true;
        break;
    }
}

if (!$wpway_found) {
    echo "❌ WPWay menu item NOT in global \$menu array\n";
    echo "   This might mean the admin_menu hook hasn't fired yet\n\n";
}

echo "\n";
echo "========== DIAGNOSTIC COMPLETE ==========\n";
echo "\nIf tests fail, copy the output and send to your developer.\n";
?>
