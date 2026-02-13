<?php
/**
 * WPWay Admin Diagnostic - Debug why menu isn't showing
 */

namespace WPWay\Admin;

if (!defined('ABSPATH')) exit;

class Diagnostic {
    public static function check() {
        // Log that diagnostic is running
        error_log('[WPWay Diagnostic] Checking admin dashboard...');
        
        // Check 1: Is user admin?
        $user = wp_get_current_user();
        if (!user_can($user, 'manage_options')) {
            error_log('[WPWay Diagnostic] Current user is NOT admin: ' . $user->user_login);
            return;
        }
        error_log('[WPWay Diagnostic] Current user is admin: ' . $user->user_login);
        
        // Check 2: Is admin dashboard class loaded?
        if (class_exists(__NAMESPACE__ . '\\AdminDashboard')) {
            error_log('[WPWay Diagnostic] AdminDashboard class EXISTS');
        } else {
            error_log('[WPWay Diagnostic] AdminDashboard class NOT FOUND');
            return;
        }
        
        // Check 3: Get instance
        try {
            $dashboard = AdminDashboard::getInstance();
            error_log('[WPWay Diagnostic] AdminDashboard instance created successfully');
        } catch (\Exception $e) {
            error_log('[WPWay Diagnostic] Error creating instance: ' . $e->getMessage());
            return;
        }
        
        // Check 4: Is WordPress admin?
        if (is_admin()) {
            error_log('[WPWay Diagnostic] Currently in WordPress admin area');
        } else {
            error_log('[WPWay Diagnostic] NOT in WordPress admin area');
        }
        
        error_log('[WPWay Diagnostic] All checks passed');
    }
}

// Run diagnostic on admin_init
add_action('admin_init', [Diagnostic::class, 'check'], 1);
