<?php

namespace Loginfy\Inc\Core;

use Loginfy\Inc\Core\Inc\Customize_Settings;
use Loginfy\Inc\Core\Inc\Output_Customization;
use Loginfy\Libs\Helper;

// no direct access allowed
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loginfy
 * Module: Login Customizer
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */

if ( ! class_exists( 'Core' ) ) {

	class Core {

		public $options;

		public function __construct() {

			$this->_hooks();

			$this->options = ( new Customize_Settings() )->get();

			// Customizer Output CSS
			new Output_Customization();
		}

		/**
		 * Hooks
		 */
		public function _hooks() {
			global $wp_version;

			if (Helper::is_plugin_active('adminify/adminify.php') || Helper::is_plugin_active('adminify-pro/adminify.php')) {
				add_action('admin_menu', [$this, 'jltwp_loginfy_submenu'], 12);
			}else{
				add_action('admin_menu', [$this, 'jltwp_loginfy_menu'], 50);
			}

			add_action( 'admin_init', [ $this, 'jlt_loginfy_redirect_customizer' ] );

			// Setup customizer.
			add_action( 'customize_register', [ $this, 'jlt_loginfy_register_panels' ] );
			add_action( 'customize_register', [ $this, 'jlt_loginfy_register_sections' ] );

			// Enqueue assets.
			add_action( 'customize_controls_print_styles', [ $this, 'jlt_loginfy_control_styles' ], 9999 );
			add_action( 'customize_controls_enqueue_scripts', [ $this, 'jlt_loginfy_control_scripts' ], 9999 );
			add_action( 'login_enqueue_scripts', [ $this, 'jlt_loginfy_preview_styles' ], 99 );
			add_action( 'customize_preview_init', [ $this, 'jlt_loginfy_preview_scripts' ], 99 );

			// Setup redirect.
			add_filter( 'template_include', [ $this, 'jlt_loginfy_template_include' ], 99 );

			// Templates Ajax
			add_action( 'wp_ajax_jlt_loginfy_adminify_presets', [ $this, 'jlt_loginfy_templates' ] );

			// TODO: for block themes
			// if ( version_compare( $wp_version, '5.9', '>=' )) {
				// if (  function_exists( 'wp_is_block_theme' ) && wp_is_block_theme()  ) {
					// add_action( 'customize_register', [ $this, 'remove_customizer_settings_for_block_theme' ], 20 );
				// }
			// }
		}


		public function remove_customizer_settings_for_block_theme( $WP_Customize_Manager ) {
			// check if WP_Customize_Nav_Menus object exist
			if ( isset( $WP_Customize_Manager->nav_menus ) && is_object( $WP_Customize_Manager->nav_menus ) ) {
				remove_action( 'customize_controls_enqueue_scripts', [ $WP_Customize_Manager->nav_menus, 'enqueue_scripts' ] );
				remove_action( 'customize_controls_print_footer_scripts', [ $WP_Customize_Manager->nav_menus, 'available_items_template' ] );
			}
			// check if WP_Customize_Widgets object exist
			if ( isset( $WP_Customize_Manager->widgets ) && is_object( $WP_Customize_Manager->widgets ) ) {
				remove_action( 'customize_controls_print_footer_scripts', [ $WP_Customize_Manager->widgets, 'output_widget_control_templates' ] );
			}
		}

		// Template Include
		public function jlt_loginfy_template_include( $template ) {
			if ( is_customize_preview() && isset( $_REQUEST['loginfy-login-customizer'] ) && is_user_logged_in() ) {
				return plugin_dir_path( __FILE__ ) . 'Inc/loginfy-login-template.php';
			}

			return $template;
		}


		/**
		 * Enqueue the login customizer control styles.
		 */
		public function jlt_loginfy_control_styles() {
			wp_enqueue_script( 'loginfy-login-customizer', LOGINFY_ASSETS . 'js/loginfy-customizer.js', [ 'jquery', 'customize-controls' ], LOGINFY_VER, true );
			wp_localize_script( 'loginfy-login-customizer', 'LoginfyCustomizer', $this->jlt_loginfy_create_js_object() );
		}

		/**
		 * Enqueue styles to login customizer preview styles.
		 */
		public function jlt_loginfy_preview_styles() {
			if ( ! is_customize_preview() ) {
				return;
			}

			wp_enqueue_style( 'loginfy-login-customizer-preview', LOGINFY_ASSETS . 'css/preview.css', [], LOGINFY_VER, 'all' );
		}

		/**
		 * Enqueue scripts to login customizer preview scripts.
		 */
		public function jlt_loginfy_preview_scripts() {
			if ( ! is_customize_preview() ) {
				return;
			}
			wp_enqueue_script( 'loginfy-login-customizer-preview', LOGINFY_ASSETS . 'js/preview.js', [ 'jquery', 'customize-preview' ], LOGINFY_VER, true );
			wp_localize_script( 'loginfy-login-customizer-preview', 'LoginfyCustomizer', $this->jlt_loginfy_create_js_object() );
		}
		/**
		 * Enqueue login customizer control scripts.
		 */
		public function jlt_loginfy_control_scripts() {

			wp_enqueue_style('loginfy');
			wp_enqueue_script('loginfy');

			// Login Customizer Control
			wp_register_script('loginfy-login-customizer-controls', LOGINFY_ASSETS . 'css/controls' . Helper::assets_ext('.css'), null, LOGINFY_VER);

			wp_enqueue_style( 'loginfy-login-customizer-controls' );
			wp_enqueue_script( 'loginfy-login-customizer', LOGINFY_ASSETS . 'js/loginfy-customizer.js', [ 'jquery', 'customize-controls' ], LOGINFY_VER, true );
			wp_localize_script( 'loginfy-login-customizer', 'LoginfyCustomizer', $this->jlt_loginfy_create_js_object() );
		}

		/**
		 * Login customizer's localized JS object.
		 *
		 * @return array The login customizer's localized JS object.
		 */
		public function jlt_loginfy_create_js_object() {
			return [
				'homeUrl'             => home_url(),
				'ajaxurl'             => admin_url( 'admin-ajax.php' ),
				'loginPageUrl'        => home_url( 'loginfy-login-customizer' ),
				'pluginUrl'           => rtrim(LOGINFY_URI, '/' ),
				'login_template'      => $this->options['templates'],
				'moduleUrl'           => LOGINFY_URI,
				'assetUrl'            => LOGINFY_ASSETS,
				'preset_nonce'        => wp_create_nonce( 'loginfy-login-customizer-template-nonce' ),
				'wpLogoUrl'           => admin_url( 'images/wordpress-logo.svg?ver=' . LOGINFY_VER ),
				'siteurl'             => get_option( 'siteurl' ),
				'register_url'        => wp_registration_url(),
				'anyone_can_register' => get_option( 'users_can_register' ),
				'filter_bg'           => apply_filters( 'loginfy_customizer_bg', '' ),
				'preset_loader'       => includes_url( 'js/tinymce/skins/lightgray/img/loader.gif' ),
				'isProActive'         => loginfy()->can_use_premium_code__premium_only() ? true : false,
			];
		}


		// Template Selection
		public function jlt_loginfy_templates() {
			check_ajax_referer( 'loginfy-login-customizer-template-nonce', 'security' );

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( 'No cheating, huh!' );
			}

			$selected_template = sanitize_key( wp_unslash( $_POST['template_id'] ) );
			if ( $selected_template == 'template-01' ) {
				include_once plugin_dir_path( __FILE__ ) . 'Inc/templates/template-01.php';
			} else {
				do_action( 'loginfy_add_templates', $selected_template );
			}
			wp_die();
		}


		/**
		 * Customizer Redirect
		 */
		public function jlt_loginfy_redirect_customizer() {

			if ( isset( $_GET['page'] ) && 'loginfy-customizer' === $_GET['page'] ) {

				// Redirect URL
				$loginfy_url = add_query_arg( array(
						'autofocus[panel]' 		=> 'loginfy_panel',
						'url' 					=> rawurlencode( get_permalink( 'page' ) ),
					),
					admin_url( 'customize.php' )
				);

				wp_safe_redirect( $loginfy_url );
			}

			// Upgrade URL
			if ( isset( $_GET['page'] ) && 'loginfy-upgrade-pro' === $_GET['page'] ) {
				$purchase_url = 'https://wpadminify.com/loginfy/pricing/?utm_source=appearance&utm_medium=upgrade_link&utm_campaign=loginfy';
				wp_redirect( $purchase_url );
				exit;
			}
		}

		/**
		 * Adminify Sub Menu
		 */
		public function jltwp_loginfy_submenu(){
			$submenu_position = apply_filters('jltwp_adminify_submenu_position', 2);
			add_submenu_page(
				'wp-adminify-settings',
				esc_html__('Login Customizer', 'loginfy'),
				esc_html__('Login Customizer', 'loginfy'),
				apply_filters('jlt_loginfy_capability', 'manage_options'),
				'loginfy-customizer',
				[$this, 'jlt_loginfy_redirect_customizer'],
				$submenu_position
			);
		}

		/**
		 * Login Customizer Submenu
		 */
		public function jltwp_loginfy_menu() {
			global $submenu;

			$loginfy_url = add_query_arg( array(
					'autofocus[panel]' 	=> 'loginfy_panel',
					'url' 				=> rawurlencode( get_permalink( 'page' ) ),
				),
				admin_url( 'customize.php' )
			);

			//Loginfy Admin Menu
			add_menu_page( __( 'Loginfy - Login Customizer', 'loginfy' ), __( 'Loginfy', 'loginfy' ), 'manage_options', 'loginfy-customizer', [$this, 'settings_page'], LOGINFY_IMAGES . 'menu-icon.svg', 50 );
			add_submenu_page( 'loginfy-customizer', __( 'Customizer', 'loginfy' ), __( 'Customizer', 'loginfy' ), 'manage_options', "loginfy-customizer", [$this, 'settings_page'] );
			// add_submenu_page( 'loginfy-customizer', __( 'Help', 'loginfy' ), __( 'Help', 'loginfy' ), 'manage_options', "loginfy-system-info", array( $this, 'jltwp_loginfy_help' ) );
			// add_submenu_page( 'loginfy-customizer', __( 'Get Pro', 'loginfy' ), __( '↳ ⭐ Get Pro', 'loginfy' ), 'manage_options', 'loginfy-upgrade-pro', '__return_null', 99 );

			// Sub Menu under Apperance
			// $submenu['themes.php'][] = array( 'Loginfy ', 'manage_options', $loginfy_url, 'loginfy' );
			// add_theme_page( __( 'Get Pro', 'loginfy' ), __( '↳ ⭐ Get Pro', 'loginfy' ), 'manage_options', 'loginfy-upgrade-pro', '__return_null', 16 );
		}

		/**
		 * Load settings page content
		 *
		 * @return void
		 */
		public function settings_page() {

			// Build page HTML
			$html  = '<div class="wrap" id="loginfy-customizer">' . "\n";
			$html .= '<h2>' . esc_html__( 'Loginfy - Login Page Customizer', 'loginfy' ) . '</h2>' . "\n";
			$html .= '<p>' . esc_html__( 'Loginfy Customizer - Design your boring WordPress Login page', 'loginfy' ) . '</p>';
			$html .= '<a href="' . get_admin_url() . 'customize.php?url=' . wp_login_url() . '" id="submit" class="button button-primary">' . __( 'Start Customizing!', 'loginfy' ) . '</a>';
			$html .= '</div>' . "\n";

			echo $html;
		}


		public function jltwp_loginfy_help(){
			$html = '<div class="loginfy-system-info-page">';
			$html .= '<h2>Help & Troubleshooting</h2>';
			$html .= sprintf( __( 'Free plugin support is available on the %1$s plugin support forums%2$s.', 'loginfy' ), '<a href="https://wordpress.org/support/plugin/loginfy" target="_blank">', '</a>' );


			$html .="<br />";
			// $html .= 'Found a bug or have a feature request? Please submit an issue <a href="https://jeweltheme.com/contact/" target="_blank">here</a>!';
			$html .= '<pre><textarea rows="25" cols="75" readonly="readonly">';
			$html .=  Helper::get_sysinfo();
			$html .= '</textarea></pre>';
			// $html .= '<input type="button" class="button loginfy-log-file" value="' . __( 'Download Log File', 'loginfy' ) . '"/>';
			// $html .= '<span class="loginfy-file-spinner"><img src="' . admin_url( 'images/wpspin_light.gif' ) . '" /></span>';
			// $html .= '<span class="loginfy-file-text">Loginfy\'s Log File Downloaded Successfully!</span>';
			$html .= '</div>';
			echo $html;
		}

		/**
		 * Register Panels
		 */
		public function jlt_loginfy_register_panels( $wp_customize ) {
			$wp_customize->add_panel(
				'loginfy_panel',
				[
					'title'       => __( 'Loginfy', 'loginfy' ),
					'description' => __( 'Customize Your WordPress Login Page with Loginfy :)', 'loginfy' ),
					'capability'  => apply_filters( 'jlt_loginfy_capability', 'manage_options' ),
					'priority'    => 10,
				]
			);
		}

		/**
		 * Login Customizer Sections
		 *
		 * @param [type] $wp_customize
		 *
		 * @return void
		 */
		public function jlt_loginfy_register_sections( $wp_customize ) {
			jlt_loginfy_sections( $wp_customize );
		}
	}
}
