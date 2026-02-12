/**
 * Rest API Integration
 * Connect frontend to WordPress REST API with framework integration
 */

(function(window) {
    'use strict';

    const RestAPI = {
        baseUrl: window.WPWAY?.rest_url || '/wp-json/wpway/v1',
        cache: new Map(),
        cacheTTL: 5 * 60 * 1000, // 5 minutes
        
        /**
         * Make API request
         */
        async request(endpoint, options = {}) {
            const url = this.baseUrl + endpoint;
            const cacheKey = `${options.method || 'GET'}:${url}`;

            // Check cache
            const cached = this.getFromCache(cacheKey);
            if (cached && options.method !== 'POST') {
                return cached;
            }

            const response = await fetch(url, {
                method: options.method || 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': options.nonce || '',
                    ...options.headers
                },
                body: options.body ? JSON.stringify(options.body) : undefined
            });

            if (!response.ok) {
                throw new Error(`API Error: ${response.status} ${response.statusText}`);
            }

            const data = await response.json();
            
            if (options.method !== 'POST') {
                this.setCache(cacheKey, data);
            }

            return data;
        },

        /**
         * GET request
         */
        get(endpoint) {
            return this.request(endpoint, { method: 'GET' });
        },

        /**
         * POST request 
         */
        post(endpoint, body, nonce = '') {
            return this.request(endpoint, { method: 'POST', body, nonce });
        },

        /**
         * Get component
         */
        getComponent(name) {
            return this.get(`/component/${name}`);
        },

        /**
         * List components
         */
        listComponents() {
            return this.get('/components');
        },

        /**
         * List blocks
         */
        listBlocks() {
            return this.get('/blocks');
        },

        /**
         * Get block schema
         */
        getBlockSchema(name) {
            return this.get(`/block/${name}`);
        },

        /**
         * Get global state
         */
        getState() {
            return this.get('/state');
        },

        /**
         * Set global state
         */
        setState(state, nonce) {
            return this.post('/state', state, nonce);
        },

        /**
         * Get page data
         */
        getPage(id) {
            return this.get(`/page/${id}`);
        },

        /**
         * List plugins
         */
        listPlugins() {
            return this.get('/plugins');
        },

        /**
         * Record metrics
         */
        recordMetrics(metrics) {
            return this.post('/metrics', metrics);
        },

        /**
         * Get hydration data
         */
        getHydrationData() {
            return this.get('/hydration');
        },

        /**
         * Cache management
         */
        getFromCache(key) {
            const cached = this.cache.get(key);
            if (cached && Date.now() - cached.timestamp < this.cacheTTL) {
                return cached.data;
            }
            this.cache.delete(key);
            return null;
        },

        setCache(key, data) {
            this.cache.set(key, {
                data,
                timestamp: Date.now()
            });
        },

        clearCache() {
            this.cache.clear();
        },

        /**
         * Batch requests
         */
        async batch(requests) {
            return Promise.all(
                requests.map(req => this[req.method.toLowerCase()](req.endpoint, req.body))
            );
        }
    };

    window.WPWayRestAPI = RestAPI;

})(window);
