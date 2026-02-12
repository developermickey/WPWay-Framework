# WPWay Framework - Getting Started Guide

## Installation

1. Copy the WPWay folder to your WordPress `wp-content/plugins/` directory
2. Activate the plugin from WordPress admin
3. The framework automatically initializes on plugins load

## Basic Usage

### Creating Your First Component

**PHP Component:**
```php
<?php
namespace MyTheme\Components;
use WPWay\Core\Component;

class Welcome extends Component {
    public function render() {
        $name = $this->props['name'] ?? 'Guest';
        return sprintf(
            '<div class="welcome"><h1>Welcome, %s!</h1></div>',
            esc_html($name)
        );
    }
}
```

**Register Component:**
```php
// In your theme functions.php or plugin
$framework = \WPWay\Core\Framework::getInstance();
$framework->registerComponent('Welcome', 'MyTheme\Components\Welcome');
```

**Use in Template:**
```php
$hydration = \WPWay\SSR\Hydration::getInstance();
echo $hydration->renderServer('Welcome', ['name' => 'World']);
```

### Creating a Gutenberg Block

```php
// Register in your theme or plugin setup
$engine = \WPWay\Gutenberg\BlockEngine::getInstance();
$engine->registerBlock('myblock/hero', [
    'title' => 'My Hero Block',
    'description' => 'A customizable hero section',
    'render_component' => 'Hero',
    'attributes' => [
        'title' => [
            'type' => 'string',
            'default' => 'Welcome to My Site'
        ],
        'subtitle' => [
            'type' => 'string',
            'default' => ''
        ],
        'buttonText' => [
            'type' => 'string',
            'default' => 'Get Started'
        ],
        'buttonUrl' => [
            'type' => 'string',
            'default' => '#'
        ]
    ],
    'supports' => [
        'align' => ['full', 'wide'],
        'color' => ['text', 'background'],
        'spacing' => ['padding', 'margin']
    ],
    'category' => 'common'
]);
```

### JavaScript Components

Create interactive components on the frontend:

```javascript
// In your browser console or in a script tag
class Counter extends WPWay.Component {
    render() {
        const [count, setCount] = WPWay.useState(0);
        
        return WPWay.createElement('div', { class: 'counter' },
            WPWay.createElement('h2', {}, `Count: ${count}`),
            WPWay.createElement('button', 
                { onClick: () => setCount(count + 1) },
                'Increment'
            )
        );
    }
}

WPWay.components.Counter = Counter;
```

### State Management

**Global State:**
```php
// Set state server-side
$framework = \WPWay\Core\Framework::getInstance();
$framework->setState('user_id', 42);
$framework->setState('theme', 'dark');

// Create actions via REST API
// POST /wp-json/wpway/v1/state
// Body: {"logged_in": true, "user_role": "admin"}
```

**Frontend Access:**
```javascript
// Get current state
const state = await WPWayRestAPI.getState();
console.log(state);

// Update state
await WPWayRestAPI.setState({ logged_in: true });
```

### SPA Routing

```javascript
const router = new WPWay.Router();

router.registerRoutes({
    '/blog': 'BlogList',
    '/blog/:id': 'SinglePost',
    '/about': 'About',
    '/contact': 'Contact'
});

// Navigate programmatically
router.navigate('/blog/123', { id: '123' });

// Get current route
const currentRoute = router.getCurrentRoute();
console.log(currentRoute.path);
```

### Building Plugins

Extend WPWay functionality with plugins:

**PHP Plugin:**
```php
<?php
namespace MyPlugins;
use WPWay\PluginAPI\Plugin;

class SEOPlugin extends Plugin {
    protected $name = 'WPWay SEO';
    protected $version = '1.0.0';
    
    public function init() {
        $system = \WPWay\PluginAPI\PluginSystem::getInstance();
        
        // Register hooks
        $system->addPluginHook('page_rendered', [$this, 'addMetaTags']);
        
        // Register filters
        $system->addPluginFilter('component_props', [$this, 'filterProps']);
    }
    
    public function addMetaTags() {
        // Add SEO meta tags
    }
    
    public function filterProps($props) {
        // Filter component props
        return $props;
    }
    
    protected function setupCapabilities() {
        $this->capabilities = ['seo', 'meta-tags', 'structured-data'];
    }
}
```

**Register Plugin:**
```php
$pluginSystem = \WPWay\PluginAPI\PluginSystem::getInstance();
$pluginSystem->registerPlugin('seo', 'MyPlugins\SEOPlugin');
```

### Performance Optimization

**Mark Components for Lazy Loading:**
```php
$optimizer = \WPWay\Performance\Optimizer::getInstance();
$optimizer->markLazy('HeavyComponent', 2000);
$optimizer->addCriticalComponent('Hero');
$optimizer->registerChunk('analytics', ['Analytics', 'Tracking']);
```

**JavaScript:**
```javascript
// Lazy load large components on demand
WPWayPerformance.lazyLoadComponent('Chart', '/chunks/chart.js');

// Prefetch resources
WPWayPerformance.prefetch('/api/data');

// Debounce search input
const searchHandler = WPWayPerformance.debounce(function(query) {
    fetch(`/wp-json/wpway/v1/search?q=${query}`);
}, 300);
```

### REST API Integration

**Fetch Data:**
```javascript
// Get component data
const heroData = await WPWayRestAPI.getComponent('hero');

// Get all plugins
const plugins = await WPWayRestAPI.listPlugins();

// Get post data
const post = await WPWayRestAPI.getPage(123);

// Get block schemas
const blocks = await WPWayRestAPI.listBlocks();
```

### Debugging

**PHP:**
```php
// Log messages
\WPWay\DevTools\Console::log('User logged in', ['id' => 123]);

// Log errors
\WPWay\DevTools\Console::error('Payment failed', ['code' => 'ERROR_001']);

// Get all logs
$logs = \WPWay\DevTools\Console::getLogs();
$errors = \WPWay\DevTools\Console::getErrors();
```

**JavaScript:**
```javascript
// Inspect framework state
WPWayDevTools.inspect();

// Get all registered components
WPWayDevTools.getComponents();

// Test API connection
WPWayDevTools.testAPI();

// Get performance report
WPWayDevTools.getPerformance();

// Export diagnostics for debugging
console.log(WPWayDevTools.exportDiagnostics());
```

### Server-Side Rendering

**Render components server-side:**
```php
$hydration = \WPWay\SSR\Hydration::getInstance();

// Render single component
$html = $hydration->renderServer('Hero', [
    'title' => 'Welcome',
    'subtitle' => 'The best way to build WordPress sites'
]);

// Render batch
$html = $hydration->renderBatch([
    'Hero' => ['title' => 'Welcome'],
    'BlogList' => ['posts' => 5]
]);

// Output hydration script for client
echo $hydration->createHydrationScript();
```

**Client-side hydration:**
```javascript
// Automatically called on page load
WPWayHydration.init();

// Get hydration stats
const stats = WPWayHydration.getStats();
console.log(`${stats.totalHydrated} components hydrated`);
```

## Configuration

Override default config in your theme/plugin:

```php
// Set custom configuration
\WPWay\Config\Configuration::set('ssr_enabled', false);
\WPWay\Config\Configuration::set('cache_ttl', 7200);
\WPWay\Config\Configuration::merge([
    'debug' => false,
    'lazy_loading' => true,
    'spa_root' => '#wpway-app'
]);
```

## API Reference

### Key Classes and Methods

#### Framework
```php
$framework = \WPWay\Core\Framework::getInstance();
$framework->registerComponent($name, $class)
$framework->getComponent($name)
$framework->setState($key, $value)
$framework->getState($key)
$framework->addHook($hook, $callback)
$framework->executeHooks($hook, ...$args)
```

#### Router
```php
$router = \WPWay\Router\Router::getInstance();
$router->registerRoute($path, $component, $options)
$router->navigate($path, $params)
$router->matchRoute($path)
$router->getCurrentRoute()
$router->getHistory()
```

#### Store
```php
$store = \WPWay\State\Store::getInstance();
$store->registerReducer($key, $reducer)
$store->dispatch($action)
$store->subscribe($callback)
$store->getState($key)
$store->getHistory()
```

#### BlockEngine
```php
$engine = \WPWay\Gutenberg\BlockEngine::getInstance();
$engine->registerBlock($name, $config)
$engine->getBlock($name)
$engine->renderBlock($name, $attributes, $content)
$engine->exportBlockSchema($name)
```

#### Optimizer
```php
$optimizer = \WPWay\Performance\Optimizer::getInstance();
$optimizer->registerChunk($name, $components, $dependencies)
$optimizer->markLazy($componentName, $threshold)
$optimizer->addCriticalComponent($name)
$optimizer->recordMetric($name, $value)
```

## Best Practices

1. **Component Organization**: Keep components focused and modular
2. **State Management**: Use the global store for shared state
3. **Performance**: Mark heavy components as lazy
4. **Caching**: Leverage built-in TTL caching
5. **Error Handling**: Use console logging for debugging
6. **Testing**: Use DevTools console for manual testing
7. **SSR**: Always include hydration data for server-rendered components
8. **Plugins**: Build extensible plugins for reusable functionality
9. **REST API**: Use batch requests when fetching multiple endpoints
10. **TypeScript**: Use provided type definitions for better IDE support

## Common Patterns

### Modal Component
```php
class Modal extends Component {
    public function render() {
        $is_open = $this->getState('is_open');
        if (!$is_open) return '';
        
        return sprintf(
            '<div class="wpway-modal-overlay">
                <div class="wpway-modal">
                    <button class="wpway-modal-close">×</button>
                    <div class="wpway-modal-content">%s</div>
                </div>
            </div>',
            $this->props['content'] ?? ''
        );
    }
}
```

### Form Hander
```javascript
class ContactForm extends WPWay.Component {
    render() {
        return WPWay.createElement('form', {
            onSubmit: (e) => this.handleSubmit(e)
        },
            WPWay.createElement('input', { name: 'email', type: 'email' }),
            WPWay.createElement('button', {}, 'Submit')
        );
    }
    
    handleSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        WPWayRestAPI.post('/contact', Object.fromEntries(data));
    }
}
```

## Troubleshooting

**Components not loading?**
- Check if component is registered
- Verify class namespace
- Check browser console for errors

**State not updating?**
- Ensure dispatch is called for store updates
- Check reducer implementation
- Verify listeners are subscribed

**Performance issues?**
- Mark heavy components as lazy
- Use code splitting with chunks
- Monitor metrics with DevTools.getPerformance()

## Support & Resources

- Check ARCHITECTURE.md for detailed technical documentation
- Use WPWayDevTools for debugging
- Check WordPress debug.log for errors
- Visit wpway.dev for more examples and documentation
