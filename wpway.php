<?php
/**
 * Plugin Name: WPWay
 * Description: React-like Frontend Framework for WordPress with SPA, SSR, Gutenberg blocks, and plugin ecosystem
 * Version: 1.0.0
 * Author: Mukesh Pathak
 * License: GPL-2.0-or-later
 * Text Domain: wpway
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('WPWay: Direct access not allowed');
}

// Define plugin constants
define('WPWAY_VERSION', '1.0.0');
define('WPWAY_DIR', plugin_dir_path(__FILE__));
define('WPWAY_URL', plugin_dir_url(__FILE__));

// ============================================================================
// INITIALIZATION HOOK
// ============================================================================

add_action('plugins_loaded', 'wpway_initialize', 5);

/**
 * Initialize WPWay Framework
 * Called after WordPress and all plugins are loaded
 */
function wpway_initialize() {
    // Log initialization start
    error_log('[WPWay] Initialization started');
    
    // Load bootstrap
    $bootstrap_file = WPWAY_DIR . 'includes/bootstrap-simple.php';
    
    if (!file_exists($bootstrap_file)) {
        error_log('[WPWay] Bootstrap file not found: ' . $bootstrap_file);
        return;
    }
    
    // Check if bootstrap can be included
    error_log('[WPWay] Loading bootstrap from: ' . $bootstrap_file);
    
    // Use output buffering to catch any output
    ob_start();
    try {
        require_once $bootstrap_file;
    } catch (\Exception $e) {
        ob_end_clean();
        error_log('[WPWay] Exception during bootstrap load: ' . $e->getMessage());
        error_log('[WPWay] Exception file: ' . $e->getFile() . ' Line: ' . $e->getLine());
        return;
    }
    ob_end_clean();
    
    // Log bootstrap loaded
    error_log('[WPWay] Bootstrap file loaded successfully');
    
    // Now initialize the framework
    if (class_exists('WPWay\BootstrapSimple')) {
        error_log('[WPWay] BootstrapSimple class found, calling init()');
        try {
            \WPWay\BootstrapSimple::init();
            error_log('[WPWay] Framework init() completed successfully');
        } catch (\Exception $e) {
            error_log('[WPWay] Exception during init(): ' . $e->getMessage());
            error_log('[WPWay] Exception file: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
    } else {
        error_log('[WPWay] BootstrapSimple class NOT FOUND after including bootstrap file');
    }
}

// ============================================================================
// ACTIVATION & DEACTIVATION HOOKS
// ============================================================================

/**
 * Run on plugin activation
 */
function wpway_activate() {
    // Create required directories if they don't exist
    $dirs = [
        WPWAY_DIR . 'cache',
        WPWAY_DIR . 'logs'
    ];
    
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            @wp_mkdir_p($dir);
        }
    }
    
    error_log('[WPWay] Plugin activated successfully');
}

/**
 * Run on plugin deactivation
 */
function wpway_deactivate() {
    error_log('[WPWay] Plugin deactivated');
}

// Register activation/deactivation hooks
register_activation_hook(__FILE__, 'wpway_activate');
register_deactivation_hook(__FILE__, 'wpway_deactivate');

