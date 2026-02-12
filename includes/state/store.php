<?php
/**
 * Global State Management Store
 * Flux-like state management for WPWay
 */

namespace WPWay\State;

if (!defined('ABSPATH')) exit;

class Store {
    private static $instance;
    private $state = [];
    private $reducers = [];
    private $middlewares = [];
    private $listeners = [];
    private $history = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register reducer
     */
    public function registerReducer($key, $reducer) {
        $this->reducers[$key] = $reducer;
        $this->state[$key] = null;
    }

    /**
     * Add middleware
     */
    public function use($middleware) {
        $this->middlewares[] = $middleware;
    }

    /**
     * Subscribe to state changes
     */
    public function subscribe($callback) {
        $this->listeners[] = $callback;

        // Return unsubscribe function
        return function() use ($callback) {
            $this->listeners = array_filter($this->listeners, fn($l) => $l !== $callback);
        };
    }

    /**
     * Dispatch action
     */
    public function dispatch($action) {
        // Apply middlewares
        $final_action = $action;
        foreach ($this->middlewares as $middleware) {
            $final_action = $middleware($final_action);
        }

        $prev_state = $this->state;

        // Apply reducers
        foreach ($this->reducers as $key => $reducer) {
            if (isset($final_action['path']) && $final_action['path'] === $key) {
                $this->state[$key] = $reducer($this->state[$key] ?? null, $final_action);
            }
        }

        // Record in history
        $this->history[] = [
            'action' => $final_action,
            'prev_state' => $prev_state,
            'next_state' => $this->state,
            'timestamp' => microtime(true)
        ];

        // Notify listeners
        $this->notifyListeners($final_action);
    }

    /**
     * Get state
     */
    public function getState($key = null) {
        if ($key === null) {
            return $this->state;
        }
        return $this->state[$key] ?? null;
    }

    /**
     * Get history
     */
    public function getHistory() {
        return $this->history;
    }

    /**
     * Notify listeners
     */
    private function notifyListeners($action) {
        foreach ($this->listeners as $listener) {
            call_user_func($listener, $action, $this->state);
        }
    }

    /**
     * Time travel debugging - go to specific state
     */
    public function timeTravel($index) {
        if (isset($this->history[$index])) {
            $this->state = $this->history[$index]['next_state'];
            $this->notifyListeners(['type' => 'time_travel', 'index' => $index]);
            return true;
        }
        return false;
    }

    /**
     * Export state snapshot
     */
    public function exportSnapshot() {
        return [
            'state' => $this->state,
            'history_length' => count($this->history)
        ];
    }

    /**
     * Restore from snapshot
     */
    public function restoreSnapshot($snapshot) {
        if (isset($snapshot['state'])) {
            $this->state = $snapshot['state'];
            $this->notifyListeners(['type' => 'snapshot_restore']);
            return true;
        }
        return false;
    }
}
