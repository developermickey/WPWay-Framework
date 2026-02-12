<?php
/**
 * Virtual DOM Engine
 * Lightweight virtual DOM implementation for efficient rendering
 */

namespace WPWay\Core;

if (!defined('ABSPATH')) exit;

class VirtualDOM {
    private $vdom = null;
    private $prev_vdom = null;
    private $dom_cache = [];

    /**
     * Create virtual element
     */
    public static function createElement($tag, $props = [], ...$children) {
        return [
            'tag' => $tag,
            'props' => $props,
            'children' => array_merge(...$children)
        ];
    }

    /**
     * Create virtual component
     */
    public static function createComponent($component_name, $props = [], ...$children) {
        return [
            'type' => 'component',
            'name' => $component_name,
            'props' => $props,
            'children' => array_merge(...$children)
        ];
    }

    /**
     * Create virtual text node
     */
    public static function createText($content) {
        return [
            'type' => 'text',
            'content' => $content
        ];
    }

    /**
     * Fragment for multiple elements without wrapper
     */
    public static function createFragment(...$children) {
        return [
            'type' => 'fragment',
            'children' => array_merge(...$children)
        ];
    }

    /**
     * Render virtual DOM tree to HTML
     */
    public function render($vdom) {
        $this->vdom = $vdom;
        return $this->renderNode($vdom);
    }

    /**
     * Render a single node
     */
    private function renderNode($node) {
        if (is_string($node)) {
            return htmlspecialchars($node, ENT_QUOTES, 'UTF-8');
        }

        if (is_numeric($node)) {
            return (string)$node;
        }

        if (!is_array($node)) {
            return '';
        }

        // Text node
        if (isset($node['type']) && $node['type'] === 'text') {
            return htmlspecialchars($node['content'], ENT_QUOTES, 'UTF-8');
        }

        // Fragment
        if (isset($node['type']) && $node['type'] === 'fragment') {
            $html = '';
            foreach ($node['children'] as $child) {
                $html .= $this->renderNode($child);
            }
            return $html;
        }

        // Component
        if (isset($node['type']) && $node['type'] === 'component') {
            $framework = Framework::getInstance();
            $component_class = $framework->getComponent($node['name']);
            
            if ($component_class) {
                $component = new $component_class($node['props'] ?? []);
                return $this->renderNode($component->render());
            }
        }

        // Regular element
        $tag = $node['tag'] ?? 'div';
        $props = $node['props'] ?? [];
        $children = $node['children'] ?? [];

        $html = "<$tag";

        // Add attributes
        foreach ($props as $key => $value) {
            if ($key === 'children' || $key === 'key') continue;
            if ($value === false || $value === null) continue;
            if ($value === true) {
                $html .= " $key";
            } else {
                $html .= ' ' . htmlspecialchars($key, ENT_QUOTES) . '="' . htmlspecialchars($value, ENT_QUOTES) . '"';
            }
        }

        $html .= '>';

        // Self-closing tags
        if (in_array($tag, ['img', 'input', 'br', 'hr', 'meta', 'link'])) {
            return $html;
        }

        // Render children
        foreach ($children as $child) {
            $html .= $this->renderNode($child);
        }

        $html .= "</$tag>";

        return $html;
    }

    /**
     * Diff algorithm - compare old and new virtual DOM
     */
    public function diff($old_vdom, $new_vdom) {
        if ($old_vdom === $new_vdom) {
            return [];
        }

        $changes = [];

        // Node type changed
        if (gettype($old_vdom) !== gettype($new_vdom)) {
            return [['type' => 'replace', 'node' => $new_vdom]];
        }

        // Both are arrays (elements or components)
        if (is_array($old_vdom) && is_array($new_vdom)) {
            // Check props/attributes changed
            if ($old_vdom['props'] ?? [] !== $new_vdom['props'] ?? []) {
                $changes[] = ['type' => 'update_props', 'props' => $new_vdom['props']];
            }

            // Check children changed
            $old_children = $old_vdom['children'] ?? [];
            $new_children = $new_vdom['children'] ?? [];

            if ($old_children !== $new_children) {
                $changes[] = ['type' => 'update_children', 'children' => $new_children];
            }
        }

        return $changes;
    }

    /**
     * Reconcile changes (apply patches)
     */
    public function reconcile($changes, $dom_element) {
        foreach ($changes as $change) {
            switch ($change['type']) {
                case 'replace':
                    $new_dom = $this->renderNode($change['node']);
                    $dom_element->innerHTML = $new_dom;
                    break;

                case 'update_props':
                    $this->updateProps($dom_element, $change['props']);
                    break;

                case 'update_children':
                    $children_html = '';
                    foreach ($change['children'] as $child) {
                        $children_html .= $this->renderNode($child);
                    }
                    $dom_element->innerHTML = $children_html;
                    break;
            }
        }
    }

    /**
     * Update element properties
     */
    private function updateProps($element, $props) {
        foreach ($props as $key => $value) {
            if ($key === 'children' || $key === 'key') continue;
            if ($value === false || $value === null) {
                $element->removeAttribute($key);
            } else if ($value === true) {
                $element->setAttribute($key, $key);
            } else {
                $element->setAttribute($key, $value);
            }
        }
    }
}
