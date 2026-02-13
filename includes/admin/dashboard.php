<?php
/**
 * WPWay Admin Dashboard
 * Provides GUI for creating, editing, and managing components
 */

namespace WPWay\Admin;

if (!defined('ABSPATH')) exit;

class AdminDashboard {
    private static $instance;
    
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Initialize admin dashboard
     */
    public function init() {
        // Add admin menu
        add_action('admin_menu', [$this, 'addMenuPages']);
        
        // Enqueue admin assets
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
        
        // Register REST API routes
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
    }
    
    /**
     * Add menu pages to WordPress admin
     */
    public function addMenuPages() {
        // Main WPWay menu
        add_menu_page(
            'WPWay Dashboard',              // Page title
            'WPWay',                        // Menu title
            'manage_options',               // Capability
            'wpway-dashboard',              // Menu slug
            [$this, 'dashboardPage'],       // Callback
            'dashicons-layout',             // Icon
            76                              // Position
        );
        
        // Components submenu
        add_submenu_page(
            'wpway-dashboard',
            'Components',
            'Components',
            'manage_options',
            'wpway-components',
            [$this, 'componentsPage']
        );
        
        // Pages submenu
        add_submenu_page(
            'wpway-dashboard',
            'Pages',
            'Pages',
            'manage_options',
            'wpway-pages',
            [$this, 'pagesPage']
        );
        
        // Code Editor submenu
        add_submenu_page(
            'wpway-dashboard',
            'Code Editor',
            'Code Editor',
            'manage_options',
            'wpway-code-editor',
            [$this, 'codeEditorPage']
        );
        
        // Settings submenu
        add_submenu_page(
            'wpway-dashboard',
            'Settings',
            'Settings',
            'manage_options',
            'wpway-settings',
            [$this, 'settingsPage']
        );
        
        // Documentation submenu
        add_submenu_page(
            'wpway-dashboard',
            'Documentation',
            'Documentation',
            'manage_options',
            'wpway-docs',
            [$this, 'docsPage']
        );
    }
    
    /**
     * Main dashboard page
     */
    public function dashboardPage() {
        ?>
        <div class="wrap wpway-dashboard-wrap">
            <h1>WPWay Dashboard</h1>
            
            <div class="wpway-dashboard-welcome">
                <h2>Welcome to WPWay Framework</h2>
                <p>Build and manage modern WordPress components with ease.</p>
            </div>
            
            <div class="wpway-dashboard-grid">
                <!-- Components Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">📦</div>
                    <h3>Components</h3>
                    <p>Create, edit, and manage your WPWay components</p>
                    <a href="<?php echo admin_url('admin.php?page=wpway-components'); ?>" class="button button-primary">
                        Manage Components
                    </a>
                </div>
                
                <!-- Pages Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">📄</div>
                    <h3>Pages</h3>
                    <p>Create pages using WPWay components</p>
                    <a href="<?php echo admin_url('admin.php?page=wpway-pages'); ?>" class="button button-primary">
                        Manage Pages
                    </a>
                </div>
                
                <!-- Code Editor Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">💻</div>
                    <h3>Code Editor</h3>
                    <p>Edit component code directly from the GUI</p>
                    <a href="<?php echo admin_url('admin.php?page=wpway-code-editor'); ?>" class="button button-primary">
                        Open Editor
                    </a>
                </div>
                
                <!-- Settings Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">⚙️</div>
                    <h3>Settings</h3>
                    <p>Configure WPWay framework settings</p>
                    <a href="<?php echo admin_url('admin.php?page=wpway-settings'); ?>" class="button button-primary">
                        Configure
                    </a>
                </div>
                
                <!-- Documentation Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">📚</div>
                    <h3>Documentation</h3>
                    <p>Learn how to use WPWay</p>
                    <a href="<?php echo admin_url('admin.php?page=wpway-docs'); ?>" class="button button-primary">
                        View Docs
                    </a>
                </div>
                
                <!-- Stats Card -->
                <div class="wpway-card">
                    <div class="wpway-card-icon">📊</div>
                    <h3>Statistics</h3>
                    <p>View component and page statistics</p>
                    <button class="button button-primary" id="wpway-refresh-stats">
                        Refresh Stats
                    </button>
                </div>
            </div>
            
            <div class="wpway-dashboard-stats">
                <h3>Quick Stats</h3>
                <div id="wpway-stats-container" class="wpway-stats-grid">
                    <div class="stat-box">
                        <strong id="stat-components">0</strong>
                        <span>Components</span>
                    </div>
                    <div class="stat-box">
                        <strong id="stat-pages">0</strong>
                        <span>Pages</span>
                    </div>
                    <div class="stat-box">
                        <strong id="stat-blocks">0</strong>
                        <span>Blocks</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Components management page
     */
    public function componentsPage() {
        ?>
        <div class="wrap wpway-components-wrap">
            <h1>
                WPWay Components
                <a href="#" class="page-title-action" id="wpway-create-component">
                    Add New Component
                </a>
            </h1>
            
            <div class="wpway-tabs">
                <button class="wpway-tab-btn active" data-tab="list">Components List</button>
                <button class="wpway-tab-btn" data-tab="create">Create New</button>
            </div>
            
            <!-- Components List Tab -->
            <div id="list-tab" class="wpway-tab-content active">
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Component Name</th>
                            <th>Type</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="wpway-components-list">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 30px;">
                                <em>Loading components...</em>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Create Component Tab -->
            <div id="create-tab" class="wpway-tab-content">
                <form id="wpway-component-form" class="wpway-form">
                    <div class="form-group">
                        <label for="component-name">Component Name:</label>
                        <input type="text" id="component-name" placeholder="e.g., Hero, BlogList" required>
                        <small>Use PascalCase (e.g., MyComponent)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="component-type">Component Type:</label>
                        <select id="component-type" required>
                            <option value="">-- Select Type --</option>
                            <option value="php">PHP Component</option>
                            <option value="javascript">JavaScript Component</option>
                            <option value="hybrid">Hybrid (PHP + JS)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="component-description">Description:</label>
                        <textarea id="component-description" placeholder="Describe what this component does" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group" id="php-template-group">
                        <label for="component-template">Template:</label>
                        <select id="component-template">
                            <option value="">-- Custom --</option>
                            <option value="hero">Hero Section</option>
                            <option value="card">Card</option>
                            <option value="blog-list">Blog List</option>
                            <option value="newsletter">Newsletter</option>
                            <option value="cta">Call to Action</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="component-code">Component Code:</label>
                        <div id="component-code-editor" style="border: 1px solid #ddd; height: 300px;"></div>
                        <textarea id="component-code" style="display: none;"></textarea>
                    </div>
                    
                    <button type="submit" class="button button-primary button-large">
                        Create Component
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Pages management page
     */
    public function pagesPage() {
        ?>
        <div class="wrap wpway-pages-wrap">
            <h1>
                WPWay Pages
                <a href="#" class="page-title-action" id="wpway-create-page">
                    Create New Page
                </a>
            </h1>
            
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Page Title</th>
                        <th>Components Used</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="wpway-pages-list">
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 30px;">
                            <em>Loading pages...</em>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <!-- Create Page Modal -->
            <div id="wpway-page-modal" class="wpway-modal" style="display: none;">
                <div class="wpway-modal-content">
                    <span class="wpway-modal-close">&times;</span>
                    <h2>Create New Page</h2>
                    
                    <form id="wpway-page-form" class="wpway-form">
                        <div class="form-group">
                            <label for="page-title">Page Title:</label>
                            <input type="text" id="page-title" placeholder="Enter page title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="page-slug">URL Slug:</label>
                            <input type="text" id="page-slug" placeholder="auto-generated">
                        </div>
                        
                        <div class="form-group">
                            <label>Select Components:</label>
                            <div id="page-components-list">
                                <em>Loading components...</em>
                            </div>
                        </div>
                        
                        <button type="submit" class="button button-primary">
                            Create Page
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Code editor page
     */
    public function codeEditorPage() {
        ?>
        <div class="wrap wpway-code-editor-wrap">
            <h1>WPWay Code Editor</h1>
            
            <div class="wpway-editor-container">
                <div class="wpway-editor-sidebar">
                    <h3>Components</h3>
                    <div id="wpway-component-files" class="file-list">
                        <em>Loading components...</em>
                    </div>
                </div>
                
                <div class="wpway-editor-main">
                    <div id="wpway-editor-header" class="wpway-editor-header">
                        <span id="editor-filename">Select a component</span>
                        <button id="save-code" class="button button-primary" style="display: none;">
                            Save Changes
                        </button>
                    </div>
                    
                    <div id="wpway-code-editor-container" style="height: 500px; border: 1px solid #ddd;">
                        <textarea id="wpway-code-textarea" style="display: none;"></textarea>
                    </div>
                    
                    <div class="wpway-editor-footer">
                        <small>Language: <span id="editor-language">PHP</span></small>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Settings page
     */
    public function settingsPage() {
        ?>
        <div class="wrap wpway-settings-wrap">
            <h1>WPWay Settings</h1>
            
            <form id="wpway-settings-form" method="post" class="wpway-form">
                <h2>Framework Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="wpway-debug">Debug Mode</label></th>
                        <td>
                            <input type="checkbox" id="wpway-debug" name="wpway_debug" value="1">
                            <p class="description">Enable debug logging and verbose errors</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-cache">Enable Caching</label></th>
                        <td>
                            <input type="checkbox" id="wpway-cache" name="wpway_cache" value="1" checked>
                            <p class="description">Cache components and assets for better performance</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-ssr">Server-Side Rendering</label></th>
                        <td>
                            <input type="checkbox" id="wpway-ssr" name="wpway_ssr" value="1" checked>
                            <p class="description">Render components on the server</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-lazy-load">Lazy Loading</label></th>
                        <td>
                            <input type="checkbox" id="wpway-lazy-load" name="wpway_lazy_load" value="1" checked>
                            <p class="description">Lazy load components below the fold</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-cache-ttl">Cache Duration (seconds)</label></th>
                        <td>
                            <input type="number" id="wpway-cache-ttl" name="wpway_cache_ttl" value="3600">
                            <p class="description">How long to cache components</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-component-dir">Components Directory</label></th>
                        <td>
                            <input type="text" id="wpway-component-dir" name="wpway_component_dir" class="regular-text" 
                                   value="wp-content/themes/your-theme/components" readonly>
                            <p class="description">Default location for components</p>
                        </td>
                    </tr>
                </table>
                
                <h2>Advanced Settings</h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="wpway-spa-root">SPA Root Element</label></th>
                        <td>
                            <input type="text" id="wpway-spa-root" name="wpway_spa_root" value="#app" class="regular-text">
                            <p class="description">CSS selector for SPA container</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row"><label for="wpway-api-prefix">REST API Prefix</label></th>
                        <td>
                            <input type="text" id="wpway-api-prefix" name="wpway_api_prefix" value="wpway/v1" class="regular-text">
                            <p class="description">REST API endpoint prefix</p>
                        </td>
                    </tr>
                </table>
                
                <?php wp_nonce_field('wpway_settings_nonce'); ?>
                <submit class="button button-primary button-large">Save Settings</submit>
            </form>
        </div>
        <?php
    }
    
    /**
     * Documentation page
     */
    public function docsPage() {
        ?>
        <div class="wrap wpway-docs-wrap">
            <h1>WPWay Documentation</h1>
            
            <div class="wpway-docs-container">
                <div class="wpway-docs-toc">
                    <h3>Documentation</h3>
                    <ul>
                        <li><a href="#getting-started">Getting Started</a></li>
                        <li><a href="#components">Components</a></li>
                        <li><a href="#pages">Pages</a></li>
                        <li><a href="#api">REST API</a></li>
                        <li><a href="#hooks">Hooks & Filters</a></li>
                        <li><a href="#faq">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="wpway-docs-content">
                    <h2 id="getting-started">Getting Started</h2>
                    <p>WPWay Framework makes it easy to build modern WordPress sites using component-based architecture.</p>
                    
                    <h2 id="components">Creating Components</h2>
                    <p>Components are reusable pieces of UI. Use the Components tab above to create new components.</p>
                    
                    <h2 id="pages">Creating Pages</h2>
                    <p>Build pages by combining WPWay components. Components are rendered server-side for better performance.</p>
                    
                    <h2 id="api">REST API</h2>
                    <p>WPWay provides REST API endpoints for managing components and pages programmatically.</p>
                    
                    <h2 id="hooks">Hooks & Filters</h2>
                    <p>Extend WPWay functionality using WordPress hooks and filters.</p>
                    
                    <h2 id="faq">Frequently Asked Questions</h2>
                    <dl>
                        <dt>How do I create a component?</dt>
                        <dd>Go to Components tab and click "Create New Component"</dd>
                        
                        <dt>Can I use components in regular WordPress posts?</dt>
                        <dd>Yes! You can use the block editor or shortcodes.</dd>
                        
                        <dt>Where are components stored?</dt>
                        <dd>Components are saved in your theme's components folder.</dd>
                    </dl>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueueAssets($hook) {
        // Only load on WPWay admin pages
        if (strpos($hook, 'wpway') === false) {
            return;
        }
        
        $plugin_url = plugin_dir_url(__DIR__ . '/../wpway.php');
        
        // Enqueue styles
        wp_enqueue_style(
            'wpway-admin',
            $plugin_url . 'assets/admin.css',
            [],
            '1.0.0'
        );
        
        // Enqueue scripts
        wp_enqueue_script(
            'wpway-admin',
            $plugin_url . 'assets/admin.js',
            ['wp-api', 'wp-element', 'jquery'],
            '1.0.0',
            true
        );
        
        // Load code editor library (Ace Editor)
        wp_enqueue_script(
            'ace-editor',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.13/ace.min.js',
            [],
            '1.4.13'
        );
        
        // Localize script with nonce and data
        wp_localize_script('wpway-admin', 'wpwayAdmin', [
            'nonce' => wp_create_nonce('wpway_admin_nonce'),
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'restUrl' => rest_url('wpway/v1'),
            'homeUrl' => home_url()
        ]);
    }
    
    /**
     * Register REST API routes
     */
    public function registerRestRoutes() {
        // Get all components
        register_rest_route('wpway/v1', '/components', array(
            'methods' => 'GET',
            'callback' => [$this, 'getComponents'],
            'permission_callback' => [$this, 'checkPermission']
        ));
        
        // Create component
        register_rest_route('wpway/v1', '/components', array(
            'methods' => 'POST',
            'callback' => [$this, 'createComponent'],
            'permission_callback' => [$this, 'checkPermission']
        ));
        
        // Edit component
        register_rest_route('wpway/v1', '/components/(?P<id>\w+)', array(
            'methods' => 'POST',
            'callback' => [$this, 'editComponent'],
            'permission_callback' => [$this, 'checkPermission']
        ));
        
        // Delete component
        register_rest_route('wpway/v1', '/components/(?P<id>\w+)', array(
            'methods' => 'DELETE',
            'callback' => [$this, 'deleteComponent'],
            'permission_callback' => [$this, 'checkPermission']
        ));
        
        // Get pages
        register_rest_route('wpway/v1', '/pages', array(
            'methods' => 'GET',
            'callback' => [$this, 'getPages'],
            'permission_callback' => [$this, 'checkPermission']
        ));
    }
    
    /**
     * Check permission for REST requests
     */
    public function checkPermission() {
        return current_user_can('manage_options');
    }
    
    /**
     * Get all components via REST API
     */
    public function getComponents() {
        $framework = \WPWay\Core\Framework::getInstance();
        $components = $framework->getComponents();
        
        return new \WP_REST_Response([
            'success' => true,
            'components' => $components
        ], 200);
    }
    
    /**
     * Create component via REST API
     */
    public function createComponent(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        
        $name = sanitize_text_field($params['name']);
        $type = sanitize_text_field($params['type']);
        $code = $params['code'];
        
        // Save component to file
        $file_path = $this->getComponentsPath() . '/' . $name . '.php';
        
        if (file_put_contents($file_path, $code)) {
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Component created successfully',
                'component' => ['name' => $name, 'type' => $type]
            ], 201);
        }
        
        return new \WP_REST_Response([
            'success' => false,
            'message' => 'Failed to create component'
        ], 400);
    }
    
    /**
     * Edit component via REST API
     */
    public function editComponent(\WP_REST_Request $request) {
        $params = $request->get_json_params();
        $component_id = $request['id'];
        
        $code = $params['code'];
        $file_path = $this->getComponentsPath() . '/' . $component_id . '.php';
        
        if (file_put_contents($file_path, $code)) {
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Component updated successfully'
            ], 200);
        }
        
        return new \WP_REST_Response([
            'success' => false,
            'message' => 'Failed to update component'
        ], 400);
    }
    
    /**
     * Delete component via REST API
     */
    public function deleteComponent(\WP_REST_Request $request) {
        $component_id = $request['id'];
        $file_path = $this->getComponentsPath() . '/' . $component_id . '.php';
        
        if (file_exists($file_path) && unlink($file_path)) {
            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Component deleted successfully'
            ], 200);
        }
        
        return new \WP_REST_Response([
            'success' => false,
            'message' => 'Failed to delete component'
        ], 400);
    }
    
    /**
     * Get pages via REST API
     */
    public function getPages() {
        $pages = get_posts([
            'post_type' => 'page',
            'posts_per_page' => -1
        ]);
        
        return new \WP_REST_Response([
            'success' => true,
            'pages' => $pages
        ], 200);
    }
    
    /**
     * Get components directory
     */
    private function getComponentsPath() {
        $theme_dir = get_template_directory();
        $components_dir = $theme_dir . '/components/php';
        
        if (!is_dir($components_dir)) {
            wp_mkdir_p($components_dir);
        }
        
        return $components_dir;
    }
}

// Initialize admin dashboard on plugins_loaded (earlier timing)
add_action('plugins_loaded', function() {
    AdminDashboard::getInstance()->init();
});
