<?php
defined( 'ABSPATH' ) || die( 'No Direct Access Sir!' );
use Loginfy\Libs\Helper;

$loginfy_video_type = $this->options['jlt_loginfy_login_bg_video_type'];

if ( $loginfy_video_type == 'youtube' ) {
	$loginfy_source = $this->options['jlt_loginfy_login_bg_video_youtube'];
} else {
	$loginfy_source = $this->options['jlt_loginfy_login_bg_video_self_hosted']['url'];
}

// if ( empty( $loginfy_source ) ) {
// 	return;
// }

if ( $loginfy_video_type ) {
	$loginfy_video_autoloop = $this->options['jlt_loginfy_login_bg_video_loop'];
	$loginfy_video_poster   = '';

	if ( ! empty( $this->options['jlt_loginfy_login_bg_video_poster'] ) && ! empty( $this->options['jlt_loginfy_login_bg_video_poster']['url'] ) ) {
		$this->options['jlt_loginfy_login_bg_video_poster']['url'];
	}

	ob_start(); ?>
	<script type='text/javascript' src='<?php echo LOGINFY_ASSETS . 'vendors/vidim/vidim.min.js'; ?>?ver=<?php echo esc_html( LOGINFY_VER ); ?>'></script>
	<script>
		<?php
		switch ( $loginfy_video_type ) {
			case 'youtube':
				?>
				var src = '<?php echo esc_url( $loginfy_source ); ?>';
				new vidim('.login-background', {
					src: src,
					type: 'YouTube',
					quality: 'hd1080',
					muted: true,
					startAt: 0,
					poster: '<?php echo esc_js( $loginfy_video_poster ); ?>',
					loop: '<?php echo esc_js( $loginfy_video_autoloop ); ?>',
					showPosterBeforePlay: '<?php echo esc_js( $loginfy_video_poster ); ?>'
				});
				<?php
				break;

			case 'self_hosted':
				?>
			   new vidim('.login-background', {
					src: [{
						type: 'video/mp4',
						src: '<?php echo esc_js( $loginfy_source ); ?>',
					}],
					poster: '<?php echo esc_js( $loginfy_video_poster ); ?>',
					loop: '<?php echo esc_js( $loginfy_video_autoloop ); ?>',
					showPosterBeforePlay: '<?php echo esc_js($loginfy_video_poster ); ?>'
				});
				<?php
				break;

			default:
				break;
		}
		?>
	</script>
	<?php

	$output = ob_get_clean();
	// echo Helper::wp_kses_custom( $output );
	echo $output;
}
