# WPWay Framework - Installation & Workflow Guide

**Complete step-by-step guide to install, configure, use, and contribute to WPWay**

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation](#installation)
3. [Getting Started](#getting-started)
4. [Configuration](#configuration)
5. [Building Your First Project](#building-your-first-project)
6. [Contributing to WPWay](#contributing-to-wpway)
7. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements
- **WordPress:** 5.8 or higher
- **PHP:** 7.4 or higher
- **MySQL:** 5.6 or higher (for WordPress)
- **Web Server:** Apache or Nginx

### Recommended Requirements
- **WordPress:** 6.0 or higher
- **PHP:** 8.0 or higher
- **MySQL:** 8.0 or higher
- **Node.js:** 16.0+ (for development)
- **npm:** 8.0+ (for development)

### Browser Support
- Chrome/Edge 90+
- Firefox 88+
- Safari 14+
- Mobile browsers (iOS Safari 14+, Chrome Mobile)

---

## Installation

### Step 1: Download WPWay

#### Option A: Manual Download
```bash
# Clone from GitHub (when available)
cd wp-content/plugins/
git clone https://github.com/wpway/wpway-framework.git wpway

# Or download ZIP file
# Extract to wp-content/plugins/wpway/
```

#### Option B: From WordPress Admin
1. Go to **Plugins → Add New**
2. Search for "WPWay"
3. Click **Install Now**
4. Click **Activate**

### Step 2: Verify Installation

#### From WordPress Dashboard
1. Go to **Plugins**
2. Look for "WPWay" plugin
3. Status should show **Activated**

#### From FTP/Command Line
```bash
# Check if plugin folder exists
ls -la wp-content/plugins/wpway/

# Should see: wpway.php, assets/, includes/, etc.
```

#### From Browser Console
```javascript
// Check if framework loaded
console.log(WPWay.version)  // Should output: 1.0.0

// Check all components
WPWayDevTools.inspect()  // Shows framework state
```

---

## Getting Started

### Step 1: Activate Developer Mode

**Edit wp-config.php:**
```php
// Around line 60, set debug to true
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', true);
```

### Step 2: Check Debug Output

**View logs in browser:**
1. Press F12 to open DevTools
2. Go to **Console** tab
3. You should see: `[WPWay] Framework initialized`

**Check PHP logs:**
```bash
# If using local development
tail -f wp-content/debug.log | grep WPWay
```

### Step 3: Access Developer Tools

**In browser console (DevTools):**
```javascript
// Show framework state
WPWayDevTools.inspect()

// Get all registered components
WPWayDevTools.getComponents()

// Test API connection
await WPWayDevTools.testAPI()

// View help
WPWayDevTools.help()
```

---

## Configuration

### Step 1: Basic Configuration

Create a configuration file in your theme:

**`wp-content/themes/your-theme/wpway-config.php`:**
```php
<?php
/**
 * WPWay Configuration for Your Theme
 */

// Get framework instance
$framework = \WPWay\Core\Framework::getInstance();

// Enable/disable features
\WPWay\Config\Configuration::merge([
    'debug' => WP_DEBUG,
    'ssr_enabled' => true,
    'hydration_enabled' => true,
    'lazy_loading' => true,
    'plugin_ecosystem' => true,
    'cache_ttl' => 3600,
]);

// Optional: Register your theme's components
$framework->registerComponent('ThemeHero', 'MyTheme\Components\Hero');
$framework->registerComponent('ThemeBlog', 'MyTheme\Components\BlogList');

// Optional: Register Gutenberg blocks
require_once __DIR__ . '/blocks-config.php';
```

### Step 2: Load Configuration

Add to your theme's **functions.php:**
```php
<?php
/**
 * Theme functions.php
 */

// Load WPWay configuration
if (file_exists(get_template_directory() . '/wpway-config.php')) {
    require_once get_template_directory() . '/wpway-config.php';
}

// Your other theme functions...
```

### Step 3: Configuration Options

**Available settings in `/includes/config.php`:**

```php
\WPWay\Config\Configuration::set('key', 'value');

// Common configurations:
'debug' => true/false                      // Enable debug mode
'cache_ttl' => 3600                        // Cache duration (seconds)
'ssr_enabled' => true/false               // Server-side rendering
'hydration_enabled' => true/false         // Client hydration
'lazy_loading' => true/false              // Lazy load components
'plugin_ecosystem' => true/false          // Plugin system
'gutenberg_integration' => true/false     // Gutenberg blocks
'spa_root' => '#app'                      // SPA container ID
'hash_based_routing' => false             // Use hash-based routes
```

---

## Building Your First Project

### Project 1: Blog Theme with WPWay

#### Step 1: Create Theme Structure

```bash
mkdir -p wp-content/themes/wpway-blog
cd wp-content/themes/wpway-blog

# Create directories
mkdir -p components/{php,js}
mkdir -p pages
mkdir -p assets/{css,js}
```

#### Step 2: Create Components

**`components/php/Hero.php`:**
```php
<?php
namespace WPWayBlog\Components;
use WPWay\Core\Component;

class Hero extends Component {
    public function render() {
        $title = $this->props['title'] ?? 'Welcome to Our Blog';
        $subtitle = $this->props['subtitle'] ?? 'Latest news and articles';
        
        return sprintf(
            '<section class="wpway-hero">
                <div class="wpway-hero-inner">
                    <h1>%s</h1>
                    <p>%s</p>
                </div>
            </section>',
            esc_html($title),
            esc_html($subtitle)
        );
    }
}
```

**`components/php/BlogGrid.php`:**
```php
<?php
namespace WPWayBlog\Components;
use WPWay\Core\Component;

class BlogGrid extends Component {
    public function render() {
        $per_page = $this->props['per_page'] ?? 6;
        $columns = $this->props['columns'] ?? 3;
        
        $posts = get_posts([
            'posts_per_page' => $per_page,
            'post_type' => 'post',
            'post_status' => 'publish'
        ]);
        
        $html = sprintf(
            '<div class="wpway-blog-grid wpway-grid-columns-%d">',
            $columns
        );
        
        foreach ($posts as $post) {
            $html .= sprintf(
                '<article class="wpway-blog-card">
                    %s
                    <h3>
                        <a href="%s">%s</a>
                    </h3>
                    <p class="excerpt">%s</p>
                    <a href="%s" class="wpway-link">Read More →</a>
                </article>',
                has_post_thumbnail($post->ID) ? get_the_post_thumbnail($post->ID, 'medium') : '',
                esc_url(get_permalink($post->ID)),
                esc_html(get_the_title($post->ID)),
                esc_html(wp_trim_words(get_the_excerpt($post->ID), 15)),
                esc_url(get_permalink($post->ID))
            );
        }
        
        $html .= '</div>';
        return $html;
    }
}
```

#### Step 3: Register Components

**`wpway-config.php`:**
```php
<?php
// Register theme components
$framework = \WPWay\Core\Framework::getInstance();
$framework->registerComponent('BlogHero', 'WPWayBlog\Components\Hero');
$framework->registerComponent('BlogGrid', 'WPWayBlog\Components\BlogGrid');

// Register blocks for Gutenberg
$engine = \WPWay\Gutenberg\BlockEngine::getInstance();
$engine->registerBlock('wpway-blog/hero', [
    'title' => 'Blog Hero',
    'render_component' => 'BlogHero',
    'attributes' => [
        'title' => ['type' => 'string', 'default' => 'Welcome to Our Blog'],
        'subtitle' => ['type' => 'string', 'default' => 'Latest news and articles']
    ]
]);

$engine->registerBlock('wpway-blog/grid', [
    'title' => 'Blog Grid',
    'render_component' => 'BlogGrid',
    'attributes' => [
        'per_page' => ['type' => 'number', 'default' => 6],
        'columns' => ['type' => 'number', 'default' => 3]
    ]
]);
```

#### Step 4: Create Template

**`index.php`:**
```php
<?php
get_header();

// Render SSR component
$hydration = \WPWay\SSR\Hydration::getInstance();
echo $hydration->renderServer('BlogHero', [
    'title' => 'Our Blog',
    'subtitle' => 'Discover our latest articles'
]);
echo $hydration->renderServer('BlogGrid', ['per_page' => 9]);
echo $hydration->createHydrationScript();

get_footer();
```

#### Step 5: Add Styles

**`assets/css/style.css`:**
```css
/* WPWay Blog Theme Styles */

.wpway-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 20px;
    text-align: center;
}

.wpway-hero-inner h1 {
    font-size: 3em;
    margin-bottom: 10px;
}

.wpway-hero-inner p {
    font-size: 1.2em;
    opacity: 0.9;
}

.wpway-blog-grid {
    display: grid;
    gap: 30px;
    padding: 40px 20px;
    margin: 0 auto;
    max-width: 1200px;
}

.wpway-grid-columns-3 {
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
}

.wpway-blog-card {
    background: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.wpway-blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.wpway-blog-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.wpway-blog-card h3 {
    padding: 20px;
    margin: 0;
    font-size: 1.3em;
}

.wpway-blog-card a {
    color: #667eea;
    text-decoration: none;
}

.wpway-blog-card .excerpt {
    padding: 0 20px;
    color: #666;
    margin: 10px 0;
}

.wpway-link {
    display: inline-block;
    padding: 20px;
    color: #667eea;
    font-weight: bold;
    text-decoration: none;
}

.wpway-link:hover {
    color: #764ba2;
}
```

#### Step 6: Activate Theme

1. Go to **Appearance → Themes**
2. Find your "WPWay Blog" theme
3. Click **Activate**

**Done!** You now have a working WPWay-powered theme.

---

### Project 2: Create a Custom Plugin

#### Step 1: Create Plugin Structure

```bash
mkdir -p wp-content/plugins/wpway-custom
cd wp-content/plugins/wpway-custom

# Create files
touch wpway-custom.php
mkdir -p includes/{components,plugins}
```

#### Step 2: Create Plugin File

**`wpway-custom.php`:**
```php
<?php
/**
 * Plugin Name: WPWay Custom Plugin
 * Description: Custom functionality for WPWay
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0-or-later
 */

if (!defined('ABSPATH')) exit;

define('WPWAY_CUSTOM_DIR', plugin_dir_path(__FILE__));
define('WPWAY_CUSTOM_URL', plugin_dir_url(__FILE__));

// Register custom components
add_action('init', function() {
    if (!class_exists('WPWay\Core\Framework')) {
        return; // WPWay not active
    }
    
    $framework = \WPWay\Core\Framework::getInstance();
    
    // Register custom components
    require_once WPWAY_CUSTOM_DIR . 'includes/components.php';
    
    // Register custom plugin
    require_once WPWAY_CUSTOM_DIR . 'includes/plugin.php';
});
```

#### Step 3: Create Custom Component

**`includes/components.php`:**
```php
<?php
namespace WPWayCustom\Components;
use WPWay\Core\Component;

class CustomGallery extends Component {
    public function render() {
        $images = $this->props['images'] ?? [];
        $columns = $this->props['columns'] ?? 3;
        
        if (empty($images)) {
            return '<div class="wpway-custom-gallery">No images</div>';
        }
        
        $html = sprintf(
            '<div class="wpway-custom-gallery wpway-gallery-columns-%d">',
            $columns
        );
        
        foreach ($images as $image) {
            $html .= sprintf(
                '<figure class="wpway-gallery-item">
                    <img src="%s" alt="%s">
                </figure>',
                esc_url($image['url']),
                esc_attr($image['alt'] ?? '')
            );
        }
        
        $html .= '</div>';
        return $html;
    }
}

// Register component
$framework = \WPWay\Core\Framework::getInstance();
$framework->registerComponent('CustomGallery', __NAMESPACE__ . '\CustomGallery');
```

#### Step 4: Create Custom Plugin

**`includes/plugin.php`:**
```php
<?php
namespace WPWayCustom\Plugin;
use WPWay\PluginAPI\Plugin;

class Analytics extends Plugin {
    protected $name = 'WPWay Analytics';
    protected $version = '1.0.0';
    
    public function init() {
        $system = \WPWay\PluginAPI\PluginSystem::getInstance();
        
        // Register hooks
        $system->addPluginHook('page_load', [$this, 'trackPageLoad']);
        
        // Register filters
        $system->addPluginFilter('state_value', [$this, 'filterState']);
    }
    
    public function trackPageLoad($data) {
        // Track page load event
        error_log('Page loaded: ' . json_encode($data));
    }
    
    public function filterState($state) {
        // Add tracking to state
        return $state;
    }
    
    protected function setupCapabilities() {
        $this->capabilities = ['tracking', 'analytics'];
    }
}

// Register plugin
$pluginSystem = \WPWay\PluginAPI\PluginSystem::getInstance();
$pluginSystem->registerPlugin('custom-analytics', __NAMESPACE__ . '\Analytics');
```

#### Step 5: Activate Plugin

1. Go to **Plugins**
2. Find "WPWay Custom Plugin"
3. Click **Activate**

---

## Contributing to WPWay

### Step 1: Setup Development Environment

#### Fork Repository
```bash
# Go to https://github.com/wpway/wpway-framework
# Click "Fork" button
# Clone your fork

git clone https://github.com/YOUR-USERNAME/wpway-framework.git wpway-dev
cd wpway-dev
```

#### Setup WordPress Locally
```bash
# Using Local WP (recommended)
local-cli create
cd /Users/you/Local\ Sites/wpway-dev/app/public

# Or use Docker
docker-compose up -d

# Or use XAMPP/MAMP
cp -r wpway /Applications/XAMPP/xamppfiles/htdocs/wordpress/wp-content/plugins/
```

### Step 2: Create Feature Branch

```bash
# Always create a new branch for features
git checkout -b feature/my-new-feature

# Or for bug fixes
git checkout -b bugfix/issue-title

# Or for documentation
git checkout -b docs/update-guide
```

### Step 3: Make Changes

#### Code Changes
```
includes/
  ├── core/
  │   └── framework.php (modify here)
  ├── gutenberg/
  │   └── blocks.php
  └── plugin-api/
      └── plugin-system.php

assets/
  ├── wpway-core.js (modify here)
  ├── performance.js
  └── rest-api.js
```

#### Documentation Changes
```
documentation/
  ├── README.md
  ├── ARCHITECTURE.md
  ├── GETTING_STARTED.md
  └── WORKFLOW.md (this file)
```

### Step 4: Test Your Changes

#### Local Testing
```bash
# Test in browser
# Go to http://localhost/wordpress/wp-admin

# Check console
F12 → Console tab
WPWayDevTools.inspect()

# Run PHP linter
php -l includes/core/framework.php

# Check your code follows standards
# Run WordPress coding standards
```

#### Test Checklist
- [ ] Feature works as intended
- [ ] No console errors
- [ ] No PHP errors in debug.log
- [ ] Code follows WordPress standards
- [ ] Documentation updated
- [ ] Example added if needed

### Step 5: Commit Changes

```bash
# Stage your changes
git add includes/core/framework.php
git add assets/wpway-core.js
git add README.md

# Commit with clear message
git commit -m "feat: add new component lifecycle hook

- Add beforeRender hook
- Add afterRender hook
- Document hooks in ARCHITECTURE.md
- Add example in GETTING_STARTED.md"

# Or for bug fixes
git commit -m "fix: resolve component re-render issue

Fixes #123
- Prevent unnecessary re-renders
- Add memo to expensive computations
- Add test case"

# Or for docs
git commit -m "docs: improve component guide

- Add typescript examples
- Clarify lifecycle timing
- Add performance tips"
```

### Step 6: Push & Create Pull Request

```bash
# Push to your fork
git push origin feature/my-new-feature

# Go to GitHub
# You'll see "Create Pull Request" button
# Fill in description
# Reference issue: Fixes #123
# List changes made
# Submit PR
```

### Step 7: PR Template

**Use this format for pull requests:**

```markdown
## Description
Brief explanation of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Documentation update
- [ ] Performance improvement

## Related Issues
Fixes #123
Related to #456

## Changes Made
- Change 1
- Change 2
- Change 3

## How to Test
1. Do this
2. Then this
3. Verify this

## Checklist
- [ ] Code follows framework standards
- [ ] Documentation updated
- [ ] Tests added/updated
- [ ] No breaking changes
- [ ] Ready for production
```

### Step 8: Code Standards

#### PHP Style Guide
```php
<?php
// Use PSR-12 style
class MyClass {
    public function myMethod($param) {
        // 4 space indentation
        if ($param) {
            return true;
        }
        return false;
    }
}
```

#### JavaScript Style Guide
```javascript
// Use consistent formatting
class MyComponent extends WPWay.Component {
    render() {
        const [state, setState] = WPWay.useState(false);
        
        return WPWay.createElement('div', { class: 'my-component' },
            WPWay.createElement('h1', {}, 'Title')
        );
    }
}
```

#### Comment Style
```php
/**
 * Brief description of function/class
 * 
 * Longer description explaining purpose
 * and how to use it
 * 
 * @param string $param Description
 * @return string Description
 */
```

### Step 9: Documentation

#### Update Relevant Files
- **README.md** - Feature overview
- **ARCHITECTURE.md** - Technical details
- **GETTING_STARTED.md** - Usage examples
- **MANIFEST.md** - API reference

#### Add Examples
```php
// In GETTING_STARTED.md

### Using New Feature

**Basic usage:**
\`\`\`php
// Code example
\`\`\`

**Advanced usage:**
\`\`\`php
// Advanced example
\`\`\`
```

---

## Troubleshooting

### Installation Issues

#### "WPWay plugin not appearing"
```bash
# Check permissions
chmod -R 755 wp-content/plugins/wpway/

# Check PHP version
php -v  # Should be 7.4+

# Check wp-config.php loaded
wp --allow-root config get DB_NAME
```

#### "Fatal error: Class not found"
```bash
# Ensure WPWay is activated
wp plugin activate wpway --allow-root

# Check bootstrap.php is loaded
grep "require_once.*bootstrap" wpway.php
```

### Configuration Issues

#### "Debug bar not showing"
```php
// In wp-config.php, ensure:
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);

// Check debug.log exists
tail -f wp-content/debug.log
```

#### "Components not registering"
```php
// In wpway-config.php, check:
$framework = \WPWay\Core\Framework::getInstance();
// Component class must exist
// Namespace must be correct
// Must be called on 'init' hook
```

### Performance Issues

#### "Slow component rendering"
```javascript
// In console:
WPWayDevTools.getPerformance()

// Check for:
- Large components
- Unnecessary re-renders
- Missing memoization
- Slow queries
```

#### "High memory usage"
```php
// Check cache not filling up
\WPWay\Config\Configuration::set('cache_ttl', 1800);

// Clear cache
$framework->clearCache();
```

### API Issues

#### "REST endpoint returning 404"
```bash
# Flush WordPress rewrite rules
wp rewrite flush --allow-root

# Check endpoint exists
curl http://localhost/wp-json/wpway/v1/components
```

#### "CORS errors"
```javascript
// Add headers in functions.php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
```

### Browser Console Issues

#### "WPWay is undefined"
```javascript
// Check in console:
console.log(document.currentScript)
// Ensure wpway-core.js loaded

// Check Network tab:
// Should see wpway-core.js loaded
// Status 200 OK
```

#### "Hydration not completing"
```javascript
// Check in console:
WPWayHydration.getStats()

// Debug:
window.addEventListener('wpway:hydration-complete', (e) => {
    console.log('Hydration complete:', e.detail)
})
```

---

## Common Workflows

### Workflow 1: Create and Test Component

```bash
# 1. Create component
touch wp-content/themes/my-theme/components/MyComponent.php

# 2. Register component
# Edit functions.php, add registration

# 3. Test in template
# Create test page, render component

# 4. View in browser
# http://localhost/wordpress/test-page

# 5. Check performance
# F12 → WPWayDevTools.getPerformance()
```

### Workflow 2: Submit Contribution

```bash
# 1. Create branch
git checkout -b feature/awesome-feature

# 2. Make changes
# Edit files, add docs

# 3. Test locally
# Run through test checklist

# 4. Commit
git commit -m "feat: add awesome feature"

# 5. Push
git push origin feature/awesome-feature

# 6. Create PR on GitHub
# Fill in PR template
# Wait for review
```

### Workflow 3: Debug Performance Issue

```bash
# 1. Open DevTools
F12

# 2. Check performance
WPWayDevTools.getPerformance()

# 3. Identify slow component
// Look at render times

# 4. Add memo/optimization
// Modify component

# 5. Benchmark improvement
WPWayDevTools.getPerformance()

# 6. Submit fix
git commit -m "perf: optimize component rendering"
```

---

## Quick Reference

### Essential Commands

```bash
# WordPress CLI
wp plugin activate wpway
wp plugin deactivate wpway
wp rewrite flush
wp option get wpway_version

# Git
git clone <repo>
git checkout -b <branch>
git add .
git commit -m "<message>"
git push origin <branch>

# PHP
php -v              # Check version
php -l file.php     # Lint file
php -r "code"       # Run code
```

### Browser Console

```javascript
// Check framework
WPWay.version
WPWayDevTools.inspect()

// Get components
WPWayDevTools.getComponents()

// Test API
await WPWayDevTools.testAPI()

// Get performance
WPWayDevTools.getPerformance()

// Help
WPWayDevTools.help()
```

### File Locations

```
Installation: wp-content/plugins/wpway/
Theme: wp-content/themes/your-theme/
Custom Plugins: wp-content/plugins/my-plugin/
Documentation: /docs (in plugin root)
```

---

## Getting Help

### Resources
- **Documentation:** Check README.md, ARCHITECTURE.md
- **Examples:** View example-components.php
- **GitHub Issues:** Report bugs and request features
- **Discussions:** Ask questions in GitHub discussions

### Report Issues
```markdown
1. Describe the problem
2. Show steps to reproduce
3. Include error messages
4. Provide WordPress/PHP version
5. Submit on GitHub Issues
```

### Request Features
```markdown
1. Describe the feature
2. Explain the use case
3. Provide examples
4. Discuss implementation
5. Submit on GitHub Issues
```

---

## Next Steps

### For Users
1. ✅ Install WPWay
2. ✅ Read GETTING_STARTED.md
3. ✅ Create your first component
4. ✅ Build a theme or plugin
5. Check out examples

### For Contributors
1. ✅ Fork repository
2. ✅ Create feature branch
3. ✅ Make changes
4. ✅ Test locally
5. ✅ Submit pull request

### For Maintainers
1. Review pull requests
2. Merge approved changes
3. Update documentation
4. Release new versions
5. Community engagement

---

## Support This Project

### Ways to Help
- ⭐ Star on GitHub
- 📢 Share with others
- 🐛 Report bugs
- 💡 Suggest features
- 📝 Improve docs
- 🛠️ Submit PRs
- 💻 Build extensions

### License
GPL-2.0-or-later - Free to use and modify

---

## Summary

You now know how to:
- ✅ Install WPWay Framework
- ✅ Configure it for your needs
- ✅ Build themes and plugins
- ✅ Create custom components
- ✅ Contribute to the project
- ✅ Troubleshoot issues

**Ready to build something amazing with WPWay?** 🚀

---

**WPWay Framework v1.0.0**  
*React-like Frontend Framework for WordPress*  
*Open Source • Production Ready • Community Driven*

**GitHub:** https://github.com/wpway/wpway-framework  
**Website:** https://wpway.dev  
**Documentation:** See INDEX.md
