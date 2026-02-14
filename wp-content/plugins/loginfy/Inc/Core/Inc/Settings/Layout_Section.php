<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Libs\Helper;
use Loginfy\Inc\Core\Inc\Customize_Model;
if ( !defined( 'ABSPATH' ) ) {
    die;
}
// Cannot access directly.
class Layout_Section extends Customize_Model {
    public function __construct() {
        $this->layout_customizer();
    }

    public function get_defaults() {
        return [
            'alignment_login_width'             => '',
            'alignment_login_column'            => '',
            'alignment_login_horizontal'        => '',
            'alignment_login_vertical'          => '',
            'alignment_login_bg_type'           => 'color',
            'alignment_login_bg_color'          => [
                'background-color'      => '',
                'background-position'   => 'center center',
                'background-repeat'     => 'repeat-x',
                'background-attachment' => 'fixed',
                'background-size'       => 'cover',
            ],
            'alignment_login_bg_gradient_color' => [
                'background-color'              => '',
                'background-gradient-color'     => '',
                'background-gradient-direction' => '',
            ],
            'alignment_login_bg_skew'           => 0,
        ];
    }

    public function layout_fields_settings( &$layout_fields ) {
        $layout_images = LOGINFY_IMAGES . 'layouts/';
        $layout_fields[] = [
            'id'      => 'alignment_login_width',
            'type'    => 'image_select',
            'title'   => __( 'Layout', 'loginfy' ),
            'options' => [
                'fullwidth'        => $layout_images . 'width-full.png',
                'width_two_column' => $layout_images . 'width-2column.png',
            ],
            'default' => $this->get_default_field( 'alignment_login_width' ),
        ];
        $layout_fields[] = [
            'id'         => 'alignment_login_column',
            'type'       => 'image_select',
            'title'      => __( 'Column Alignment', 'loginfy' ),
            'options'    => [
                'top'    => $layout_images . 'column-top.png',
                'right'  => $layout_images . 'column-right.png',
                'bottom' => $layout_images . 'column-bottom.png',
                'left'   => $layout_images . 'column-left.png',
            ],
            'default'    => $this->get_default_field( 'alignment_login_column' ),
            'dependency' => ['alignment_login_width', '==', 'width_two_column'],
        ];
        $layout_fields[] = [
            'id'      => 'alignment_login_horizontal',
            'type'    => 'image_select',
            'title'   => __( 'Horizontal Alignment', 'loginfy' ),
            'options' => [
                'left_center'   => $layout_images . 'form-left-center.png',
                'center_center' => $layout_images . 'form-center.png',
                'right_center'  => $layout_images . 'form-right-center.png',
            ],
            'default' => $this->get_default_field( 'alignment_login_horizontal' ),
        ];
        $layout_fields[] = [
            'id'      => 'alignment_login_vertical',
            'type'    => 'image_select',
            'title'   => __( 'Vertical Alignment', 'loginfy' ),
            'options' => [
                'center_top'    => $layout_images . 'form-center-top.png',
                'center_center' => $layout_images . 'form-center-center.png',
                'center_bottom' => $layout_images . 'form-center-bottom.png',
            ],
            'default' => $this->get_default_field( 'alignment_login_vertical' ),
        ];
        $layout_fields[] = [
            'id'      => 'alignment_login_bg_type',
            'type'    => 'button_set',
            'title'   => __( 'Side Background', 'loginfy' ),
            'options' => [
                'color'    => __( 'Color ', 'loginfy' ),
                'gradient' => __( 'Gradient', 'loginfy' ),
            ],
            'default' => $this->get_default_field( 'alignment_login_bg_type' ),
            'class'   => 'loginfy-cs',
        ];
        $layout_fields[] = [
            'id'         => 'alignment_login_bg_color',
            'type'       => 'background',
            'default'    => $this->get_default_field( 'alignment_login_bg_color' ),
            'dependency' => [
                'alignment_login_bg_type',
                '==',
                'color',
                true
            ],
        ];
        $layout_fields[] = [
            'type'       => 'notice',
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => [
                'alignment_login_bg_type',
                '==',
                'gradient',
                true
            ],
        ];
        $layout_fields[] = [
            'type'       => 'notice',
            'title'      => __( 'Skew', 'loginfy' ),
            'style'      => 'warning',
            'content'    => Helper::loginfy_upgrade_pro(),
            'dependency' => [
                'alignment_login_width',
                '==',
                'fullwidth',
                true
            ],
        ];
    }

    public function layout_customizer() {
        if ( !class_exists( 'LOGINFY' ) ) {
            return;
        }
        $layout_fields = [];
        $this->layout_fields_settings( $layout_fields );
        /**
         * Section: Layout Section
         */
        \LOGINFY::createSection( $this->prefix, [
            'assign' => 'jlt_loginfy_customizer_layout_section',
            'title'  => __( 'Layout', 'loginfy' ),
            'fields' => $layout_fields,
        ] );
    }

}
