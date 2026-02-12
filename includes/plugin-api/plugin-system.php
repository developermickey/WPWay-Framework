<?php
/**
 * Plugin Ecosystem System
 * Third-party plugin registration and integration
 */

namespace WPWay\PluginAPI;

if (!defined('ABSPATH')) exit;

class PluginSystem {
    private static $instance;
    private $plugins = [];
    private $plugin_hooks = [];
    private $plugin_filters = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a plugin
     */
    public function registerPlugin($plugin_name, $plugin_class) {
        if (!class_exists($plugin_class)) {
            return new \WP_Error('invalid_plugin', "Plugin class $plugin_class not found");
        }

        $plugin = new $plugin_class();
        $this->plugins[$plugin_name] = $plugin;

        // Run plugin init if exists
        if (method_exists($plugin, 'init')) {
            $plugin->init();
        }

        return true;
    }

    /**
     * Get registered plugin
     */
    public function getPlugin($plugin_name) {
        return $this->plugins[$plugin_name] ?? null;
    }

    /**
     * Get all plugins
     */
    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * Add plugin hook
     */
    public function addPluginHook($hook_name, $callback, $priority = 10) {
        if (!isset($this->plugin_hooks[$hook_name])) {
            $this->plugin_hooks[$hook_name] = [];
        }

        $this->plugin_hooks[$hook_name][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->plugin_hooks[$hook_name], fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    /**
     * Execute plugin hook
     */
    public function executePluginHook($hook_name, ...$args) {
        if (!isset($this->plugin_hooks[$hook_name])) {
            return null;
        }

        foreach ($this->plugin_hooks[$hook_name] as $hook_data) {
            call_user_func_array($hook_data['callback'], $args);
        }
    }

    /**
     * Apply plugin filter
     */
    public function applyPluginFilter($filter_name, $value, ...$args) {
        if (!isset($this->plugin_filters[$filter_name])) {
            return $value;
        }

        foreach ($this->plugin_filters[$filter_name] as $filter_data) {
            $value = call_user_func_array($filter_data['callback'], array_merge([$value], $args));
        }

        return $value;
    }

    /**
     * Add plugin filter
     */
    public function addPluginFilter($filter_name, $callback, $priority = 10) {
        if (!isset($this->plugin_filters[$filter_name])) {
            $this->plugin_filters[$filter_name] = [];
        }

        $this->plugin_filters[$filter_name][] = [
            'callback' => $callback,
            'priority' => $priority
        ];

        // Sort by priority
        usort($this->plugin_filters[$filter_name], fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    /**
     * Check if plugin is active
     */
    public function isPluginActive($plugin_name) {
        return isset($this->plugins[$plugin_name]);
    }

    /**
     * Get plugin capabilities
     */
    public function getPluginCapabilities($plugin_name) {
        $plugin = $this->getPlugin($plugin_name);
        if ($plugin && method_exists($plugin, 'getCapabilities')) {
            return $plugin->getCapabilities();
        }
        return [];
    }

    /**
     * Export plugin manifest
     */
    public function exportPluginManifests() {
        $manifests = [];
        foreach ($this->plugins as $name => $plugin) {
            if (method_exists($plugin, 'getManifest')) {
                $manifests[$name] = $plugin->getManifest();
            }
        }
        return $manifests;
    }
}

/**
 * Base Plugin Class
 */
class Plugin {
    protected $name = '';
    protected $version = '1.0.0';
    protected $capabilities = [];

    public function __construct() {
        $this->setupCapabilities();
    }

    /**
     * Override in subclass to setup capabilities
     */
    protected function setupCapabilities() {
    }

    /**
     * Override in subclass for initialization
     */
    public function init() {
    }

    /**
     * Get plugin manifest
     */
    public function getManifest() {
        return [
            'name' => $this->name,
            'version' => $this->version,
            'capabilities' => $this->capabilities
        ];
    }

    /**
     * Get plugin capabilities
     */
    public function getCapabilities() {
        return $this->capabilities;
    }
}
