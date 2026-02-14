<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Inc\Core\Inc\Customize_Model;

// Cannot access directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class Templates extends Customize_Model {


	public function __construct() {
		$this->template_customizer();
	}

	public function get_defaults() {
		return [
			'templates' => 'template-01',
		];
	}

	public static function get_default_templates() {
		$templates_images = LOGINFY_IMAGES . 'templates/';

		return [
			'template-01' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-01.png', LOGINFY_BASE ) ),
			'template-02' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-02.png', LOGINFY_BASE ) ),
			'template-03' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-03.png', LOGINFY_BASE ) ),
			'template-04' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-04.png', LOGINFY_BASE ) ),
			'template-05' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-05.png', LOGINFY_BASE ) ),
			'template-06' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-06.png', LOGINFY_BASE ) ),
			'template-07' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-07.png', LOGINFY_BASE ) ),
			'template-08' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-08.png', LOGINFY_BASE ) ),
			'template-09' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-09.png', LOGINFY_BASE ) ),
			'template-10' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-10.png', LOGINFY_BASE ) ),
			'template-11' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-11.png', LOGINFY_BASE ) ),
			'template-12' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-12.png', LOGINFY_BASE ) ),
			'template-13' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-13.png', LOGINFY_BASE ) ),
			'template-14' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-14.png', LOGINFY_BASE ) ),
			'template-15' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-15.png', LOGINFY_BASE ) ),
			'template-16' => esc_url( apply_filters( 'loginfy_customizer_bg', $templates_images . 'template-16.png', LOGINFY_BASE ) ),
		];
	}

	public function template_customizer() {
		if ( ! class_exists( 'LOGINFY' ) ) {
			return;
		}

		/**
		 * Section: Templates
		 */
		\LOGINFY::createSection(
			$this->prefix,
			[
				'assign' => 'jlt_loginfy_customizer_template_section',
				'title'  => __( 'Templates', 'loginfy' ),
				'fields' => [
					[
						'id'      => 'templates',
						'type'    => 'image_select',
						'title'   => __( 'Templates', 'loginfy' ),
						'options' => self::get_default_templates(),
						'class'   => loginfy()->can_use_premium_code__premium_only() ? 'upgrade-to-pro' : '',
						'default' => $this->get_default_field( 'templates' ),
					],
				],
			]
		);
	}
}
