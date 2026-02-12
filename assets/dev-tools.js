/**
 * Developer Tools for Browser Console
 * Utilities for debugging and development
 */

(function(window) {
    'use strict';

    const DevTools = {
        logs: [],
        components: {},
        showPanel: false,

        /**
         * Log to internal store
         */
        log(...args) {
            this.logs.push({
                timestamp: performance.now(),
                args: args
            });
            console.log('[WPWay]', ...args);
        },

        /**
         * Inspect framework state
         */
        inspect() {
            return {
                components: window.WPWay?.components || {},
                router: window.WPWay?.router || {},
                store: window.WPWayRestAPI ? 'Available' : 'Not loaded',
                performance: window.WPWayPerformance?.getReport() || 'Not available'
            };
        },

        /**
         * Get all components
         */
        getComponents() {
            return window.WPWay?.components || {};
        },

        /**
         * Get component info
         */
        getComponentInfo(name) {
            const component = window.WPWay?.components[name];
            return {
                name: name,
                available: !!component,
                type: typeof component
            };
        },

        /**
         * List all logs
         */
        getLogs() {
            return this.logs;
        },

        /**
         * Clear logs
         */
        clearLogs() {
            this.logs = [];
            console.log('[WPWay] Logs cleared');
        },

        /**
         * Get performance summary
         */
        getPerformance() {
            if (window.WPWayPerformance) {
                return window.WPWayPerformance.getReport();
            }
            return null;
        },

        /**
         * Get API health
         */
        async testAPI() {
            try {
                const result = await window.WPWayRestAPI.listComponents();
                return { status: 'ok', data: result };
            } catch (e) {
                return { status: 'error', error: e.message };
            }
        },

        /**
         * Export diagnostics
         */
        exportDiagnostics() {
            return JSON.stringify({
                framework: this.inspect(),
                logs: this.logs,
                performance: this.getPerformance(),
                timestamp: new Date().toISOString()
            }, null, 2);
        },

        /**
         * Print help
         */
        help() {
            console.log(`
WPWay Developer Tools
=====================

Commands:
  WPWayDevTools.inspect()           - Inspect framework state
  WPWayDevTools.getComponents()     - List all components
  WPWayDevTools.getComponentInfo(name) - Get component details
  WPWayDevTools.getPerformance()    - Get performance metrics
  WPWayDevTools.testAPI()           - Test REST API connection
  WPWayDevTools.exportDiagnostics() - Export diagnostics
  WPWayDevTools.help()              - Show this help
            `);
        }
    };

    window.WPWayDevTools = DevTools;

    // Auto-log framework initialization
    if (window.WPWay) {
        DevTools.log('WPWay framework loaded');
    }

})(window);
