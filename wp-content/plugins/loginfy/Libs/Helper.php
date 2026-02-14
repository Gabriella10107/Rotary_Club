<?php

namespace Loginfy\Libs;

// No, Direct access Sir !!!
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
/*
 * Helper Class
 *
 * Jewel Theme <support@jeweltheme.com>
 */
if ( !class_exists( 'Helper' ) ) {
    /**
     * Helper class
     *
     * Jewel Theme <support@jeweltheme.com>
     */
    class Helper {
        /**
         * Check is Plugin Active
         *
         * @param [type] $plugin_path
         *
         * @return boolean
         */
        public static function is_plugin_active( $plugin_path ) {
            include_once ABSPATH . 'wp-admin/includes/plugin.php';
            return is_plugin_active( $plugin_path );
        }

        public static function is_adminify_active() {
            return self::is_plugin_active( 'adminify/adminify.php' );
        }

        /**
         * Remove spaces from Plugin Slug
         */
        public static function jlt_loginfy_slug_cleanup() {
            return str_replace( '-', '_', strtolower( LOGINFY_SLUG ) );
        }

        /**
         * Function current_datetime() compability for wp version < 5.3
         *
         * @return DateTimeImmutable
         */
        public static function jlt_loginfy_current_datetime() {
            if ( function_exists( 'current_datetime' ) ) {
                return current_datetime();
            }
            return new \DateTimeImmutable('now', self::jlt_loginfy_wp_timezone());
        }

        /**
         * Function jlt_loginfy_wp_timezone() compability for wp version < 5.3
         *
         * @return DateTimeZone
         */
        public static function jlt_loginfy_wp_timezone() {
            if ( function_exists( 'wp_timezone' ) ) {
                return wp_timezone();
            }
            return new \DateTimeZone(self::jlt_loginfy_wp_timezone_string());
        }

        /**
         * API Endpoint
         *
         * @return string
         */
        public static function api_endpoint() {
            $api_endpoint_url = 'https://bo.jeweltheme.com';
            $api_endpoint = apply_filters( 'jlt_loginfy_endpoint', $api_endpoint_url );
            return trailingslashit( $api_endpoint );
        }

        /**
         * CRM Endpoint
         *
         * @return string
         */
        public static function crm_endpoint() {
            $crm_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/subscribe';
            // Endpoint .
            $crm_endpoint = apply_filters( 'jlt_loginfy_crm_crm_endpoint', $crm_endpoint_url );
            return trailingslashit( $crm_endpoint );
        }

        /**
         * CRM Endpoint
         *
         * @return string
         */
        public static function crm_survey_endpoint() {
            $crm_feedback_endpoint_url = 'https://bo.jeweltheme.com/wp-json/jlt-api/v1/survey';
            // Endpoint .
            $crm_feedback_endpoint = apply_filters( 'jlt_loginfy_crm_crm_endpoint', $crm_feedback_endpoint_url );
            return trailingslashit( $crm_feedback_endpoint );
        }

        /**
         * Function jlt_loginfy_wp_timezone_string() compability for wp version < 5.3
         *
         * @return string
         */
        public static function jlt_loginfy_wp_timezone_string() {
            $timezone_string = get_option( 'timezone_string' );
            if ( $timezone_string ) {
                return $timezone_string;
            }
            $offset = (float) get_option( 'gmt_offset' );
            $hours = (int) $offset;
            $minutes = $offset - $hours;
            $sign = ( $offset < 0 ? '-' : '+' );
            $abs_hour = abs( $hours );
            $abs_mins = abs( $minutes * 60 );
            $tz_offset = sprintf(
                '%s%02d:%02d',
                $sign,
                $abs_hour,
                $abs_mins
            );
            return $tz_offset;
        }

        /**
         * Get Merged Data
         *
         * @param [type] $data .
         * @param string $start_date .
         * @param string $end_data .
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function get_merged_data( $data, $start_date = '', $end_data = '' ) {
            $_data = shortcode_atts( array(
                'image_url'        => LOGINFY_IMAGES . '/promo-image.png',
                'start_date'       => $start_date,
                'end_date'         => $end_data,
                'counter_time'     => '',
                'is_campaign'      => 'false',
                'button_text'      => __( 'Get Premium', 'loginfy' ),
                'button_url'       => 'https://wpadminify.com/loginfy/pricing',
                'btn_color'        => '#CC22FF',
                'notice'           => '',
                'notice_timestamp' => '',
            ), $data );
            if ( empty( $_data['image_url'] ) ) {
                $_data['image_url'] = LOGINFY_IMAGES . '/promo-image.png';
            }
            return $_data;
        }

        /**
         * wp_kses attributes map
         *
         * @param array $attrs .
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function wp_kses_atts_map( array $attrs ) {
            return array_fill_keys( array_values( $attrs ), true );
        }

        /**
         * Assets File Extensions
         *
         * @param [type] $ext
         *
         * @return void
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function assets_ext( $ext ) {
            if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
                return $ext;
            }
            return '.min' . $ext;
        }

        /**
         * Custom method
         *
         * @param [type] $content .
         *
         * @author Jewel Theme <support@jeweltheme.com>
         */
        public static function wp_kses_custom( $content ) {
            $allowed_tags = wp_kses_allowed_html( 'post' );
            $custom_tags = array(
                'select'         => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'style',
                    'width',
                    'height',
                    'title',
                    'data',
                    'name',
                    'autofocus',
                    'disabled',
                    'multiple',
                    'required',
                    'size'
                ) ),
                'input'          => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'style',
                    'width',
                    'height',
                    'title',
                    'data',
                    'name',
                    'autofocus',
                    'disabled',
                    'required',
                    'size',
                    'type',
                    'checked',
                    'readonly',
                    'placeholder',
                    'value',
                    'maxlength',
                    'min',
                    'max',
                    'multiple',
                    'pattern',
                    'step',
                    'autocomplete'
                ) ),
                'textarea'       => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'style',
                    'width',
                    'height',
                    'title',
                    'data',
                    'name',
                    'autofocus',
                    'disabled',
                    'required',
                    'rows',
                    'cols',
                    'wrap',
                    'maxlength'
                ) ),
                'option'         => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'label',
                    'disabled',
                    'label',
                    'selected',
                    'value'
                ) ),
                'optgroup'       => self::wp_kses_atts_map( array(
                    'disabled',
                    'label',
                    'class',
                    'id'
                ) ),
                'form'           => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'data',
                    'style',
                    'width',
                    'height',
                    'accept-charset',
                    'action',
                    'autocomplete',
                    'enctype',
                    'method',
                    'name',
                    'novalidate',
                    'rel',
                    'target'
                ) ),
                'svg'            => self::wp_kses_atts_map( array(
                    'class',
                    'xmlns',
                    'viewbox',
                    'width',
                    'height',
                    'fill',
                    'aria-hidden',
                    'aria-labelledby',
                    'role'
                ) ),
                'rect'           => self::wp_kses_atts_map( array(
                    'rx',
                    'width',
                    'height',
                    'fill'
                ) ),
                'path'           => self::wp_kses_atts_map( array('d', 'fill') ),
                'g'              => self::wp_kses_atts_map( array('fill') ),
                'defs'           => self::wp_kses_atts_map( array('fill') ),
                'linearGradient' => self::wp_kses_atts_map( array(
                    'id',
                    'x1',
                    'x2',
                    'y1',
                    'y2',
                    'gradientUnits'
                ) ),
                'stop'           => self::wp_kses_atts_map( array('stop-color', 'offset', 'stop-opacity') ),
                'style'          => self::wp_kses_atts_map( array('type') ),
                'div'            => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'ul'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'li'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'label'          => self::wp_kses_atts_map( array('class', 'for') ),
                'span'           => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h1'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h2'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h3'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h4'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h5'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'h6'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'a'              => self::wp_kses_atts_map( array(
                    'class',
                    'href',
                    'target',
                    'rel'
                ) ),
                'p'              => self::wp_kses_atts_map( array(
                    'class',
                    'id',
                    'style',
                    'data'
                ) ),
                'table'          => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'thead'          => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'tbody'          => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'tr'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'th'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'td'             => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'i'              => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'button'         => self::wp_kses_atts_map( array('class', 'id') ),
                'nav'            => self::wp_kses_atts_map( array('class', 'id', 'style') ),
                'time'           => self::wp_kses_atts_map( array('datetime') ),
                'br'             => array(),
                'strong'         => array(),
                'style'          => array(),
                'img'            => self::wp_kses_atts_map( array(
                    'class',
                    'src',
                    'alt',
                    'height',
                    'width',
                    'srcset',
                    'id',
                    'loading'
                ) ),
            );
            $allowed_tags = array_merge_recursive( $allowed_tags, $custom_tags );
            return wp_kses( stripslashes_deep( $content ), $allowed_tags );
        }

        /**
         * Upgrade Pro Icon
         *
         * @return void
         */
        public static function jlt_loginfy_upgrade_pro_icon() {
            return '<svg class="loginfy-pro-notice-icon is-pulled-left mr-2" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M21.8233 0.18318C21.8541 0.215042 21.8753 0.255055 21.8843 0.298527C22.5767 3.32046 20.085 9.8239 16.4731 13.4209C15.8343 14.064 15.1407 14.6502 14.4002 15.1727C14.4955 16.2787 14.411 17.3846 13.9985 18.3318C12.8302 21.0043 9.75855 21.7824 8.44197 21.9937C8.36979 22.0059 8.29577 22.0012 8.22571 21.9799C8.15566 21.9587 8.09147 21.9215 8.03819 21.8713C7.9849 21.821 7.94397 21.7591 7.9186 21.6904C7.89323 21.6217 7.88411 21.548 7.89196 21.4751L8.36241 17.189C8.04096 17.1858 7.71987 17.1663 7.40039 17.1305C7.17735 17.1087 6.96893 17.0096 6.81109 16.8503L5.15076 15.1924C4.99146 15.0346 4.89242 14.8259 4.87084 14.6026C4.83497 14.281 4.81547 13.9578 4.8124 13.6343L0.524795 14.1076C0.451994 14.1156 0.378341 14.1065 0.309629 14.0811C0.240917 14.0558 0.17902 14.0148 0.128806 13.9615C0.0785915 13.9081 0.0414297 13.8438 0.0202432 13.7736C-0.000943262 13.7035 -0.0055768 13.6293 0.00670721 13.5571C0.223273 12.2368 1.00065 9.16771 3.67065 7.99148C4.61695 7.57859 5.72728 7.4965 6.83761 7.59481C7.35968 6.85546 7.94513 6.16306 8.58733 5.52547C12.1879 1.92304 18.8337 -0.585245 21.7099 0.118627C21.7531 0.128924 21.7924 0.151317 21.8233 0.18318ZM12.224 7.92186C12.3151 8.37957 12.5397 8.79996 12.8695 9.12986C13.0882 9.34908 13.348 9.52298 13.6339 9.64164C13.9198 9.7603 14.2263 9.82137 14.5358 9.82137C14.8453 9.82137 15.1517 9.7603 15.4377 9.64164C15.7236 9.52298 15.9833 9.34908 16.202 9.12986C16.5318 8.79996 16.7565 8.37957 16.8475 7.92186C16.9386 7.46415 16.892 6.98968 16.7137 6.55848C16.5353 6.12727 16.2332 5.7587 15.8455 5.49939C15.4578 5.24007 15.002 5.10166 14.5358 5.10166C14.0695 5.10166 13.6137 5.24007 13.226 5.49939C12.8384 5.7587 12.5362 6.12727 12.3579 6.55848C12.1795 6.98968 12.1329 7.46415 12.224 7.92186ZM5.47798 18.5161C5.99754 18.4262 6.4292 18.321 6.69831 18.0511C6.83974 17.9032 7.0897 18.0256 7.07153 18.2311C6.99299 18.8853 6.69667 19.4941 6.23032 19.9593C5.07579 21.1158 0.785726 21.225 0.785726 21.225C0.785726 21.225 0.894746 16.9334 2.04927 15.7768C2.51439 15.3115 3.12167 15.0153 3.77443 14.9353C3.81912 14.9292 3.86459 14.9374 3.90439 14.9586C3.94419 14.9799 3.97629 15.0131 3.99613 15.0537C4.01597 15.0942 4.02255 15.14 4.01493 15.1845C4.00731 15.229 3.98588 15.2699 3.95368 15.3015C3.80635 15.449 3.56965 16.0767 3.48961 16.5245C3.27991 17.7056 4.31069 18.7152 5.47798 18.5161Z" fill="#00BA88"/>
				</svg>';
        }

        // Upgrade to Pro Notice
        public static function loginfy_upgrade_pro( $custom_message = '' ) {
            if ( empty( $custom_message ) ) {
                $pro_content = sprintf( __( '<strong>Unlock this feature</strong> .', 'loginfy' ) );
            } else {
                $pro_content = $custom_message;
            }
            $upgrade_notice_msg = sprintf(
                __( '<div class="loginfy-pro-notice"> %1$s <p> %2$s <a href="%3$s" target="_blank">%4$s</a>  </p></div>', 'loginfy' ),
                self::jlt_loginfy_upgrade_pro_icon(),
                self::wp_kses_custom( $pro_content ),
                esc_url( 'https://wpadminify.com/loginfy/pricing' ),
                __( 'Upgrade to Pro Now!', 'loginfy' )
            );
            return self::wp_kses_custom( $upgrade_notice_msg );
        }

        public static function jlt_loginfy_class_cleanup( $string ) {
            // Lower case everything
            $string = strtolower( $string );
            // Make alphanumeric (removes all other characters)
            $string = preg_replace( '/[^a-z0-9_\\s-]/', '', $string );
            // Clean up multiple dashes or whitespaces
            $string = preg_replace( '/[\\s-]+/', ' ', $string );
            // Convert whitespaces and underscore to dash
            $string = preg_replace( '/[\\s_]/', '-', $string );
            return $string;
        }

        /**
         * Returns the plugin & system information.
         * @access public
         * @return string
         */
        public static function get_sysinfo() {
            global $wpdb;
            $html = '### Begin System Info ###' . "\n\n";
            // Basic site info
            $html .= '-- WordPress Configuration --' . "\n\n";
            $html .= 'Site URL:                 ' . site_url() . "\n";
            $html .= 'Home URL:                 ' . home_url() . "\n";
            $html .= 'Multisite:                ' . (( is_multisite() ? 'Yes' : 'No' )) . "\n";
            $html .= 'Version:                  ' . get_bloginfo( 'version' ) . "\n";
            $html .= 'Language:                 ' . get_locale() . "\n";
            $html .= 'Table Prefix:             ' . 'Length: ' . strlen( $wpdb->prefix ) . "\n";
            $html .= 'WP_DEBUG:                 ' . (( defined( 'WP_DEBUG' ) ? ( WP_DEBUG ? 'Enabled' : 'Disabled' ) : 'Not set' )) . "\n";
            $html .= 'Memory Limit:             ' . WP_MEMORY_LIMIT . "\n";
            // Plugin Configuration
            $html .= "\n" . '-- Login Customizer Configuration --' . "\n\n";
            $html .= 'Plugin Version:           ' . LOGINFY_VER . "\n";
            // Server Configuration.
            $html .= "\n" . '-- Server Configuration --' . "\n\n";
            $html .= 'Operating System:         ' . php_uname( 's' ) . "\n";
            $html .= 'PHP Version:              ' . PHP_VERSION . "\n";
            $html .= 'MySQL Version:            ' . $wpdb->db_version() . "\n";
            $html .= 'Server Software:          ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
            // PHP configs... now we're getting to the important stuff
            $html .= "\n" . '-- PHP Configuration --' . "\n\n";
            // $html .= 'Safe Mode:                ' . ( ini_get( 'safe_mode' ) ? 'Enabled' : 'Disabled' . "\n" );
            $html .= 'Memory Limit:             ' . ini_get( 'memory_limit' ) . "\n";
            $html .= 'Post Max Size:            ' . ini_get( 'post_max_size' ) . "\n";
            $html .= 'Upload Max Filesize:      ' . ini_get( 'upload_max_filesize' ) . "\n";
            $html .= 'Time Limit:               ' . ini_get( 'max_execution_time' ) . "\n";
            $html .= 'Max Input Vars:           ' . ini_get( 'max_input_vars' ) . "\n";
            $html .= 'Display Errors:           ' . (( ini_get( 'display_errors' ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A' )) . "\n";
            // WordPress active themes
            $html .= "\n" . '-- WordPress Active Theme --' . "\n\n";
            $my_theme = wp_get_theme();
            $html .= 'Name:                     ' . $my_theme->get( 'Name' ) . "\n";
            $html .= 'URI:                      ' . $my_theme->get( 'ThemeURI' ) . "\n";
            $html .= 'Author:                   ' . $my_theme->get( 'Author' ) . "\n";
            $html .= 'Version:                  ' . $my_theme->get( 'Version' ) . "\n";
            // WordPress active plugins
            $html .= "\n" . '-- WordPress Active Plugins --' . "\n\n";
            $plugins = get_plugins();
            $active_plugins = get_option( 'active_plugins', array() );
            foreach ( $plugins as $plugin_path => $plugin ) {
                if ( !in_array( $plugin_path, $active_plugins, true ) ) {
                    continue;
                    $html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
                }
            }
            // WordPress inactive plugins
            $html .= "\n" . '-- WordPress Inactive Plugins --' . "\n\n";
            foreach ( $plugins as $plugin_path => $plugin ) {
                if ( in_array( $plugin_path, $active_plugins ) ) {
                    continue;
                }
                $html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
            }
            if ( is_multisite() ) {
                // WordPress Multisite active plugins
                $html .= "\n" . '-- Network Active Plugins --' . "\n\n";
                $plugins = wp_get_active_network_plugins();
                $active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                foreach ( $plugins as $plugin_path ) {
                    $plugin_base = plugin_basename( $plugin_path );
                    if ( !array_key_exists( $plugin_base, $active_plugins ) ) {
                        continue;
                    }
                    $plugin = get_plugin_data( $plugin_path );
                    $html .= $plugin['Name'] . ': v(' . $plugin['Version'] . ")\n";
                }
            }
            $html .= "\n" . '### End System Info ###';
            return $html;
        }

    }

}