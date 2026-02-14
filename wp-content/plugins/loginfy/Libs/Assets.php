<?php
		namespace Loginfy\Libs;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Assets' ) ) {

	/**
	 * Assets Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 * @version     1.0.0
	 */
	class Assets {

		/**
		 * Constructor method
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'jlt_loginfy_admin_enqueue_scripts' ), 100 );
		}

		/**
		 * Get environment mode
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function get_mode() {
			return defined( 'WP_DEBUG' ) && WP_DEBUG ? 'development' : 'production';
		}


		/**
		 * Enqueue Scripts
		 *
		 * @method admin_enqueue_scripts()
		 */
		public function jlt_loginfy_admin_enqueue_scripts() {
			// CSS Files .
			wp_enqueue_style( 'loginfy-admin', LOGINFY_ASSETS . 'css/loginfy-sdk.min.css', array( 'dashicons' ), LOGINFY_VER, 'all' );

			// JS Files .
			wp_enqueue_script( 'loginfy-admin', LOGINFY_ASSETS . 'js/loginfy-admin.js', array( 'jquery' ), LOGINFY_VER, true );
			wp_localize_script(
				'loginfy-admin',
				'LOGINFYCORE',
				array(
					'admin_ajax'        => admin_url( 'admin-ajax.php' ),
					'recommended_nonce' => wp_create_nonce( 'jlt_loginfy_recommended_nonce' ),
					'is_premium'        => loginfy()->can_use_premium_code__premium_only() ? true : false,
					'is_agency'         => loginfy()->is_plan( 'agency' ) ? true : false,
				)
			);
		}
	}
}
