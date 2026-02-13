<?php
/**
 * WPWay Plugin Standalone Syntax Checker
 * 
 * This runs WITHOUT loading WordPress, so it can find parse errors
 * 
 * Upload to WordPress root and visit: yoursite.com/wpway-syntax-check.php
 * Then DELETE this file
 */

// Very simple - don't use WordPress functions

echo '<html><head><title>WPWay Syntax Checker</title></head><body>';
echo '<h1 style="font-family: Arial;">WPWay Plugin Syntax Check</h1>';
echo '<pre style="background: #f5f5f5; padding: 15px; border-radius: 5px;">';

// Define the WordPress root
$wp_root = __DIR__;
$plugin_dir = $wp_root . '/wp-content/plugins/wpway';

echo "Checking plugin files for PHP syntax errors...\n\n";

$files = [
    'wpway.php' => 'Main plugin file',
    'includes/bootstrap-simple.php' => 'Bootstrap',
    'includes/admin/menu.php' => 'Admin menu',
    'includes/config.php' => 'Config',
    'includes/core/framework.php' => 'Core framework',
    'includes/core/component.php' => 'Component class',
];

$all_ok = true;

foreach ($files as $file => $desc) {
    $full_path = $plugin_dir . '/' . $file;
    
    echo "Checking: $desc ($file)\n";
    
    if (!file_exists($full_path)) {
        echo "  ✗ FILE NOT FOUND!\n\n";
        $all_ok = false;
        continue;
    }
    
    // Use PHP to check syntax
    $output = shell_exec('php -l ' . escapeshellarg($full_path) . ' 2>&1');
    
    if (strpos($output, 'No syntax errors') !== false) {
        echo "  ✓ OK - No syntax errors\n\n";
    } else {
        echo "  ✗ SYNTAX ERROR:\n";
        echo "    " . trim($output) . "\n\n";
        $all_ok = false;
    }
}

if ($all_ok) {
    echo "\n✓✓✓ ALL FILES PASSED SYNTAX CHECK! ✓✓✓\n";
    echo "\nThe plugin files are syntactically correct.\n";
    echo "Any errors must be coming from somewhere else.\n";
} else {
    echo "\n✗✗✗ SYNTAX ERRORS FOUND ✗✗✗\n";
    echo "\nFix the errors above and try again.\n";
}

// Also check if bootstrap-simple.php includes
echo "\n" . str_repeat("=", 70) . "\n";
echo "Checking if bootstrap-simple.php (or its requires) can be included...\n\n";

$bootstrap = $plugin_dir . '/includes/bootstrap-simple.php';

if (file_exists($bootstrap)) {
    ob_start();
    $error = false;
    
    try {
        // Set error handler to catch warnings too
        set_error_handler(function($errno, $errstr) {
            throw new Exception("PHP Error: $errstr");
        });
        
        include $bootstrap;
        
        restore_error_handler();
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "✓ Bootstrap file included successfully (no output)\n";
            echo "✓ Class should be defined\n";
        } else {
            echo "! Bootstrap generated output: $output\n";
        }
    } catch (Exception $e) {
        restore_error_handler();
        ob_end_clean();
        echo "✗ Error including bootstrap:\n";
        echo "  " . $e->getMessage() . "\n";
        echo "  File: " . $e->getFile() . "\n";
        echo "  Line: " . $e->getLine() . "\n";
    }
} else {
    echo "✗ Bootstrap file not found!\n";
}

echo "\n" . str_repeat("=", 70) . "\n";
echo "\n✅ Syntax check complete!\n";
echo "\nNEXT: Delete this file (wpway-syntax-check.php)\n";
echo "THEN: Check wp-content/debug.log for [WPWay] initialization messages\n";

echo '</pre>';
echo '</body></html>';
