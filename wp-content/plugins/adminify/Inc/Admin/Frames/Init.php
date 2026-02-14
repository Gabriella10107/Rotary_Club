<?php

namespace WPAdminify\Inc\Admin\Frames;

// no direct access allowed
if (!defined('ABSPATH')) {
    exit;
}
/**
 * WP Adminify
 * Init Class
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

if (!class_exists('Init')) {
    class Init
    {
        public static $instance;
        public $admin;
        public $frame;

        public static function instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {

            if ( ! $this->is_allowed() ) {
                if ( is_iframe() ) {
                    $actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    Frames::custom_plugin_change_reload($actual_link);
                }
                return;
            }

            if ( is_iframe() ) {
                $this->frame = new Frames();
            } else {
                $this->admin = new Admin();
            }

        }

        /**
         * Get the relative admin path without subdirectory prefix
         * Handles root, subdirectory, subdomain, and multisite installations
         *
         * @return string Normalized path (e.g., /wp-admin/edit.php)
         */
        private function get_normalized_admin_path() {
            $php_self = $_SERVER['PHP_SELF'] ?? '';

            // Method 1: Use WordPress native function to get subdirectory path
            // site_url() returns full URL including subdirectory
            // e.g., https://example.com/blog or https://example.com
            $site_url_path = wp_parse_url( site_url(), PHP_URL_PATH );

            // Remove subdirectory prefix if exists
            if ( ! empty( $site_url_path ) && $site_url_path !== '/' ) {
                // Ensure path starts with subdirectory
                if ( strpos( $php_self, $site_url_path ) === 0 ) {
                    $php_self = substr( $php_self, strlen( $site_url_path ) );
                }
            }

            // Ensure path starts with /
            if ( empty( $php_self ) || $php_self[0] !== '/' ) {
                $php_self = '/' . $php_self;
            }

            return $php_self;
        }

        /**
         * Check if current path matches the blocked URL pattern
         * Supports exact match and ends-with matching for subdirectory compatibility
         *
         * @param string $blocked_url The URL pattern to check against
         * @return bool True if current path matches the blocked URL
         */
        private function matches_blocked_url( $blocked_url ) {
            $current_path = $this->get_normalized_admin_path();

            // Exact match (normalized)
            if ( $current_path === $blocked_url ) {
                return true;
            }

            // Fallback: ends-with check for edge cases
            // e.g., /wp-admin/customize.php should match even if normalization fails
            if ( $this->url_ends_with( $_SERVER['PHP_SELF'] ?? '', $blocked_url ) ) {
                return true;
            }

            return false;
        }

        /**
         * Check if a URL ends with a specific path
         * Useful for subdirectory WordPress installs
         *
         * @param string $url Full URL or path to check
         * @param string $ending The ending pattern to match
         * @return bool
         */
        private function url_ends_with( $url, $ending ) {
            $ending_length = strlen( $ending );
            if ( $ending_length === 0 ) {
                return true;
            }
            return substr( $url, -$ending_length ) === $ending;
        }

        /**
         * Get WordPress installation context for debugging
         *
         * @return array Installation details
         */
        public function get_install_context() {
            return [
                'is_multisite'     => is_multisite(),
                'is_subdomain'     => defined( 'SUBDOMAIN_INSTALL' ) && SUBDOMAIN_INSTALL,
                'site_url'         => site_url(),
                'home_url'         => home_url(),
                'admin_url'        => admin_url(),
                'subdirectory'     => wp_parse_url( site_url(), PHP_URL_PATH ) ?: '/',
                'php_self'         => $_SERVER['PHP_SELF'] ?? '',
                'normalized_path'  => $this->get_normalized_admin_path(),
            ];
        }

        public function is_allowed() {

            $not_allowed_urls = Admin::get_not_allowed_urls();

            foreach ( $not_allowed_urls as $url_object ) {
                if ( is_string( $url_object ) ) {

                    $is_allowed = true; // Scoped Default allowed
                    // Use normalized path matching for subdirectory compatibility
                    if ( $this->matches_blocked_url( $url_object ) ) {
                        $is_allowed = false; // not allowed
                    }

                } else {

                    $is_allowed = false; // Scoped Default not allowed

                    // Use normalized path matching for subdirectory compatibility
                    if ( $url_object['url'] !== '*' && ! $this->matches_blocked_url( $url_object['url'] ) ) {
                        $is_allowed = true; // allowed
                    }

                    if ( ! $is_allowed && array_key_exists( 'query_params', $url_object ) ) {
                        if ( ! $this->check_query_params( $url_object['query_params'] ) ) {
                            $is_allowed = true; // allowed
                        }
                    }

                    if ( ! $is_allowed && array_key_exists( 'post_type', $url_object ) ) {
                        if ( ! $this->check_post_type( $url_object['post_type'] ) ) {
                            $is_allowed = true; // allowed
                        }
                    }

                }

                if ( ! $is_allowed ) {
                    return $is_allowed;
                }

            }

            return true;

        }

        function check_query_params($query_params) {
            // Pattern 1: Both keys and their values should check in $_GET
            if (array_keys($query_params) === $query_params) {
                foreach ($query_params as $key => $value) {
                    if (!isset($_GET[$key]) || $_GET[$key] != $value) {
                        return false; // Key doesn't exist or the value doesn't match
                    }
                }
                return true; // All keys and values match
            }

            // Pattern 2: Check for only keys in $_GET, no need to check their values
            if (array_values($query_params) === $query_params) {
                foreach ($query_params as $param) {
                    if ( substr($param, -1) === '!' ) {
                        $param = substr($param, 0, -1);
                        if ( isset($_GET[$param]) ) return false; // The key exists in $_GET
                    } else {
                        if ( ! isset($_GET[$param]) ) return false; // The key doesn't exist in $_GET
                    }

                }
                return true; // All keys exist
            }

            // Pattern 3: A mix of key existence and key-value matching
            foreach ($query_params as $key => $value) {
                if (is_numeric($key)) {
                    // For numeric keys, we're checking only existence (Pattern 1 behavior)
                    if ( substr($value, -1) === '!' ) {
                        $value = substr($value, 0, -1);
                        if ( isset($_GET[$value]) ) return false; // The key exists in $_GET
                    } else {
                        if ( ! isset($_GET[$value]) ) return false; // The key doesn't exist in $_GET
                    }
                } else {
                    // For associative keys, we check for both key and value (Pattern 2 behavior)
                    if (!isset($_GET[$key]) || $_GET[$key] != $value) {
                        return false; // Key doesn't exist or value doesn't match
                    }
                }
            }

            return true; // All conditions are met
        }

        function check_post_type($post_types) {
            if ( isset( $_GET['post_type'] ) ) {
                return in_array( $_GET['post_type'], $post_types );
            } else if ( isset( $_GET['post'] ) ) {
                return in_array( get_post_type( $_GET['post'] ), $post_types );
            }
            return in_array( 'post', $post_types );
        }

    }

}
