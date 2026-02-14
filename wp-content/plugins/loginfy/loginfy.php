<?php

/**
 * Plugin Name: Loginfy
 * Plugin URI:  https://jeweltheme.com
 * Description: WordPress Login Page Customizer Plugin
 * Version:     1.0.4.0
 * Author:      Jewel Theme
 * Author URI:  https://wpadminify.com/loginfy
 * Text Domain: loginfy
 * Domain Path: languages/
 * License:     GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package loginfy
 *  */
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// Exit if accessed directly
$jlt_loginfy_plugin_data = get_file_data( __FILE__, array(
    'Version'     => 'Version',
    'Plugin Name' => 'Plugin Name',
    'Author'      => 'Author',
    'Description' => 'Description',
    'Plugin URI'  => 'Plugin URI',
), false );
// Define Constants.
if ( !defined( 'LOGINFY_NAME' ) ) {
    define( 'LOGINFY_NAME', $jlt_loginfy_plugin_data['Plugin Name'] );
}
if ( !defined( 'LOGINFY_VER' ) ) {
    define( 'LOGINFY_VER', $jlt_loginfy_plugin_data['Version'] );
}
if ( !defined( 'LOGINFY_AUTHOR' ) ) {
    define( 'LOGINFY_AUTHOR', $jlt_loginfy_plugin_data['Author'] );
}
if ( !defined( 'LOGINFY_DESC' ) ) {
    define( 'LOGINFY_DESC', $jlt_loginfy_plugin_data['Author'] );
}
if ( !defined( 'LOGINFY_URI' ) ) {
    define( 'LOGINFY_URI', $jlt_loginfy_plugin_data['Plugin URI'] );
}
if ( !defined( 'LOGINFY_DIR' ) ) {
    define( 'LOGINFY_DIR', __DIR__ );
}
if ( !defined( 'LOGINFY_FILE' ) ) {
    define( 'LOGINFY_FILE', __FILE__ );
}
if ( !defined( 'LOGINFY_SLUG' ) ) {
    define( 'LOGINFY_SLUG', dirname( plugin_basename( __FILE__ ) ) );
}
if ( !defined( 'LOGINFY_BASE' ) ) {
    define( 'LOGINFY_BASE', plugin_basename( __FILE__ ) );
}
if ( !defined( 'LOGINFY_PATH' ) ) {
    define( 'LOGINFY_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}
if ( !defined( 'LOGINFY_URL' ) ) {
    define( 'LOGINFY_URL', trailingslashit( plugins_url( '/', __FILE__ ) ) );
}
if ( !defined( 'LOGINFY_INC' ) ) {
    define( 'LOGINFY_INC', LOGINFY_PATH . '/Inc/' );
}
if ( !defined( 'LOGINFY_LIBS' ) ) {
    define( 'LOGINFY_LIBS', LOGINFY_PATH . 'Libs' );
}
if ( !defined( 'LOGINFY_ASSETS' ) ) {
    define( 'LOGINFY_ASSETS', LOGINFY_URL . 'assets/' );
}
if ( !defined( 'LOGINFY_IMAGES' ) ) {
    define( 'LOGINFY_IMAGES', LOGINFY_ASSETS . 'images/' );
}
// Autoload Files.
include_once LOGINFY_DIR . '/vendor/autoload.php';
if ( !function_exists( 'loginfy' ) ) {
    // Create a helper function for easy SDK access.
    function loginfy() {
        global $loginfy;
        if ( !isset( $loginfy ) ) {
            // Activate multisite network integration.
            if ( !defined( 'WP_FS__PRODUCT_15290_MULTISITE' ) ) {
                define( 'WP_FS__PRODUCT_15290_MULTISITE', true );
            }
            // Include Freemius SDK.
            // SDK is auto-loaded through composer
            $loginfy = fs_dynamic_init( array(
                'id'               => '15290',
                'slug'             => 'loginfy',
                'first-path'       => admin_url( 'customize.php?autofocus[panel]=loginfy_panel' ),
                'premium_slug'     => 'loginfy-pro',
                'type'             => 'plugin',
                'premium_suffix'   => 'Pro',
                'public_key'       => 'pk_970cd387f2278010a4df61a57eaf0',
                'is_premium'       => false,
                'has_addons'       => false,
                'has_paid_plans'   => true,
                'is_org_compliant' => true,
                'navigation'       => 'tabs',
                'has_affiliation'  => 'selected',
                'menu'             => array(
                    'account'     => false,
                    'support'     => false,
                    'contact'     => false,
                    'pricing'     => true,
                    'network'     => false,
                    'affiliation' => false,
                ),
                'is_live'          => true,
            ) );
        }
        return $loginfy;
    }

    // Init Freemius.
    loginfy();
    // Signal that SDK was initiated.
    do_action( 'loginfy_loaded' );
}
if ( !class_exists( '\\Loginfy\\Loginfy' ) ) {
    // Instantiate Loginfy Class.
    include_once LOGINFY_DIR . '/class-loginfy.php';
}
// Activation and Deactivation hooks.
if ( class_exists( '\\Loginfy\\Loginfy' ) ) {
    register_activation_hook( LOGINFY_FILE, array('\\Loginfy\\Loginfy', 'jlt_loginfy_activation_hook') );
    // register_deactivation_hook( LOGINFY_FILE, array( '\\Loginfy\\Loginfy', 'jlt_loginfy_deactivation_hook' ) );
}