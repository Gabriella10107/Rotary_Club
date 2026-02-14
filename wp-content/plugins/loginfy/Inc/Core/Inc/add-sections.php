<?php

namespace Loginfy\Inc\Core;

// no direct access allowed
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function jlt_loginfy_sections( $wp_customize ) {
	$wp_customize->add_section(
		'jlt_loginfy_customizer_template_section',
		[
			'title' => esc_html__( 'Templates', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_logo_section',
		[
			'title' => esc_html__( 'Logo', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_bg_section',
		[
			'title' => esc_html__( 'Background', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_layout_section',
		[
			'title' => esc_html__( 'Layout', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_login_form_section',
		[
			'title' => esc_html__( 'Login Form', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_login_form_fields_section',
		[
			'title' => esc_html__( 'Form Fields', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_login_form_button_section',
		[
			'title' => esc_html__( 'Button', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_login_others_section',
		[
			'title' => esc_html__( 'Others', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_fonts_section',
		[
			'title' => esc_html__( 'Google Fonts', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_error_messages_section',
		[
			'title' => esc_html__( 'Error Messages', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_custom_css_js_section',
		[
			'title' => esc_html__( 'Custom CSS & JS', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);

	$wp_customize->add_section(
		'jlt_loginfy_customizer_credits_section',
		[
			'title' => esc_html__( 'Credits', 'loginfy' ),
			'panel' => 'loginfy_panel',
		]
	);
};
