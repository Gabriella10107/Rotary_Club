<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: repeater
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'LOGINFY_Field_repeater' ) ) {
  class LOGINFY_Field_repeater extends LOGINFY_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $args = wp_parse_args( $this->field, array(
        'max'          => 0,
        'min'          => 0,
        'button_title' => '<i class="fas fa-plus-circle"></i>',
      ) );

      if ( preg_match( '/'. preg_quote( '['. $this->field['id'] .']' ) .'/', $this->unique ) ) {

        echo '<div class="loginfy-notice loginfy-notice-danger">'. esc_html__( 'Error: Field ID conflict.', 'loginfy' ) .'</div>';

      } else {

        echo $this->field_before();

        echo '<div class="loginfy-repeater-item loginfy-repeater-hidden" data-depend-id="'. esc_attr( $this->field['id'] ) .'">';
        echo '<div class="loginfy-repeater-content">';
        foreach ( $this->field['fields'] as $field ) {

          $field_default = ( isset( $field['default'] ) ) ? $field['default'] : '';
          $field_unique  = ( ! empty( $this->unique ) ) ? $this->unique .'['. $this->field['id'] .'][0]' : $this->field['id'] .'[0]';

          LOGINFY::field( $field, $field_default, '___'. $field_unique, 'field/repeater' );

        }
        echo '</div>';
        echo '<div class="loginfy-repeater-helper">';
        echo '<div class="loginfy-repeater-helper-inner">';
        echo '<i class="loginfy-repeater-sort fas fa-arrows-alt"></i>';
        echo '<i class="loginfy-repeater-clone far fa-clone"></i>';
        echo '<i class="loginfy-repeater-remove loginfy-confirm fas fa-times" data-confirm="'. esc_html__( 'Are you sure to delete this item?', 'loginfy' ) .'"></i>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<div class="loginfy-repeater-wrapper loginfy-data-wrapper" data-field-id="['. esc_attr( $this->field['id'] ) .']" data-max="'. esc_attr( $args['max'] ) .'" data-min="'. esc_attr( $args['min'] ) .'">';

        if ( ! empty( $this->value ) && is_array( $this->value ) ) {

          $num = 0;

          foreach ( $this->value as $key => $value ) {

            echo '<div class="loginfy-repeater-item">';
            echo '<div class="loginfy-repeater-content">';
            foreach ( $this->field['fields'] as $field ) {

              $field_unique = ( ! empty( $this->unique ) ) ? $this->unique .'['. $this->field['id'] .']['. $num .']' : $this->field['id'] .'['. $num .']';
              $field_value  = ( isset( $field['id'] ) && isset( $this->value[$key][$field['id']] ) ) ? $this->value[$key][$field['id']] : '';

              LOGINFY::field( $field, $field_value, $field_unique, 'field/repeater' );

            }
            echo '</div>';
            echo '<div class="loginfy-repeater-helper">';
            echo '<div class="loginfy-repeater-helper-inner">';
            echo '<i class="loginfy-repeater-sort fas fa-arrows-alt"></i>';
            echo '<i class="loginfy-repeater-clone far fa-clone"></i>';
            echo '<i class="loginfy-repeater-remove loginfy-confirm fas fa-times" data-confirm="'. esc_html__( 'Are you sure to delete this item?', 'loginfy' ) .'"></i>';
            echo '</div>';
            echo '</div>';
            echo '</div>';

            $num++;

          }

        }

        echo '</div>';

        echo '<div class="loginfy-repeater-alert loginfy-repeater-max">'. esc_html__( 'You cannot add more.', 'loginfy' ) .'</div>';
        echo '<div class="loginfy-repeater-alert loginfy-repeater-min">'. esc_html__( 'You cannot remove more.', 'loginfy' ) .'</div>';
        echo '<a href="#" class="button button-primary loginfy-repeater-add">'. $args['button_title'] .'</a>';

        echo $this->field_after();

      }

    }

    public function enqueue() {

      if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
        wp_enqueue_script( 'jquery-ui-sortable' );
      }

    }

  }
}
