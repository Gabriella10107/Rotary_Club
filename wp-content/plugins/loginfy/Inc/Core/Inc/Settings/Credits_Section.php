<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Inc\Core\Inc\Customize_Model;

if ( ! defined( 'ABSPATH' ) ) {
	 die;
} // Cannot access directly.

class Credits_Section extends Customize_Model {

	public function __construct() {
		$this->credits_customizer();
	}

	public function get_defaults() {
		return [
			'jlt_loginfy_credits' => true,
			'credits_text_color'     => '',
			'credits_logo_position'  => [
				'background-position' => 'right bottom',
			],
		];
	}



	public function credits_settings( &$credit_fields ) {
		$credit_fields[] = [
			'type'    => 'heading',
			'content' => __( 'Show Some Love', 'loginfy' ),
		];
		$credit_fields[] = [
			'type'    => 'notice',
			'style'   => 'normal',
			'content' => __('Show some love and help others to learn about this free plugin by adding a Powered by Loginfy Logo to your login page', 'loginfy' ),
		];
		$credit_fields[] = [
			'id'       => 'jlt_loginfy_credits',
			'type'     => 'switcher',
			'title'    => __( 'Enable Credits?', 'loginfy' ),
			'text_on'  => __( 'Yes', 'loginfy' ),
			'text_off' => __( 'No', 'loginfy' ),
			'default'  => $this->get_default_field( 'jlt_loginfy_credits' ),
			'class'    => 'loginfy-cs',
		];
		$credit_fields[] = [
			'id'         => 'credits_text_color',
			'type'       => 'color',
			'title'      => __( 'Text Color', 'loginfy' ),
			'class'      => 'loginfy-cs',
			'dependency' => [ 'jlt_loginfy_credits', '==', 'true' ],
		];
		// array(
		// 'id'      => 'jlt_loginfy_customizer_credits_logo_color',
		// 'type'    => 'color',
		// 'title'   => 'Logo Color',
		// 'dependency' => array('jlt_loginfy_credits', '==', 'true'),
		// ),
		$credit_fields[] = [
			'id'                    => 'credits_logo_position',
			'type'                  => 'background',
			'title'                 => __( 'Position', 'loginfy' ),
			'background_color'      => false,
			'background_image'      => false,
			'background_position'   => true,
			'background_repeat'     => false,
			'background_attachment' => false,
			'background_size'       => false,
			'background_origin'     => false,
			'background_clip'       => false,
			'background_blend_mode' => false,
			'background_gradient'   => false,
			'default'               => $this->get_default_field( 'credits_logo_position' ),
			'class'                 => 'loginfy-cs',
			'dependency'            => [ 'jlt_loginfy_credits', '==', 'true' ],
		];
	}


	public function credits_customizer() {
		if ( ! class_exists( 'LOGINFY' ) ) {
			return;
		}

		$credit_fields = [];
		$this->credits_settings( $credit_fields );

		/**
		 * Section: Credits Section
		 */
		\LOGINFY::createSection(
			$this->prefix,
			[
				'assign' => 'jlt_loginfy_customizer_credits_section',
				'title'  => __( 'Credits', 'loginfy' ),
				'fields' => $credit_fields,
			]
		);
	}
}
