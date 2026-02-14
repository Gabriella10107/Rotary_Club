<?php

namespace Loginfy\Inc\Core\Inc\Settings;

use Loginfy\Libs\Helper;
use Loginfy\Inc\Core\Inc\Customize_Model;
if ( !defined( 'ABSPATH' ) ) {
    die;
}
// Cannot access directly.
class Others_Section extends Customize_Model {
    public function __construct() {
        $this->others_section_customizer();
    }

    public function get_defaults() {
        return [
            'login_form_button_remember_me'        => false,
            'login_form_disable_login_shake'       => false,
            'login_form_disable_register'          => false,
            'login_form_disable_lost_pass'         => false,
            'login_form_disable_privacy_policy'    => false,
            'login_form_disable_language_switcher' => false,
            'login_form_disable_back_to_site'      => false,
        ];
    }

    public function login_form_others_settings( &$login_form_others ) {
        $login_form_others[] = [
            'id'       => 'login_form_button_remember_me',
            'type'     => 'switcher',
            'title'    => __( 'Hide Remember Me?', 'loginfy' ),
            'text_on'  => __( 'Show', 'loginfy' ),
            'text_off' => __( 'Hide', 'loginfy' ),
            'default'  => $this->get_default_field( 'login_form_button_remember_me' ),
            'class'    => 'loginfy-cs',
        ];
        $login_form_others[] = [
            'id'       => 'login_form_disable_login_shake',
            'type'     => 'switcher',
            'title'    => __( 'Disable Login shake?', 'loginfy' ),
            'default'  => $this->get_default_field( 'login_form_disable_login_shake' ),
            'text_on'  => __( 'Yes', 'loginfy' ),
            'text_off' => __( 'No', 'loginfy' ),
            'class'    => 'loginfy-cs',
        ];
        if ( get_option( 'users_can_register' ) == 1 ) {
            $login_form_others[] = [
                'id'       => 'login_form_disable_register',
                'type'     => 'switcher',
                'title'    => __( 'Disable Register?', 'loginfy' ),
                'default'  => $this->get_default_field( 'login_form_disable_register' ),
                'text_on'  => __( 'Yes', 'loginfy' ),
                'text_off' => __( 'No', 'loginfy' ),
                'class'    => 'loginfy-cs',
            ];
        }
        $login_form_others[] = [
            'id'       => 'login_form_disable_lost_pass',
            'type'     => 'switcher',
            'title'    => __( 'Disable Lost Password?', 'loginfy' ),
            'default'  => $this->get_default_field( 'login_form_disable_lost_pass' ),
            'text_on'  => __( 'Yes', 'loginfy' ),
            'text_off' => __( 'No', 'loginfy' ),
            'class'    => 'loginfy-cs',
        ];
        $login_form_others[] = [
            'id'      => 'login_form_disable_back_to_site',
            'type'    => 'notice',
            'title'   => __( 'Disable "Back to Website" ?', 'loginfy' ),
            'content' => Helper::loginfy_upgrade_pro( '  ' ),
            'class'   => 'loginfy-cs',
        ];
        $login_form_others[] = [
            'id'      => 'login_form_disable_privacy_policy',
            'type'    => 'notice',
            'title'   => __( 'Disable "Privary Policy" ?', 'loginfy' ),
            'content' => Helper::loginfy_upgrade_pro( '  ' ),
            'class'   => 'loginfy-cs',
        ];
        $login_form_others[] = [
            'id'      => 'login_form_disable_language_switcher',
            'type'    => 'notice',
            'title'   => __( 'Disable "Language Switcher" ?', 'loginfy' ),
            'content' => Helper::loginfy_upgrade_pro( '  ' ),
            'class'   => 'loginfy-cs',
        ];
    }

    public function others_section_customizer() {
        if ( !class_exists( 'LOGINFY' ) ) {
            return;
        }
        $login_form_others = [];
        $this->login_form_others_settings( $login_form_others );
        /**
         * Section: Others Settings
         */
        \LOGINFY::createSection( $this->prefix, [
            'assign' => 'jlt_loginfy_customizer_login_others_section',
            'title'  => __( 'Others', 'loginfy' ),
            'fields' => $login_form_others,
        ] );
    }

}
