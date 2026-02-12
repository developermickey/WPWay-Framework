<?php
/**
 * WPWay Core Framework
 * React-like component system with Virtual DOM and lifecycle management
 */

namespace WPWay\Core;

if (!defined('ABSPATH')) exit;

class Framework {
    private static $instance;
    private $components = [];
    private $hooks = [];
    private $state_store = [];
    private $cache = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a component
     */
    public function registerComponent($name, $component_class) {
        if (!class_exists($component_class)) {
            return new \WP_Error('invalid_component', "Component class $component_class not found");
        }
        $this->components[$name] = $component_class;
        return true;
    }

    /**
     * Get registered component
     */
    public function getComponent($name) {
        return $this->components[$name] ?? null;
    }

    /**
     * Get all registered components
     */
    public function getComponents() {
        return $this->components;
    }

    /**
     * Add action hook (like WordPress but for framework events)
     */
    public function addHook($hook, $callback, $priority = 10, $accepted_args = 1) {
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = [];
        }
        $this->hooks[$hook][] = [
            'callback' => $callback,
            'priority' => $priority,
            'args' => $accepted_args
        ];
    }

    /**
     * Execute hooks
     */
    public function executeHooks($hook, ...$args) {
        if (!isset($this->hooks[$hook])) {
            return null;
        }

        // Sort by priority
        usort($this->hooks[$hook], fn($a, $b) => $a['priority'] <=> $b['priority']);

        $value = array_shift($args);
        foreach ($this->hooks[$hook] as $hook_data) {
            $value = call_user_func_array($hook_data['callback'], array_merge([$value], $args));
        }
        return $value;
    }

    /**
     * Global state management (simple Flux-like store)
     */
    public function setState($key, $value) {
        $this->state_store[$key] = $value;
        $this->executeHooks('state_changed', $key, $value);
    }

    /**
     * Get state
     */
    public function getState($key = null) {
        if ($key === null) {
            return $this->state_store;
        }
        return $this->state_store[$key] ?? null;
    }

    /**
     * Cache management with TTL
     */
    public function setCache($key, $value, $ttl = 3600) {
        $this->cache[$key] = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
    }

    /**
     * Get from cache
     */
    public function getCache($key) {
        if (!isset($this->cache[$key])) {
            return null;
        }

        if ($this->cache[$key]['expires'] < time()) {
            unset($this->cache[$key]);
            return null;
        }

        return $this->cache[$key]['value'];
    }

    /**
     * Clear cache
     */
    public function clearCache($key = null) {
        if ($key === null) {
            $this->cache = [];
        } else {
            unset($this->cache[$key]);
        }
    }
}
