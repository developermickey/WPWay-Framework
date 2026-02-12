<?php
/**
 * Base Component Class - Foundation for all WPWay components
 */

namespace WPWay\Core;

if (!defined('ABSPATH')) exit;

abstract class Component {
    protected $props = [];
    protected $state = [];
    protected $mounted = false;
    protected $hooks = [];
    protected $memo = [];

    /**
     * Constructor
     */
    public function __construct($props = []) {
        $this->props = $props;
        $this->state = [];
        $this->render();
    }

    /**
     * Set component props
     */
    public function setProps($props) {
        $old_props = $this->props;
        $this->props = array_merge($this->props, $props);
        $this->componentDidUpdate($old_props, $this->props);
    }

    /**
     * Get props
     */
    public function getProps() {
        return $this->props;
    }

    /**
     * Set component state (triggers re-render)
     */
    public function setState($key, $value) {
        $old_state = $this->state;
        $this->state[$key] = $value;
        
        if ($this->mounted) {
            $this->componentDidUpdate($old_state, $this->state);
        }
    }

    /**
     * Get component state
     */
    public function getState($key = null) {
        if ($key === null) {
            return $this->state;
        }
        return $this->state[$key] ?? null;
    }

    /**
     * Lifecycle: Component mounted
     */
    public function componentDidMount() {
        $this->mounted = true;
    }

    /**
     * Lifecycle: Component updated
     */
    public function componentDidUpdate($prev_state, $next_state) {
    }

    /**
     * Lifecycle: Component will unmount
     */
    public function componentWillUnmount() {
        $this->mounted = false;
    }

    /**
     * Memoization for expensive computations
     */
    public function useMemo($callback, $dependencies) {
        $key = md5(serialize($dependencies));
        
        if (isset($this->memo[$key]) && $this->memo[$key]['deps'] === $dependencies) {
            return $this->memo[$key]['value'];
        }

        $value = $callback();
        $this->memo[$key] = [
            'value' => $value,
            'deps' => $dependencies
        ];

        return $value;
    }

    /**
     * Add effect hook
     */
    public function useEffect($callback, $dependencies = []) {
        $this->hooks[] = [
            'type' => 'effect',
            'callback' => $callback,
            'dependencies' => $dependencies
        ];
    }

    /**
     * Abstract render method - must be implemented by subclasses
     */
    abstract public function render();

    /**
     * Get component as HTML string or array
     */
    public function toArray() {
        return [
            'type' => get_class($this),
            'props' => $this->props,
            'state' => $this->state
        ];
    }
}
