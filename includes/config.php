<?php
/**
 * WPWay Configuration
 * Framework initialization and setup
 */

namespace WPWay\Config;

if (!defined('ABSPATH')) exit;

class Configuration {
    private static $config = [
        'version' => '1.0.0',
        'debug' => false,  // Will be set in init()
        'cache' => true,
        'ssr_enabled' => true,
        'hydration_enabled' => true,
        'lazy_loading' => true,
        'plugin_ecosystem' => true,
        'gutenberg_integration' => true,
        'rest_api_enabled' => true,
        'performance_optimization' => true,
        'dev_tools' => false,  // Will be set in init()
        
        // Asset URLs
        'assets_url' => '',
        'components_url' => '',
        
        // Cache duration (in seconds)
        'cache_ttl' => 3600,
        'component_cache_ttl' => 1800,
        
        // Performance thresholds
        'lazy_load_threshold' => 1000,
        'code_split_threshold' => 50000,
        
        // SSR/Hydration
        'prerender' => false,
        'hydration_data_inline' => true,
        
        // Routes for SPA
        'spa_root' => '#app',
        'hash_based_routing' => false,
        'history_api' => true
    ];

    /**
     * Initialize configuration
     */
    public static function init($plugin_dir_url = '') {
        // Set debug mode based on WordPress settings
        if (defined('WP_DEBUG')) {
            self::$config['debug'] = WP_DEBUG;
            self::$config['dev_tools'] = WP_DEBUG;
        }
        
        // Set asset URLs
        if (!empty($plugin_dir_url)) {
            self::$config['assets_url'] = $plugin_dir_url . 'assets/';
            self::$config['components_url'] = $plugin_dir_url . 'wpway-components/';
        }
    }

    /**
     * Get configuration value
     */
    public static function get($key = null) {
        if ($key === null) {
            return self::$config;
        }
        return self::$config[$key] ?? null;
    }

    /**
     * Set configuration value
     */
    public static function set($key, $value) {
        self::$config[$key] = $value;
    }

    /**
     * Merge configuration
     */
    public static function merge($config) {
        self::$config = array_merge(self::$config, $config);
    }

    /**
     * Check if feature is enabled
     */
    public static function isEnabled($feature) {
        return (bool)(self::$config[$feature] ?? false);
    }

    /**
     * Get environment
     */
    public static function getEnvironment() {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            return 'development';
        }
        return 'production';
    }

    /**
     * Export configuration for JavaScript
     */
    public static function exportToJs() {
        return [
            'version' => self::$config['version'],
            'debug' => self::$config['debug'],
            'assetsUrl' => self::$config['assets_url'],
            'componentsUrl' => self::$config['components_url'],
            'spaRoot' => self::$config['spa_root'],
            'features' => [
                'ssr' => self::$config['ssr_enabled'],
                'hydration' => self::$config['hydration_enabled'],
                'lazyLoading' => self::$config['lazy_loading'],
                'pluginEcosystem' => self::$config['plugin_ecosystem'],
                'gutenberg' => self::$config['gutenberg_integration'],
                'restApi' => self::$config['rest_api_enabled'],
                'performanceOptimization' => self::$config['performance_optimization']
            ]
        ];
    }
}

// Export to global
if (!function_exists('wpway_config')) {
    function wpway_config($key = null) {
        return Configuration::get($key);
    }
}

if (!function_exists('wpway_config_set')) {
    function wpway_config_set($key, $value) {
        return Configuration::set($key, $value);
    }
}
