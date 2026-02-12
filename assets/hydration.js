/**
 * Hydration Engine
 * Handles server-side rendering hydration on client side
 */

(function(window) {
    'use strict';

    const Hydration = {
        hydrationData: {},
        hydratedComponents: new WeakMap(),
        isHydrating: true,
        hydratedCount: 0,

        /**
         * Initialize hydration from server data
         */
        init(data = null) {
            const hydrationScript = document.getElementById('wpway-hydration-data');
            
            if (hydrationScript && hydrationScript.textContent) {
                try {
                    this.hydrationData = JSON.parse(hydrationScript.textContent);
                } catch (e) {
                    console.error('Failed to parse hydration data:', e);
                }
            }

            if (data) {
                this.hydrationData = { ...this.hydrationData, ...data };
            }

            this.hydrateComponents();
        },

        /**
         * Hydrate all components marked with hydration ID
         */
        hydrateComponents() {
            const elements = document.querySelectorAll('[data-wp-way-hydration-id]');
            
            elements.forEach(element => {
                const hydrationId = element.getAttribute('data-wp-way-hydration-id');
                const componentName = element.getAttribute('data-wp-way-component');

                if (this.hydrationData[hydrationId]) {
                    this.hydrateElement(element, hydrationId, componentName);
                    this.hydratedCount++;
                }
            });

            this.isHydrating = false;
            this.onComplete();
        },

        /**
         * Hydrate individual element
         */
        hydrateElement(element, hydrationId, componentName) {
            const data = this.hydrationData[hydrationId];
            const Component = window.WPWay.components[componentName];

            if (Component) {
                const instance = new Component(data.props);
                instance.state = data.state || {};
                instance.mounted = true;
                instance.domElement = element;

                this.hydratedComponents.set(element, instance);

                // Attach event listeners
                this.attachEventListeners(element, instance);
            }
        },

        /**
         * Attach event listeners to hydrated component
         */
        attachEventListeners(element, component) {
            element.querySelectorAll('[data-wp-way-event]').forEach(el => {
                const events = el.getAttribute('data-wp-way-event').split(',');
                events.forEach(eventName => {
                    const methodName = el.getAttribute(`data-on-${eventName}`);
                    if (methodName && typeof component[methodName] === 'function') {
                        el.addEventListener(eventName, (e) => {
                            component[methodName].call(component, e);
                        });
                    }
                });
            });
        },

        /**
         * Get hydration data for component
         */
        getComponentData(hydrationId) {
            return this.hydrationData[hydrationId];
        },

        /**
         * Mark component as hydrated
         */
        markHydrated(component) {
            component.isHydrated = true;
        },

        /**
         * Check if currently hydrating
         */
        isHydrating() {
            return this.isHydrating;
        },

        /**
         * Completion callback
         */
        onComplete() {
            console.log(`WPWay: ${this.hydratedCount} components hydrated`);
            document.dispatchEvent(new CustomEvent('wpway:hydration-complete', {
                detail: { count: this.hydratedCount }
            }));
        },

        /**
         * Get hydration statistics
         */
        getStats() {
            return {
                totalHydrated: this.hydratedCount,
                isComplete: !this.isHydrating,
                dataSize: Object.keys(this.hydrationData).length
            };
        }
    };

    window.WPWayHydration = Hydration;

})(window);
