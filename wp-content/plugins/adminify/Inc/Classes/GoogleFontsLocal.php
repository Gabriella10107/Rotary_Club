<?php

namespace WPAdminify\Inc\Classes;

// no direct access allowed
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Google Fonts Local
 *
 * Downloads Google Fonts locally and serves them from wp-content/uploads/adminify-google-fonts/
 *
 * @since 4.0.0
 */
class GoogleFontsLocal
{
	/**
	 * Singleton instance
	 */
	private static $instance = null;

	/**
	 * User agent for fetching woff2 fonts
	 */
	private $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/112.0.0.0 Safari/537.36';

	/**
	 * Base folder name for storing fonts
	 */
	private $folder_name = 'adminify-google-fonts';

	/**
	 * Get singleton instance
	 */
	public static function get_instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct()
	{
		// Hook into framework's save filter (most reliable)
		add_filter('adminify__wpadminify_saved_before', [$this, 'on_settings_save'], 10, 1);

		// Enqueue local fonts
		add_action('admin_enqueue_scripts', [$this, 'enqueue_local_fonts'], 99);
	}

	/**
	 * Handle settings save via framework filter
	 */
	public function on_settings_save($data)
	{
		$this->process_fonts($data);
		return $data;
	}

	/**
	 * Process fonts from settings data
	 */
	public function process_fonts($data)
	{
		if (!is_array($data)) {
			return;
		}

		// Process Body Font
		if (!empty($data['admin_general_google_font']['font-family'])) {
			$font_family = $data['admin_general_google_font']['font-family'];
			$font_weights = !empty($data['admin_general_google_font']['font-weight']) ? $data['admin_general_google_font']['font-weight'] : '400';
			$result = $this->download_font($font_family, $font_weights);
		} 

		// Process Light Mode Logo Typography
		if (!empty($data['light_dark_mode']['admin_ui_light_mode']['admin_ui_light_logo_text_typo']['font-family'])) {
			$typo = $data['light_dark_mode']['admin_ui_light_mode']['admin_ui_light_logo_text_typo'];
			$font_family = $typo['font-family'];
			$font_weights = !empty($typo['font-weight']) ? $typo['font-weight'] : '400';
			$this->download_font($font_family, $font_weights);
		}

		// Process Dark Mode Logo Typography
		if (!empty($data['light_dark_mode']['admin_ui_dark_mode']['admin_ui_dark_logo_text_typo']['font-family'])) {
			$typo = $data['light_dark_mode']['admin_ui_dark_mode']['admin_ui_dark_logo_text_typo'];
			$font_family = $typo['font-family'];
			$font_weights = !empty($typo['font-weight']) ? $typo['font-weight'] : '400';
			$this->download_font($font_family, $font_weights);
		}
	}

	/**
	 * Get the local fonts folder path
	 */
	public function get_folder()
	{
		$upload_dir = wp_get_upload_dir();
		return $upload_dir['basedir'] . '/' . $this->folder_name;
	}

	/**
	 * Get the local fonts folder URL
	 */
	public function get_folder_url()
	{
		$upload_dir = wp_get_upload_dir();
		$folder_url = $upload_dir['baseurl'] . '/' . $this->folder_name;

		if (is_ssl()) {
			$folder_url = set_url_scheme($folder_url, 'https');
		}

		return $folder_url;
	}

	/**
	 * Download a Google Font locally
	 */
	public function download_font($font_family, $font_weights = '400')
	{
		if (empty($font_family)) {
			return false;
		}

		// Sanitize font family for folder name
		$font_slug = sanitize_title($font_family);
		$folder = $this->get_folder() . '/' . $font_slug;

		// Create base folder
		if (!file_exists($this->get_folder())) {
			wp_mkdir_p($this->get_folder());
		}

		// Create font folder
		if (!file_exists($folder)) {
			wp_mkdir_p($folder);
		}

		// Normalize font weights - convert 'normal' to '400', 'bold' to '700'
		$weight_map = [
			'normal' => '400',
			'bold' => '700',
			'lighter' => '300',
			'bolder' => '700',
		];

		if (is_array($font_weights)) {
			$font_weights = array_map(function($w) use ($weight_map) {
				return isset($weight_map[$w]) ? $weight_map[$w] : $w;
			}, $font_weights);
			$weights_string = implode(',', $font_weights);
			$weights_array = $font_weights;
		} else {
			$font_weights = isset($weight_map[$font_weights]) ? $weight_map[$font_weights] : $font_weights;
			$weights_string = $font_weights;
			$weights_array = [$font_weights];
		}

		// Try the newer css2 API first
		$font_string = urlencode($font_family);
		$wght_string = implode(';', $weights_array);
		$google_url = 'https://fonts.googleapis.com/css2?family=' . $font_string . ':wght@' . $wght_string . '&display=swap';

		// Fetch CSS from Google
		$response = wp_remote_get($google_url, [
			'user-agent' => $this->user_agent,
			'timeout' => 30,
		]);

		$css_content = '';
		if (!is_wp_error($response)) {
			$css_content = wp_remote_retrieve_body($response);
		}

		// Fallback: Use the older Google Fonts API (css) which is more forgiving
		if (empty($css_content) || strpos($css_content, '@font-face') === false) {
			$font_string = urlencode($font_family);
			if (!empty($weights_string)) {
				$font_string .= ':' . $weights_string;
			}
			$google_url = 'https://fonts.googleapis.com/css?family=' . $font_string . '&display=swap';

			// Fetch CSS from Google using older API
			$response = wp_remote_get($google_url, [
				'user-agent' => $this->user_agent,
				'timeout' => 30,
			]);

			if (!is_wp_error($response)) {
				$css_content = wp_remote_retrieve_body($response);
			}
		}

		// Final fallback: Generate system font fallback CSS if Google Font not found
		if (empty($css_content) || strpos($css_content, '@font-face') === false) {
			$css_content = $this->generate_system_font_fallback($font_family, $weights_array);
		}

		// Find all font file URLs
		preg_match_all('/url\(([^)]+)\)/i', $css_content, $matches);

		if (!empty($matches[1])) {
			foreach ($matches[1] as $font_url) {
				$font_url = trim($font_url, '"\'');

				// Only process Google font URLs
				if (strpos($font_url, 'fonts.gstatic.com') === false) {
					continue;
				}

				// Generate local filename
				$url_hash = substr(md5($font_url), 0, 8);
				$extension = pathinfo(parse_url($font_url, PHP_URL_PATH), PATHINFO_EXTENSION);
				if (empty($extension)) {
					$extension = 'woff2';
				}
				$local_filename = $font_slug . '-' . $url_hash . '.' . $extension;
				$local_path = $folder . '/' . $local_filename;
				$local_url = $this->get_folder_url() . '/' . $font_slug . '/' . $local_filename;

				// Download font file if not exists
				if (!file_exists($local_path)) {
					$font_response = wp_remote_get($font_url, [
						'user-agent' => $this->user_agent,
						'timeout' => 30,
					]);

					if (!is_wp_error($font_response)) {
						$font_data = wp_remote_retrieve_body($font_response);
						if (!empty($font_data)) {
							file_put_contents($local_path, $font_data);
						}
					}
				}

				// Replace URL in CSS
				$css_content = str_replace($font_url, $local_url, $css_content);
			}
		}

		// Save local CSS file
		$css_file = $folder . '/' . $font_slug . '.css';
		$header = "/* Local Google Font: {$font_family} */\n";
		$header .= "/* Generated by WP Adminify - " . date('Y-m-d H:i:s') . " */\n\n";
		file_put_contents($css_file, $header . $css_content);

		return true;
	}

	/**
	 * Get local font CSS URL
	 */
	public function get_local_font_url($font_family)
	{
		if (empty($font_family)) {
			return false;
		}

		$font_slug = sanitize_title($font_family);
		$css_file = $this->get_folder() . '/' . $font_slug . '/' . $font_slug . '.css';

		if (file_exists($css_file)) {
			return $this->get_folder_url() . '/' . $font_slug . '/' . $font_slug . '.css?ver=' . filemtime($css_file);
		}

		return false;
	}

	/**
	 * Check if font exists locally
	 */
	public function is_font_local($font_family)
	{
		if (empty($font_family)) {
			return false;
		}

		$font_slug = sanitize_title($font_family);
		$css_file = $this->get_folder() . '/' . $font_slug . '/' . $font_slug . '.css';

		return file_exists($css_file);
	}

	/**
	 * Enqueue local fonts for admin
	 */
	public function enqueue_local_fonts()
	{
		$options = get_option('_wpadminify');

		if (empty($options) || !is_array($options)) {
			return;
		}

		// Body Font
		if (!empty($options['admin_general_google_font']['font-family'])) {
			$font_family = $options['admin_general_google_font']['font-family'];
			$local_url = $this->get_local_font_url($font_family);

			if ($local_url) {
				wp_enqueue_style('adminify-local-body-font', $local_url, [], null);
			}
		}

		// Light Mode Logo Typography
		if (!empty($options['light_dark_mode']['admin_ui_light_mode']['admin_ui_light_logo_text_typo']['font-family'])) {
			$font_family = $options['light_dark_mode']['admin_ui_light_mode']['admin_ui_light_logo_text_typo']['font-family'];
			$local_url = $this->get_local_font_url($font_family);

			if ($local_url) {
				wp_enqueue_style('adminify-local-logo-light-font', $local_url, [], null);
			}
		}

		// Dark Mode Logo Typography
		if (!empty($options['light_dark_mode']['admin_ui_dark_mode']['admin_ui_dark_logo_text_typo']['font-family'])) {
			$font_family = $options['light_dark_mode']['admin_ui_dark_mode']['admin_ui_dark_logo_text_typo']['font-family'];
			$local_url = $this->get_local_font_url($font_family);

			if ($local_url) {
				wp_enqueue_style('adminify-local-logo-dark-font', $local_url, [], null);
			}
		}
	}

	/**
	 * Generate system font fallback CSS when Google Font is not found
	 */
	private function generate_system_font_fallback($font_family, $weights_array)
	{
		// System fonts to try as local fallbacks
		$system_fonts = [
			'BlinkMacSystemFont',
			'Segoe UI',
			'Roboto',
			'Helvetica Neue',
			'Arial',
			'sans-serif',
		];

		$css = "/* Fallback: Google Font '{$font_family}' not found */\n";
		$css .= "/* Using system font stack as fallback */\n\n";

		foreach ($weights_array as $weight) {
			$local_src = [];
			foreach ($system_fonts as $font) {
				$local_src[] = "local('{$font}')";
			}

			$css .= "@font-face {\n";
			$css .= "  font-family: '{$font_family}';\n";
			$css .= "  font-style: normal;\n";
			$css .= "  font-weight: {$weight};\n";
			$css .= "  font-display: swap;\n";
			$css .= "  src: " . implode(",\n       ", $local_src) . ";\n";
			$css .= "}\n\n";
		}

		return $css;
	}
}
