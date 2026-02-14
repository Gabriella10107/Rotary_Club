<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*
 * @version       1.0.0
 * @package       Loginfy
 * @license       Copyright Loginfy
 */

if ( ! function_exists( 'jlt_loginfy_option' ) ) {
	/**
	 * Get setting database option
	 *
	 * @param string $section default section name jlt_loginfy_general .
	 * @param string $key .
	 * @param string $default .
	 *
	 * @return string
	 */
	function jlt_loginfy_option( $section = 'jlt_loginfy_general', $key = '', $default = '' ) {
		$settings = get_option( $section );

		return isset( $settings[ $key ] ) ? $settings[ $key ] : $default;
	}
}

if ( ! function_exists( 'jlt_loginfy_exclude_pages' ) ) {
	/**
	 * Get exclude pages setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function jlt_loginfy_exclude_pages() {
		return jlt_loginfy_option( 'jlt_loginfy_triggers', 'exclude_pages', array() );
	}
}

if ( ! function_exists( 'jlt_loginfy_exclude_pages_except' ) ) {
	/**
	 * Get exclude pages except setting option data
	 *
	 * @return string|array
	 *
	 * @version 1.0.0
	 */
	function jlt_loginfy_exclude_pages_except() {
		return jlt_loginfy_option( 'jlt_loginfy_triggers', 'exclude_pages_except', array() );
	}
}




