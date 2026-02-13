# WPWay Admin Dashboard

**Complete GUI for managing components, pages, and settings from WordPress dashboard**

---

## 📊 Overview

WPWay Admin Dashboard provides a complete graphical interface for:
- ✅ Creating, editing, and deleting components
- ✅ Managing pages with components
- ✅ Code editor for component editing
- ✅ Configuration settings
- ✅ Live statistics and monitoring
- ✅ Built-in documentation

---

## 🚀 Getting Started

### Access the Dashboard

1. Login to WordPress admin
2. Look for **"WPWay"** in the left sidebar (main menu)
3. Click to open dashboard

**Menu Structure:**
```
WPWay (Main Dashboard)
├── Components
├── Pages
├── Code Editor
├── Settings
└── Documentation
```

---

## 📦 Components Management

### Create New Component

**Step 1: Go to Components**
- Click **WPWay** → **Components**

**Step 2: Click "Create New Component"**
- Navigate to "Create New" tab

**Step 3: Fill Component Details**

| Field | Description | Example |
|-------|-------------|---------|
| **Component Name** | Unique component name | Hero |
| **Component Type** | PHP, JavaScript, or Hybrid | PHP Component |
| **Description** | What component does | "Hero section with image and CTA button" |
| **Template** | Pre-built template (optional) | Hero Section |
| **Component Code** | Actual component code | (PHP code) |

**Step 4: Click "Create Component"**
- Component will be saved to: `wp-content/themes/your-theme/components/php/`

### Edit Component

**Method 1: From Components List**
1. Go to **Components**
2. Find component in "Components List" tab
3. Click **Edit** button
4. Modify code in Code Editor
5. Click **Save Changes**

**Method 2: From Code Editor**
1. Go to **Code Editor**
2. Select component from sidebar
3. Edit code directly
4. Click **Save** button

### Delete Component

1. Go to **Components**
2. Find component in list
3. Click **Delete** button
4. Confirm deletion

**Warning:** Deletion is permanent!

---

## 📄 Pages Management

### Create New Page

**Step 1: Click "Create New Page"**
- Go to **WPWay** → **Pages**
- Click **Create New Page** button

**Step 2: Enter Page Details**
- **Page Title:** Name of the page
- **URL Slug:** Auto-generated from title
- **Select Components:** Choose components to include

**Step 3: Click "Create Page"**
- Page will be created as WordPress page with selected components

### Manage Pages

**View All Pages:**
- Go to **WPWay** → **Pages**
- See table with all WPWay pages

**Edit Page:**
- Click **Edit** button in table
- Modify in standard WordPress editor

**View Page:**
- Click **View** button in table
- Opens page on front-end

**Delete Page:**
- Use standard WordPress page deletion

---

## 💻 Code Editor

### Access Code Editor

1. Go to **WPWay** → **Code Editor**
2. Left panel shows all components
3. Click component to open its code

### Edit Code

**Features:**
- ✅ Full-featured code editor (Ace Editor)
- ✅ Syntax highlighting
- ✅ Line numbers
- ✅ Auto-indentation
- ✅ Code folding

**Supported Languages:**
- PHP (`.php`)
- JavaScript (`.js`)
- CSS (`.css`)

### Save Changes

1. Edit code in editor
2. Click **Save** button (top right)
3. Get confirmation message

---

## ⚙️ Settings

### Access Settings

1. Go to **WPWay** → **Settings**
2. Modify settings as needed

### Available Settings

#### Framework Settings

| Setting | Description | Default |
|---------|-------------|---------|
| **Debug Mode** | Enable verbose logging | Off |
| **Enable Caching** | Cache components | On |
| **Server-Side Rendering** | Render on server | On |
| **Lazy Loading** | Load below fold | On |
| **Cache Duration** | Cache TTL (seconds) | 3600 |
| **Components Directory** | Component folder path | Read-only |

#### Advanced Settings

| Setting | Description | Default |
|---------|-------------|---------|
| **SPA Root Element** | CSS selector for SPA | `#app` |
| **REST API Prefix** | API endpoint prefix | `wpway/v1` |

### Save Settings

1. Modify settings
2. Click **Save Settings** button
3. Settings updated immediately

---

## 📚 Documentation

### Access Documentation

1. Go to **WPWay** → **Documentation**
2. Explore sections:
   - Getting Started
   - Components
   - Pages
   - REST API
   - Hooks & Filters
   - FAQ

### Quick Links

- [Creating Components](../CREATE_COMPONENTS.md)
- [Complete Workflow](../WORKFLOW.md)
- [Getting Started](../GETTING_STARTED.md)
- [Architecture](../ARCHITECTURE.md)

---

## 📊 Dashboard Statistics

### View Quick Stats

On main dashboard, see:
- **Components:** Total created
- **Pages:** Total created
- **Blocks:** Total registered

### Refresh Stats

Click **Refresh Stats** button to update numbers

---

## 🎨 Component Templates

### Available Pre-built Templates

When creating a component, choose from:

#### 1. Hero Template
```php
// Hero section with title, subtitle, image, and CTA button
class Hero extends Component {
    public function render() {
        return '<!-- Hero HTML -->';
    }
}
```

#### 2. Card Template
```php
// Card component with image, title, description, and link
class Card extends Component {
    public function render() {
        return '<!-- Card HTML -->';
    }
}
```

#### 3. Blog List Template
```php
// Display list of blog posts
class BlogList extends Component {
    public function render() {
        return '<!-- Blog list HTML -->';
    }
}
```

#### 4. Newsletter Template
```php
// Email subscription form
class Newsletter extends Component {
    public function render() {
        return '<!-- Newsletter form -->';
    }
}
```

#### 5. Call to Action Template
```php
// CTA button section
class CTA extends Component {
    public function render() {
        return '<!-- CTA section -->';
    }
}
```

---

## 🔌 REST API Endpoints

The dashboard uses these endpoints (all require admin capability):

### Components

**Get all components:**
```
GET /wp-json/wpway/v1/components
```

**Create component:**
```
POST /wp-json/wpway/v1/components
Body: {
    "name": "MyComponent",
    "type": "php",
    "code": "<?php ... ?>"
}
```

**Update component:**
```
POST /wp-json/wpway/v1/components/ComponentName
Body: {
    "code": "<?php ... ?>"
}
```

**Delete component:**
```
DELETE /wp-json/wpway/v1/components/ComponentName
```

### Pages

**Get all pages:**
```
GET /wp-json/wpway/v1/pages
```

---

## 🎯 Common Workflows

### Workflow 1: Create Hero Component

1. **Go to Components**
   - Click WPWay → Components

2. **Click Create New Tab**

3. **Enter Details**
   - Name: "Hero"
   - Type: "PHP Component"
   - Template: "Hero Section"

4. **Customize Code**
   - Edit HTML/PHP in code editor
   - Change styling/layout

5. **Click Create**
   - Component saved and ready to use

### Workflow 2: Create Page with Components

1. **Go to Pages**
   - Click WPWay → Pages

2. **Click Create New Page**

3. **Enter Details**
   - Title: "Home"
   - Slug: "home"

4. **Select Components**
   - Check: Hero
   - Check: BlogList
   - Check: Newsletter

5. **Click Create**
   - Page created with components

### Workflow 3: Edit Component Code

1. **Go to Code Editor**
   - Click WPWay → Code Editor

2. **Select Component**
   - Click component name in sidebar

3. **Edit Code**
   - Modify in editor
   - See syntax highlighting

4. **Save**
   - Click Save button
   - Confirmation message

---

## 🔒 Security & Permissions

### Who Can Access Dashboard?

- Only users with **"manage_options"** capability
- Typically: Administrator role
- Cannot be accessed by editors, authors, etc.

### Security Features

- ✅ WordPress nonce verification
- ✅ Capability checks
- ✅ Sanitized input
- ✅ Escaped output
- ✅ REST API authentication

### Best Practices

1. **Don't share admin access** loosely
2. **Regularly update WordPress**
3. **Use strong passwords**
4. **Enable 2FA if possible**
5. **Monitor component changes** in debug log

---

## 🐛 Troubleshooting

### Dashboard Menu Not Showing

**Problem:** WPWay menu not visible in admin

**Solution:**
1. Ensure plugin is activated
2. Ensure you're logged in as admin
3. Hard refresh page (Ctrl+Shift+R)
4. Check browser console for errors

### Components Not Loading

**Problem:** Components list is empty

**Solution:**
1. Check components actually exist in filesystem
2. Verify file permissions (644 for files, 755 for dirs)
3. Check debug.log for errors
4. Run diagnostic tool

### Code Editor Issues

**Problem:** Code editor not loading

**Solution:**
1. Check Ace Editor library loads (DevTools Network tab)
2. Browser might not support it (try different browser)
3. Clear browser cache
4. Check for JS errors in console

### Page Creation Fails

**Problem:** Can't create pages with components

**Solution:**
1. Verify components exist
2. Check "manage_pages" capability
3. Try creating regular WordPress page first
4. Check error in browser console

---

## 🚀 Advanced Features

### Batch Operations

Coming soon:
- [ ] Bulk delete components
- [ ] Export/import components
- [ ] Duplicate component
- [ ] Component versioning

### Advanced Code Editor

Features available:
- ✅ Line numbers
- ✅ Syntax highlighting
- ✅ Code folding
- ✅ Find/replace (Ctrl+H)
- ✅ Multiple themes available

### Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| Ctrl+S | Save component |
| Ctrl+F | Find in code |
| Ctrl+H | Find & replace |
| Ctrl+Option+F | Format code |

---

## 📈 Statistics & Monitoring

### View Component Statistics

1. Go to WPWay → Dashboard (main)
2. Scroll to "Quick Stats"
3. See count of:
   - Components created
   - Pages created
   - Blocks registered

### Monitor Changes

Check `wp-content/debug.log` for:
```
[WPWay] Component created: Hero
[WPWay] Component deleted: OldComponent
[WPWay] Component updated: BlogList
```

---

## 🎓 Learning Resources

### Inside Dashboard

- **Documentation Tab:** Built-in docs
- **FAQ:** Common questions answered
- **Examples:** See example components

### External Resources

- [CREATE_COMPONENTS.md](../CREATE_COMPONENTS.md) - Component creation
- [WORKFLOW.md](../WORKFLOW.md) - Complete workflow
- [ARCHITECTURE.md](../ARCHITECTURE.md) - Technical details

---

## 💡 Tips & Tricks

### Tip 1: Use Templates
Creating first component? Use built-in templates as starting point!

### Tip 2: Copy & Modify
Copy an existing component, rename, and modify instead of building from scratch.

### Tip 3: Enable Debug Mode
Turn on Debug Mode in settings to get detailed error logs.

### Tip 4: Organize Files
Create subfolders in components directory for organization:
```
components/
├── sections/
│   ├── Hero.php
│   ├── BlogList.php
├── elements/
│   ├── Button.php
│   ├── Card.php
```

### Tip 5: Use Code Comments
Add comments in code to explain functionality:
```php
// Display featured posts
// Parameters: limit, category, show_excerpt
```

---

## 📞 Support

### Getting Help

1. **Dashboard Documentation** - Click "Documentation" tab
2. **Error Messages** - Read console and debug.log
3. **Code Examples** - Check component templates
4. **Community** - Check GitHub issues

### Report Issues

If dashboard has issues:
1. Check browser console for errors
2. Check `wp-content/debug.log`
3. Create GitHub issue with:
   - Error message
   - Browser/WP version
   - Steps to reproduce

---

## ✨ Next Steps

1. ✅ Install WPWay plugin
2. ✅ Access admin dashboard
3. ✅ Create first component
4. ✅ Build page with components
5. ✅ Customize settings
6. ✅ Explore documentation

---

**Happy administrating with WPWay!** 🎉
