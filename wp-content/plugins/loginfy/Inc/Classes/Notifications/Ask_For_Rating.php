<?php
namespace Loginfy\Inc\Classes\Notifications;

use Loginfy\Inc\Classes\Notifications\Model\Notice;

if ( ! class_exists( 'Ask_For_Rating' ) ) {
	/**
	 * Ask For Rating Class
	 *
	 * Jewel Theme <support@jeweltheme.com>
	 */
	class Ask_For_Rating extends Notice {

		/**
		 * Notice Content
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function notice_content() {
			printf(
				'<h2 style="margin:0">Enjoying %1$s?</h2><p>%2$s</p>',
				esc_html__( 'Loginfy', 'loginfy' ),
				__( 'A positive rating will keep us motivated to continue supporting and improving this free plugin, and will help spread its popularity.<br> Your help is greatly appreciated!', 'loginfy' )
			);
		}

		/**
		 * Rate Plugin URL
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function plugin_rate_url() {
			return '' ;
		}

		/**
		 * Footer content
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function footer_content() {
			?>
			<a class="button button-primary rate-plugin-button" href="<?php echo esc_url( $this->plugin_rate_url() ); ?>" rel="nofollow" target="_blank">
				<?php echo esc_html__( 'Rate Now', 'loginfy' ); ?>
			</a>
			<a class="button notice-review-btn review-later loginfy-notice-dismiss" href="#" rel="nofollow">
				<?php echo esc_html__( 'Later', 'loginfy' ); ?>
			</a>
			<a class="button notice-review-btn review-done loginfy-notice-disable" href="#" rel="nofollow">
				<?php echo esc_html__( 'I already did', 'loginfy' ); ?>
			</a>
			<?php
		}

		/**
		 * Intervals
		 *
		 * @author Jewel Theme <support@jeweltheme.com>
		 */
		public function intervals() {
			return array( 5, 4, 10, 20, 15, 25, 30, 27, 13, 26, 26, 39, 9, 3, 9, 2, 6 );
		}
	}
}
