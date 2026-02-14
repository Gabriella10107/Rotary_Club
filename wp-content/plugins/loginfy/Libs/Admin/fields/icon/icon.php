<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: icon
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'LOGINFY_Field_icon' ) ) {
  class LOGINFY_Field_icon extends LOGINFY_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'button_title' => esc_html__( 'Add Icon', 'loginfy' ),
        'remove_title' => esc_html__( 'Remove Icon', 'loginfy' ),
      ) );

      echo $this->field_before();

      $nonce  = wp_create_nonce( 'loginfy_icon_nonce' );
      $hidden = ( empty( $this->value ) ) ? ' hidden' : '';

      echo '<div class="loginfy-icon-select">';
      echo '<span class="loginfy-icon-preview'. esc_attr( $hidden ) .'"><i class="'. esc_attr( $this->value ) .'"></i></span>';
      echo '<a href="#" class="button button-primary loginfy-icon-add" data-nonce="'. esc_attr( $nonce ) .'">'. $args['button_title'] .'</a>';
      echo '<a href="#" class="button loginfy-warning-primary loginfy-icon-remove'. esc_attr( $hidden ) .'">'. $args['remove_title'] .'</a>';
      echo '<input type="hidden" name="'. esc_attr( $this->field_name() ) .'" value="'. esc_attr( $this->value ) .'" class="loginfy-icon-value"'. $this->field_attributes() .' />';
      echo '</div>';

      echo $this->field_after();

    }

    public function enqueue() {
      add_action( 'admin_footer', array( 'LOGINFY_Field_icon', 'add_footer_modal_icon' ) );
      add_action( 'customize_controls_print_footer_scripts', array( 'LOGINFY_Field_icon', 'add_footer_modal_icon' ) );
    }

    public static function add_footer_modal_icon() {
    ?>
      <div id="loginfy-modal-icon" class="loginfy-modal loginfy-modal-icon hidden">
        <div class="loginfy-modal-table">
          <div class="loginfy-modal-table-cell">
            <div class="loginfy-modal-overlay"></div>
            <div class="loginfy-modal-inner">
              <div class="loginfy-modal-title">
                <?php esc_html_e( 'Add Icon', 'loginfy' ); ?>
                <div class="loginfy-modal-close loginfy-icon-close"></div>
              </div>
              <div class="loginfy-modal-header">
                <input type="text" placeholder="<?php esc_html_e( 'Search...', 'loginfy' ); ?>" class="loginfy-icon-search" />
              </div>
              <div class="loginfy-modal-content">
                <div class="loginfy-modal-loading"><div class="loginfy-loading"></div></div>
                <div class="loginfy-modal-load"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php
    }

  }
}
