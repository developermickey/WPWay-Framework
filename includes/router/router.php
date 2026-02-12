<?php
/**
 * SPA Router - Client-side navigation without full page reload
 */

namespace WPWay\Router;

if (!defined('ABSPATH')) exit;

class Router {
    private static $instance;
    private $routes = [];
    private $before_navigate = [];
    private $after_navigate = [];
    private $current_route = null;
    private $history = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a route
     */
    public function registerRoute($path, $component, $options = []) {
        $this->routes[$path] = [
            'component' => $component,
            'options' => $options
        ];
    }

    /**
     * Get all routes
     */
    public function getRoutes() {
        return $this->routes;
    }

    /**
     * Add before navigation hook
     */
    public function beforeNavigate($callback) {
        $this->before_navigate[] = $callback;
    }

    /**
     * Add after navigation hook
     */
    public function afterNavigate($callback) {
        $this->after_navigate[] = $callback;
    }

    /**
     * Navigate to route
     */
    public function navigate($path, $params = []) {
        // Execute before hooks
        foreach ($this->before_navigate as $hook) {
            if (call_user_func($hook, $path, $params) === false) {
                return false;
            }
        }

        $this->current_route = [
            'path' => $path,
            'params' => $params,
            'timestamp' => time()
        ];

        $this->history[] = $this->current_route;

        // Execute after hooks
        foreach ($this->after_navigate as $hook) {
            call_user_func($hook, $path, $params);
        }

        return true;
    }

    /**
     * Get current route
     */
    public function getCurrentRoute() {
        return $this->current_route;
    }

    /**
     * Get history
     */
    public function getHistory() {
        return $this->history;
    }

    /**
     * Match route pattern
     */
    public function matchRoute($path) {
        foreach ($this->routes as $pattern => $route) {
            if ($this->matchPattern($pattern, $path)) {
                return [
                    'component' => $route['component'],
                    'params' => $this->extractParams($pattern, $path),
                    'options' => $route['options']
                ];
            }
        }
        return null;
    }

    /**
     * Check if pattern matches path
     */
    private function matchPattern($pattern, $path) {
        $pattern = preg_replace('/:[^\/]+/', '[^/]+', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        return preg_match("/^$pattern$/", $path) === 1;
    }

    /**
     * Extract parameters from URL
     */
    private function extractParams($pattern, $path) {
        $params = [];
        preg_match_all('/:([^\/]+)/', $pattern, $keys);
        preg_match_all('/([^\/]+)/', $path, $values);

        if (!empty($keys[1]) && !empty($values[1])) {
            foreach ($keys[1] as $i => $key) {
                $params[$key] = $values[1][$i] ?? null;
            }
        }

        return $params;
    }

    /**
     * Generate URL from route
     */
    public function generateUrl($path, $params = []) {
        $url = $path;
        foreach ($params as $key => $value) {
            $url = str_replace(":$key", $value, $url);
        }
        return $url;
    }
}
