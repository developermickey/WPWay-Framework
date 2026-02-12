<?php
/**
 * Performance Optimizer
 * Code splitting, lazy loading, and optimization strategies
 */

namespace WPWay\Performance;

if (!defined('ABSPATH')) exit;

class Optimizer {
    private static $instance;
    private $chunks = [];
    private $lazy_components = [];
    private $metrics = [];
    private $critical_path = [];

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register a code chunk for code splitting
     */
    public function registerChunk($chunk_name, $components = [], $dependencies = []) {
        $this->chunks[$chunk_name] = [
            'components' => $components,
            'dependencies' => $dependencies,
            'size' => 0,
            'cached' => false
        ];
    }

    /**
     * Mark component for lazy loading
     */
    public function markLazy($component_name, $threshold = 1000) {
        $this->lazy_components[$component_name] = [
            'threshold' => $threshold,
            'loaded' => false
        ];
    }

    /**
     * Get lazy components
     */
    public function getLazyComponents() {
        return $this->lazy_components;
    }

    /**
     * Add to critical rendering path
     */
    public function addCriticalComponent($component_name) {
        $this->critical_path[] = $component_name;
    }

    /**
     * Get critical path components
     */
    public function getCriticalPath() {
        return array_unique($this->critical_path);
    }

    /**
     * Prefetch resource
     */
    public function prefetch($url, $as = 'script') {
        return sprintf(
            '<link rel="prefetch" href="%s" as="%s">',
            htmlspecialchars($url),
            htmlspecialchars($as)
        );
    }

    /**
     * Preload resource
     */
    public function preload($url, $as = 'script') {
        return sprintf(
            '<link rel="preload" href="%s" as="%s">',
            htmlspecialchars($url),
            htmlspecialchars($as)
        );
    }

    /**
     * Record performance metric
     */
    public function recordMetric($metric_name, $value) {
        $this->metrics[$metric_name] = [
            'value' => $value,
            'timestamp' => microtime(true)
        ];
    }

    /**
     * Get all metrics
     */
    public function getMetrics() {
        return $this->metrics;
    }

    /**
     * Generate performance report
     */
    public function generateReport() {
        return [
            'chunks' => $this->chunks,
            'lazy_components' => $this->lazy_components,
            'critical_path' => $this->getCriticalPath(),
            'metrics' => $this->metrics
        ];
    }

    /**
     * Enable caching for chunk
     */
    public function cacheChunk($chunk_name, $duration = 3600) {
        if (isset($this->chunks[$chunk_name])) {
            $this->chunks[$chunk_name]['cached'] = true;
            $this->chunks[$chunk_name]['cache_duration'] = $duration;
        }
    }

    /**
     * Get optimized bundle info
     */
    public function getBundleInfo() {
        $total_size = 0;
        $chunk_info = [];

        foreach ($this->chunks as $name => $chunk) {
            $size = $chunk['size'];
            $total_size += $size;
            $chunk_info[$name] = [
                'size' => $size,
                'components' => count($chunk['components']),
                'cached' => $chunk['cached']
            ];
        }

        return [
            'total_size' => $total_size,
            'chunks' => $chunk_info,
            'lazy_count' => count($this->lazy_components),
            'critical_count' => count($this->critical_path)
        ];
    }
}
