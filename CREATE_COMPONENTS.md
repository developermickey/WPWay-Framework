# Creating Custom Components in WPWay

**Complete guide on where and how to create your own components**

---

## 📁 Where to Create Components

### Option 1: In Your Theme (Recommended for Most Users)

**Location:** `wp-content/themes/your-theme/`

```
wp-content/themes/your-theme/
├── components/              ← Create this folder
│   ├── php/                 ← PHP components
│   │   ├── Hero.php
│   │   ├── BlogList.php
│   │   ├── ProductCard.php
│   │   └── Newsletter.php
│   └── js/                  ← JavaScript components
│       ├── Counter.js
│       ├── Modal.js
│       └── Slider.js
├── functions.php            ← Register components here
├── index.php
└── style.css
```

### Option 2: In a Custom Plugin (For Reusable Components)

**Location:** `wp-content/plugins/my-components/`

```
wp-content/plugins/my-components/
├── my-components.php        ← Main plugin file
├── includes/
│   ├── components.php       ← Register components
│   └── features/
│       ├── Hero/
│       ├── BlogList/
│       └── Newsletter/
├── assets/
│   ├── css/
│   └── js/
└── readme.txt
```

### Option 3: In WPWay Components Folder (Extend Framework)

**Location:** `wp-content/plugins/wpway/wpway-components/`

```
wp-content/plugins/wpway/wpway-components/
├── my-custom-component.js
└── my-custom-component.php
```

---

## 🎯 Creating Your First Component

### Step 1: Choose Component Type

#### Type 1: PHP Component (Server-side)
Best for:
- Fetching database data
- Complex business logic
- Server-side rendering
- WordPress integration

#### Type 2: JavaScript Component (Client-side)
Best for:
- Interactive UI
- Real-time updates
- Client-side logic
- Animations

#### Type 3: Hybrid Component (PHP + JavaScript)
Best for:
- Initial server render
- Client hydration
- Progressive enhancement

---

## 📝 Example 1: Simple PHP Component

### Create the file:
`wp-content/themes/your-theme/components/php/Hero.php`

```php
<?php
/**
 * Hero Component
 * Displays a hero section banner
 */

namespace MyTheme\Components;
use WPWay\Core\Component;

class Hero extends Component {
    
    /**
     * Render the component
     */
    public function render() {
        // Get props (passed from parent)
        $title = $this->props['title'] ?? 'Welcome to Our Site';
        $subtitle = $this->props['subtitle'] ?? '';
        $background_image = $this->props['background_image'] ?? '';
        $button_text = $this->props['button_text'] ?? 'Get Started';
        $button_url = $this->props['button_url'] ?? '#';
        
        // Build HTML
        $background = $background_image ? sprintf(
            'style="background-image: url(%s)"',
            esc_url($background_image)
        ) : '';
        
        return sprintf(
            '<section class="hero" %s>
                <div class="hero-content">
                    <h1 class="hero-title">%s</h1>
                    %s
                    <a href="%s" class="hero-button">%s</a>
                </div>
            </section>',
            $background,
            esc_html($title),
            $subtitle ? '<p class="hero-subtitle">' . esc_html($subtitle) . '</p>' : '',
            esc_url($button_url),
            esc_html($button_text)
        );
    }
}
```

### Register the component:
Add to `wp-content/themes/your-theme/functions.php`

```php
<?php
/**
 * Register WPWay Components
 */

// Load component
require_once get_template_directory() . '/components/php/Hero.php';

// Register with WPWay
add_action('init', function() {
    $framework = \WPWay\Core\Framework::getInstance();
    $framework->registerComponent('Hero', 'MyTheme\Components\Hero');
});
```

### Use the component in template:
Edit `wp-content/themes/your-theme/front-page.php`

```php
<?php
get_header();

// Render component
$hydration = \WPWay\SSR\Hydration::getInstance();
echo $hydration->renderServer('Hero', [
    'title' => 'Build Amazing Sites',
    'subtitle' => 'With WPWay Framework',
    'background_image' => get_template_directory_uri() . '/assets/hero-bg.jpg',
    'button_text' => 'Learn More',
    'button_url' => '/about'
]);

get_footer();
?>
```

---

## 🎨 Example 2: JavaScript Component

### Create the file:
`wp-content/themes/your-theme/components/js/Counter.js`

```javascript
/**
 * Counter Component
 * Interactive counter with increment/decrement buttons
 */

class Counter extends WPWay.Component {
    /**
     * Component render method
     */
    render() {
        // Use state hook
        const [count, setCount] = WPWay.useState(0);
        const initialValue = this.props.initialValue || 0;
        
        // Use effect hook
        WPWay.useEffect(() => {
            // Initialize count
            if (count === 0 && initialValue > 0) {
                setCount(initialValue);
            }
            console.log('Counter mounted or updated:', count);
        }, [count]);
        
        // Return JSX-like structure
        return WPWay.createElement('div', { 
            class: 'counter',
            id: 'counter-' + this.props.id 
        },
            WPWay.createElement('div', { class: 'counter-display' },
                WPWay.createElement('h2', {}, `Count: ${count}`)
            ),
            WPWay.createElement('div', { class: 'counter-buttons' },
                WPWay.createElement('button', {
                    class: 'btn btn-minus',
                    onClick: () => setCount(count - 1)
                }, '−'),
                WPWay.createElement('button', {
                    class: 'btn btn-reset',
                    onClick: () => setCount(0)
                }, 'Reset'),
                WPWay.createElement('button', {
                    class: 'btn btn-plus',
                    onClick: () => setCount(count + 1)
                }, '+')
            )
        );
    }
}

// Register component
WPWay.registerComponent('Counter', Counter);
```

### Register and enqueue:
Add to `wp-content/themes/your-theme/functions.php`

```php
<?php
add_action('wp_enqueue_scripts', function() {
    // Enqueue component file
    wp_enqueue_script(
        'counter-component',
        get_template_directory_uri() . '/components/js/Counter.js',
        ['wpway-core'],  // Dependency on WPWay core
        '1.0.0',
        true
    );
});
```

### Use in template:
`wp-content/themes/your-theme/page-counter.php`

```php
<?php
get_header();
?>

<div class="container">
    <h1>Counter Demo</h1>
    
    <!-- Component will be mounted here -->
    <div id="app" data-component="Counter" data-props='{"id": "main", "initialValue": 5}'></div>
</div>

<?php get_footer(); ?>
```

---

## 🔗 Example 3: Hybrid Component (PHP + JavaScript)

### PHP Component:
`wp-content/themes/your-theme/components/php/BlogList.php`

```php
<?php
namespace MyTheme\Components;
use WPWay\Core\Component;

class BlogList extends Component {
    public function render() {
        $per_page = $this->props['per_page'] ?? 6;
        $category = $this->props['category'] ?? '';
        
        // Query posts
        $args = [
            'posts_per_page' => $per_page,
            'post_type' => 'post',
            'post_status' => 'publish'
        ];
        
        if ($category) {
            $args['category_name'] = $category;
        }
        
        $posts = get_posts($args);
        
        // Render server-side
        $html = '<div class="blog-list">';
        foreach ($posts as $post) {
            $html .= sprintf(
                '<article class="blog-item" data-post-id="%d">
                    <h3>%s</h3>
                    <p>%s</p>
                </article>',
                $post->ID,
                esc_html($post->post_title),
                wp_trim_words($post->post_excerpt, 20)
            );
        }
        $html .= '</div>';
        
        return $html;
    }
}
```

### JavaScript Enhancement:
`wp-content/themes/your-theme/components/js/BlogList.js`

```javascript
/**
 * BlogList Component - Client-side interactivity
 */

class BlogList extends WPWay.Component {
    constructor(props) {
        super(props);
        this.addEventListeners();
    }
    
    addEventListeners() {
        const items = document.querySelectorAll('.blog-item');
        
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                const postId = item.dataset.postId;
                console.log('Clicked post:', postId);
                
                // Navigate or open modal
                window.location.href = `/blog/${postId}`;
            });
        });
    }
}

// Auto-initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (window.WPWayHydration) {
        window.WPWayHydration.hydrateComponents();
    }
});
```

---

## 📋 Component Folder Structure (Complete Example)

```
wp-content/themes/my-theme/
├── components/
│   ├── php/
│   │   ├── Hero.php
│   │   ├── BlogList.php
│   │   ├── ProductCard.php
│   │   ├── Newsletter.php
│   │   ├── Testimonials.php
│   │   └── CTA.php
│   ├── js/
│   │   ├── Counter.js
│   │   ├── Modal.js
│   │   ├── Tabs.js
│   │   ├── Slider.js
│   │   └── Accordion.js
│   └── css/
│       ├── hero.css
│       ├── blog-list.css
│       ├── counter.css
│       └── modal.css
├── functions.php
├── index.php
└── style.css
```

---

## ⚙️ Registering Components

### Method 1: In Theme Functions.php

```php
<?php
/**
 * Register all theme components
 */

// Load PHP component classes
require_once get_template_directory() . '/components/php/Hero.php';
require_once get_template_directory() . '/components/php/BlogList.php';

add_action('init', function() {
    $framework = \WPWay\Core\Framework::getInstance();
    
    // Register PHP components
    $framework->registerComponent('Hero', 'MyTheme\Components\Hero');
    $framework->registerComponent('BlogList', 'MyTheme\Components\BlogList');
    
    // Register Gutenberg blocks
    $engine = \WPWay\Gutenberg\BlockEngine::getInstance();
    $engine->registerBlock('mytheme/hero', [
        'title' => 'Theme Hero',
        'render_component' => 'Hero',
        'attributes' => [
            'title' => ['type' => 'string', 'default' => 'Welcome'],
            'subtitle' => ['type' => 'string', 'default' => '']
        ]
    ]);
});

// Enqueue JavaScript components
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_script(
        'theme-components',
        get_template_directory_uri() . '/components/js/all-components.js',
        ['wpway-core'],
        '1.0.0',
        true
    );
});
```

### Method 2: In a Separate Config File

Create `wp-content/themes/my-theme/config/components.php`:

```php
<?php
/**
 * Component Configuration
 */

return [
    'php_components' => [
        'Hero' => 'MyTheme\Components\Hero',
        'BlogList' => 'MyTheme\Components\BlogList',
        'ProductCard' => 'MyTheme\Components\ProductCard',
    ],
    
    'blocks' => [
        'mytheme/hero' => [
            'title' => 'Theme Hero',
            'render_component' => 'Hero',
        ],
        'mytheme/blog' => [
            'title' => 'Blog List',
            'render_component' => 'BlogList',
        ]
    ],
    
    'js_components' => [
        'counter' => '/components/js/Counter.js',
        'modal' => '/components/js/Modal.js',
    ]
];
```

Then load in `functions.php`:

```php
<?php
$config = require get_template_directory() . '/config/components.php';

add_action('init', function() use ($config) {
    $framework = \WPWay\Core\Framework::getInstance();
    
    foreach ($config['php_components'] as $name => $class) {
        $framework->registerComponent($name, $class);
    }
});
```

---

## 🎓 Using Components in Templates

### Method 1: Direct PHP Rendering

```php
<?php
get_header();

$hydration = \WPWay\SSR\Hydration::getInstance();

// Render Hero component
echo $hydration->renderServer('Hero', [
    'title' => get_option('blog_name'),
    'subtitle' => get_option('blog_description')
]);

// Render Blog List
echo $hydration->renderServer('BlogList', [
    'per_page' => 6,
    'category' => 'news'
]);

// Include hydration script for JavaScript components
echo $hydration->createHydrationScript();

get_footer();
?>
```

### Method 2: Using Gutenberg Blocks

In WordPress block editor:
1. Click **+** to add block
2. Search "WPWay Hero" (or your component)
3. Configure block properties
4. Publish

### Method 3: Using Shortcodes

```php
<?php
/**
 * Register component as shortcode
 */

add_shortcode('wpway_hero', function($atts) {
    $hydration = \WPWay\SSR\Hydration::getInstance();
    
    return $hydration->renderServer('Hero', [
        'title' => $atts['title'] ?? 'Welcome',
        'subtitle' => $atts['subtitle'] ?? ''
    ]);
});
```

Use in post:
```
[wpway_hero title="My Hero Title" subtitle="My subtitle"]
```

---

## 🜔 Component Lifecycle

### PHP Component Lifecycle

```php
<?php
class MyComponent extends Component {
    
    // Called when component is created
    public function __construct($props = []) {
        parent::__construct($props);
        // Initialize component
    }
    
    // Called before render
    public function componentWillMount() {
        // Fetch data, setup state
    }
    
    // Main render method
    public function render() {
        // Return HTML
    }
    
    // Called after render
    public function componentDidMount() {
        // Post-processing
    }
}
```

### JavaScript Component Lifecycle

```javascript
class MyComponent extends WPWay.Component {
    render() {
        // Initialize hooks
        const [state, setState] = WPWay.useState(null);
        
        WPWay.useEffect(() => {
            // Called on mount and on state change
            console.log('Component mounted or state changed');
            
            return () => {
                // Cleanup function
                console.log('Component will unmount');
            };
        }, [state]);  // Dependencies
        
        // Return component
        return WPWay.createElement('div', {}, 'Content');
    }
}
```

---

## 💾 File Structure Best Practices

### Organized Theme Structure

```
wp-content/themes/my-theme/
├── assets/
│   ├── css/
│   │   ├── style.css
│   │   ├── components.css
│   │   └── admin.css
│   ├── js/
│   │   ├── main.js
│   │   └── theme.js
│   └── images/
├── components/
│   ├── php/
│   ├── js/
│   └── css/
├── config/
│   └── components.php
├── includes/
│   ├── helpers.php
│   ├── hooks.php
│   └── customizer.php
├── template-parts/
│   ├── header/
│   ├── footer/
│   └── content/
├── templates/
│   ├── front-page.php
│   ├── single.php
│   ├── archive.php
│   └── 404.php
├── functions.php
├── style.css
└── readme.txt
```

---

## ✅ Step-by-Step: Create Your First Component

### Step 1: Create Folder
```bash
mkdir -p wp-content/themes/your-theme/components/php
```

### Step 2: Create Component File
`wp-content/themes/your-theme/components/php/Welcome.php`

```php
<?php
namespace MyTheme\Components;
use WPWay\Core\Component;

class Welcome extends Component {
    public function render() {
        return '<div class="welcome"><h1>' . 
               esc_html($this->props['message'] ?? 'Welcome') . 
               '</h1></div>';
    }
}
```

### Step 3: Register Component
In `wp-content/themes/your-theme/functions.php`:

```php
<?php
require_once get_template_directory() . '/components/php/Welcome.php';

add_action('init', function() {
    \WPWay\Core\Framework::getInstance()
        ->registerComponent('Welcome', 'MyTheme\Components\Welcome');
});
```

### Step 4: Use Component
In `wp-content/themes/your-theme/front-page.php`:

```php
<?php
get_header();

$hydration = \WPWay\SSR\Hydration::getInstance();
echo $hydration->renderServer('Welcome', [
    'message' => 'Hello, ' . get_bloginfo('name')
]);

get_footer();
?>
```

### Step 5: View Result
Go to your website's homepage - you should see your component!

---

## 🎯 Common Component Patterns

### Pattern 1: Card Component

```php
<?php
class Card extends Component {
    public function render() {
        return sprintf(
            '<div class="card">
                <img src="%s" alt="">
                <h3>%s</h3>
                <p>%s</p>
                <a href="%s" class="btn">%s</a>
            </div>',
            esc_url($this->props['image']),
            esc_html($this->props['title']),
            esc_html($this->props['description']),
            esc_url($this->props['link']),
            esc_html($this->props['link_text'] ?? 'Read More')
        );
    }
}
```

### Pattern 2: Loop Component

```php
<?php
class ItemList extends Component {
    public function render() {
        $items = $this->props['items'] ?? [];
        $html = '<ul class="item-list">';
        
        foreach ($items as $item) {
            $html .= sprintf(
                '<li>%s</li>',
                esc_html($item['title'])
            );
        }
        
        $html .= '</ul>';
        return $html;
    }
}
```

### Pattern 3: Conditional Component

```php
<?php
class ConditionalCard extends Component {
    public function render() {
        if (!$this->props['show']) {
            return '';
        }
        
        return '<div class="card">' . 
               esc_html($this->props['content']) . 
               '</div>';
    }
}
```

---

## 🚀 Next Steps

1. ✅ Choose a location (theme or plugin)
2. ✅ Create your component file
3. ✅ Write component code
4. ✅ Register component
5. ✅ Use in template
6. ✅ Test and iterate

---

## 📚 Additional Resources

- [WORKFLOW.md](../WORKFLOW.md) - Complete workflow guide
- [GETTING_STARTED.md](../GETTING_STARTED.md) - Getting started tutorial
- [ARCHITECTURE.md](../ARCHITECTURE.md) - Technical architecture

---

## 💬 Questions?

Check these files:
- `includes/example-components.php` - Working examples
- [TROUBLESHOOTING.md](../TROUBLESHOOTING.md) - Common issues
- [WORKFLOW.md](../WORKFLOW.md) - Complete guide

---

**Happy component building!** 🎉
