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
    // Load bootstrap
    $bootstrap_file = WPWAY_DIR . 'includes/bootstrap-simple.php';
    
    if (file_exists($bootstrap_file)) {
        try {
            require_once $bootstrap_file;
        } catch (\Exception $e) {
            // Log error but don't crash
            error_log('[WPWay] Fatal Error: ' . $e->getMessage());
            error_log('[WPWay] File: ' . $e->getFile() . ' Line: ' . $e->getLine());
        }
    } else {
        error_log('[WPWay] Bootstrap file not found: ' . $bootstrap_file);
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

