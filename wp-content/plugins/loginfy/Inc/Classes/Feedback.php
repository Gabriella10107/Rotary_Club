<?php
namespace Loginfy\Inc\Classes;

use Loginfy\Inc\Classes\Notifications\Base\User_Data;

// No, Direct access Sir !!!
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Feedback
 *
 * @author Jewel Theme <support@jeweltheme.com>
 */
class Feedback {

    use User_Data;

	/**
	 * Construct Method
	 *
	 * @return void
	 * @author Jewel Theme <support@jeweltheme.com>
	 */
    public function __construct(){
        add_action( 'admin_enqueue_scripts' , [ $this,'admin_suvery_scripts'] );
        add_action( 'admin_footer' , [ $this , 'deactivation_footer' ] );
        add_action( 'wp_ajax_jlt_loginfy_deactivation_survey', array( $this, 'jlt_loginfy_deactivation_survey' ) );

    }


    public function proceed(){

        global $current_screen;
        if(
            isset($current_screen->parent_file)
            && $current_screen->parent_file == 'plugins.php'
            && isset($current_screen->id)
            && $current_screen->id == 'plugins'
        ){
           return true;
        }
        return false;

    }

    public function admin_suvery_scripts($handle){
        if('plugins.php' === $handle){
            wp_enqueue_style( 'loginfy-survey' , LOGINFY_ASSETS . 'css/loginfy-survey.css' );
        }
    }

    /**
     * Deactivation Survey
     */
    public function jlt_loginfy_deactivation_survey(){
        check_ajax_referer( 'jlt_loginfy_deactivation_nonce' );

        $deactivation_reason  = ! empty( $_POST['deactivation_reason'] ) ? sanitize_text_field( wp_unslash( $_POST['deactivation_reason'] ) ) : '';

        if( empty( $deactivation_reason )){
            return;
        }

        $email = get_bloginfo( 'admin_email' );
        $author_obj = get_user_by( 'email', $email );
        $user_id    = $author_obj->ID;
        $full_name  = $author_obj->display_name;

        $response = $this->get_collect_data( $user_id, array(
            'first_name'              => $full_name,
            'email'                   => $email,
            'deactivation_reason'     => $deactivation_reason,
        ) );

        return $response;
    }


    public function get_survey_questions(){

        return [
			'no_longer_needed' => [
				'title' => esc_html__( 'I no longer need the plugin', 'loginfy' ),
				'input_placeholder' => '',
			],
			'found_a_better_plugin' => [
				'title' => esc_html__( 'I found a better plugin', 'loginfy' ),
				'input_placeholder' => esc_html__( 'Please share which plugin', 'loginfy' ),
			],
			'couldnt_get_the_plugin_to_work' => [
				'title' => esc_html__( 'I couldn\'t get the plugin to work', 'loginfy' ),
				'input_placeholder' => '',
			],
			'temporary_deactivation' => [
				'title' => esc_html__( 'It\'s a temporary deactivation', 'loginfy' ),
				'input_placeholder' => '',
			],
			'jlt_loginfy_pro' => [
				'title' => sprintf( esc_html__( 'I have %1$s Pro', 'loginfy' ), LOGINFY_NAME ),
				'input_placeholder' => '',
				'alert' => sprintf( esc_html__( 'Wait! Don\'t deactivate %1$s. You have to activate both %1$s and %1$s Pro in order for the plugin to work.', 'loginfy' ), LOGINFY_NAME ),
			],
			'need_better_design' => [
				'title' => esc_html__( 'I need better design and presets', 'loginfy' ),
				'input_placeholder' => esc_html__( 'Let us know your thoughts', 'loginfy' ),
			],
            'other' => [
				'title' => esc_html__( 'Other', 'loginfy' ),
				'input_placeholder' => esc_html__( 'Please share the reason', 'loginfy' ),
			],
		];
    }


        /**
         * Deactivation Footer
         */
        public function deactivation_footer(){

        if(!$this->proceed()){
            return;
        }

        ?>
        <div class="loginfy-deactivate-survey-overlay" id="loginfy-deactivate-survey-overlay"></div>
        <div class="loginfy-deactivate-survey-modal" id="loginfy-deactivate-survey-modal">
            <header>
                <div class="loginfy-deactivate-survey-header">
                    <img src="<?php echo esc_url(LOGINFY_IMAGES . '/menu-icon.svg'); ?>" />
                    <h3><?php echo wp_sprintf( '%1$s %2$s', LOGINFY_NAME, esc_html__( '- Feedback', 'loginfy' ) ); ?></h3>
                </div>
            </header>
            <div class="loginfy-deactivate-info">
                <?php echo wp_sprintf( '%1$s %2$s', esc_html__( 'If you have a moment, please share why you are deactivating', 'loginfy' ), LOGINFY_NAME ); ?>
            </div>
            <div class="loginfy-deactivate-content-wrapper">
                <form action="#" class="loginfy-deactivate-form-wrapper">
                    <?php foreach($this->get_survey_questions() as $reason_key => $reason){ ?>
                        <div class="loginfy-deactivate-input-wrapper">
                            <input id="loginfy-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="loginfy-deactivate-feedback-dialog-input" type="radio" name="reason_key" value="<?php echo $reason_key; ?>">
                            <label for="loginfy-deactivate-feedback-<?php echo esc_attr($reason_key); ?>" class="loginfy-deactivate-feedback-dialog-label"><?php echo esc_html( $reason['title'] ); ?></label>
							<?php if ( ! empty( $reason['input_placeholder'] ) ) : ?>
								<input class="loginfy-deactivate-feedback-text" type="text" name="reason_<?php echo esc_attr( $reason_key ); ?>" placeholder="<?php echo esc_attr( $reason['input_placeholder'] ); ?>" />
							<?php endif; ?>
                        </div>
                    <?php } ?>
                    <div class="loginfy-deactivate-footer">
                        <button id="loginfy-dialog-lightbox-submit" class="loginfy-dialog-lightbox-submit"><?php echo esc_html__( 'Submit &amp; Deactivate', 'loginfy' ); ?></button>
                        <button id="loginfy-dialog-lightbox-skip" class="loginfy-dialog-lightbox-skip"><?php echo esc_html__( 'Skip & Deactivate', 'loginfy' ); ?></button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            var deactivate_url = '#';

            jQuery(document).on('click', '#deactivate-loginfy', function(e) {
                e.preventDefault();
                deactivate_url = e.target.href;
                jQuery('#loginfy-deactivate-survey-overlay').addClass('loginfy-deactivate-survey-is-visible');
                jQuery('#loginfy-deactivate-survey-modal').addClass('loginfy-deactivate-survey-is-visible');
            });

            jQuery('#loginfy-dialog-lightbox-skip').on('click', function (e) {
                e.preventDefault();
                window.location.replace(deactivate_url);
            });


            jQuery(document).on('click', '#loginfy-dialog-lightbox-submit', async function(e) {
                e.preventDefault();

                jQuery('#loginfy-dialog-lightbox-submit').addClass('loginfy-loading');

                var $dialogModal = jQuery('.loginfy-deactivate-input-wrapper'),
                    radioSelector = '.loginfy-deactivate-feedback-dialog-input';
                $dialogModal.find(radioSelector).on('change', function () {
                    $dialogModal.attr('data-feedback-selected', jQuery(this).val());
                });
                $dialogModal.find(radioSelector + ':checked').trigger('change');


                // Reasons for deactivation
                var deactivation_reason = '';
                var reasonData = jQuery('.loginfy-deactivate-form-wrapper').serializeArray();

                jQuery.each(reasonData, function (reason_index, reason_value) {
                    if ('reason_key' == reason_value.name && reason_value.value != '') {
                        const reason_input_id = '#loginfy-deactivate-feedback-' + reason_value.value,
                            reason_title = jQuery(reason_input_id).siblings('label').text(),
                            reason_placeholder_input = jQuery(reason_input_id).siblings('input').val(),
                            format_title_with_key = reason_value.value + ' - '  + reason_placeholder_input,
                            format_title = reason_title + ' - '  + reason_placeholder_input;

                        deactivation_reason = reason_value.value;

                        if ('found_a_better_plugin' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('need_better_design' == reason_value.value ) {
                            deactivation_reason = format_title_with_key;
                        }

                        if ('other' == reason_value.value) {
                            deactivation_reason = format_title_with_key;
                        }
                    }
                });

                await jQuery.ajax({
                        url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
                        method: 'POST',
                        // crossDomain: true,
                        async: true,
                        // dataType: 'jsonp',
                        data: {
                            action: 'jlt_loginfy_deactivation_survey',
                            _wpnonce: '<?php echo esc_js( wp_create_nonce( 'jlt_loginfy_deactivation_nonce' ) ); ?>',
                            deactivation_reason: deactivation_reason
                        },
                        success:function(response){
                            window.location.replace(deactivate_url);
                        }
                });
                return true;
            });

            jQuery('#loginfy-deactivate-survey-overlay').on('click', function () {
                jQuery('#loginfy-deactivate-survey-overlay').removeClass('loginfy-deactivate-survey-is-visible');
                jQuery('#loginfy-deactivate-survey-modal').removeClass('loginfy-deactivate-survey-is-visible');
            });
        </script>
        <?php
    }

}
