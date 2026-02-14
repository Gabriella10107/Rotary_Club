<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Libs\Helper;
use Loginfy\Inc\Core\Inc\Customize_Model;
if ( !defined( 'ABSPATH' ) ) {
    die;
}
// Cannot access directly.
class Background_Section extends Customize_Model {
    public function __construct() {
        $this->adminify_background_customizer();
    }

    public function get_defaults() {
        return [
            'jlt_loginfy_login_bg_video_type'             => '',
            'jlt_loginfy_login_bg_video_self_hosted'      => '',
            'jlt_loginfy_login_bg_video_youtube'          => '',
            'jlt_loginfy_login_bg_video_loop'             => true,
            'jlt_loginfy_login_bg_video_poster'           => '',
            'jlt_loginfy_login_bg_slideshow'              => '',
            'jlt_loginfy_login_bg_type'                   => 'color_image',
            'jlt_loginfy_login_bg_color_opt'              => 'color',
            'jlt_loginfy_login_bg_color'                  => [
                'background-color'      => '',
                'background-position'   => 'center center',
                'background-repeat'     => 'repeat-x',
                'background-attachment' => 'fixed',
                'background-size'       => 'cover',
            ],
            'jlt_loginfy_login_gradient_bg'               => [
                'background-color'              => '',
                'background-gradient-color'     => '',
                'background-gradient-direction' => '',
                'background-position'           => 'center center',
                'background-repeat'             => 'repeat-x',
                'background-attachment'         => 'fixed',
                'background-size'               => 'cover',
                'background-origin'             => 'border-box',
                'background-clip'               => 'padding-box',
                'background-blend-mode'         => 'normal',
            ],
            'jlt_loginfy_login_bg_overlay_type'           => '',
            'jlt_loginfy_login_bg_overlay_color'          => '',
            'jlt_loginfy_login_bg_overlay_gradient_color' => '',
            'jlt_loginfy_login_overlay_opacity'           => '',
        ];
    }

    /**
     * Background Settings
     */
    public function login_customizer_bg_settings( &$bg_fields ) {
        $bg_fields[] = [
            'id'      => 'jlt_loginfy_login_bg_type',
            'type'    => 'button_set',
            'options' => [
                'color_image' => __( 'Color/Image', 'loginfy' ),
                'video'       => __( 'Video', 'loginfy' ),
                'slideshow'   => __( 'Slideshow', 'loginfy' ),
            ],
            'default' => $this->get_default_field( 'jlt_loginfy_login_bg_type' ),
        ];
        $bg_fields[] = [
            'id'         => 'jlt_loginfy_login_bg_color_opt',
            'type'       => 'button_set',
            'options'    => [
                'color'    => __( 'Color ', 'loginfy' ),
                'gradient' => __( 'Gradient', 'loginfy' ),
            ],
            'default'    => $this->get_default_field( 'jlt_loginfy_login_bg_color_opt' ),
            'dependency' => [
                'jlt_loginfy_login_bg_type',
                '==',
                'color_image',
                true
            ],
        ];
        $bg_fields[] = [
            'id'         => 'jlt_loginfy_login_bg_color',
            'type'       => 'background',
            'title'      => 'Background',
            'default'    => $this->get_default_field( 'jlt_loginfy_login_bg_color' ),
            'dependency' => [
                'jlt_loginfy_login_bg_type|jlt_loginfy_login_bg_color_opt',
                '==|==',
                'color_image|color',
                true
            ],
        ];
        $bg_fields[] = [
            'type'       => 'notice',
            'title'      => __( 'Background', 'loginfy' ),
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => [
                'jlt_loginfy_login_bg_type|jlt_loginfy_login_bg_color_opt',
                '==|==',
                'color_image|gradient',
                true
            ],
        ];
        $bg_fields[] = [
            'type'       => 'notice',
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => ['jlt_loginfy_login_bg_type', 'any', 'video,slideshow'],
        ];
        $bg_fields[] = [
            'id'      => 'jlt_loginfy_login_bg_overlay_type',
            'type'    => 'button_set',
            'title'   => __( 'Overlay', 'loginfy' ),
            'options' => [
                'color'    => __( 'Color ', 'loginfy' ),
                'gradient' => __( 'Gradient', 'loginfy' ),
            ],
            'default' => $this->get_default_field( 'jlt_loginfy_login_bg_overlay_type' ),
        ];
        $bg_fields[] = [
            'id'                    => 'jlt_loginfy_login_bg_overlay_color',
            'type'                  => 'background',
            'background_image'      => false,
            'background_position'   => false,
            'background_repeat'     => false,
            'background_attachment' => false,
            'background_size'       => false,
            'default'               => $this->get_default_field( 'jlt_loginfy_login_bg_overlay_color' ),
            'dependency'            => [
                'jlt_loginfy_login_bg_overlay_type',
                '==',
                'color',
                true
            ],
        ];
        $bg_fields[] = [
            'type'       => 'notice',
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => ['jlt_loginfy_login_bg_overlay_type', '==', 'gradient'],
        ];
        $bg_fields[] = [
            'id'         => 'jlt_loginfy_login_overlay_opacity',
            'type'       => 'slider',
            'title'      => __( 'Overlay Opacity', 'loginfy' ),
            'dependency' => [
                'jlt_loginfy_login_bg_overlay_type',
                '!=',
                '',
                true
            ],
            'default'    => $this->get_default_field( 'jlt_loginfy_login_overlay_opacity' ),
        ];
    }

    public function adminify_background_customizer() {
        if ( !class_exists( 'LOGINFY' ) ) {
            return;
        }
        $bg_fields = [];
        $this->login_customizer_bg_settings( $bg_fields );
        /**
         * Section: Background Section
         */
        \LOGINFY::createSection( $this->prefix, [
            'assign' => 'jlt_loginfy_customizer_bg_section',
            'title'  => __( 'Background', 'loginfy' ),
            'fields' => $bg_fields,
        ] );
    }

}
