<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Libs\Helper;
use Loginfy\Inc\Core\Inc\Customize_Model;
if ( !defined( 'ABSPATH' ) ) {
    die;
}
// Cannot access directly.
class Google_Fonts extends Customize_Model {
    public function __construct() {
        $this->google_fonts_customizer();
    }

    public function get_defaults() {
        return [
            'login_google_font' => [
                'font-family' => 'Lato',
                'type'        => 'google',
            ],
        ];
    }

    /**
     * Google Fonts Settings
     *
     * @param [type] $fonts_field
     *
     * @return void
     */
    public function google_fonts_settings( &$fonts_field ) {
        $fonts_field[] = [
            'type'    => 'notice',
            'style'   => 'warning',
            'content' => Helper::loginfy_upgrade_pro(),
        ];
    }

    public function google_fonts_customizer() {
        if ( !class_exists( 'LOGINFY' ) ) {
            return;
        }
        $fonts_field = [];
        $this->google_fonts_settings( $fonts_field );
        /**
         * Section: Google Fonts Section
         */
        \LOGINFY::createSection( $this->prefix, [
            'assign' => 'jlt_loginfy_customizer_fonts_section',
            'title'  => __( 'Google Fonts', 'loginfy' ),
            'fields' => $fonts_field,
        ] );
    }

}
