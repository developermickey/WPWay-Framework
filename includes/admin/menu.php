<?php
/**
 * WPWay Admin Menu
 * Simple, direct menu registration
 */

if (!defined('ABSPATH')) exit;

// Define callback functions FIRST in global namespace
// This ensures WordPress can find them when the menu calls them

function wpway_dashboard_page();
function wpway_components_page();
function wpway_pages_page();
function wpway_code_editor_page();
function wpway_settings_page();
function wpway_documentation_page();

// ============================================================================
// 1. REGISTER ADMIN MENUS - Runs on admin_menu hook
// ============================================================================

add_action('admin_menu', function() {
    // Check user is admin
    if (!current_user_can('manage_options')) {
        return;
    }

    // Main WPWay Menu
    add_menu_page(
        'WPWay Dashboard',                      // Page title
        'WPWay',                                // Menu title  
        'manage_options',                       // Capability
        'wpway-dashboard',                      // Menu slug
        'wpway_dashboard_page',                 // Callback function
        'dashicons-layout',                     // Icon
        76                                      // Position
    );

    // Dashboard submenu
    add_submenu_page(
        'wpway-dashboard',
        'Dashboard',
        'Dashboard',
        'manage_options',
        'wpway-dashboard',
        'wpway_dashboard_page'
    );

    // Components submenu
    add_submenu_page(
        'wpway-dashboard',
        'Components',
        'Components',
        'manage_options',
        'wpway-components',
        'wpway_components_page'
    );

    // Pages submenu
    add_submenu_page(
        'wpway-dashboard',
        'Pages',
        'Pages',
        'manage_options',
        'wpway-pages',
        'wpway_pages_page'
    );

    // Code Editor submenu
    add_submenu_page(
        'wpway-dashboard',
        'Code Editor',
        'Code Editor',
        'manage_options',
        'wpway-code-editor',
        'wpway_code_editor_page'
    );

    // Settings submenu
    add_submenu_page(
        'wpway-dashboard',
        'Settings',
        'Settings',
        'manage_options',
        'wpway-settings',
        'wpway_settings_page'
    );

    // Documentation submenu
    add_submenu_page(
        'wpway-dashboard',
        'Documentation',
        'Documentation',
        'manage_options',
        'wpway-documentation',
        'wpway_documentation_page'
    );

    error_log('[WPWay Menu] Menus registered successfully');
});

// ============================================================================
// 2. DASHBOARD PAGE CALLBACK
// ============================================================================

function wpway_dashboard_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 30px;">
            <span style="color: #0073aa;">WPWay</span> Dashboard
        </h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h2>Welcome to WPWay Framework! 🎉</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 30px 0;">
                
                <!-- Quick Stats -->
                <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; border-left: 4px solid #0073aa;">
                    <h3 style="margin: 0 0 10px 0;">📦 Components</h3>
                    <p style="margin: 0; font-size: 24px; color: #0073aa; font-weight: bold;">0</p>
                </div>

                <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; border-left: 4px solid #00a32a;">
                    <h3 style="margin: 0 0 10px 0;">📄 Pages</h3>
                    <p style="margin: 0; font-size: 24px; color: #00a32a; font-weight: bold;">0</p>
                </div>

                <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; border-left: 4px solid #ffb900;">
                    <h3 style="margin: 0 0 10px 0;">🔧 Settings</h3>
                    <p style="margin: 0; font-size: 14px; color: #666;">Configure framework</p>
                </div>

            </div>

            <h3 style="margin-top: 30px;">Quick Start Guide</h3>
            <ol style="line-height: 2; color: #555;">
                <li><strong>Create Component:</strong> Go to WPWay → Components → Click "Create Component"</li>
                <li><strong>Create Page:</strong> Go to WPWay → Pages → Click "Create Page"</li>
                <li><strong>Edit Code:</strong> Go to WPWay → Code Editor → Select and edit</li>
                <li><strong>Configure:</strong> Go to WPWay → Settings → Save changes</li>
            </ol>

            <p style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-left: 4px solid #0073aa; color: #003d82;">
                <strong>💡 Tip:</strong> Each component can be reused across multiple pages. Create once, use everywhere!
            </p>
        </div>
    </div>
    <?php
}

// ============================================================================
// 3. COMPONENTS PAGE CALLBACK
// ============================================================================

function wpway_components_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">📦 Components</h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            
            <!-- Create Component Button -->
            <div style="margin-bottom: 20px;">
                <a href="#" class="button button-primary" style="padding: 10px 20px;">+ Create Component</a>
            </div>

            <!-- Create Component Form -->
            <div id="create-component-form" style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                <h3>Create New Component</h3>

                <form style="max-width: 600px;">
                    <table class="form-table">
                        <tr>
                            <th><label for="component-name">Component Name</label></th>
                            <td>
                                <input type="text" id="component-name" name="component_name" 
                                       class="regular-text" placeholder="e.g., Hero, Card, BlogList"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <p class="description">Use PascalCase (MyComponent)</p>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="component-type">Component Type</label></th>
                            <td>
                                <select id="component-type" name="component_type" 
                                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                    <option value="">-- Select Type --</option>
                                    <option value="php">PHP Component</option>
                                    <option value="javascript">JavaScript Component</option>
                                    <option value="hybrid">Hybrid (PHP + JS)</option>
                                </select>
                                <p class="description">Choose component type</p>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="component-code">Component Code</label></th>
                            <td>
                                <textarea id="component-code" name="component_code" 
                                         style="width: 100%; height: 300px; padding: 8px; border: 1px solid #ddd; border-radius: 3px; font-family: monospace;"
                                         placeholder="&lt;?php
class MyComponent extends Component {
    public function render() {
        return '&lt;div&gt;Hello World&lt;/div&gt;';
    }
}"><?php echo htmlspecialchars('<?php
class MyComponent extends Component {
    public function render() {
        return "<div>Hello World</div>";
    }
}'); ?></textarea>
                                <p class="description">Paste your component code here</p>
                            </td>
                        </tr>
                    </table>

                    <p>
                        <button type="submit" class="button button-primary" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                            Create Component
                        </button>
                        <button type="button" class="button" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                            Cancel
                        </button>
                    </p>
                </form>
            </div>

            <!-- Components List -->
            <h3>Your Components</h3>
            <p style="color: #999; padding: 20px; text-align: center; background: #f5f5f5; border-radius: 3px;">
                No components created yet. Create one above to get started! ➕
            </p>

        </div>
    </div>
    <?php
}

// ============================================================================
// 4. PAGES PAGE CALLBACK
// ============================================================================

function wpway_pages_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">📄 Pages</h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            
            <!-- Create Page Button -->
            <div style="margin-bottom: 20px;">
                <a href="#" class="button button-primary" style="padding: 10px 20px;">+ Create Page</a>
            </div>

            <!-- Create Page Form -->
            <div id="create-page-form" style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin-bottom: 30px;">
                <h3>Create New Page</h3>

                <form style="max-width: 600px;">
                    <table class="form-table">
                        <tr>
                            <th><label for="page-title">Page Title</label></th>
                            <td>
                                <input type="text" id="page-title" name="page_title" 
                                       class="regular-text" placeholder="e.g., Home, About, Services"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <p class="description">Name of your page</p>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="page-url">URL Slug</label></th>
                            <td>
                                <input type="text" id="page-url" name="page_url" 
                                       class="regular-text" placeholder="Auto-generated from title"
                                       style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                                <p class="description">Leave blank for auto-generation</p>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="page-description">Description</label></th>
                            <td>
                                <textarea id="page-description" name="page_description" 
                                         style="width: 100%; height: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 3px;"
                                         placeholder="Describe this page..."></textarea>
                                <p class="description">Optional description</p>
                            </td>
                        </tr>
                    </table>

                    <p>
                        <button type="submit" class="button button-primary" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                            Create Page
                        </button>
                        <button type="button" class="button" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                            Cancel
                        </button>
                    </p>
                </form>
            </div>

            <!-- Pages List -->
            <h3>Your Pages</h3>
            <p style="color: #999; padding: 20px; text-align: center; background: #f5f5f5; border-radius: 3px;">
                No pages created yet. Create one above to get started! ➕
            </p>

        </div>
    </div>
    <?php
}

// ============================================================================
// 5. CODE EDITOR PAGE CALLBACK
// ============================================================================

function wpway_code_editor_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">💻 Code Editor</h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            
            <div style="display: grid; grid-template-columns: 220px 1fr; gap: 20px;">
                
                <!-- File Browser -->
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #ddd;">
                    <h3 style="margin-top: 0;">Components</h3>
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 5px 0; color: #999;">
                            👉 Create a component first to edit
                        </li>
                    </ul>
                </div>

                <!-- Code Editor -->
                <div>
                    <h3>Select Component to Edit</h3>
                    <div style="background: #2d2d2d; color: #f8f8f2; padding: 20px; border-radius: 5px; margin-bottom: 20px; min-height: 400px; font-family: 'Courier New', monospace; font-size: 14px; line-height: 1.5;">
                        <p style="color: #75715e;">// Select a component from the list to edit its code</p>
                    </div>

                    <p>
                        <button type="button" class="button button-primary" style="padding: 10px 20px;">Save Changes</button>
                        <button type="button" class="button" style="padding: 10px 20px;">Reset</button>
                    </p>
                </div>

            </div>

        </div>
    </div>
    <?php
}

// ============================================================================
// 6. SETTINGS PAGE CALLBACK
// ============================================================================

function wpway_settings_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">⚙️ Settings</h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            
            <form method="post" style="max-width: 600px;">
                <table class="form-table">
                    <tr>
                        <th><label for="debug-mode">Enable Debug Mode</label></th>
                        <td>
                            <input type="checkbox" id="debug-mode" name="debug_mode" value="1" checked>
                            <p class="description">Enable debug logging and verbose errors</p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="enable-cache">Enable Caching</label></th>
                        <td>
                            <input type="checkbox" id="enable-cache" name="enable_cache" value="1" checked>
                            <p class="description">Cache components and assets for better performance</p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="ssr">Server-Side Rendering</label></th>
                        <td>
                            <input type="checkbox" id="ssr" name="ssr" value="1" checked>
                            <p class="description">Render components on server for faster initial load</p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="lazy-load">Lazy Loading</label></th>
                        <td>
                            <input type="checkbox" id="lazy-load" name="lazy_load" value="1" checked>
                            <p class="description">Load components below the fold on-demand</p>
                        </td>
                    </tr>

                    <tr>
                        <th><label for="cache-duration">Cache Duration (seconds)</label></th>
                        <td>
                            <input type="number" id="cache-duration" name="cache_duration" value="3600" 
                                   style="width: 150px; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                            <p class="description">How long to cache components (default: 1 hour)</p>
                        </td>
                    </tr>
                </table>

                <p>
                    <button type="submit" class="button button-primary" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                        Save Settings
                    </button>
                    <button type="reset" class="button" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
                        Reset
                    </button>
                </p>
            </form>

        </div>
    </div>
    <?php
}

// ============================================================================
// 7. DOCUMENTATION PAGE CALLBACK
// ============================================================================

function wpway_documentation_page() {
    ?>
    <div class="wrap" style="margin-top: 20px;">
        <h1 style="color: #333; margin-bottom: 20px;">📚 Documentation</h1>

        <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            
            <div style="display: grid; grid-template-columns: 250px 1fr; gap: 30px;">
                
                <!-- Table of Contents -->
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
                    <h3 style="margin-top: 0;">Table of Contents</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">Getting Started</a></li>
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">Creating Components</a></li>
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">Creating Pages</a></li>
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">REST API</a></li>
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">Troubleshooting</a></li>
                        <li><a href="#" style="color: #0073aa; text-decoration: none; display: block; padding: 5px 0;">FAQ</a></li>
                    </ul>
                </div>

                <!-- Content -->
                <div>
                    <h2 id="getting-started">Getting Started</h2>
                    
                    <h3>What is WPWay?</h3>
                    <p>WPWay is a React-like component framework for WordPress. It helps you build dynamic, reusable components that work beautifully in WordPress.</p>

                    <h3>Quick Start (3 Steps)</h3>
                    <ol style="line-height: 2;">
                        <li><strong>Create a Component:</strong> Go to Components menu and click "Create Component"</li>
                        <li><strong>Create a Page:</strong> Go to Pages menu and select components for your page</li>
                        <li><strong>View Page:</strong> Click "View" to see your page live on the site</li>
                    </ol>

                    <h3>Creating Your First Component</h3>
                    <pre style="background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto;"><code>&lt;?php
class MyComponent extends Component {
    public function render() {
        return '&lt;div class="my-component"&gt;
            &lt;h2&gt;Hello World&lt;/h2&gt;
        &lt;/div&gt;';
    }
}</code></pre>

                    <p>
                        <em>For more detailed help, see the full documentation files in your WordPress folder.</em>
                    </p>
                </div>

            </div>

        </div>
    </div>
    <?php
}

// ============================================================================
// 8. ENQUEUE ADMIN ASSETS
// ============================================================================

add_action('admin_enqueue_scripts', function($hook) {
    // Check if we're on WPWay pages
    if (strpos($hook, 'wpway-') === false) {
        return;
    }

    // Enqueue admin CSS
    $plugin_url = plugin_dir_url(__DIR__ . '/../../wpway.php');
    wp_enqueue_style(
        'wpway-admin-simple',
        $plugin_url . 'assets/admin.css',
        [],
        '1.0.0'
    );

    // Enqueue admin JS
    wp_enqueue_script(
        'wpway-admin-simple',
        $plugin_url . 'assets/admin.js',
        ['jquery', 'wp-plugins', 'wp-edit-post'],
        '1.0.0',
        true
    );

    // Localize script
    wp_localize_script('wpway-admin-simple', 'wpwayAdmin', [
        'nonce' => wp_create_nonce('wpway_nonce'),
        'restUrl' => rest_url('wpway/v1/'),
        'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
});

error_log('[WPWay] Menu module loaded successfully');