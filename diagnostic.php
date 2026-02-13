#!/usr/bin/env php
<?php
/**
 * WPWay Plugin Diagnostic Tool
 * 
 * Usage:
 * php diagnostic.php
 * 
 * This tool tests each component of WPWay independently to identify issues
 */

// Prevent execution in web context
if (php_sapi_name() !== 'cli') {
    die('This script must be run from command line (CLI) only');
}

echo "\n";
echo "╔════════════════════════════════════════════════════════████\n";
echo "║ WPWay Framework - Diagnostic Tool\n";
echo "╠════════════════════════════════════════════════════════════\n";

$errors = [];
$warnings = [];
$success = [];

// ============================================================================
// 1. Check PHP Version
// ============================================================================
echo "\n[1/10] Checking PHP Version...";
$php_version = phpversion();
if (version_compare($php_version, '7.4.0') >= 0) {
    echo " ✓\n";
    $success[] = "PHP Version: $php_version (OK)";
} else {
    echo " ✗\n";
    $errors[] = "PHP Version: $php_version - WPWay requires PHP 7.4+ (CRITICAL)";
}

// ============================================================================
// 2. Check File Structure
// ============================================================================
echo "[2/10] Checking File Structure...";

$required_files = [
    'wpway.php',
    'includes/bootstrap-simple.php',
    'includes/config.php',
    'includes/core/framework.php',
    'includes/core/component.php',
    'includes/core/virtual-dom.php',
    'includes/router/router.php',
    'includes/state/store.php',
    'includes/gutenberg/blocks.php',
    'includes/ssr/hydration.php',
    'includes/plugin-api/plugin-system.php',
    'includes/performance/optimizer.php',
    'includes/dev-tools.php',
];

$plugin_dir = dirname(__FILE__);
$missing_files = [];

foreach ($required_files as $file) {
    $full_path = $plugin_dir . '/' . $file;
    if (!file_exists($full_path)) {
        $missing_files[] = $file;
    }
}

if (empty($missing_files)) {
    echo " ✓\n";
    $success[] = "All required files present";
} else {
    echo " ✗\n";
    $errors[] = "Missing files: " . implode(', ', $missing_files);
}

// ============================================================================
// 3. Check File Permissions
// ============================================================================
echo "[3/10] Checking File Permissions...";

$permission_issues = [];
foreach ($required_files as $file) {
    $full_path = $plugin_dir . '/' . $file;
    if (file_exists($full_path) && !is_readable($full_path)) {
        $permission_issues[] = $file;
    }
}

if (empty($permission_issues)) {
    echo " ✓\n";
    $success[] = "All files are readable";
} else {
    echo " ✗\n";
    $warnings[] = "Permission issues with: " . implode(', ', $permission_issues);
}

// ============================================================================
// 4. Check Syntax of Core Files
// ============================================================================
echo "[4/10] Checking PHP Syntax...";

$syntax_errors = [];
foreach ($required_files as $file) {
    $full_path = $plugin_dir . '/' . $file;
    if (file_exists($full_path)) {
        $output = shell_exec("php -l " . escapeshellarg($full_path) . " 2>&1");
        if (strpos($output, 'No syntax errors') === false) {
            $syntax_errors[$file] = trim($output);
        }
    }
}

if (empty($syntax_errors)) {
    echo " ✓\n";
    $success[] = "All PHP files have valid syntax";
} else {
    echo " ✗\n";
    foreach ($syntax_errors as $file => $error) {
        $errors[] = "Syntax error in $file: $error";
    }
}

// ============================================================================
// 5. Check Namespace Declarations
// ============================================================================
echo "[5/10] Checking Namespace Declarations...";

$invalid_namespaces = [];
$namespace_patterns = [
    'includes/config.php' => 'namespace WPWay\Config',
    'includes/core/framework.php' => 'namespace WPWay\Core',
    'includes/core/component.php' => 'namespace WPWay\Core',
    'includes/router/router.php' => 'namespace WPWay\Router',
    'includes/state/store.php' => 'namespace WPWay\State',
];

foreach ($namespace_patterns as $file => $pattern) {
    $full_path = $plugin_dir . '/' . $file;
    if (file_exists($full_path)) {
        $content = file_get_contents($full_path, false, null, 0, 500);
        if (strpos($content, $pattern) === false) {
            $invalid_namespaces[] = $file;
        }
    }
}

if (empty($invalid_namespaces)) {
    echo " ✓\n";
    $success[] = "All namespace declarations are correct";
} else {
    echo " ✗\n";
    $warnings[] = "Potential namespace issues in: " . implode(', ', $invalid_namespaces);
}

// ============================================================================
// 6. Test File Loading
// ============================================================================
echo "[6/10] Testing File Loading...";

// Simulate minimal WordPress constants
if (!defined('ABSPATH')) {
    define('ABSPATH', $plugin_dir . '/../../../');
}

$load_errors = [];
foreach ($required_files as $file) {
    $full_path = $plugin_dir . '/' . $file;
    if (file_exists($full_path)) {
        // Skip if WordPress functions are required
        $content = file_get_contents($full_path);
        
        if (strpos($content, 'wp_enqueue') === false && 
            strpos($content, 'add_action') === false) {
            
            if (@include_once $full_path === false) {
                $load_errors[] = $file;
            }
        }
    }
}

if (empty($load_errors)) {
    echo " ✓\n";
    $success[] = "Basic file loading successful";
} else {
    echo " ✗\n";
    $errors[] = "Error loading files: " . implode(', ', $load_errors);
}

// ============================================================================
// 7. Check for Common Issues
// ============================================================================
echo "[7/10] Checking for Common Issues...";

$issues = [];

// Check for closing tag issues
$bootstrap = file_get_contents($plugin_dir . '/includes/bootstrap-simple.php');
$closing_braces = substr_count($bootstrap, '}');
$opening_braces = substr_count($bootstrap, '{');

if ($closing_braces !== $opening_braces + 1) {
    $issues[] = "Potential brace mismatch in bootstrap-simple.php";
}

if (empty($issues)) {
    echo " ✓\n";
    $success[] = "No common issues detected";
} else {
    echo " ✗\n";
    $warnings = array_merge($warnings, $issues);
}

// ============================================================================
// 8. Check WordPress Integration
// ============================================================================
echo "[8/10] Checking WordPress Integration...";

$wp_functions = ['add_action', 'wp_enqueue_script', 'wp_enqueue_style'];
$main_file = file_get_contents($plugin_dir . '/wpway.php');

$wp_issues = [];
foreach ($wp_functions as $func) {
    if (strpos($main_file, $func) === false) {
        $wp_issues[] = $func;
    }
}

if (empty($wp_issues)) {
    echo " ✓\n";
    $success[] = "WordPress integration appears correct";
} else {
    echo " ✗\n";
    $warnings[] = "Missing WordPress functions: " . implode(', ', $wp_issues);
}

// ============================================================================
// 9. Check Asset Files
// ============================================================================
echo "[9/10] Checking Asset Files...";

$asset_files = [
    'assets/wpway-core.js',
    'assets/wpway.css',
];

$missing_assets = [];
foreach ($asset_files as $file) {
    if (!file_exists($plugin_dir . '/' . $file)) {
        $missing_assets[] = $file;
    }
}

if (empty($missing_assets)) {
    echo " ✓\n";
    $success[] = "All asset files present";
} else {
    echo " ✗\n";
    $warnings[] = "Missing asset files: " . implode(', ', $missing_assets);
}

// ============================================================================
// 10. Generate Summary
// ============================================================================
echo "[10/10] Generating Summary...\n";

echo "\n╠════════════════════════════════════════════════════════════\n";
echo "║ RESULTS\n";
echo "╠════════════════════════════════════════════════════════════\n";

// Show successes
if (!empty($success)) {
    echo "\n✓ SUCCESS (" . count($success) . ")\n";
    foreach ($success as $msg) {
        echo "  • $msg\n";
    }
}

// Show warnings
if (!empty($warnings)) {
    echo "\n⚠ WARNINGS (" . count($warnings) . ")\n";
    foreach ($warnings as $msg) {
        echo "  • $msg\n";
    }
}

// Show errors
if (!empty($errors)) {
    echo "\n✗ ERRORS (" . count($errors) . ")\n";
    foreach ($errors as $msg) {
        echo "  • $msg\n";
    }
}

// Overall status
echo "\n╠════════════════════════════════════════════════════════════\n";
if (empty($errors)) {
    echo "║ STATUS: ✓ READY TO ACTIVATE\n";
    $exit_code = 0;
} else {
    echo "║ STATUS: ✗ ISSUES FOUND - Please review errors above\n";
    $exit_code = 1;
}

echo "╚════════════════════════════════════════════════════════════\n\n";

exit($exit_code);
