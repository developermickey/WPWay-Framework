<?php
/**
 * Bootstrap WPWay Framework - Simplified Version
 * This version loads core components with better error handling
 */

namespace WPWay;

if (!defined('ABSPATH')) exit;

final class BootstrapSimple {
    private static $initialized = false;

    /**
     * Initialize framework - simplified version
     */
    public static function init() {
        if (self::$initialized) {
            error_log('[WPWay] Framework already initialized');
            return;
        }

        self::$initialized = true;
        error_log('[WPWay] === Framework Initialization Starting ===');
        
        // Step 1: Load config only
        self::safeRequire(__DIR__ . '/config.php');
        
        // Step 2: Initialize configuration
        error_log('[WPWay] Step 2: Initializing configuration');
        if (class_exists('WPWay\Config\Configuration')) {
            try {
                \WPWay\Config\Configuration::init(plugin_dir_url(__DIR__ . '/../wpway.php'));
            } catch (\Exception $e) {
                error_log('WPWay Config Error: ' . $e->getMessage());
            }
        }
        
        // Step 3: Load core framework
        error_log('[WPWay] Step 3: Loading core framework');
        self::safeRequire(__DIR__ . '/core/framework.php');
        self::safeRequire(__DIR__ . '/core/component.php');
        self::safeRequire(__DIR__ . '/core/virtual-dom.php');

        // Step 4: Load other modules
        error_log('[WPWay] Step 4: Loading additional modules');
        self::safeRequire(__DIR__ . '/router/router.php');
        self::safeRequire(__DIR__ . '/state/store.php');
        self::safeRequire(__DIR__ . '/gutenberg/blocks.php');
        self::safeRequire(__DIR__ . '/ssr/hydration.php');
        self::safeRequire(__DIR__ . '/plugin-api/plugin-system.php');
        self::safeRequire(__DIR__ . '/performance/optimizer.php');
        self::safeRequire(__DIR__ . '/dev-tools.php');
        
        // Step 5: Load admin menu/dashboard (temporarily disabled for debugging)
        error_log('[WPWay] Step 5: Skipping admin menu for now (debugging)');
        // self::safeRequire(__DIR__ . '/admin/menu.php');

        // Step 6: Register hooks
        error_log('[WPWay] Step 6: Registering hooks');
        add_action('wp_enqueue_scripts', [self::class, 'enqueueAssets']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminAssets']);
        add_action('init', [self::class, 'registerComponents']);
        
        error_log('[WPWay] === Framework Initialization Complete ===');
    }

    /**
     * Safely require a file
     */
    private static function safeRequire($file) {
        if (!file_exists($file)) {
            error_log('[WPWay] File not found: ' . $file);
            return false;
        }

        $filename = basename($file);
        error_log('[WPWay] Loading: ' . $filename);
        
        try {
            require_once $file;
            error_log('[WPWay] ✓ Loaded: ' . $filename);
            return true;
        } catch (\Throwable $e) {
            error_log('[WPWay] ERROR loading ' . $filename . ': ' . $e->getMessage());
            error_log('[WPWay] Line: ' . $e->getLine());
            return false;
        }
    }

    /**
     * Enqueue frontend assets
     */
    public static function enqueueAssets() {
        $plugin_url = plugin_dir_url(__DIR__ . '/../wpway.php');

        // Core framework
        wp_enqueue_script(
            'wpway-core',
            $plugin_url . 'assets/wpway-core.js',
            [],
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'wpway-css',
            $plugin_url . 'assets/wpway.css'
        );
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueueAdminAssets() {
        $plugin_url = plugin_dir_url(__DIR__ . '/../wpway.php');

        wp_enqueue_script(
            'wpway-admin',
            $plugin_url . 'assets/admin.js',
            ['wp-plugins', 'wp-edit-post', 'wp-element'],
            '1.0.0'
        );
    }

    /**
     * Register components
     */
    public static function registerComponents() {
        if (!class_exists('WPWay\Core\Framework')) {
            return;
        }

        $framework = \WPWay\Core\Framework::getInstance();
        
        // Register example components
        $framework->registerComponent('BlogList', 'WPWay\Components\BlogList');
        $framework->registerComponent('Hero', 'WPWay\Components\Hero');
    }
}

// Initialize on plugins_loaded
add_action('plugins_loaded', [BootstrapSimple::class, 'init'], 5);
