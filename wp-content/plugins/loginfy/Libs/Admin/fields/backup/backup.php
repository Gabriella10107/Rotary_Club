<?php if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access directly.
/**
 *
 * Field: backup
 *
 * @since 1.0.0
 * @version 1.0.0
 *
 */
if ( ! class_exists( 'LOGINFY_Field_backup' ) ) {
  class LOGINFY_Field_backup extends LOGINFY_Fields {

    public function __construct( $field, $value = '', $unique = '', $where = '', $parent = '' ) {
      parent::__construct( $field, $value, $unique, $where, $parent );
    }

    public function render() {

      $unique = $this->unique;
      $nonce  = wp_create_nonce( 'loginfy_backup_nonce' );
      $export = add_query_arg( array( 'action' => 'loginfy-export', 'unique' => $unique, 'nonce' => $nonce ), admin_url( 'admin-ajax.php' ) );

      echo $this->field_before();

      echo '<textarea name="loginfy_import_data" class="loginfy-import-data"></textarea>';
      echo '<button type="submit" class="button button-primary loginfy-confirm loginfy-import" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Import', 'loginfy' ) .'</button>';
      echo '<hr />';
      echo '<textarea readonly="readonly" class="loginfy-export-data">'. esc_attr( json_encode( get_option( $unique ) ) ) .'</textarea>';
      echo '<a href="'. esc_url( $export ) .'" class="button button-primary loginfy-export" target="_blank">'. esc_html__( 'Export & Download', 'loginfy' ) .'</a>';
      echo '<hr />';
      echo '<button type="submit" name="loginfy_transient[reset]" value="reset" class="button loginfy-warning-primary loginfy-confirm loginfy-reset" data-unique="'. esc_attr( $unique ) .'" data-nonce="'. esc_attr( $nonce ) .'">'. esc_html__( 'Reset', 'loginfy' ) .'</button>';

      echo $this->field_after();

    }

  }
}
