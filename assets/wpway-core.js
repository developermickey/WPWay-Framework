/**
 * WPWay - React-like Frontend Framework for WordPress
 * Core runtime and Virtual DOM engine
 */

(function(window) {
    'use strict';

    // Main WPWay namespace
    const WPWay = {
        components: {},
        hooks: {},
        state: {},
        router: {},
        version: '1.0.0'
    };

    /**
     * Virtual DOM element creation
     */
    WPWay.createElement = function(tag, props, ...children) {
        return {
            type: 'element',
            tag: tag,
            props: props || {},
            children: children.flat().filter(child => child !== null && child !== false && child !== undefined)
        };
    };

    /**
     * Virtual component creation
     */
    WPWay.createComponent = function(componentName, props, ...children) {
        return {
            type: 'component',
            name: componentName,
            props: props || {},
            children: children.flat().filter(child => child !== null && child !== false && child !== undefined)
        };
    };

    /**
     * Fragment
     */
    WPWay.Fragment = function(props) {
        return {
            type: 'fragment',
            children: props.children || []
        };
    };

    /**
     * JSX-like syntax helper
     */
    window.h = WPWay.createElement;

    /**
     * Component class base
     */
    class Component {
        constructor(props) {
            this.props = props || {};
            this.state = {};
            this.hooks = [];
            this.mounted = false;
            this.updateScheduled = false;
        }

        setState(key, value) {
            const oldState = { ...this.state };
            this.state[key] = value;

            if (this.mounted) {
                this.scheduleUpdate();
                this.componentDidUpdate(oldState, this.state);
            }
        }

        getState(key) {
            if (key === undefined) return this.state;
            return this.state[key];
        }

        setProps(props) {
            const oldProps = this.props;
            this.props = { ...this.props, ...props };
            if (this.mounted) {
                this.componentDidUpdate(oldProps, this.props);
            }
        }

        scheduleUpdate() {
            if (!this.updateScheduled) {
                this.updateScheduled = true;
                requestAnimationFrame(() => {
                    this.update();
                    this.updateScheduled = false;
                });
            }
        }

        componentDidMount() {}
        componentDidUpdate(prev, next) {}
        componentWillUnmount() {}
        render() { return null; }

        update() {
            // Override in subclass
        }
    }

    /**
     * Virtual DOM Renderer
     */
    class VirtualDOMRenderer {
        constructor() {
            this.vdom = null;
            this.domRegistry = new WeakMap();
        }

        render(vnode, container) {
            const domNode = this.renderNode(vnode);
            if (container) {
                container.innerHTML = '';
                container.appendChild(domNode);
            }
            return domNode;
        }

        renderNode(vnode) {
            // Text node
            if (typeof vnode === 'string' || typeof vnode === 'number') {
                return document.createTextNode(String(vnode));
            }

            if (!vnode) {
                return document.createTextNode('');
            }

            // Fragment
            if (vnode.type === 'fragment') {
                const fragment = document.createDocumentFragment();
                vnode.children.forEach(child => {
                    fragment.appendChild(this.renderNode(child));
                });
                return fragment;
            }

            // Element
            if (vnode.type === 'element') {
                const el = document.createElement(vnode.tag);
                this.setAttributes(el, vnode.props);
                vnode.children.forEach(child => {
                    el.appendChild(this.renderNode(child));
                });
                return el;
            }

            // Component
            if (vnode.type === 'component') {
                const Component = WPWay.components[vnode.name];
                if (Component) {
                    const instance = new Component(vnode.props);
                    instance.mounted = true;
                    instance.componentDidMount();
                    const rendered = instance.render();
                    return this.renderNode(rendered);
                }
            }

            return document.createTextNode('');
        }

        setAttributes(el, props) {
            for (const [key, value] of Object.entries(props)) {
                if (key === 'children' || key === 'key') continue;

                if (key.startsWith('on')) {
                    const eventName = key.slice(2).toLowerCase();
                    el.addEventListener(eventName, value);
                } else if (typeof value === 'boolean') {
                    if (value) el.setAttribute(key, key);
                    else el.removeAttribute(key);
                } else if (value !== null && value !== undefined) {
                    el.setAttribute(key, String(value));
                }
            }
        }

        diff(oldVnode, newVnode) {
            // Simple diff algorithm
            if (oldVnode?.tag !== newVnode?.tag || oldVnode?.name !== newVnode?.name) {
                return { type: 'replace', node: newVnode };
            }

            const changes = [];
            
            // Check props
            if (JSON.stringify(oldVnode?.props) !== JSON.stringify(newVnode?.props)) {
                changes.push({ type: 'updateProps', props: newVnode.props });
            }

            // Check children
            if (oldVnode?.children.length !== newVnode?.children.length) {
                changes.push({ type: 'updateChildren', children: newVnode.children });
            }

            return changes;
        }
    }

    /**
     * Router
     */
    class Router {
        constructor() {
            this.routes = new Map();
            this.beforeHooks = [];
            this.afterHooks = [];
            this.currentRoute = null;
            this.history = [];
        }

        registerRoute(path, component, options = {}) {
            this.routes.set(path, { component, options });
        }

        registerRoutes(routes) {
            Object.entries(routes).forEach(([path, component]) => {
                this.registerRoute(path, component);
            });
        }

        beforeNavigate(callback) {
            this.beforeHooks.push(callback);
        }

        afterNavigate(callback) {
            this.afterHooks.push(callback);
        }

        async navigate(path, params = {}) {
            // Execute before hooks
            for (const hook of this.beforeHooks) {
                if (await hook(path, params) === false) return false;
            }

            this.currentRoute = { path, params, timestamp: Date.now() };
            this.history.push(this.currentRoute);

            // Execute after hooks
            for (const hook of this.afterHooks) {
                await hook(path, params);
            }

            return true;
        }

        matchRoute(path) {
            for (const [pattern, route] of this.routes) {
                const regex = new RegExp('^' + pattern.replace(/:[^\s/]+/g, '[^/]+') + '$');
                if (regex.test(path)) {
                    const params = this.extractParams(pattern, path);
                    return { ...route, params };
                }
            }
            return null;
        }

        extractParams(pattern, path) {
            const params = {};
            const paramNames = (pattern.match(/:[^\s/]+/g) || []).map(p => p.slice(1));
            const pathParts = path.split('/');
            const patternParts = pattern.split('/');

            paramNames.forEach((name, i) => {
                params[name] = pathParts[i];
            });

            return params;
        }

        getCurrentRoute() {
            return this.currentRoute;
        }

        getHistory() {
            return this.history;
        }
    }

    /**
     * Global State Store (Flux-like)
     */
    class Store {
        constructor() {
            this.state = {};
            this.reducers = {};
            this.middlewares = [];
            this.listeners = [];
            this.history = [];
        }

        registerReducer(key, reducer) {
            this.reducers[key] = reducer;
            this.state[key] = null;
        }

        use(middleware) {
            this.middlewares.push(middleware);
        }

        subscribe(callback) {
            this.listeners.push(callback);
            return () => {
                this.listeners = this.listeners.filter(l => l !== callback);
            };
        }

        dispatch(action) {
            let finalAction = action;
            
            // Apply middlewares
            for (const middleware of this.middlewares) {
                finalAction = middleware(finalAction) || finalAction;
            }

            const prevState = { ...this.state };

            // Apply reducers
            Object.entries(this.reducers).forEach(([key, reducer]) => {
                if (!finalAction.path || finalAction.path === key) {
                    this.state[key] = reducer(this.state[key], finalAction);
                }
            });

            // Record history
            this.history.push({
                action: finalAction,
                prevState,
                nextState: { ...this.state },
                timestamp: performance.now()
            });

            // Notify listeners
            this.listeners.forEach(listener => {
                listener(finalAction, this.state);
            });
        }

        getState(key) {
            if (key === undefined) return this.state;
            return this.state[key];
        }

        getHistory() {
            return this.history;
        }
    }

    /**
     * Hooks implementation
     */
    let currentComponent = null;

    function useState(initialValue) {
        const component = currentComponent;
        const hookIndex = component.hooks.length;
        
        if (!component.hooks[hookIndex]) {
            component.hooks[hookIndex] = {
                state: typeof initialValue === 'function' ? initialValue() : initialValue
            };
        }

        const setState = (newValue) => {
            const currentState = component.hooks[hookIndex].state;
            const nextState = typeof newValue === 'function' ? newValue(currentState) : newValue;
            
            if (JSON.stringify(currentState) !== JSON.stringify(nextState)) {
                component.hooks[hookIndex].state = nextState;
                component.scheduleUpdate();
            }
        };

        return [component.hooks[hookIndex].state, setState];
    }

    function useEffect(callback, dependencies) {
        const component = currentComponent;
        const hookIndex = component.hooks.length;

        if (!component.hooks[hookIndex]) {
            component.hooks[hookIndex] = {
                dependencies: null,
                cleanup: null
            };

            component.hooks[hookIndex].cleanup = callback();

            if (component.mounted) {
                component.componentDidMount();
            }
        } else {
            const prevDeps = component.hooks[hookIndex].dependencies;
            const depsChanged = !prevDeps || 
                prevDeps.length !== dependencies.length || 
                prevDeps.some((dep, i) => dep !== dependencies[i]);

            if (depsChanged) {
                if (component.hooks[hookIndex].cleanup) {
                    component.hooks[hookIndex].cleanup();
                }
                component.hooks[hookIndex].cleanup = callback();
                component.hooks[hookIndex].dependencies = dependencies;
            }
        }
    }

    function useMemo(callback, dependencies) {
        const component = currentComponent;
        const hookIndex = component.hooks.length;

        if (!component.hooks[hookIndex]) {
            component.hooks[hookIndex] = {
                value: callback(),
                dependencies
            };
        } else {
            const prevDeps = component.hooks[hookIndex].dependencies;
            const depsChanged = prevDeps.length !== dependencies.length || 
                prevDeps.some((dep, i) => dep !== dependencies[i]);

            if (depsChanged) {
                component.hooks[hookIndex].value = callback();
                component.hooks[hookIndex].dependencies = dependencies;
            }
        }

        return component.hooks[hookIndex].value;
    }

    function useCallback(callback, dependencies) {
        return useMemo(() => callback, dependencies);
    }

    function useContext(context) {
        return context?.value || null;
    }

    /**
     * Export to global namespace
     */
    WPWay.Component = Component;
    WPWay.VirtualDOMRenderer = VirtualDOMRenderer;
    WPWay.Router = Router;
    WPWay.Store = Store;
    WPWay.useState = useState;
    WPWay.useEffect = useEffect;
    WPWay.useMemo = useMemo;
    WPWay.useCallback = useCallback;
    WPWay.useContext = useContext;

    window.WPWay = WPWay;
    window.Component = Component;

})(window);
