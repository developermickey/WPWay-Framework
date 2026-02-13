<?php
/**
 * Plugin Name: WPWay
 * Description: React-like Frontend Framework for WordPress with SPA, SSR, Gutenberg blocks, and plugin ecosystem
 * Version: 1.0.0
 * Author: Mukesh Pathak
 * License: GPL-2.0-or-later
 * Text Domain: wpway
 */

if (!defined('ABSPATH')) exit;

define('WPWAY_VERSION', '1.0.0');
define('WPWAY_DIR', plugin_dir_path(__FILE__));
define('WPWAY_URL', plugin_dir_url(__FILE__));

// Initialize framework
require_once WPWAY_DIR . 'includes/bootstrap.php';

// Hook into plugins_loaded to initialize
add_action('plugins_loaded', function() {
    try {
        if (class_exists('WPWay\Bootstrap')) {
            \WPWay\Bootstrap::init();
        }
    } catch (\Throwable $e) {
        error_log('WPWay Plugin Error: ' . $e->getMessage());
    }
});

