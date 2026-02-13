# WPWay Framework - Troubleshooting Guide

## Critical Error: "There has been a critical error on this website"

### What This Means
This generic WordPress error usually indicates a PHP fatal error occurs early in plugin loading. Follow the steps below to diagnose the issue.

---

## Step 1: Enable Debug Mode

### On Your Server

Edit **`wp-config.php`** (usually in your WordPress root directory):

```php
// Add or modify these lines (around line 60)
define('WP_DEBUG', true);
define('WP_DEBUG_DISPLAY', false);  // Don't display errors on site
define('WP_DEBUG_LOG', true);        // Log errors instead
```

### Check the Error Log

```bash
# On Linux/Mac via SSH
tail -f wp-content/debug.log | grep WPWay

# On Windows, open the file in an editor
# wp-content/debug.log
```

---

## Step 2: Common Issues & Solutions

### Issue 1: "Class not found" Error

**Error in debug.log:**
```
Fatal error: Class 'WPWay\Bootstrap' not found in ...wpway.php
```

**Solution:**
```php
// Verify in wpway.php:
if (!defined('ABSPATH')) exit;  // Must be first
require_once WPWAY_DIR . 'includes/bootstrap.php';  // File must exist

// Make sure file exists:
// wp-content/plugins/wpway/includes/bootstrap.php
```

**Fix:**
```bash
# Check file exists
ls -la wp-content/plugins/wpway/includes/bootstrap.php

# If missing, re-upload the wpway folder
# Then activate plugin again
```

---

### Issue 2: "Cannot redeclare class" Error

**Error in debug.log:**
```
Fatal error: Cannot redeclare class WPWay\Core\Framework
```

**Solution:**
The same file is being included twice. This usually means:

1. **Plugin is in wrong location**
   - Move from `wp-content/plugins/wpway/` to a subdirectory won't work
   - Correct: `wp-content/plugins/wpway/` ✅
   - Wrong: `wp-content/plugins/wpway-framework/` (will cause issues if bootstrap path is wrong)

2. **Fix:**
   ```bash
   # Deactivate plugin first
   # Delete wp-content/plugins/wpway/
   # Re-upload wpway folder properly
   # Reactivate
   ```

---

### Issue 3: PHP Version Error

**Error in debug.log:**
```
Parse error: syntax error, unexpected T_FN in ...framework.php on line 65
```

**Cause:** Your server has **PHP 7.3 or lower**. WPWay requires **PHP 7.4+**

**Solution:**
```bash
# Check PHP version
php -v

# If lower than 7.4, contact your host to upgrade
# Or upgrade your hosting provider
```

**Minimum Requirements:**
- PHP: 7.4 or higher ✅
- WordPress: 5.8 or higher ✅
- MySQL: 5.6 or higher ✅

---

### Issue 4: Permission Problems

**Error in debug.log:**
```
Warning: require_once(...): Failed to open stream: Permission denied
```

**Solution:**
```bash
# Fix file permissions (on Linux/Mac)
chmod 755 wp-content/plugins/wpway/
chmod 755 wp-content/plugins/wpway/includes/
chmod 755 wp-content/plugins/wpway/includes/core/
chmod 755 wp-content/plugins/wpway/includes/*/
chmod 644 wp-content/plugins/wpway/*.php
chmod 644 wp-content/plugins/wpway/**/*.php
chmod 644 wp-content/plugins/wpway/assets/*
```

---

### Issue 5: Missing Required Files

**Error in debug.log:**
```
File not found - wp-content/plugins/wpway/includes/config.php
```

**Solution:**
Ensure all required files exist:

```bash
# List all required files (must exist)
wp-content/plugins/wpway/
├── wpway.php                    # ✅ Main plugin file
├── includes/
│   ├── bootstrap.php            # ✅ Initialization
│   ├── config.php               # ✅ Configuration
│   ├── dev-tools.php            # ✅ Developer tools
│   ├── rest-api-enhanced.php    # ✅ REST API
│   ├── example-components.php   # ✅ Examples
│   ├── core/
│   │   ├── framework.php        # ✅ Core
│   │   ├── component.php        # ✅ Component class
│   │   └── virtual-dom.php      # ✅ Virtual DOM
│   ├── router/router.php        # ✅ Router
│   ├── state/store.php          # ✅ State store
│   ├── gutenberg/blocks.php     # ✅ Gutenberg
│   ├── ssr/hydration.php        # ✅ SSR/Hydration
│   ├── plugin-api/plugin-system.php  # ✅ Plugin system
│   └── performance/optimizer.php     # ✅ Performance
├── assets/
│   ├── wpway-core.js            # ✅ Core JS
│   ├── hydration.js             # ✅ Hydration
│   ├── performance.js           # ✅ Performance
│   ├── rest-api.js              # ✅ REST API
│   ├── dev-tools.js             # ✅ Dev tools
│   └── wpway.css                # ✅ Styles
└── wpway-components/
    ├── blog-list.js
    └── single-post.js
```

**If files are missing:**
1. Download the complete package again
2. Delete old `wpway` folder
3. Upload new `wpway` folder
4. Activate plugin

---

## Step 3: Verify Installation

### From WordPress Dashboard

1. Go to **Plugins**
2. Look for "WPWay"
3. Click **Activate** if not activated

### From Browser Console

```javascript
// Open DevTools (F12)
// Go to Console tab
// Type:

console.log(WPWay.version)  // Should output: 1.0.0

// Or test the dev tools:
WPWayDevTools.help()        // Should show help menu
```

### From Server Terminal

```bash
# Check if plugin is active
wp plugin is-active wpway --allow-root

# List active plugins
wp plugin list --allow-root | grep wpway

# Activate if needed
wp plugin activate wpway --allow-root
```

---

## Step 4: Check Server Logs

### Apache Error Log
```bash
# On many servers
tail -f /var/log/apache2/error.log | grep wpway

# On cPanel
tail -f /home/username/public_html/error_log
```

### Nginx Error Log
```bash
tail -f /var/log/nginx/error.log | grep wpway
```

### PHP FPM Log (if using PHP-FPM)
```bash
tail -f /var/log/php-fpm/error.log | grep wpway
```

---

## Step 5: Disable & Re-enable

### Via WordPress Admin

1. Go to **Plugins**
2. Find **WPWay**
3. Click **Deactivate**
4. **Wait 1 minute**
5. Check if site works (no error)
6. Click **Activate**

### Via WP-CLI

```bash
# Deactivate
wp plugin deactivate wpway --allow-root

# Wait a moment
sleep 2

# Reactivate
wp plugin activate wpway --allow-root

# Check status
wp plugin is-active wpway --allow-root
```

### Via Manual File Edit

```php
// In wp-content/plugins/wpway/wpway.php
// Comment out the main function temporarily to test:

/*
add_action('plugins_loaded', function() {
    try {
        if (class_exists('WPWay\Bootstrap')) {
            \WPWay\Bootstrap::init();
        }
    } catch (\Throwable $e) {
        error_log('WPWay Plugin Error: ' . $e->getMessage());
    }
});
*/
```

This will deactivate the plugin without deleting files.

---

## Step 6: Test Components

Once plugin is working:

```javascript
// In browser console (F12 → Console)

// Test framework is loaded
console.log(typeof WPWay)  // Should show "object"

// Get all registered components
console.log(WPWayDevTools.getComponents())

// Get performance metrics
console.log(WPWayDevTools.getPerformance())

// Test REST API
fetch('/wp-json/wpway/v1/components')
  .then(r => r.json())
  .then(d => console.log(d))
  .catch(e => console.error(e))
```

---

## Step 7: Contact Support

If issue persists, gather this information:

1. **PHP Version**
   ```bash
   php -v
   ```

2. **WordPress Version**
   - Dashboard → Updates

3. **Error Log Content**
   - Share lines from `wp-content/debug.log`

4. **Plugin List**
   ```bash
   wp plugin list --allow-root
   ```

5. **Active Theme**
   - Dashboard → Appearance → Themes

6. **Server Info**
   - Hosting provider
   - Server type (Apache/Nginx)
   - PHP type (CGI/FPM/API)

---

## Common Questions

### Q: How do I completely remove WPWay?

```bash
# Via WP-CLI
wp plugin delete wpway --allow-root

# Or manually:
rm -rf wp-content/plugins/wpway/
```

### Q: Can I have multiple versions of WPWay?

**No.** Only one instance can be active. Remove old versions:

```bash
rm -rf wp-content/plugins/wpway-framework/
rm -rf wp-content/plugins/wpway-old/
```

### Q: How do I reset WPWay?

1. Deactivate plugin
2. Delete `wp-content/plugins/wpway/` folder
3. Clear browser cache (Ctrl+Shift+Delete)
4. Re-upload fresh `wpway` folder
5. Reactivate

### Q: Where are error logs?

- Main debug log: `wp-content/debug.log`
- Server errors: Check server error logs
- Browser console: Press F12 → Console tab

---

## Checklist for Troubleshooting

- [ ] Enabled WP_DEBUG and checked debug.log
- [ ] Verified PHP version is 7.4+
- [ ] Verified WordPress version is 5.8+
- [ ] Confirmed all required files exist
- [ ] Fixed file permissions
- [ ] Deactivated and reactivated plugin
- [ ] Cleared browser cache
- [ ] Checked server error logs
- [ ] Tested in browser console
- [ ] Tried on different browser

---

## Still Having Issues?

**Before contacting support, provide:**

1. Full error message from debug.log
2. PHP version
3. WordPress version
4. Screenshots of the error
5. Steps you took to install
6. List of other active plugins

**Get Help:**
- GitHub Issues: https://github.com/mukesh-pathak/wpway/issues
- Documentation: See README.md and ARCHITECTURE.md
- Check WORKFLOW.md for installation steps

---

## Next Steps

Once plugin is working:
1. Read GETTING_STARTED.md
2. Create your first component
3. Build a theme or plugin
4. Check out examples in WORKFLOW.md

**Happy coding with WPWay!** 🚀
