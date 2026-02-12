/**
 * TypeScript Definitions for WPWay Framework
 * Complete type definitions for IDE support
 */

declare namespace WPWay {
    // Component system
    interface Props {
        [key: string]: any;
        children?: ReactNode | ReactNode[];
    }

    type ReactNode = string | number | Component | null | boolean | undefined;

    interface ComponentLifecycle {
        componentDidMount?(): void;
        componentDidUpdate?(prevState: object, nextState: object): void;
        componentWillUnmount?(): void;
    }

    class Component implements ComponentLifecycle {
        props: Props;
        state: object;
        mounted: boolean;
        hooks: any[];

        constructor(props?: Props);
        setState(key: string, value: any): void;
        getState(key?: string): any;
        setProps(props: Props): void;
        getProps(): Props;
        render(): ReactNode;
        scheduleUpdate(): void;
        componentDidMount(): void;
        componentDidUpdate(prev: object, next: object): void;
        componentWillUnmount(): void;
        update(): void;
    }

    interface VNode {
        type: 'element' | 'component' | 'fragment' | 'text';
        tag?: string;
        name?: string;
        props?: Props;
        children?: ReactNode[];
        content?: string;
    }

    class VirtualDOMRenderer {
        render(vnode: VNode, container: HTMLElement): HTMLElement;
        renderNode(vnode: VNode): Node;
        diff(oldVnode: VNode, newVnode: VNode): any[];
        reconcile(changes: any[], element: HTMLElement): void;
    }

    // Routing
    interface RouteConfig {
        component: string;
        options?: object;
    }

    interface Route {
        path: string;
        params: object;
        timestamp: number;
    }

    class Router {
        registerRoute(path: string, component: string, options?: object): void;
        registerRoutes(routes: Record<string, string>): void;
        beforeNavigate(callback: (path: string, params: object) => boolean | Promise<boolean>): void;
        afterNavigate(callback: (path: string, params: object) => Promise<void>): void;
        navigate(path: string, params?: object): Promise<boolean>;
        matchRoute(path: string): RouteConfig | null;
        getCurrentRoute(): Route | null;
        getHistory(): Route[];
    }

    // State Management
    type Reducer = (state: any, action: object) => any;
    type Middleware = (action: object) => object;
    type UnsubscribeFn = () => void;

    class Store {
        registerReducer(key: string, reducer: Reducer): void;
        use(middleware: Middleware): void;
        subscribe(callback: (action: object, state: object) => void): UnsubscribeFn;
        dispatch(action: object): void;
        getState(key?: string): any;
        getHistory(): any[];
        timeTravel(index: number): boolean;
        exportSnapshot(): object;
        restoreSnapshot(snapshot: object): boolean;
    }

    // Hooks
    type SetStateAction<T> = T | ((prev: T) => T);
    type EffectCallback = () => (() => void) | void;

    function useState<T>(initialValue: T | (() => T)): [T, (value: SetStateAction<T>) => void];
    function useEffect(callback: EffectCallback, dependencies?: any[]): void;
    function useMemo<T>(callback: () => T, dependencies?: any[]): T;
    function useCallback<T extends (...args: any[]) => any>(callback: T, dependencies?: any[]): T;
    function useContext(context: any): any;

    // Virtual DOM
    function createElement(
        tag: string | typeof Component,
        props?: Props,
        ...children: ReactNode[]
    ): VNode;

    function createComponent(
        name: string,
        props?: Props,
        ...children: ReactNode[]
    ): VNode;

    function Fragment(props: { children: ReactNode[] }): VNode;

    // REST API
    interface RestAPIOptions {
        method?: 'GET' | 'POST' | 'PUT' | 'DELETE';
        body?: object;
        headers?: Record<string, string>;
        nonce?: string;
    }

    interface RestAPI {
        baseUrl: string;
        cache: Map<string, any>;
        request(endpoint: string, options?: RestAPIOptions): Promise<any>;
        get(endpoint: string): Promise<any>;
        post(endpoint: string, body: object, nonce?: string): Promise<any>;
        getComponent(name: string): Promise<any>;
        listComponents(): Promise<any>;
        listBlocks(): Promise<any>;
        getBlockSchema(name: string): Promise<any>;
        getState(): Promise<any>;
        setState(state: object, nonce?: string): Promise<any>;
        getPage(id: number): Promise<any>;
        listPlugins(): Promise<any>;
        recordMetrics(metrics: object): Promise<any>;
        batch(requests: Array<{ method: string; endpoint: string; body?: object }>): Promise<any[]>;
    }

    // Performance
    interface PerformanceMetrics {
        name: string;
        value: number;
        timestamp: number;
    }

    interface PerformanceReport {
        metrics: Record<string, PerformanceMetrics>;
        navigationTiming: object;
        resourceTiming: object;
        lazyComponents: string[];
    }

    interface Performance {
        recordMetric(name: string, value: number): void;
        mark(name: string): void;
        measure(name: string, start: string, end: string): void;
        lazyLoadComponent(name: string, url: string): Promise<typeof Component>;
        observeLazy(element: HTMLElement, callback: (el: HTMLElement) => void): void;
        prefetch(url: string): void;
        preload(url: string, as?: string): void;
        onIdle(callback: () => void): void;
        debounce(callback: Function, delay: number): Function;
        throttle(callback: Function, delay: number): Function;
        getReport(): PerformanceReport;
        logSummary(): void;
    }

    // Hydration
    interface HydrationData {
        [hydrationId: string]: {
            component: string;
            props: Props;
            state: object;
            hydrated?: boolean;
        };
    }

    interface Hydration {
        init(data?: object): void;
        hydrateComponents(): void;
        hydrateElement(element: HTMLElement, id: string, name: string): void;
        getComponentData(id: string): object;
        markHydrated(component: Component): void;
        isHydrating(): boolean;
        getStats(): object;
    }

    // Global exports
    let components: Record<string, typeof Component>;
    let hooks: object;
    let state: object;
    let router: Router;
    const version: string;
}

declare const h: typeof WPWay.createElement;
declare const Component: typeof WPWay.Component;

declare const WPWay: typeof WPWay;
declare const WPWayHydration: WPWay.Hydration;
declare const WPWayRestAPI: WPWay.RestAPI;
declare const WPWayPerformance: WPWay.Performance;
declare const WPWayDevTools: {
    inspect(): object;
    getComponents(): Record<string, any>;
    getComponentInfo(name: string): object;
    getLogs(): any[];
    clearLogs(): void;
    getPerformance(): WPWay.PerformanceReport | null;
    testAPI(): Promise<object>;
    exportDiagnostics(): string;
    help(): void;
};
