<?php
defined('ABSPATH') || die("Can't access directly");

use Loginfy\Libs\Helper;

/**
 * Login styles.
 *
 * @package Loginfy
 *
 * @subpackage Login_Customizer
 */

?>

<style class="loginfy-login-customizer-style">
	<?php ob_start(); ?>

	/* Logo: None */
	body.loginfy-login-customizer:not(.loginfy-text-logo):not(.loginfy-image-logo) h1 {
		display: none;
	}

	body.loginfy-login-customizer:not(.loginfy-text-logo):not(.loginfy-image-logo) #login {
		padding-top: 0px;
	}

	/* Logo: Text Only */
	body.loginfy-login-customizer.loginfy-text-logo:not(.loginfy-image-logo) h1 a {
		background: none !important;
		text-indent: unset;
		width: auto !important;
		height: auto !important;
	}

	/* Logo: Image Only */
	body.loginfy-login-customizer.loginfy-image-logo:not(.loginfy-text-logo) #login h1 a {
		background-size: contain;
	}

	/* Logo: Image & Text */
	body.loginfy-login-customizer.loginfy-text-logo.loginfy-image-logo #login h1 {
		overflow: hidden;
		width: 350px;
		max-width: 80%;
		margin: 0 auto;
	}

	body.loginfy-login-customizer.loginfy-text-logo.loginfy-image-logo #login h1 a {
		width: 100%;
		height: auto;
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap;
		-webkit-box-align: end;
		-webkit-align-items: flex-end;
		-ms-flex-align: end;
		align-items: flex-end;
		-webkit-box-pack: center;
		-webkit-justify-content: center;
		-ms-flex-pack: center;
		justify-content: center;
		margin-bottom: 0px;
		background-size: 0px;
	}

	body.loginfy-login-customizer.loginfy-text-logo.loginfy-image-logo #login h1 a:before {
		content: "";
		background: inherit;
		background-size: contain;
		background-position: center;
		display: block;
		width: 100%;
		height: 84px;
		margin-bottom: 16px;
	}

	body.loginfy-login-customizer.loginfy-text-logo #login h1 a {
		text-indent: unset;
	}

	body.loginfy-login-customizer .loginfy-general-actions {
		position: absolute;
		top: 10px;
		left: 10px;
		z-index: 100;
	}

	body.loginfy-login-customizer .loginfy-preview-event {
		cursor: pointer;
		background-color: #008ec2;
		-webkit-border-radius: 100%;
		border-radius: 100%;
		color: #fff;
		width: 30px;
		height: 30px;
		text-align: center;
		border: 2px solid #fff;
		-webkit-box-shadow: 0 2px 1px rgba(46, 68, 83, .15);
		box-shadow: 0 2px 1px rgba(46, 68, 83, .15);
	}

	body.loginfy-login-customizer .loginfy-preview-event>span {
		margin-top: 5px;
	}

	body.loginfy-login-customizer .loginfy-general-actions>.loginfy-preview-event {
		display: inline-block;
	}

	body.loginfy-login-customizer .loginfy-form-container {
		width: 100%;
		position: relative;
	}

	body.loginfy-login-customizer #login {
		padding: 8% 0 0;
		position: relative;
		z-index: 2;
	}

	body.loginfy-login-customizer #login h1 a {
		position: relative;
		overflow: visible;
	}

	body.loginfy-login-customizer #login h1 a .loginfy-preview-event {
		position: absolute;
		left: -15px;
		top: -15px;
	}

	body.loginfy-login-customizer .customize-partial--loginform {
		top: 0 !important;
		left: -10px !important;
	}

	body.loginfy-login-customizer .loginfy-edit-loginform .customize-partial--loginform {
		opacity: 0 !important;
		visibility: hidden !important;
	}

	body.loginfy-login-customizer #login #nav {
		left: 0;
	}

	body.loginfy-login-customizer .loginfy-background .login-overlay {
		background-color: rgba(0, 0, 0, 0.4);
	}

	body.loginfy-login-customizer .loginfy-container {
		position: relative;
		height: 100vh;
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		width: 100%;
		overflow: hidden;
	}

	body.loginfy-login-customizer.loginfy-half-screen .loginfy-container:before {
		content: "";
	}

	body.loginfy-login-customizer.loginfy-half-screen .loginfy-container:after {
		content: "";
		position: absolute;
		top: 0;
		left: 0;
	}

	body.loginfy-login-customizer.loginfy-half-screen .loginfy-container:before,
	body.loginfy-login-customizer.loginfy-half-screen .loginfy-container:after,
	body.loginfy-login-customizer.loginfy-half-screen .loginfy-form-container {
		height: 100%;
		width: 50%;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top .loginfy-container:before,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top .loginfy-container:after,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-bottom .loginfy-container:before,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-bottom .loginfy-container:after,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top .loginfy-form-container,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-bottom .loginfy-form-container {
		height: 50%;
		width: 100%;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-container:before,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-container:after,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-right .loginfy-container:before,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-right .loginfy-container:after,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-form-container,
	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-right .loginfy-form-container {
		height: 100%;
		width: 50%;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-container {
		-webkit-box-orient: horizontal;
		-webkit-box-direction: reverse;
		-webkit-flex-direction: row-reverse;
		-ms-flex-direction: row-reverse;
		flex-direction: row-reverse;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-container:after {
		right: 0;
		left: auto;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top .loginfy-container {
		-webkit-box-orient: vertical;
		-webkit-box-direction: reverse;
		-webkit-flex-direction: column-reverse;
		-ms-flex-direction: column-reverse;
		flex-direction: column-reverse;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top .loginfy-container:after {
		bottom: 0;
		top: auto;
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-top #login h1 {
		top: 75%;
		left: 50%
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-bottom #login h1 {
		top: 25%;
		left: 50%
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left #login h1 {
		top: 50%;
		left: 75%
	}

	body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-right #login h1 {
		top: 50%;
		left: 25%
	}

	body.loginfy-login-customizer .loginfy-form-container {
		display: -webkit-box;
		display: -webkit-flex;
		display: -ms-flexbox;
		display: flex;
		-webkit-box-align: center;
		-webkit-align-items: center;
		-ms-flex-align: center;
		align-items: center;
		-webkit-box-pack: center;
		-webkit-justify-content: center;
		-ms-flex-pack: center;
		justify-content: center;
		overflow: hidden;
	}

	body.loginfy-login-customizer:not(.loginfy-half-screen) .loginfy-container .loginfy-form-container {
		width: 100%;
		min-height: 100vh
	}

	body.loginfy-login-customizer.loginfy-half-screen .loginfy-container {
		-webkit-flex-wrap: wrap;
		-ms-flex-wrap: wrap;
		flex-wrap: wrap
	}

	body.loginfy-login-customizer.loginfy-half-screen .loginfy-form-container {
		z-index: 0;
	}

	body.loginfy-login-customizer.loginfy-horizontal-align-left_center .loginfy-form-container {
		-webkit-box-pack: start;
		-webkit-justify-content: flex-start;
		-ms-flex-pack: start;
		justify-content: flex-start
	}

	body.loginfy-login-customizer.loginfy-horizontal-align-right_center .loginfy-form-container {
		-webkit-box-pack: end;
		-webkit-justify-content: flex-end;
		-ms-flex-pack: end;
		justify-content: flex-end
	}

	body.loginfy-login-customizer.loginfy-vertical-align-center_top .loginfy-form-container {
		-webkit-box-align: start;
		-webkit-align-items: flex-start;
		-ms-flex-align: start;
		align-items: flex-start
	}

	body.loginfy-login-customizer.loginfy-vertical-align-center_bottom .loginfy-form-container {
		-webkit-box-align: end;
		-webkit-align-items: flex-end;
		-ms-flex-align: end;
		align-items: flex-end
	}

	body.loginfy-login-customizer.ml-login-horizontal-align-1 .loginfy-form-container {
		-webkit-box-pack: start;
		-webkit-justify-content: flex-start;
		-ms-flex-pack: start;
		justify-content: flex-start
	}

	body.loginfy-login-customizer.ml-login-horizontal-align-3 .loginfy-form-container {
		-webkit-box-pack: end;
		-webkit-justify-content: flex-end;
		-ms-flex-pack: end;
		justify-content: flex-end
	}

	@media only screen and (max-width: 768px) {
		body.loginfy-login-customizer.loginfy-half-screen .loginfy-container>.loginfy-form-container {
			width: 50% !important;
		}
	}

	@media only screen and (max-width: 577px) {
		body.loginfy-login-customizer.loginfy-half-screen .loginfy-container>.loginfy-form-container {
			width: 100% !important;
		}

		body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-left .loginfy-container .loginfy-form-container,
		body.loginfy-login-customizer.loginfy-half-screen.jltloginfy-login-right .loginfy-container .loginfy-form-container {
			width: 100%;
		}
	}

	/* Skew */
	body.loginfy-login-customizer .loginfy-form-container:after {
		content: '';
		z-index: -1;
		top: 0px;
		left: -100%;
		width: 200%;
		height: 200%;
		position: absolute;
		-webkit-transform-origin: center right;
		-ms-transform-origin: center right;
		transform-origin: center right;
		display: none;
	}

	body.loginfy-login-customizer.loginfy-fullwidth .loginfy-form-container:after {
		display: block;
	}

	body.loginfy-login-customizer.loginfy-fullwidth.jltloginfy-login-right .loginfy-form-container:after {
		left: inherit;
		right: -100%;
	}

	body.loginfy-login-customizer.loginfy-fullwidth.jltloginfy-login-top .loginfy-form-container:after {
		top: inherit;
		bottom: 0;
	}

	body.loginfy-login-customizer .loginfy-background-wrapper {
		overflow: hidden;
	}

	body.loginfy-login-customizer .login-background,
	body.loginfy-login-customizer .login-background:after,
	body.loginfy-login-customizer .login-overlay,
	body.loginfy-login-customizer .loginfy-background-wrapper {
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: -1;
	}

	body.loginfy-login-customizer .login-background:after {
		content: '';
		z-index: 1;
	}

	#login #backtoblog,
	#login #nav {
		text-align: center;
	}


	<?php echo Helper::wp_kses_custom(apply_filters('wp_adminify_login_styles', ob_get_clean())); ?>
</style>
