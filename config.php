<?php
/**
 * Package Configuration - package.json equivalent for PHP/WordPress
 * Framework version, dependencies, and metadata
 */

return [
    'name' => 'wpway',
    'version' => '1.0.0',
    'description' => 'React-like Frontend Framework for WordPress',
    'type' => 'wordpress-plugin',
    'license' => 'GPL-2.0-or-later',
    'author' => 'WPWay Team',
    'homepage' => 'https://wpway.dev',
    
    'keywords' => [
        'wordpress',
        'framework',
        'react',
        'spa',
        'components',
        'gutenberg',
        'frontend'
    ],
    
    'supports' => [
        'php' => '7.4+',
        'wordpress' => '5.8+'
    ],
    
    'modules' => [
        'core' => [
            'file' => 'includes/core/framework.php',
            'exports' => [
                'Framework',
                'Component',
                'VirtualDOM'
            ]
        ],
        'router' => [
            'file' => 'includes/router/router.php',
            'exports' => ['Router']
        ],
        'state' => [
            'file' => 'includes/state/store.php',
            'exports' => ['Store']
        ],
        'gutenberg' => [
            'file' => 'includes/gutenberg/blocks.php',
            'exports' => ['BlockEngine']
        ],
        'ssr' => [
            'file' => 'includes/ssr/hydration.php',
            'exports' => ['Hydration']
        ],
        'plugins' => [
            'file' => 'includes/plugin-api/plugin-system.php',
            'exports' => ['PluginSystem', 'Plugin']
        ],
        'performance' => [
            'file' => 'includes/performance/optimizer.php',
            'exports' => ['Optimizer']
        ]
    ],
    
    'features' => [
        'spa_navigation' => true,
        'ssr_hydration' => true,
        'gutenberg_reactive_blocks' => true,
        'plugin_ecosystem' => true,
        'performance_optimization' => true,
        'developer_tools' => true,
        'rest_api' => true,
        'state_management' => true,
        'component_system' => true,
        'virtual_dom' => true
    ],
    
    'api_endpoints' => [
        '/wpway/v1/component/:name' => ['GET'],
        '/wpway/v1/components' => ['GET'],
        '/wpway/v1/blocks' => ['GET'],
        '/wpway/v1/block/:name' => ['GET'],
        '/wpway/v1/state' => ['GET', 'POST'],
        '/wpway/v1/page/:id' => ['GET'],
        '/wpway/v1/plugins' => ['GET'],
        '/wpway/v1/metrics' => ['POST'],
        '/wpway/v1/hydration' => ['GET']
    ],
    
    'assets' => [
        'scripts' => [
            'wpway-core.js' => '1.0.0',
            'hydration.js' => '1.0.0',
            'performance.js' => '1.0.0',
            'rest-api.js' => '1.0.0',
            'dev-tools.js' => '1.0.0'
        ],
        'styles' => [
            'wpway.css' => '1.0.0'
        ]
    ],
    
    'hooks' => [
        'filters' => [
            'wpway_component_props',
            'wpway_block_attributes',
            'wpway_state_value',
            'wpway_api_response'
        ],
        'actions' => [
            'wpway_component_before_render',
            'wpway_component_after_render',
            'wpway_block_registered',
            'wpway_state_changed',
            'wpway_plugin_loaded'
        ]
    ],
    
    'options' => [
        'wpway_version' => ['type' => 'string', 'default' => '1.0.0'],
        'wpway_cache_ttl' => ['type' => 'integer', 'default' => 3600],
        'wpway_debug_mode' => ['type' => 'boolean', 'default' => false],
        'wpway_ssr_enabled' => ['type' => 'boolean', 'default' => true],
        'wpway_hydration_enabled' => ['type' => 'boolean', 'default' => true],
        'wpway_plugin_ecosystem_enabled' => ['type' => 'boolean', 'default' => true]
    ],
    
    'database' => [
        'tables' => [
            'wpway_components' => [
                'name' => 'component_name',
                'class' => 'component_class',
                'config' => 'component_config',
                'registered' => 'registered_date'
            ],
            'wpway_blocks' => [
                'name' => 'block_name',
                'title' => 'block_title',
                'component' => 'render_component',
                'attributes' => 'block_attributes',
                'registered' => 'registered_date'
            ],
            'wpway_plugins' => [
                'name' => 'plugin_name',
                'class' => 'plugin_class',
                'version' => 'plugin_version',
                'active' => 'is_active',
                'registered' => 'registered_date'
            ]
        ]
    ],
    
    'scripts' => [
        'build' => 'npm run build',
        'dev' => 'npm run dev',
        'test' => 'npm run test',
        'lint' => 'npm run lint'
    ],
    
    'repository' => [
        'type' => 'git',
        'url' => 'https://github.com/wpway/wpway-framework.git'
    ],
    
    'changelog' => [
        '1.0.0' => [
            'date' => '2026-02-12',
            'changes' => [
                'Initial release',
                'Core component system',
                'Virtual DOM engine',
                'SPA routing',
                'SSR/Hydration',
                'Gutenberg integration',
                'Plugin ecosystem',
                'Performance optimization',
                'Developer tools'
            ]
        ]
    ]
];
