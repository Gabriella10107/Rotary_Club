<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Inc\Core\Inc\Customize_Model;

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

class Custom_CSS_JS extends Customize_Model {

	public function __construct() {
		$this->custom_css_js_customizer();
	}

	public function get_defaults() {
		return [
			'jlt_loginfy_customizer_custom_css' => '',
			'jlt_loginfy_customizer_custom_js'  => '',
		];
	}


	public function custom_css_js_fields_settings( &$custom_css_js_fields ) {
		$custom_css_js_fields[] = [
			'id'       => 'jlt_loginfy_customizer_custom_css',
			'type'     => 'code_editor',
			'title'    => 'CSS Editor',
			'subtitle' => sprintf( __( 'Custom CSS doesn\'t work on live. Save Settings and Preview %1$s login%2$s page or hit refresh after save Customer.', 'loginfy' ), '<a href="' . wp_login_url() . '"title="Login" target="_blank">', '</a>' ),
			'desc'     => __( 'Don\'t place &lt;style&gt;&lt;/style&gt; tag inside editor.', 'loginfy' ),
			'settings' => [
				'theme' => 'monokai',
				'mode'  => 'css',
			],
			'sanitize' => false,
			'default'  => $this->get_default_field( 'jlt_loginfy_customizer_custom_css' ),
		];

		$custom_css_js_fields[] = [
			'id'       => 'jlt_loginfy_customizer_custom_js',
			'type'     => 'code_editor',
			'title'    => __( 'JS Editor', 'loginfy' ),
			'subtitle' => sprintf( __( 'Custom JS doesn\'t work on live. Save Settings and Preview %1$s login%2$s page or hit refresh after save Customer.', 'loginfy' ), '<a href="' . wp_login_url() . '"title="Login" target="_blank">', '</a>' ),
			'desc'     => __( 'Don\'t place &lt;script&gt;&lt;/script&gt; tag inside editor.', 'loginfy' ),
			'settings' => [
				'theme' => 'dracula',
				'mode'  => 'javascript',
			],
			'sanitize' => false,
			'default'  => $this->get_default_field( 'jlt_loginfy_customizer_custom_js' ),
		];
	}

	public function custom_css_js_customizer() {
		if ( ! class_exists( 'LOGINFY' ) ) {
			return;
		}

		$custom_css_js_fields = [];
		$this->custom_css_js_fields_settings( $custom_css_js_fields );

		/**
		 * Section: Custom CSS & JS Section
		 */
		\LOGINFY::createSection(
			$this->prefix,
			[
				'assign' => 'jlt_loginfy_customizer_custom_css_js_section',
				'title'  => __( 'Custom CSS & JS', 'loginfy' ),
				'fields' => $custom_css_js_fields,
			]
		);
	}
}
