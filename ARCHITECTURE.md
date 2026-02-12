# WPWay Framework - Complete Architecture

## Overview

WPWay is a sophisticated, modular React-like framework for WordPress that brings modern frontend development patterns to the WordPress ecosystem. It features Single Page Application (SPA) navigation, Server-Side Rendering (SSR) with hydration, a reactive Gutenberg block engine, comprehensive developer tools, and a plugin ecosystem.

## Core Architecture

### 1. Framework Core (`includes/core/`)

#### `framework.php` - Central Framework Instance
The backbone of WPWay. Provides:
- **Component Registration**: Register and retrieve components
- **Hook System**: Extensible event-driven architecture
- **Global State Management**: Simple Flux-like state store
- **Caching**: Built-in TTL cache for performance

**Usage:**
```php
$framework = \WPWay\Core\Framework::getInstance();
$framework->registerComponent('MyComponent', 'My\Component\Class');
$framework->setState('user_id', 123);
$framework->addHook('state_changed', function($key, $value) {
    // Handle state changes
});
```

#### `component.php` - Base Component Class
All WPWay components extend this base class. Features:
- Lifecycle methods: `componentDidMount`, `componentDidUpdate`, `componentWillUnmount`
- State management with `setState()` / `getState()`
- Props handling
- Hooks: memo, effects
- Virtual component rendering

**Creating a Component:**
```php
class MyComponent extends \WPWay\Core\Component {
    public function render() {
        return [
            'tag' => 'div',
            'children' => ['Hello World']
        ];
    }
}
```

#### `virtual-dom.php` - Virtual DOM Engine
Efficient DOM manipulation:
- Creates virtual elements/components
- Renders VNodes to HTML
- Implements diff algorithm for updates
- Reconciliation system for applying patches

**Virtual Element Creation:**
```php
$vnode = \WPWay\Core\VirtualDOM::createElement('div', ['class' => 'container'],
    \WPWay\Core\VirtualDOM::createElement('h1', [], 'Title'),
    \WPWay\Core\VirtualDOM::createComponent('MyComponent', ['prop' => 'value'])
);
```

### 2. Routing System (`includes/router/`)

#### `router.php` - SPA Router
Client-side routing without page reloads:
- Route registration and pattern matching
- Parameter extraction from URLs
- Navigation hooks (before/after)
- History tracking
- Route generation

**Using the Router:**
```php
$router = \WPWay\Router\Router::getInstance();
$router->registerRoute('/blog/:id', 'SinglePost');
$router->beforeNavigate(function($path, $params) {
    // Validate navigation
    return true;
});
$router->navigate('/blog/123', ['id' => '123']);
```

### 3. State Management (`includes/state/`)

#### `store.php` - Global State Store
Flux-like state management:
- Reducers: Pure functions for state transformation
- Middlewares: Intercept actions
- Subscribers: Listen to state changes
- History tracking: Time-travel debugging
- Snapshots: Export/import state

**Setup Store:**
```php
$store = \WPWay\State\Store::getInstance();
$store->registerReducer('user', function($state, $action) {
    // Handle user actions
    return $state;
});
$store->use(function($action) {
    // Middleware
    return $action;
});
$store->subscribe(function($action, $state) {
    // Listen to changes
});
$store->dispatch(['type' => 'SET_USER', 'path' => 'user', 'id' => 123]);
```

### 4. Gutenberg Integration (`includes/gutenberg/`)

#### `blocks.php` - Reactive Block Engine
Transform Gutenberg blocks into reactive components:
- Block registration with schema
- Attribute validation and parsing
- Component-based rendering
- Block schema export for JavaScript
- Reactive block updates

**Register Block:**
```php
$engine = \WPWay\Gutenberg\BlockEngine::getInstance();
$engine->registerBlock('wpway/hero', [
    'title' => 'Hero Section',
    'render_component' => 'Hero',
    'attributes' => [
        'title' => ['type' => 'string', 'default' => 'Welcome'],
        'background' => ['type' => 'string']
    ]
]);
```

### 5. SSR & Hydration (`includes/ssr/`)

#### `hydration.php` - Server-Side Rendering
Efficient server-side rendering and client hydration:
- Server-side component rendering
- Hydration data generation
- Hydration markers in HTML
- Hydration script injection
- Batch rendering support

**Render Server-Side:**
```php
$hydration = \WPWay\SSR\Hydration::getInstance();
$html = $hydration->renderServer('Hero', ['title' => 'Welcome']);
// Output hydration data script
echo $hydration->createHydrationScript();
```

### 6. Plugin Ecosystem (`includes/plugin-api/`)

#### `plugin-system.php` - Plugin Management
Third-party plugin integration:
- Plugin registration and lifecycle
- Plugin hooks and filters
- Plugin capabilities/manifests
- Active plugin checking
- Plugin manifest export

**Create Plugin:**
```php
class MyPlugin extends \WPWay\PluginAPI\Plugin {
    protected $name = 'My Plugin';
    protected $version = '1.0.0';
    
    public function init() {
        // Initialize plugin
    }
    
    protected function setupCapabilities() {
        $this->capabilities = ['feature1', 'feature2'];
    }
}

$system = \WPWay\PluginAPI\PluginSystem::getInstance();
$system->registerPlugin('my-plugin', MyPlugin::class);
```

### 7. Performance Optimization (`includes/performance/`)

#### `optimizer.php` - Performance System
Code splitting, lazy loading, and optimization:
- Chunk registration for code splitting
- Lazy loading markers for components
- Critical rendering path tracking
- Resource prefetching/preloading
- Performance metrics recording
- Bundle analysis tools

**Optimize Components:**
```php
$optimizer = \WPWay\Performance\Optimizer::getInstance();
$optimizer->registerChunk('chunk1', ['Component1', 'Component2']);
$optimizer->markLazy('Component3', 1000);
$optimizer->addCriticalComponent('Hero');
$optimizer->recordMetric('page_load', 1234);
```

### 8. Configuration (`includes/config.php`)

Centralized framework configuration:
- Feature flags
- Asset URLs
- Cache settings
- SSR/Hydration options
- Performance thresholds

```php
\WPWay\Config\Configuration::set('ssr_enabled', true);
\WPWay\Config\Configuration::set('cache_ttl', 3600);
```

### 9. Developer Tools (`includes/dev-tools.php`)

Debugging and development utilities:
- Console logging system
- Debug bar for WordPress footer
- Component inspection tools
- Error tracking
- Performance tracing

### 10. REST API (`includes/rest-api-enhanced.php`)

Comprehensive REST endpoints:
- `/wpway/v1/component/:name` - Get component data
- `/wpway/v1/components` - List all components
- `/wpway/v1/blocks` - List Gutenberg blocks
- `/wpway/v1/state` - Get/set global state
- `/wpway/v1/page/:id` - Render post/page
- `/wpway/v1/plugins` - List active plugins
- `/wpway/v1/metrics` - Record performance metrics
- `/wpway/v1/hydration` - Get hydration data

## JavaScript Layer

### Frontend Core (`assets/wpway-core.js`)

Main framework runtime:
- **Component System**: Base component class with lifecycle
- **Virtual DOM Rendering**: Efficient DOM updates
- **Router**: Client-side SPA routing
- **Hooks**: useState, useEffect, useMemo, useCallback, useContext
- **Store**: Global state management
- **JSX Helper**: `createElement()` for component definitions

**Creating Components:**
```javascript
class Hero extends WPWay.Component {
    render() {
        return WPWay.createElement('section', { class: 'hero' },
            WPWay.createElement('h1', {}, this.props.title)
        );
    }
}

WPWay.components.Hero = Hero;
```

**Using Hooks:**
```javascript
const MyComponent = class extends WPWay.Component {
    render() {
        const [count, setCount] = WPWay.useState(0);
        
        WPWay.useEffect(() => {
            console.log('Component mounted');
        }, []);
        
        return WPWay.createElement('button', 
            { onClick: () => setCount(count + 1) },
            `Count: ${count}`
        );
    }
};
```

### Hydration (`assets/hydration.js`)

Client-side hydration:
- Detects server-rendered components
- Reattaches JavaScript
- Event listener binding
- Hydration statistics
- Completion events

### Performance (`assets/performance.js`)

Performance utilities:
- Lazy component loading
- Intersection observer for lazy-loading
- Resource prefetch/preload
- Performance metrics collection
- Navigation timing analysis
- Resource timing breakdown

### REST API Client (`assets/rest-api.js`)

WordPress REST API integration:
- Automatic caching with TTL
- Batch requests
- CORS handling
- Nonce support
- Error handling

**Usage:**
```javascript
const components = await WPWayRestAPI.listComponents();
const state = await WPWayRestAPI.getState();
await WPWayRestAPI.setState({ user_id: 123 });
```

### Developer Tools (`assets/dev-tools.js`)

Browser console utilities:
```javascript
WPWayDevTools.inspect()              // Show framework state
WPWayDevTools.getComponents()        // List components
WPWayDevTools.getPerformance()       // Get metrics
WPWayDevTools.exportDiagnostics()    // Export debug info
```

## Integration Points

### WordPress Hooks

WPWay integrates seamlessly with WordPress:
- Registers REST API endpoints on `rest_api_init`
- Enqueues scripts on `wp_enqueue_scripts`
- Registers components on `init`
- Renders debug bar on `wp_footer` (in debug mode)

### Gutenberg Integration

- Blocks render as WPWay components
- Reactive block updates
- Block schema export for JavaScript editor
- Attribute validation

### Headless WordPress Compatibility

Perfect for headless WordPress setups:
- REST API-first architecture
- SSR support for any Node.js server
- Component-based rendering
- State management ready for API-driven UIs

## Plugin Ecosystem

Extend WPWay with plugins:

```php
class AnalyticsPlugin extends \WPWay\PluginAPI\Plugin {
    protected $name = 'WPWay Analytics';
    
    public function init() {
        $system = \WPWay\PluginAPI\PluginSystem::getInstance();
        $system->addPluginHook('page_view', [$this, 'trackPageView']);
    }
    
    public function trackPageView($route) {
        // Track page views
    }
}
```

## Performance Features

- **Code Splitting**: Divide code into loadable chunks
- **Lazy Loading**: Load components on demand with Intersection Observer
- **Critical Path**: Mark essential components for preload
- **Caching**: Built-in TTL cache for components and state
- **Resource Hints**: Prefetch/preload resources
- **Hydration**: Efficient SSR hydration

## Developer Workflow

### Development

Enable debugging:
```php
define('WP_DEBUG', true);
// Debug bar appears in footer
// Console logging available
```

### Console Access

In browser devtools:
```javascript
// Inspect framework
WPWayDevTools.inspect()

// Get all components
Object.keys(WPWay.components)

// Manually dispatch actions
window.store.dispatch({ type: 'SET_USER', id: 123 })

// Check performance
WPWayDevTools.getPerformance()
```

### TypeScript Support

Full TypeScript definitions in `wpway.d.ts`:
```typescript
const store: WPWay.Store;
const router: WPWay.Router;
const component: WPWay.Component;
```

## File Structure

```
wpway/
├── wpway.php                    # Main plugin file
├── wpway.d.ts                   # TypeScript definitions
├── assets/
│   ├── wpway-core.js           # Core framework
│   ├── hydration.js            # SSR hydration
│   ├── performance.js          # Performance utilities
│   ├── rest-api.js             # REST API client
│   ├── dev-tools.js            # Developer tools
│   ├── wpway.css               # Styles
│   └── admin.js                # Admin panel
├── includes/
│   ├── bootstrap.php           # Framework initialization
│   ├── config.php              # Configuration
│   ├── dev-tools.php           # Dev tools (PHP)
│   ├── example-components.php  # Example components
│   ├── rest-api-enhanced.php   # REST API endpoints
│   ├── core/
│   │   ├── framework.php       # Core framework
│   │   ├── component.php       # Base component
│   │   └── virtual-dom.php     # Virtual DOM
│   ├── router/
│   │   └── router.php          # SPA router
│   ├── state/
│   │   └── store.php           # State store
│   ├── gutenberg/
│   │   └── blocks.php          # Block engine
│   ├── ssr/
│   │   └── hydration.php       # SSR/Hydration
│   ├── plugin-api/
│   │   └── plugin-system.php   # Plugin system
│   └── performance/
│       └── optimizer.php       # Performance tools
└── wpway-components/           # Example components
```

## Key Features Summary

✅ **React-like Components**: Familiar component API with lifecycle
✅ **Virtual DOM**: Efficient rendering and diffing
✅ **SPA Navigation**: Client-side routing
✅ **SSR/Hydration**: Server-side rendering support
✅ **Gutenberg Integration**: Reactive block engine
✅ **State Management**: Flux-like global store
✅ **Plugin Ecosystem**: Extensible via plugins
✅ **Performance**: Code splitting, lazy loading
✅ **Developer Tools**: Console utilities and debug bar
✅ **REST API**: Complete WordPress integration
✅ **TypeScript**: Full type definitions
✅ **Caching**: Built-in TTL cache
✅ **Headless Ready**: Perfect for decoupled WordPress

## Next Steps

1. Create custom components by extending `\WPWay\Core\Component`
2. Register components with the framework
3. Create Gutenberg blocks using the block engine
4. Develop plugins using the plugin ecosystem
5. Use REST API endpoints for frontend communication
6. Monitor performance with built-in tools
