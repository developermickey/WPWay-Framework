<?php
/**
 * Bootstrap WPWay Framework
 * Initialize all framework components and register hooks
 */

namespace WPWay;

if (!defined('ABSPATH')) exit;

final class Bootstrap {
    private static $initialized = false;
    private static $files_loaded = [];

    /**
     * Initialize framework
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        self::$initialized = true;

        try {
            // Load configuration
            if (self::loadFile(__DIR__ . '/config.php')) {
                \WPWay\Config\Configuration::init(plugin_dir_url(__DIR__ . '/../wpway.php'));
            }

            // Load core framework
            self::loadFile(__DIR__ . '/core/framework.php');
            self::loadFile(__DIR__ . '/core/component.php');
            self::loadFile(__DIR__ . '/core/virtual-dom.php');

            // Load routing
            self::loadFile(__DIR__ . '/router/router.php');

            // Load state management
            self::loadFile(__DIR__ . '/state/store.php');

            // Load Gutenberg integration
            self::loadFile(__DIR__ . '/gutenberg/blocks.php');

            // Load SSR/Hydration
            self::loadFile(__DIR__ . '/ssr/hydration.php');

            // Load plugin ecosystem
            self::loadFile(__DIR__ . '/plugin-api/plugin-system.php');

            // Load performance optimizer
            self::loadFile(__DIR__ . '/performance/optimizer.php');

            // Load dev tools
            self::loadFile(__DIR__ . '/dev-tools.php');

            // Load REST API
            self::loadFile(__DIR__ . '/rest-api-enhanced.php');

            // Load example components
            self::loadFile(__DIR__ . '/example-components.php');

            // Setup WordPress hooks
            self::setupHooks();

            // Log initialization
            if (class_exists('WPWay\DevTools\Console')) {
                DevTools\Console::log('WPWay Framework initialized successfully');
            }

        } catch (\Throwable $e) {
            // Log error to debug.log
            error_log('WPWay Framework Error: ' . $e->getMessage());
            error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            error_log('Trace: ' . $e->getTraceAsString());
            
            // Show admin notice
            add_action('admin_notices', function() use ($e) {
                echo '<div class="notice notice-error"><p><strong>WPWay Error:</strong> ' . 
                     esc_html($e->getMessage()) . '</p></div>';
            });
        }
    }

    /**
     * Safely load a file
     */
    private static function loadFile($file) {
        if (!file_exists($file)) {
            error_log('WPWay: File not found - ' . $file);
            return false;
        }
        
        // Prevent duplicate loading
        if (in_array($file, self::$files_loaded)) {
            return true;
        }

        try {
            require_once $file;
            self::$files_loaded[] = $file;
            return true;
        } catch (\Throwable $e) {
            error_log('WPWay: Error loading file ' . $file . ' - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Setup WordPress integration hooks
     */
    private static function setupHooks() {
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [self::class, 'enqueueAssets']);
        add_action('admin_enqueue_scripts', [self::class, 'enqueueAdminAssets']);

        // Register example components
        add_action('init', [self::class, 'registerExampleComponents']);

        // Register example blocks
        add_action('init', [self::class, 'registerExampleBlocks']);

        // Debug bar
        if (WP_DEBUG) {
            add_action('wp_footer', [self::class, 'renderDebugBar']);
        }
    }

    /**
     * Enqueue frontend assets
     */
    public static function enqueueAssets() {
        $plugin_url = plugin_dir_url(__DIR__ . '/../wpway.php');
        $config = Config\Configuration::exportToJs();

        // Core framework
        wp_enqueue_script(
            'wpway-core',
            $plugin_url . 'assets/wpway-core.js',
            [],
            '1.0.0',
            true
        );

        // Hydration engine
        wp_enqueue_script(
            'wpway-hydration',
            $plugin_url . 'assets/hydration.js',
            ['wpway-core'],
            '1.0.0',
            true
        );

        // Performance utilities
        wp_enqueue_script(
            'wpway-performance',
            $plugin_url . 'assets/performance.js',
            ['wpway-core'],
            '1.0.0',
            true
        );

        // REST API utilities
        wp_enqueue_script(
            'wpway-rest-api',
            $plugin_url . 'assets/rest-api.js',
            ['wpway-core'],
            '1.0.0',
            true
        );

        // Styles
        wp_enqueue_style(
            'wpway-css',
            $plugin_url . 'assets/wpway.css'
        );

        // Localize configuration
        wp_localize_script('wpway-core', 'WPWAY', $config);

        // Initialize framework on client
        wp_add_inline_script('wpway-core', '
            if (window.WPWayHydration) {
                window.WPWayHydration.init();
            }
        ');
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueueAdminAssets() {
        $plugin_url = plugin_dir_url(__DIR__ . '/../wpway.php');

        wp_enqueue_script(
            'wpway-admin',
            $plugin_url . 'assets/admin.js',
            ['wpway-core'],
            '1.0.0',
            true
        );

        wp_enqueue_style(
            'wpway-admin-css',
            $plugin_url . 'assets/admin.css'
        );
    }

    /**
     * Register example components
     */
    public static function registerExampleComponents() {
        $framework = Core\Framework::getInstance();

        // Example: Blog list component
        $framework->registerComponent('BlogList', 'WPWay\Components\BlogList');
        $framework->registerComponent('PostCard', 'WPWay\Components\PostCard');
        $framework->registerComponent('Hero', 'WPWay\Components\Hero');
        $framework->registerComponent('Newsletter', 'WPWay\Components\Newsletter');
    }

    /**
     * Register example Gutenberg blocks
     */
    public static function registerExampleBlocks() {
        $engine = Gutenberg\BlockEngine::getInstance();

        // Hero block
        $engine->registerBlock('wpway/hero', [
            'title' => 'WPWay Hero',
            'description' => 'Hero section component',
            'render_component' => 'Hero',
            'attributes' => [
                'title' => ['type' => 'string', 'default' => 'Welcome'],
                'subtitle' => ['type' => 'string', 'default' => ''],
                'background_color' => ['type' => 'string', 'default' => '#ffffff'],
                'button_text' => ['type' => 'string', 'default' => 'Learn More'],
                'button_url' => ['type' => 'string', 'default' => '#']
            ]
        ]);

        // Blog list block
        $engine->registerBlock('wpway/blog-list', [
            'title' => 'WPWay Blog List',
            'description' => 'Display blog posts',
            'render_component' => 'BlogList',
            'attributes' => [
                'posts_per_page' => ['type' => 'number', 'default' => 5],
                'columns' => ['type' => 'number', 'default' => 3],
                'show_excerpt' => ['type' => 'boolean', 'default' => true]
            ]
        ]);

        // Newsletter block
        $engine->registerBlock('wpway/newsletter', [
            'title' => 'WPWay Newsletter',
            'description' => 'Newsletter signup form',
            'render_component' => 'Newsletter',
            'attributes' => [
                'heading' => ['type' => 'string', 'default' => 'Subscribe to our newsletter'],
                'placeholder' => ['type' => 'string', 'default' => 'Enter your email'],
                'button_text' => ['type' => 'string', 'default' => 'Subscribe']
            ]
        ]);

        DevTools\Console::log('Example Gutenberg blocks registered');
    }

    /**
     * Render debug bar
     */
    public static function renderDebugBar() {
        $store = State\Store::getInstance();
        $framework = Core\Framework::getInstance();
        
        DevTools\DebugBar::addInfo('Components', count($framework->getComponents()));
        DevTools\DebugBar::addInfo('Blocks', count(Gutenberg\BlockEngine::getInstance()->getBlocks()));
        DevTools\DebugBar::addInfo('State Keys', count((array)$framework->getState()));
        
        echo DevTools\DebugBar::render();
    }
}

// Auto-initialize on plugin load
add_action('plugins_loaded', [Bootstrap::class, 'init'], 5);
