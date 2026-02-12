<?php
/**
 * Developer Tools & Utilities
 * Debugging, logging, and development features
 */

namespace WPWay\DevTools;

if (!defined('ABSPATH')) exit;

class Console {
    private static $logs = [];
    private static $errors = [];
    private static $debug_mode = WP_DEBUG;

    /**
     * Log message
     */
    public static function log($message, $data = null) {
        self::$logs[] = [
            'timestamp' => microtime(true),
            'message' => $message,
            'data' => $data
        ];

        if (self::$debug_mode) {
            error_log('[WPWay] ' . $message . (is_array($data) ? ': ' . json_encode($data) : ''));
        }
    }

    /**
     * Log error
     */
    public static function error($message, $data = null) {
        self::$errors[] = [
            'timestamp' => microtime(true),
            'message' => $message,
            'data' => $data
        ];

        error_log('[WPWay ERROR] ' . $message . (is_array($data) ? ': ' . json_encode($data) : ''));
    }

    /**
     * Get logs
     */
    public static function getLogs() {
        return self::$logs;
    }

    /**
     * Get errors
     */
    public static function getErrors() {
        return self::$errors;
    }

    /**
     * Clear logs
     */
    public static function clear() {
        self::$logs = [];
        self::$errors = [];
    }

    /**
     * Export logs
     */
    public static function export() {
        return [
            'logs' => self::$logs,
            'errors' => self::$errors,
            'log_count' => count(self::$logs),
            'error_count' => count(self::$errors)
        ];
    }
}

class DebugBar {
    private static $info = [];

    /**
     * Add debug info
     */
    public static function addInfo($section, $data) {
        self::$info[$section] = $data;
    }

    /**
     * Get debug bar HTML
     */
    public static function render() {
        if (!WP_DEBUG) {
            return '';
        }

        $html = '<div id="wpway-debug-bar" style="background: #1a1a1a; color: #fff; padding: 10px; font-size: 12px; border-top: 2px solid #0073aa; position: fixed; bottom: 0; left: 0; right: 0; z-index: 99999; overflow-x: auto;">';
        $html .= '<strong>WPWay Debug:</strong> ';

        foreach (self::$info as $section => $data) {
            $html .= sprintf(
                '<span style="margin-right: 20px;"><strong>%s:</strong> %s</span>',
                htmlspecialchars($section),
                htmlspecialchars(is_array($data) ? json_encode($data) : $data)
            );
        }

        $html .= '</div>';

        return $html;
    }
}

class Inspector {
    /**
     * Inspect component
     */
    public static function inspectComponent($component) {
        return [
            'type' => get_class($component),
            'props' => $component->getProps() ?? [],
            'state' => $component->getState() ?? [],
            'mounted' => $component->mounted ?? false
        ];
    }

    /**
     * Get component tree
     */
    public static function getComponentTree($component, $depth = 0) {
        $tree = [
            'name' => get_class($component),
            'depth' => $depth,
            'props' => $component->getProps() ?? [],
            'state' => $component->getState() ?? []
        ];

        return $tree;
    }

    /**
     * Generate performance trace
     */
    public static function trace() {
        $framework = \WPWay\Core\Framework::getInstance();
        
        return [
            'components' => count($framework->getComponents()),
            'state' => count((array)$framework->getState()),
            'cache_entries' => 'N/A'
        ];
    }
}

/**
 * Error handler
 */
class ErrorHandler {
    public static function handle($errno, $errstr, $errfile, $errline) {
        Console::error("PHP Error: $errstr", [
            'file' => $errfile,
            'line' => $errline,
            'type' => $errno
        ]);

        return false;
    }

    public static function register() {
        if (WP_DEBUG) {
            set_error_handler([self::class, 'handle']);
        }
    }
}

// Register error handler
ErrorHandler::register();
