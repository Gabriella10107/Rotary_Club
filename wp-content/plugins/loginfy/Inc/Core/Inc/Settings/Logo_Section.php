<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Libs\Helper;
use Loginfy\Inc\Core\Inc\Customize_Model;
// Cannot access directly.
if ( !defined( 'ABSPATH' ) ) {
    die;
}
class Logo_Section extends Customize_Model {
    public function __construct() {
        $this->logo_section_customizer();
    }

    public function get_defaults() {
        return [
            'show_logo'         => true,
            'logo_settings'     => 'image-only',
            'logo_image'        => '',
            'logo_text'         => get_bloginfo( 'name' ),
            'logo_login_url'    => [
                'url'    => esc_url( site_url( '/' ) ),
                'text'   => esc_attr( get_bloginfo( 'name' ) ),
                'target' => '_blank',
            ],
            'login_page_title'  => '',
            'login_title_style' => [
                'logo_heigh_width'       => [
                    'width'  => '',
                    'height' => '',
                    'unit'   => '%',
                ],
                'login_title_typography' => [
                    'font-family' => 'Lato',
                    'font-weight' => '900',
                    'subset'      => 'latin',
                    'type'        => 'google',
                ],
                'logo_padding'           => [
                    'top'    => '',
                    'right'  => '',
                    'bottom' => '',
                    'left'   => '',
                ],
            ],
        ];
    }

    public function logo_section_settings( &$logo_settings ) {
        $login_title_style = [];
        $this->login_title_style_settings( $login_title_style );
        $logo_settings[] = [
            'id'       => 'show_logo',
            'type'     => 'switcher',
            'title'    => __( 'Display Logo?', 'loginfy' ),
            'default'  => $this->get_default_field( 'show_logo' ),
            'text_on'  => __( 'Yes', 'loginfy' ),
            'text_off' => __( 'No', 'loginfy' ),
            'class'    => 'loginfy-cs',
        ];
        $logo_settings[] = [
            'id'         => 'logo_settings',
            'type'       => 'button_set',
            'title'      => __( 'Logo Type', 'loginfy' ),
            'help'       => __( 'Select the way you want to display Logo', 'loginfy' ),
            'options'    => [
                'text-only'  => __( 'Text', 'loginfy' ),
                'image-only' => __( 'Image', 'loginfy' ),
                'both'       => __( 'Image & Text', 'loginfy' ),
                'none'       => __( 'None', 'loginfy' ),
            ],
            'default'    => $this->get_default_field( 'logo_settings' ),
            'dependency' => ['show_logo', '==', 'true'],
        ];
        $logo_settings[] = [
            'id'           => 'logo_image',
            'type'         => 'media',
            'title'        => __( 'Logo Image', 'loginfy' ),
            'library'      => 'image',
            'preview'      => true,
            'preview_size' => 'full',
            'dependency'   => [['show_logo|logo_settings|logo_settings', '==|!=|!=', 'true|text-only|none']],
        ];
        $logo_settings[] = [
            'id'          => 'logo_text',
            'type'        => 'text',
            'title'       => __( 'Text Logo', 'loginfy' ),
            'default'     => $this->get_default_field( 'logo_text' ),
            'placeholder' => __( 'Enter Logo Text here', 'loginfy' ),
            'dependency'  => [[
                'show_logo|logo_settings|logo_settings',
                '==|!=|!=',
                'true|image-only|none',
                true
            ]],
        ];
        $logo_settings[] = [
            'id'         => 'logo_login_url',
            'type'       => 'link',
            'title'      => 'Logo Link',
            'default'    => sanitize_text_field( $this->get_default_field( 'logo_login_url' ) ),
            'dependency' => [['show_logo|logo_settings', '==|!=', 'true|none']],
        ];
        $logo_settings[] = [
            'id'          => 'login_page_title',
            'type'        => 'text',
            'title'       => __( 'Login Page Title', 'loginfy' ),
            'placeholder' => __( 'Enter Login Page Title here', 'loginfy' ),
        ];
        $logo_settings[] = [
            'type'       => 'heading',
            'content'    => __( 'Logo Style', 'loginfy' ),
            'dependency' => [['show_logo', '==', 'true']],
        ];
        $logo_settings[] = [
            'id'         => 'login_title_style',
            'type'       => 'fieldset',
            'dependency' => [['show_logo', '==', 'true']],
            'fields'     => $login_title_style,
        ];
    }

    public function login_title_style_settings( &$login_title_style ) {
        $login_title_style[] = [
            'id'          => 'logo_heigh_width',
            'type'        => 'dimensions',
            'width_icon'  => 'width',
            'height_icon' => 'height',
            'units'       => [
                'px',
                '%',
                'em',
                'rem',
                'pt'
            ],
            'default'     => $this->get_default_field( 'login_title_style' )['logo_heigh_width'],
        ];
        $login_title_style[] = [
            'type'       => 'notice',
            'title'      => __( 'Title Typography', 'loginfy' ),
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => [[
                'show_logo|logo_settings|logo_settings',
                '==|!=|!=',
                'true|image-only|none',
                true
            ]],
        ];
        $login_title_style[] = [
            'type'    => 'notice',
            'title'   => __( 'Padding', 'loginfy' ),
            'style'   => 'warning',
            'content' => Helper::loginfy_upgrade_pro(),
        ];
    }

    public function logo_section_customizer() {
        if ( !class_exists( 'LOGINFY' ) ) {
            return;
        }
        $logo_settings = [];
        $this->logo_section_settings( $logo_settings );
        /**
         * Section: Logo Section
         */
        \LOGINFY::createSection( $this->prefix, [
            'assign' => 'jlt_loginfy_customizer_logo_section',
            'title'  => __( 'Logo', 'loginfy' ),
            'fields' => $logo_settings,
        ] );
    }

}
