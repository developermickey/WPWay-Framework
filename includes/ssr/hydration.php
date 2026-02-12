<?php
/**
 * SSR & Hydration Engine
 * Server-side rendering + client-side hydration
 */

namespace WPWay\SSR;

if (!defined('ABSPATH')) exit;

class Hydration {
    private static $instance;
    private $server_html = '';
    private $hydration_data = [];
    private $hydration_id_counter = 0;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Render component server-side
     */
    public function renderServer($component_name, $props = []) {
        $framework = \WPWay\Core\Framework::getInstance();
        $component_class = $framework->getComponent($component_name);

        if (!$component_class) {
            return '';
        }

        $component = new $component_class($props);
        $vdom = $component->render();

        $dom = new \WPWay\Core\VirtualDOM();
        $html = $dom->render($vdom);

        // Add hydration markers
        $hydration_id = $this->generateHydrationId();
        $hydration_wrapper = sprintf(
            '<div data-wp-way-hydration-id="%s" data-wp-way-component="%s">%s</div>',
            htmlspecialchars($hydration_id),
            htmlspecialchars($component_name),
            $html
        );

        // Store hydration data
        $this->hydration_data[$hydration_id] = [
            'component' => $component_name,
            'props' => $props,
            'state' => $component->getState()
        ];

        return $hydration_wrapper;
    }

    /**
     * Generate unique hydration ID
     */
    private function generateHydrationId() {
        return 'wpway-' . (++$this->hydration_id_counter);
    }

    /**
     * Get hydration data for client
     */
    public function getHydrationData() {
        return $this->hydration_data;
    }

    /**
     * Create hydration script
     */
    public function createHydrationScript() {
        $data = json_encode($this->hydration_data, JSON_UNESCAPED_SLASHES);
        
        return sprintf(
            '<script type="application/json" id="wpway-hydration-data">%s</script>',
            $data
        );
    }

    /**
     * Batch render multiple components
     */
    public function renderBatch($components) {
        $html = '';
        foreach ($components as $component_name => $props) {
            $html .= $this->renderServer($component_name, $props);
        }
        return $html;
    }

    /**
     * Register component as hydrated
     */
    public function registerHydrated($hydration_id, $component_instance) {
        if (isset($this->hydration_data[$hydration_id])) {
            $this->hydration_data[$hydration_id]['hydrated'] = true;
            $this->hydration_data[$hydration_id]['instance'] = $component_instance;
        }
    }

    /**
     * Reset hydration state
     */
    public function reset() {
        $this->server_html = '';
        $this->hydration_data = [];
        $this->hydration_id_counter = 0;
    }
}
