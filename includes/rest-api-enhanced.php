<?php
/**
 * Enhanced REST API Endpoints
 * Complete API for component rendering, state, and Gutenberg integration
 */

namespace WPWay\RestAPI;

if (!defined('ABSPATH')) exit;

class API {
    public static function init() {
        add_action('rest_api_init', [self::class, 'registerRoutes']);
    }

    /**
     * Register all REST routes
     */
    public static function registerRoutes() {
        // Component endpoints
        register_rest_route('wpway/v1', '/component/(?P<name>[a-zA-Z0-9\-]+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'getComponent'],
            'permission_callback' => '__return_true',
            'args' => [
                'name' => [
                    'validate_callback' => fn($param) => is_string($param),
                    'sanitize_callback' => 'sanitize_text_field'
                ]
            ]
        ]);

        // Component list
        register_rest_route('wpway/v1', '/components', [
            'methods'  => 'GET',
            'callback' => [self::class, 'listComponents'],
            'permission_callback' => '__return_true'
        ]);

        // Gutenberg blocks
        register_rest_route('wpway/v1', '/blocks', [
            'methods'  => 'GET',
            'callback' => [self::class, 'listBlocks'],
            'permission_callback' => '__return_true'
        ]);

        // Block registration
        register_rest_route('wpway/v1', '/block/(?P<name>[a-zA-Z0-9\-/]+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'getBlockSchema'],
            'permission_callback' => '__return_true'
        ]);

        // State management
        register_rest_route('wpway/v1', '/state', [
            'methods'  => 'GET',
            'callback' => [self::class, 'getState'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('wpway/v1', '/state', [
            'methods'  => 'POST',
            'callback' => [self::class, 'setState'],
            'permission_callback' => fn($req) => current_user_can('edit_posts')
        ]);

        // Page/Post rendering
        register_rest_route('wpway/v1', '/page/(?P<id>\d+)', [
            'methods'  => 'GET',
            'callback' => [self::class, 'renderPage'],
            'permission_callback' => '__return_true'
        ]);

        // Plugin system
        register_rest_route('wpway/v1', '/plugins', [
            'methods'  => 'GET',
            'callback' => [self::class, 'listPlugins'],
            'permission_callback' => '__return_true'
        ]);

        // Performance metrics
        register_rest_route('wpway/v1', '/metrics', [
            'methods'  => 'POST',
            'callback' => [self::class, 'recordMetrics'],
            'permission_callback' => '__return_true'
        ]);

        // Hydration data
        register_rest_route('wpway/v1', '/hydration', [
            'methods'  => 'GET',
            'callback' => [self::class, 'getHydrationData'],
            'permission_callback' => '__return_true'
        ]);
    }

    /**
     * Get component data
     */
    public static function getComponent($request) {
        $name = sanitize_text_field($request->get_param('name'));
        $framework = \WPWay\Core\Framework::getInstance();
        
        $component = $framework->getComponent($name);
        if (!$component) {
            return new \WP_Error('not_found', 'Component not found', ['status' => 404]);
        }

        return [
            'name' => $name,
            'found' => true,
            'class' => $component
        ];
    }

    /**
     * List all components
     */
    public static function listComponents() {
        $framework = \WPWay\Core\Framework::getInstance();
        $components = $framework->getComponents();

        return [
            'count' => count($components),
            'components' => array_keys($components)
        ];
    }

    /**
     * List all blocks
     */
    public static function listBlocks() {
        $engine = \WPWay\Gutenberg\BlockEngine::getInstance();
        $blocks = $engine->getBlocks();

        return [
            'count' => count($blocks),
            'blocks' => array_keys($blocks)
        ];
    }

    /**
     * Get block schema
     */
    public static function getBlockSchema($request) {
        $name = sanitize_text_field($request->get_param('name'));
        $engine = \WPWay\Gutenberg\BlockEngine::getInstance();

        $schema = $engine->exportBlockSchema($name);
        if (!$schema) {
            return new \WP_Error('not_found', 'Block not found', ['status' => 404]);
        }

        return $schema;
    }

    /**
     * Get global state
     */
    public static function getState($request) {
        $framework = \WPWay\Core\Framework::getInstance();
        return [
            'state' => $framework->getState(),
            'timestamp' => time()
        ];
    }

    /**
     * Set global state
     */
    public static function setState($request) {
        $framework = \WPWay\Core\Framework::getInstance();
        $body = $request->get_json_params();

        foreach ($body as $key => $value) {
            $framework->setState($key, $value);
        }

        return [
            'success' => true,
            'state' => $framework->getState()
        ];
    }

    /**
     * Render page
     */
    public static function renderPage($request) {
        $id = intval($request->get_param('id'));
        $post = get_post($id);

        if (!$post) {
            return new \WP_Error('not_found', 'Post not found', ['status' => 404]);
        }

        return [
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => apply_filters('the_content', $post->post_content),
            'excerpt' => $post->post_excerpt,
            'link' => get_permalink($post->ID),
            'permalink' => get_post_permalink($post->ID),
            'type' => $post->post_type,
            'status' => $post->post_status,
            'published' => $post->post_date,
            'modified' => $post->post_modified
        ];
    }

    /**
     * List plugins
     */
    public static function listPlugins() {
        $plugin_system = \WPWay\PluginAPI\PluginSystem::getInstance();
        $plugins = $plugin_system->getPlugins();

        return [
            'count' => count($plugins),
            'plugins' => array_keys($plugins),
            'manifests' => $plugin_system->exportPluginManifests()
        ];
    }

    /**
     * Record performance metrics
     */
    public static function recordMetrics($request) {
        $body = $request->get_json_params();
        $framework = \WPWay\Core\Framework::getInstance();

        $framework->setState('metrics_' . time(), $body);

        return [
            'success' => true,
            'metrics_recorded' => count($body)
        ];
    }

    /**
     * Get hydration data
     */
    public static function getHydrationData($request) {
        $hydration = \WPWay\SSR\Hydration::getInstance();
        
        return [
            'data' => $hydration->getHydrationData(),
            'count' => count($hydration->getHydrationData())
        ];
    }
}

// Initialize
API::init();
