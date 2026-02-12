/**
 * Performance Utilities
 * Code splitting, lazy loading, and performance metrics
 */

(function(window) {
    'use strict';

    const Performance = {
        metrics: {},
        chunks: {},
        lazyComponents: {},
        observer: null,

        /**
         * Record performance metric
         */
        recordMetric(name, value) {
            this.metrics[name] = {
                value,
                timestamp: performance.now()
            };
        },

        /**
         * Mark performance event
         */
        mark(name) {
            performance.mark(`wpway-${name}`);
        },

        /**
         * Measure time between marks
         */
        measure(name, startMark, endMark) {
            try {
                performance.measure(
                    `wpway-${name}`,
                    `wpway-${startMark}`,
                    `wpway-${endMark}`
                );
                const measure = performance.getEntriesByName(`wpway-${name}`)[0];
                this.recordMetric(name, measure.duration);
            } catch (e) {
                console.warn('Performance measurement failed:', e);
            }
        },

        /**
         * Lazy load component
         */
        lazyLoadComponent(componentName, chunkUrl) {
            return new Promise((resolve, reject) => {
                if (this.lazyComponents[componentName]) {
                    resolve(this.lazyComponents[componentName]);
                    return;
                }

                const script = document.createElement('script');
                script.src = chunkUrl;
                script.onload = () => {
                    if (window.WPWay.components[componentName]) {
                        this.lazyComponents[componentName] = window.WPWay.components[componentName];
                        resolve(window.WPWay.components[componentName]);
                    } else {
                        reject(new Error(`Component ${componentName} not loaded`));
                    }
                };
                script.onerror = () => reject(new Error(`Failed to load chunk: ${chunkUrl}`));
                document.head.appendChild(script);
            });
        },

        /**
         * Intersection Observer for lazy loading
         */
        observeLazy(element, callback) {
            if (!this.observer) {
                this.observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            callback(entry.target);
                            this.observer.unobserve(entry.target);
                        }
                    });
                }, { rootMargin: '50px' });
            }

            this.observer.observe(element);
        },

        /**
         * Prefetch resource
         */
        prefetch(url) {
            const link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            document.head.appendChild(link);
        },

        /**
         * Preload resource
         */
        preload(url, as = 'script') {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = as;
            link.href = url;
            document.head.appendChild(link);
        },

        /**
         * Enable request idle callback
         */
        onIdle(callback) {
            if ('requestIdleCallback' in window) {
                requestIdleCallback(callback);
            } else {
                setTimeout(callback, 0);
            }
        },

        /**
         * Debounce function
         */
        debounce(callback, delay) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => callback.apply(this, args), delay);
            };
        },

        /**
         * Throttle function
         */
        throttle(callback, delay) {
            let lastTime = 0;
            return function(...args) {
                const now = performance.now();
                if (now - lastTime >= delay) {
                    callback.apply(this, args);
                    lastTime = now;
                }
            };
        },

        /**
         * Get performance report
         */
        getReport() {
            return {
                metrics: this.metrics,
                navigationTiming: this.getNavigationTiming(),
                resourceTiming: this.getResourceTiming(),
                lazyComponents: Object.keys(this.lazyComponents),
                marks: performance.getEntriesByType('mark').map(m => m.name)
            };
        },

        /**
         * Get navigation timing
         */
        getNavigationTiming() {
            const navigation = performance.getEntriesByType('navigation')[0];
            if (!navigation) return {};
            return {
                dns: navigation.domainLookupEnd - navigation.domainLookupStart,
                tcp: navigation.connectEnd - navigation.connectStart,
                ttfb: navigation.responseStart - navigation.requestStart,
                download: navigation.responseEnd - navigation.responseStart,
                dom: navigation.domInteractive - navigation.domLoading,
                load: navigation.loadEventEnd - navigation.loadEventStart
            };
        },

        /**
         * Get resource timing
         */
        getResourceTiming() {
            const resources = performance.getEntriesByType('resource');
            const summary = {
                totalCount: resources.length,
                totalSize: 0,
                totalDuration: 0,
                byType: {}
            };

            resources.forEach(resource => {
                summary.totalSize += resource.transferSize || 0;
                summary.totalDuration += resource.duration;

                const type = resource.name.split('.').pop();
                if (!summary.byType[type]) {
                    summary.byType[type] = { count: 0, size: 0 };
                }
                summary.byType[type].count++;
                summary.byType[type].size += resource.transferSize || 0;
            });

            return summary;
        },

        /**
         * Log performance summary
         */
        logSummary() {
            console.group('WPWay Performance Report');
            console.log('Metrics:', this.metrics);
            console.log('Navigation Timing:', this.getNavigationTiming());
            console.log('Resource Timing:', this.getResourceTiming());
            console.groupEnd();
        }
    };

    window.WPWayPerformance = Performance;

})(window);
