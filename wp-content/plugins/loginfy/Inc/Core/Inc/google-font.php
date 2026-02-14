<?php
// Google Fonts
$loginfy_font_url = '';
if (isset($this->options['login_google_font'])) {
	$loginfy_font_family     = isset($this->options['login_google_font']['font-family']) ? $this->options['login_google_font']['font-family'] : '';
	$loginfy_font_weight     = isset($this->options['login_google_font']['font-weight']) ? $this->options['login_google_font']['font-weight'] : '';
	$loginfy_text_align      = isset($this->options['login_google_font']['text-align']) ? $this->options['login_google_font']['text-align'] : '';
	$loginfy_text_transform  = isset($this->options['login_google_font']['text-transform']) ? $this->options['login_google_font']['text-transform'] : '';
	$loginfy_text_decoration = isset($this->options['login_google_font']['text-decoration']) ? $this->options['login_google_font']['text-decoration'] : '';
	$loginfy_font_size       = isset($this->options['login_google_font']['font-size']) ? $this->options['login_google_font']['font-size'] : '';
	$loginfy_line_height     = isset($this->options['login_google_font']['line-height']) ? $this->options['login_google_font']['line-height'] : '';
	$loginfy_letter_spacing  = isset($this->options['login_google_font']['letter-spacing']) ? $this->options['login_google_font']['letter-spacing'] : '';
	$loginfy_word_spacing    = isset($this->options['login_google_font']['word-spacing']) ? $this->options['login_google_font']['word-spacing'] : '';
	$loginfy_font_color      = isset($this->options['login_google_font']['color']) ? $this->options['login_google_font']['color'] : '';
	$loginfy_font_unit       = isset($this->options['login_google_font']['unit']) ? $this->options['login_google_font']['unit'] : '';

	$jlt_loginfy_query_args = [
		'family' => rawurlencode($loginfy_font_family),
		// 'subset' => urlencode($font_style_subset),
	];
	$loginfy_font_url        = add_query_arg($jlt_loginfy_query_args, '//fonts.googleapis.com/css');
	$jlt_loginfy_fonts_url = esc_url_raw($loginfy_font_url);
?>
	<link href="<?php echo esc_url($jlt_loginfy_fonts_url); ?>" rel='stylesheet'>
	<style type="text/css">
		<?php
		if ($loginfy_font_family) {
		?>body {
			font-family: <?php echo '"' . esc_attr($loginfy_font_family) . '"'; ?> !important;
		}

		<?php } ?>.login input[type="submit"],
		.login form .input,
		.login input[type="text"] {
			<?php
			if ($loginfy_font_family) {
			?>font-family: <?php echo '"' . esc_attr($loginfy_font_family) . '"'; ?> !important;
			<?php } ?><?php
						if ($loginfy_font_weight) {
						?>font-weight: <?php echo esc_attr($loginfy_font_weight); ?> !important;
			<?php } ?><?php
						if ($loginfy_text_align) {
						?>text-align: <?php echo esc_attr($loginfy_text_align); ?> !important;
			<?php } ?><?php
						if ($loginfy_text_transform) {
						?>text-transform: <?php echo esc_attr($loginfy_text_transform); ?> !important;
			<?php } ?><?php
						if ($loginfy_text_decoration) {
						?>text-decoration: <?php echo esc_attr($loginfy_text_decoration); ?> !important;
			<?php } ?><?php
						if ($loginfy_font_size) {
						?>font-size: <?php echo esc_attr($loginfy_font_size . $loginfy_font_unit); ?> !important;
			<?php } ?><?php
						if ($loginfy_line_height) {
						?>line-height: <?php echo esc_attr($loginfy_line_height . $loginfy_font_unit); ?> !important;
			<?php } ?><?php
						if ($loginfy_letter_spacing) {
						?>letter-spacing:
				<?php echo esc_attr($loginfy_letter_spacing . $loginfy_font_unit); ?> !important;
			<?php } ?><?php
						if ($loginfy_word_spacing) {
						?>word-spacing: <?php echo esc_attr($loginfy_word_spacing . $loginfy_font_unit); ?> !important;
			<?php } ?><?php
						if ($loginfy_font_color) {
						?>color: <?php echo esc_attr($loginfy_font_color); ?> !important;
			<?php } ?>
		}
	</style>
<?php
}
?>


<?php
	if (! empty($this->options['jlt_loginfy_customizer_custom_js'])) {
		echo '<script>';
		echo "\n" . wp_strip_all_tags($this->options['jlt_loginfy_customizer_custom_js']) . "\n";
		echo '</script>';
	}
?>
<?php
if (! empty($this->options['jlt_loginfy_customizer_custom_css'])) {
	echo '<style>';
	echo "\n" . wp_strip_all_tags($this->options['jlt_loginfy_customizer_custom_css']) . "\n";
	echo '</style>';
}
?>
