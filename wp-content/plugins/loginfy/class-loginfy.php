<?php

namespace Loginfy;

use Loginfy\Libs\Assets;
use Loginfy\Libs\Helper;
use Loginfy\Libs\Featured;
use Loginfy\Inc\Classes\Recommended_Plugins;
use Loginfy\Inc\Classes\Notifications\Notifications;
use Loginfy\Inc\Classes\Pro_Upgrade;
use Loginfy\Inc\Classes\Upgrade_Plugin;
use Loginfy\Inc\Classes\Feedback;
use Loginfy\Inc\Core\Core;
/**
 * Main Class
 *
 * @Loginfy
 * Jewel Theme <support@jeweltheme.com>
 * @version     1.0.0
 */
// No, Direct access Sir !!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Loginfy Class
 */
if ( !class_exists( '\\Loginfy\\Loginfy' ) ) {
    /**
     * Class: Loginfy
     */
    final class Loginfy {
        const VERSION = LOGINFY_VER;

        private static $instance = null;

        /**
         * what we collect construct method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function __construct() {
            new Core();
            add_action( 'plugins_loaded', array($this, 'jlt_loginfy_plugins_loaded'), 999 );
            // Body Class.
            add_filter( 'admin_body_class', array($this, 'jlt_loginfy_body_class') );
            // This should run earlier .
            // add_action( 'plugins_loaded', [ $this, 'jlt_loginfy_maybe_run_upgrades' ], -100 ); .
            // Freemius Hooks
            loginfy()->add_filter( 'freemius_pricing_js_path', array($this, 'jlt_loginfy_new_freemius_pricing_js') );
            loginfy()->add_filter( 'plugin_icon', array($this, 'jlt_loginfy_logo_icon') );
            loginfy()->add_filter( 'support_forum_url', [$this, 'jlt_loginfy_support_forum_url'] );
            // Disable deactivation feedback form
            loginfy()->add_filter( 'show_deactivation_feedback_form', '__return_false' );
            // Disable after deactivation subscription cancellation window
            loginfy()->add_filter( 'show_deactivation_subscription_cancellation', '__return_false' );
        }

        /**
         * plugins_loaded method
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function jlt_loginfy_plugins_loaded() {
            $this->jlt_loginfy_activate();
            $this->includes();
        }

        /**
         * Version Key
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function plugin_version_key() {
            return Helper::jlt_loginfy_slug_cleanup() . '_version';
        }

        /**
         * Activation Hook
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function jlt_loginfy_activate() {
            $current_jlt_loginfy_version = get_option( self::plugin_version_key(), null );
            if ( get_option( 'jlt_loginfy_activation_time' ) === false ) {
                update_option( 'jlt_loginfy_activation_time', strtotime( 'now' ) );
            }
            if ( is_null( $current_jlt_loginfy_version ) ) {
                update_option( self::plugin_version_key(), self::VERSION );
            }
            $allowed = get_option( Helper::jlt_loginfy_slug_cleanup() . '_allow_tracking', 'no' );
            // if it wasn't allowed before, do nothing .
            if ( 'yes' !== $allowed ) {
                return;
            }
            // re-schedule and delete the last sent time so we could force send again .
            $hook_name = Helper::jlt_loginfy_slug_cleanup() . '_tracker_send_event';
            if ( !wp_next_scheduled( $hook_name ) ) {
                wp_schedule_event( time(), 'weekly', $hook_name );
            }
        }

        /**
         * Add Body Class
         *
         * @param [type] $classes .
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function jlt_loginfy_body_class( $classes ) {
            $classes .= ' loginfy ';
            return $classes;
        }

        /**
         * Run Upgrader Class
         *
         * @return void
         */
        public function jlt_loginfy_maybe_run_upgrades() {
            if ( !is_admin() && !current_user_can( 'manage_options' ) ) {
                return;
            }
            // Run Upgrader .
            $upgrade = new Upgrade_Plugin();
            // Need to work on Upgrade Class .
            if ( $upgrade->if_updates_available() ) {
                $upgrade->run_updates();
            }
        }

        /**
         * Include methods
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function includes() {
            new Assets();
            new Recommended_Plugins();
            new Pro_Upgrade();
            new Notifications();
            new Featured();
            new Feedback();
        }

        /**
         * Initialization
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function jlt_loginfy_init() {
            $this->jlt_loginfy_load_textdomain();
        }

        /**
         * Text Domain
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public function jlt_loginfy_load_textdomain() {
            add_action( 'init', function () {
                $domain = 'loginfy';
                $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
                load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
                load_plugin_textdomain( $domain, false, dirname( LOGINFY_BASE ) . '/languages/' );
            } );
        }

        /**
         * Deactivate Pro Plugin if it's not already active
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function jlt_loginfy_activation_hook() {
            $plugin = 'loginfy/loginfy.php';
            if ( is_plugin_active( $plugin ) ) {
                deactivate_plugins( $plugin );
            }
            if ( class_exists( '\\WPAdminify\\Inc\\Admin\\AdminSettings' ) ) {
                $sync_settings = new \Loginfy\Inc\Core\Inc\Customize_Settings();
                $sync_settings->sync_settings_from_adminify();
            }
            set_transient( '_welcome_screen_activation_redirect', true, 30 );
        }

        /**
         * Loginfy Logo
         *
         * @param [type] $logo
         *
         * @return void
         */
        public function jlt_loginfy_logo_icon( $logo ) {
            $logo = LOGINFY_PATH . '/assets/images/logo.svg';
            return $logo;
        }

        public function jlt_loginfy_new_freemius_pricing_js() {
            return LOGINFY_PATH . '/Libs/freemius-pricing/freemius-pricing.js';
        }

        /**
         * Support Contact URL
         *
         * @param [type] $support_url and Pro Support
         *
         * @return void
         */
        public function jlt_loginfy_support_forum_url( $support_url ) {
            if ( loginfy()->is_premium() ) {
                $support_url = 'https://wpadminify.com/contact';
            } else {
                $support_url = 'https://wordpress.org/support/plugin/loginfy/';
            }
            return $support_url;
        }

        /**
         * Returns the singleton instance of the class.
         */
        public static function get_instance() {
            if ( !isset( self::$instance ) && !self::$instance instanceof Loginfy ) {
                self::$instance = new Loginfy();
                self::$instance->jlt_loginfy_init();
            }
            return self::$instance;
        }

    }

    // Get Instant of Loginfy Class .
    Loginfy::get_instance();
}