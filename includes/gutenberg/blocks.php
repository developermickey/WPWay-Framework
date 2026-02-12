<?php
/**
 * Gutenberg Reactive Block Engine
 * Transform Gutenberg blocks into reactive WPWay components
 */

namespace WPWay\Gutenberg;

if (!defined('ABSPATH')) exit;

class BlockEngine {
    private static $instance;
    private $blocks = [];
    private $block_types = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a reactive block
     */
    public function registerBlock($name, $block_data) {
        $this->blocks[$name] = [
            'title' => $block_data['title'] ?? $name,
            'description' => $block_data['description'] ?? '',
            'editing_component' => $block_data['editing_component'] ?? null,
            'render_component' => $block_data['render_component'] ?? null,
            'attributes' => $block_data['attributes'] ?? [],
            'supports' => $block_data['supports'] ?? [],
            'category' => $block_data['category'] ?? 'common'
        ];
    }

    /**
     * Get registered blocks
     */
    public function getBlocks() {
        return $this->blocks;
    }

    /**
     * Get block by name
     */
    public function getBlock($name) {
        return $this->blocks[$name] ?? null;
    }

    /**
     * Parse block attributes
     */
    public function parseAttributes($block_name, $attributes_data) {
        $block = $this->getBlock($block_name);
        if (!$block) {
            return [];
        }

        $parsed = [];
        foreach ($block['attributes'] as $attr_name => $attr_config) {
            $type = $attr_config['type'] ?? 'string';
            $value = $attributes_data[$attr_name] ?? $attr_config['default'] ?? null;

            $parsed[$attr_name] = $this->validateAttribute($value, $type);
        }

        return $parsed;
    }

    /**
     * Validate attribute based on type
     */
    private function validateAttribute($value, $type) {
        switch ($type) {
            case 'boolean':
                return (bool)$value;
            case 'number':
            case 'integer':
                return (int)$value;
            case 'array':
                return is_array($value) ? $value : [];
            case 'object':
                return is_array($value) ? $value : [];
            case 'string':
            default:
                return (string)$value;
        }
    }

    /**
     * Render block with reactive component
     */
    public function renderBlock($block_name, $attributes = [], $content = '') {
        $block = $this->getBlock($block_name);
        if (!$block) {
            return '';
        }

        $parsed_attrs = $this->parseAttributes($block_name, $attributes);

        // If render component is specified, use it
        if ($block['render_component']) {
            $framework = \WPWay\Core\Framework::getInstance();
            $component_class = $framework->getComponent($block['render_component']);

            if ($component_class) {
                $component = new $component_class([
                    'attributes' => $parsed_attrs,
                    'content' => $content
                ]);
                return $component->render();
            }
        }

        // Fallback rendering
        return $this->defaultRender($block_name, $parsed_attrs, $content);
    }

    /**
     * Default block rendering
     */
    private function defaultRender($block_name, $attributes, $content) {
        $html = '<div class="wp-block-' . htmlspecialchars($block_name) . '">';
        
        foreach ($attributes as $key => $value) {
            if (is_string($value)) {
                $html .= '<span data-' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"></span>';
            }
        }
        
        $html .= $content;
        $html .= '</div>';

        return $html;
    }

    /**
     * Export block schema for JavaScript
     */
    public function exportBlockSchema($block_name) {
        $block = $this->getBlock($block_name);
        if (!$block) {
            return null;
        }

        return [
            'name' => $block_name,
            'title' => $block['title'],
            'description' => $block['description'],
            'attributes' => $block['attributes'],
            'supports' => $block['supports'],
            'category' => $block['category']
        ];
    }

    /**
     * Get all block schemas for JavaScript
     */
    public function exportAllBlockSchemas() {
        $schemas = [];
        foreach ($this->blocks as $name => $block) {
            $schemas[] = $this->exportBlockSchema($name);
        }
        return $schemas;
    }
}
