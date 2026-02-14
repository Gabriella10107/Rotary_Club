<?php

namespace Loginfy\Inc\Core\Inc;

use Loginfy\Inc\Core\LoginCustomizer;
use Loginfy\Inc\Core\Inc\Settings\Templates;
use Loginfy\Inc\Core\Inc\Settings\Logo_Section;
use Loginfy\Inc\Core\Inc\Settings\Layout_Section;
use Loginfy\Inc\Core\Inc\Settings\Form_Section;
use Loginfy\Inc\Core\Inc\Settings\Background_Section;
use Loginfy\Inc\Core\Inc\Settings\Login_Form_Fields;
use Loginfy\Inc\Core\Inc\Settings\Button_Section;
use Loginfy\Inc\Core\Inc\Settings\Others_Section;
use Loginfy\Inc\Core\Inc\Settings\Google_Fonts;
use Loginfy\Inc\Core\Inc\Settings\Error_Messages;
use Loginfy\Inc\Core\Inc\Settings\Custom_CSS_JS;
use Loginfy\Inc\Core\Inc\Settings\Credits_Section;

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! class_exists( 'Customize_Settings' ) ) {

	class Customize_Settings extends Customize_Model {

		public $defaults = [];
		public $options;

		public function __construct() {
			// this should be first so the default values get stored
			$this->login_customizer_options();
			$this->options = (array) get_option( $this->prefix );

			// $options = $this->validation_options( $options );
			parent::__construct( $this->options );
		}


		public function sync_settings_from_adminify() {

			// Already Synceed? bail Early
			if ( get_option( $this->prefix . '_is_synced', false ) ) return;

			// Adminify Settings Class
			$loginfy_login_customizer_class = \WPAdminify\Inc\Admin\AdminSettings::get_instance();

			// Adminify All Settings
			$loginfy_login_customizer = $loginfy_login_customizer_class->get();

			// Get This Module Settings
			$updated_adminify_keys = [];
			foreach( $loginfy_login_customizer as $opt_name => $option_val ){
				$key = str_replace("jltwp_adminify_", "jlt_loginfy_", $opt_name);
				$updated_adminify_keys[$key] = $option_val;
			}

			// Save The Settings
			update_option( $this->prefix,  $updated_adminify_keys);	// Replaced existing keys by new keys

			// Cleanup Adminify Data & Save
			$module_removed_settings = array_diff_key( $loginfy_login_customizer, $this->get_defaults() );
			unset( $module_removed_settings['login_customizer'] );
			update_option( 'jltwp_adminify_login', $module_removed_settings );

			// Operation Done
			update_option( $this->prefix . '_is_synced', true );
		}


		protected function get_defaults() {
			return $this->defaults;
		}

		public function login_customizer_options() {
			if ( ! class_exists( 'LOGINFY' ) ) {
				return;
			}

			// Create customize options
			\LOGINFY::createCustomizeOptions(
				$this->prefix,
				[
					'database'        => 'option',
					'transport'       => 'postMessage',
					'capability'      => 'manage_options',
					'save_defaults'   => true,
					'enqueue_webfont' => true,
					'async_webfont'   => false,
					'output_css'      => true,
				]
			);

			$this->defaults = array_merge( $this->defaults, ( new Templates() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Logo_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Background_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Layout_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Form_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Login_Form_Fields() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Button_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Others_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Google_Fonts() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Error_Messages() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Credits_Section() )->get_defaults() );
			$this->defaults = array_merge( $this->defaults, ( new Custom_CSS_JS() )->get_defaults() );
		}
	}
}
