(function ($) {

	function eventMinimizer( fn, delay ) {
		if ( window.eventFinished ) clearTimeout( window.eventFinished );
		window.eventFinished = setTimeout( fn, delay || 100 );
	}

	class CustomizerCSS {

		constructor() {
			this.styles = {}
		}

		addCSS( selector, property, value ) {
			if ( ! (selector in this.styles) ) this.styles[selector] = {}
			this.styles[selector][property] = value;
		}

		removeCSS( selector, property ) {
			let that = this;
			if ( ! (selector in this.styles) ) return;
			if ( typeof property == 'string' ) {
				delete this.styles[selector][property];
			} else {
				property.forEach( prop => {
					delete that.styles[selector][prop];
				})
			}
		}

		getCSS() {
			let styles = '';
			for ( let selector in this.styles ) {
				let css = '';
				for ( let prop in this.styles[selector] ) {
					css += `${prop}:${this.styles[selector][prop]};`;
				}
				styles += `${selector}{${css}}`;
			}
			return styles;
		}

		getStyle() {
			return `<style id="loginfy-customizer-custom-css">${ this.getCSS() }</style>`;
		}

		toDom() {
			$('html head #loginfy-customizer-custom-css').remove();
			$('html head').append( this.getStyle() );
		}

	}

	const customizer_css = new CustomizerCSS();

	function loginfyGetBoxedFormTemplates() {
		return ['template-02', 'template-13', 'template-15', 'template-16'];
	}

	function bgSizeHotfix() {
		window.dispatchEvent(new Event('resize'));
	}

	function updateLogoType( type ) {

		if ( type == "text-only" ) {
			$('body').removeClass('loginfy-image-logo').addClass('loginfy-text-logo');
		} else if ( type == "image-only" ) {
			$('body').removeClass('loginfy-text-logo').addClass('loginfy-image-logo');
		} else if ( type == "both" ) {
			$('body').addClass('loginfy-image-logo loginfy-text-logo');
		} else if ( type == "none" ) {
			$('body').removeClass('loginfy-image-logo loginfy-text-logo');
		}

	}

	function updateLogoText() {
		let text = wp.customize('_loginfy_[logo_text]').get();
		$('#login h1 a').text( text );
	}

	function updateLogoImage() {

		let image = wp.customize('_loginfy_[logo_image]').get();

		customizer_css.removeCSS( 'body.loginfy-login-customizer #login h1 a', ['background-image'] );

		if ( image && image['url'] != '' ) {
			customizer_css.addCSS( 'body.loginfy-login-customizer #login h1 a', 'background-image', `url(${image['url']})` );
		}

		customizer_css.toDom();

	}

	function updateLoginTitleStyle( modules ) {

		let logo_settings = wp.customize('_loginfy_[logo_settings]').get();

		if ( ! modules ) modules = wp.customize('_loginfy_[login_title_style]').get();

		for ( var module of Object.keys( modules ) ) {

			// Logo Width Height
			if ( module == 'logo_heigh_width' ) {

				customizer_css.removeCSS( 'body.loginfy-login-customizer #login h1 a', ['width', 'height'] );
				customizer_css.removeCSS( 'body.loginfy-login-customizer #login h1 a:before', ['width', 'height'] );

				let unit = modules[module]['unit'] || 'px';

				if ( logo_settings == 'image-only' ) {
					if ( modules[module]['width'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1 a', 'width', modules[module]['width'] + unit + '!important' );
					if ( modules[module]['height'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1 a', 'height', modules[module]['height'] + unit + '!important' );
				}

				if ( logo_settings == 'both' ) {
					if ( modules[module]['height'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1 a:before', 'height', modules[module]['height'] + unit + '!important' );
				}

			}

			// Logo Padding
			if ( module == 'logo_padding' ) {

				customizer_css.removeCSS( 'body.loginfy-login-customizer #login h1', ['padding-top', 'padding-right', 'padding-bottom', 'padding-left'] );

				let padding = modules[module], pd_unit = padding['unit'] || 'px';

				if ( padding['top'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1', 'padding-top', padding['top'] + pd_unit );
				if ( padding['right'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1', 'padding-right', padding['right'] + pd_unit );
				if ( padding['bottom'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1', 'padding-bottom', padding['bottom'] + pd_unit );
				if ( padding['left'] ) customizer_css.addCSS( 'body.loginfy-login-customizer #login h1', 'padding-left', padding['left'] + pd_unit );

			}

			// Login Title Typography
			if ( module == 'login_title_typography' ) {

				let lt_selector = 'body.loginfy-login-customizer #login h1 a';

				customizer_css.removeCSS( lt_selector, ['color', 'font-size', 'font-family', 'font-style', 'font-weight', 'letter-spacing', 'line-height', 'text-decoration', 'text-transform'] );

				let lt_typo = modules[module];

				if ( lt_typo['color'] )
					customizer_css.addCSS( lt_selector, 'color', lt_typo['color'] );

				if ( lt_typo['font-size'] )
					customizer_css.addCSS( lt_selector, 'font-size', lt_typo['font-size'] + lt_typo['unit'] );

				if ( lt_typo['font-family'] )
					customizer_css.addCSS( lt_selector, 'font-family', lt_typo['font-family'] );

				if ( lt_typo['font-style'] )
					customizer_css.addCSS( lt_selector, 'font-style', lt_typo['font-style'] );

				if ( lt_typo['font-weight'] )
					customizer_css.addCSS( lt_selector, 'font-weight', lt_typo['font-weight'] );

				if ( lt_typo['letter-spacing'] )
					customizer_css.addCSS( lt_selector, 'letter-spacing', lt_typo['letter-spacing'] + lt_typo['unit'] );

				if ( lt_typo['line-height'] )
					customizer_css.addCSS( lt_selector, 'line-height', lt_typo['line-height'] + lt_typo['unit'] );

				if ( lt_typo['text-decoration'] )
					customizer_css.addCSS( lt_selector, 'text-decoration', lt_typo['text-decoration'] );

				if ( lt_typo['text-transform'] )
					customizer_css.addCSS( lt_selector, 'text-transform', lt_typo['text-transform'] );

			}

		}

		customizer_css.toDom();

	}

	function updateLoginBackground() {

		let bg_color_opt, bg_color, gradient_bg, bg_video_type, bg_video_self_hosted, bg_video_youtube, bg_video_loop, bg_video_poster, bg_slideshow, bg_overlay_type, bg_overlay_color, bg_overlay_gradient_color, overlay_opacity;

		let bg_type = wp.customize('_loginfy_[jlt_loginfy_login_bg_type]').get();
		let selector = 'body.loginfy-login-customizer .login-background';

		customizer_css.removeCSS( selector, [ 'background', 'background-color', 'background-image', 'background-position', 'background-repeat', 'background-attachment', 'background-size'] );
		customizer_css.removeCSS( selector + ':after', ['background', 'background-color', 'background-image', 'opacity'] );

		$(selector).remove();
		$('body.loginfy-login-customizer .loginfy-background-wrapper').html('<div class="login-background"></div>');

		if ( bg_type == 'color_image' ) {

			bg_color_opt = wp.customize('_loginfy_[jlt_loginfy_login_bg_color_opt]').get();

			if ( bg_color_opt == 'color' ) {
				bg_color = wp.customize('_loginfy_[jlt_loginfy_login_bg_color]').get();
			} else {
				gradient_bg = wp.customize('_loginfy_[jlt_loginfy_login_gradient_bg]');
				if ( gradient_bg ) gradient_bg = gradient_bg.get();
			}

		} else if ( bg_type == 'video' ) {

			bg_video_type = wp.customize('_loginfy_[jlt_loginfy_login_bg_video_type]').get();
			// if ( bg_video_type ) bg_video_type = bg_video_type.get();

			if ( bg_video_type == 'self_hosted' ) {
				bg_video_self_hosted = wp.customize('_loginfy_[jlt_loginfy_login_bg_video_self_hosted]').get();
				// if ( bg_video_self_hosted ) bg_video_self_hosted = bg_video_self_hosted.get();
			} else {
				bg_video_youtube = wp.customize('_loginfy_[jlt_loginfy_login_bg_video_youtube]').get();
				// if ( bg_video_youtube ) bg_video_youtube = bg_video_youtube.get();
			}

			bg_video_loop = wp.customize('_loginfy_[jlt_loginfy_login_bg_video_loop]').get();
			// if ( bg_video_loop ) bg_video_loop = bg_video_loop.get();

			bg_video_poster = wp.customize('_loginfy_[jlt_loginfy_login_bg_video_poster]').get();
			// if ( bg_video_poster ) bg_video_poster = bg_video_poster.get();

		} else {

			bg_slideshow = wp.customize('_loginfy_[jlt_loginfy_login_bg_slideshow]').get();
			// if ( bg_slideshow ) bg_slideshow = bg_slideshow.get();
		}

		bg_overlay_type = wp.customize('_loginfy_[jlt_loginfy_login_bg_overlay_type]').get();
		bg_overlay_color = wp.customize('_loginfy_[jlt_loginfy_login_bg_overlay_color]').get();
		bg_overlay_gradient_color = wp.customize('_loginfy_[jlt_loginfy_login_bg_overlay_gradient_color]');
		if ( bg_overlay_gradient_color ) bg_overlay_gradient_color = bg_overlay_gradient_color.get();
		overlay_opacity = wp.customize('_loginfy_[jlt_loginfy_login_overlay_opacity]').get();

		if ( bg_type == 'color_image' ) {

			if ( bg_color_opt == 'color' ) {

				if ( bg_color ) {

					if ( bg_color['background-color'] ) customizer_css.addCSS( selector, 'background', bg_color['background-color'] );

					if ( bg_color['background-image'] && bg_color['background-image']['url'] ) {
						if ( bg_color['background-color'] ) customizer_css.addCSS( selector, 'background-color', bg_color['background-color'] );
						if ( bg_color['background-image']['url'] ) customizer_css.addCSS( selector, 'background-image', 'url(' + bg_color['background-image']['url'] + ')' );
						if ( bg_color['background-position'] ) customizer_css.addCSS( selector, 'background-position', bg_color['background-position'] );
						if ( bg_color['background-repeat'] ) customizer_css.addCSS( selector, 'background-repeat', bg_color['background-repeat'] );
						if ( bg_color['background-attachment'] ) customizer_css.addCSS( selector, 'background-attachment', bg_color['background-attachment'] );
						if ( bg_color['background-size'] ) customizer_css.addCSS( selector, 'background-size', bg_color['background-size'] );
					}

				}

			} else {

				if ( gradient_bg ) {

					if ( gradient_bg['background-color'] ) customizer_css.addCSS( selector, 'background', gradient_bg['background-color'] );

					if ( gradient_bg['background-color'] && gradient_bg['background-gradient-color'] ) {
						let gradient_color = gradient_bg['background-color'] +', '+ gradient_bg['background-gradient-color'];
						if ( gradient_bg['background-gradient-direction'] ) gradient_color = gradient_bg['background-gradient-direction'] +', '+ gradient_color;
						customizer_css.addCSS( selector, 'background', 'linear-gradient('+ gradient_color +')' );
					}

				}

			}

		} else if ( bg_type == 'video' ) {

			let source = bg_video_type == 'youtube' ? bg_video_youtube : bg_video_self_hosted;

			if ( source ) {

				if ( bg_video_type == 'youtube' ) {
					new vidim('.login-background', {
						src: source,
						type: 'YouTube',
						poster: bg_video_poster,
						quality: 'hd1080',
						muted: true,
						loop: bg_video_loop,
						startAt: 0,
						showPosterBeforePlay: !! bg_video_poster
					});
				}

				if ( bg_video_type == 'self_hosted' && source['url'] ) {
					new vidim('.login-background', {
						src: [{
							type: 'video/mp4',
							src: source['url'],
						}],
						poster: bg_video_poster,
						showPosterBeforePlay: !! bg_video_poster,
						loop: !! bg_video_loop
					});
				}

			}

		} else if ( bg_type == 'slideshow' ) {

			if ( bg_slideshow.trim() ) {

				bg_slideshow = bg_slideshow.trim().split(',').map( Number );

				wp.ajax.post( 'query-attachments', {
					query: { post__in: bg_slideshow }
				}).then( function( slides ) {

					slides = slides.sort( function( sl1, sl2 ) {
						return bg_slideshow.indexOf( sl1.id ) < bg_slideshow.indexOf( sl2.id ) ? -1 : 1;
					}).map( function(slide) {
						return { src: slide.url }
					});

					jQuery('body.loginfy-login-customizer .login-background').vegas({
						slides: slides,
						transition: 'fade',
						delay: 5000,
						timer: false
					});

				});
			}

		}

		if ( bg_overlay_type == 'color' && bg_overlay_color && bg_overlay_color['background-color'] ) {
			customizer_css.addCSS( selector + ':after', 'background', bg_overlay_color['background-color'] );
		}

		if ( bg_overlay_type == 'gradient' && bg_overlay_gradient_color ) {

			if ( bg_overlay_gradient_color['background-color'] ) customizer_css.addCSS( selector + ':after', 'background', bg_overlay_gradient_color['background-color'] );

			if ( bg_overlay_gradient_color['background-color'] && bg_overlay_gradient_color['background-gradient-color'] ) {
				let gradient_color = bg_overlay_gradient_color['background-color'] +', '+ bg_overlay_gradient_color['background-gradient-color'];
				if ( bg_overlay_gradient_color['background-gradient-direction'] ) gradient_color = bg_overlay_gradient_color['background-gradient-direction'] +', '+ gradient_color;
				customizer_css.addCSS( selector + ':after', 'background', 'linear-gradient('+ gradient_color +')' );
			}

		}

		if ( bg_overlay_type && overlay_opacity ) {
			customizer_css.addCSS( selector + ':after', 'opacity', overlay_opacity / 100  );
		}

		customizer_css.toDom();

		bgSizeHotfix();

		// overlay: LOGINFY_ASSETS . 'vendors/vegas/overlays/01.png'

	}

	function updateLoginLayoutAndBG() {

		let login_width, login_column, login_horizontal, login_vertical, login_bg_type, login_bg_color, login_bg_g_color, login_bg_skew;
		let $body = $('body');

		login_width 		= wp.customize( '_loginfy_[alignment_login_width]' ).get();
		login_column 		= wp.customize( '_loginfy_[alignment_login_column]' ).get();
		login_horizontal 	= wp.customize( '_loginfy_[alignment_login_horizontal]' ).get();
		login_vertical 		= wp.customize( '_loginfy_[alignment_login_vertical]' ).get();
		login_bg_type 		= wp.customize( '_loginfy_[alignment_login_bg_type]' ).get();
		login_bg_color 		= wp.customize( '_loginfy_[alignment_login_bg_color]' ).get();
		login_bg_g_color 	= wp.customize( '_loginfy_[alignment_login_bg_gradient_color]' );
		login_bg_skew 		= wp.customize( '_loginfy_[alignment_login_bg_skew]' );

		if ( login_bg_g_color ) login_bg_g_color = login_bg_g_color.get();
		if ( login_bg_skew ) login_bg_skew = login_bg_skew.get();

		$body.removeClass( 'loginfy-half-screen loginfy-fullwidth jltloginfy-login-top jltloginfy-login-right jltloginfy-login-bottom jltloginfy-login-left loginfy-horizontal-align-center_center loginfy-horizontal-align-left_center loginfy-horizontal-align-right_center loginfy-vertical-align-center_top loginfy-vertical-align-center_center loginfy-vertical-align-center_bottom' );

		if ( 'fullwidth' == login_width ) $body.addClass( 'loginfy-fullwidth' );

		if ( 'width_two_column' == login_width ) $body.addClass( 'loginfy-half-screen' );

		$body.addClass( 'jltloginfy-login-' + login_column ).addClass( 'loginfy-horizontal-align-' + login_horizontal ).addClass( 'loginfy-vertical-align-' + login_vertical );

		let selector_fullwidth = 'body.loginfy-login-customizer.loginfy-fullwidth .loginfy-form-container:after';
		let selector_half = 'body.loginfy-login-customizer.loginfy-half-screen .loginfy-container:before';

		customizer_css.removeCSS( selector_fullwidth, ['transform', 'clip-path', 'background', 'background-image', 'background-position', 'background-repeat', 'background-attachment', 'background-size'] );
		customizer_css.removeCSS( selector_half, ['background', 'background-image', 'background-position', 'background-repeat', 'background-attachment', 'background-size'] );

		let selector = login_width == 'fullwidth' ? selector_fullwidth : selector_half;


		if ( login_width == 'fullwidth' ) {
			if ( login_bg_skew > 0 ) {
				customizer_css.addCSS( selector, 'transform', 'skewX(' + login_bg_skew + 'deg)' );
				customizer_css.addCSS( selector, 'clip-path', 'none' );
			} else {
				customizer_css.addCSS( selector, 'transform', 'skewY(' + login_bg_skew + 'deg)' );
				customizer_css.addCSS( selector, 'clip-path', 'none' );
			}
		}

		if ( login_bg_type == 'color' && login_bg_color ) {

			if ( login_bg_color['background-color'] ) customizer_css.addCSS( selector, 'background', login_bg_color['background-color'] );

			if ( login_bg_color['background-image'] && login_bg_color['background-image']['url'] ) {
				if ( login_bg_color['background-image']['url'] ) customizer_css.addCSS( selector, 'background-image', `url(${login_bg_color['background-image']['url']})` );
				if ( login_bg_color['background-position'] ) customizer_css.addCSS( selector, 'background-position', login_bg_color['background-position'] );
				if ( login_bg_color['background-repeat'] ) customizer_css.addCSS( selector, 'background-repeat', login_bg_color['background-repeat'] );
				if ( login_bg_color['background-attachment'] ) customizer_css.addCSS( selector, 'background-attachment', login_bg_color['background-attachment'] );
				if ( login_bg_color['background-size'] ) customizer_css.addCSS( selector, 'background-size', login_bg_color['background-size'] );
			}

		} else if ( login_bg_type == 'gradient' && login_bg_g_color ) {

			if ( login_bg_g_color['background-color'] ) customizer_css.addCSS( selector, 'background', login_bg_g_color['background-color'] );

			if ( login_bg_g_color['background-color'] && login_bg_g_color['background-gradient-color'] ) {
				let gradient_color = login_bg_g_color['background-color'] +', '+ login_bg_g_color['background-gradient-color'];
				if ( login_bg_g_color['background-gradient-direction'] ) gradient_color = login_bg_g_color['background-gradient-direction'] +', '+ gradient_color;
				customizer_css.addCSS( selector, 'background', 'linear-gradient('+ gradient_color +')' );
			}

		}

		customizer_css.toDom();
		window.dispatchEvent(new Event('resize'));

	}

	function updateFormStyles() {

		let template, selector, login_form_bg_type, login_form_bg_color, login_form_bg_gradient, login_form_height_width, login_form_margin, login_form_padding, login_form_border, login_form_border_radius, login_form_box_shadow;

		template 					= wp.customize( '_loginfy_[templates]' ).get();
		login_form_bg_type 			= wp.customize( '_loginfy_[login_form_bg_type]' ).get();
		login_form_bg_color 		= wp.customize( '_loginfy_[login_form_bg_color]' ).get();
		login_form_bg_gradient 		= wp.customize( '_loginfy_[login_form_bg_gradient]' );
		login_form_height_width 	= wp.customize( '_loginfy_[login_form_height_width]' ).get();
		login_form_margin 			= wp.customize( '_loginfy_[login_form_margin]' );
		login_form_padding 			= wp.customize( '_loginfy_[login_form_padding]' );
		login_form_border 			= wp.customize( '_loginfy_[login_form_border]' ).get();
		login_form_border_radius 	= wp.customize( '_loginfy_[login_form_border_radius]' );
		login_form_box_shadow 		= wp.customize( '_loginfy_[login_form_box_shadow]' ).get();

		if ( login_form_bg_gradient ) login_form_bg_gradient = login_form_bg_gradient.get();
		if ( login_form_margin ) login_form_margin = login_form_margin.get();
		if ( login_form_padding ) login_form_padding = login_form_padding.get();
		if ( login_form_border_radius ) login_form_border_radius = login_form_border_radius.get();

		selector_login = 'body.loginfy-login-customizer #login';
		selector_login_form = 'body.loginfy-login-customizer #loginform';

		let props = ['background', 'background-color', 'background-image', 'background-position', 'background-repeat', 'background-attachment', 'background-size', 'width', 'height', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'border-top', 'border-right', 'border-bottom', 'border-left', 'border-raddius', 'box-shadow'];

		customizer_css.removeCSS( selector_login, props );
		customizer_css.removeCSS( selector_login_form, props );

		selector =
      loginfyGetBoxedFormTemplates().indexOf(template) > -1
        ? selector_login
        : selector_login_form;

		// Background Color
		if ( login_form_bg_type == 'color' ) {
			if ( login_form_bg_color ) {
				if ( login_form_bg_color['background-color'] ) customizer_css.addCSS( selector, 'background', login_form_bg_color['background-color'] );
				if ( login_form_bg_color['background-image'] && login_form_bg_color['background-image']['url'] ) {
					if ( login_form_bg_color['background-image']['url'] ) customizer_css.addCSS( selector, 'background-image', `url(${login_form_bg_color['background-image']['url']})` );
					if ( login_form_bg_color['background-position'] ) customizer_css.addCSS( selector, 'background-position', login_form_bg_color['background-position'] );
					if ( login_form_bg_color['background-repeat'] ) customizer_css.addCSS( selector, 'background-repeat', login_form_bg_color['background-repeat'] );
					if ( login_form_bg_color['background-attachment'] ) customizer_css.addCSS( selector, 'background-attachment', login_form_bg_color['background-attachment'] );
					if ( login_form_bg_color['background-size'] ) customizer_css.addCSS( selector, 'background-size', login_form_bg_color['background-size'] );
				}
			}
		}

		// Background Gradient
		if ( login_form_bg_type == 'gradient' ) {
			if ( login_form_bg_gradient ) {
				if ( login_form_bg_gradient['background-color'] ) customizer_css.addCSS( selector, 'background', login_form_bg_gradient['background-color'] );
				if ( login_form_bg_gradient['background-color'] && login_form_bg_gradient['background-gradient-color'] ) {
					let gradient_color = login_form_bg_gradient['background-color'] +', '+ login_form_bg_gradient['background-gradient-color'];
					if ( login_form_bg_gradient['background-gradient-direction'] ) gradient_color = login_form_bg_gradient['background-gradient-direction'] +', '+ gradient_color;
					customizer_css.addCSS( selector, 'background', `linear-gradient(${gradient_color})` );
				}
			}
		}

		// Height Width
		if ( login_form_height_width ) {
			let unit = login_form_height_width['unit'] || 'px';
			if ( login_form_height_width['width'] ) customizer_css.addCSS( selector_login, 'width', login_form_height_width['width'] + unit );
			if ( login_form_height_width['height'] ) customizer_css.addCSS( selector_login_form, 'height', login_form_height_width['height'] + unit );
		}

		// Margin
		if ( login_form_margin ) {
			let unit = login_form_margin['unit'] || 'px';
			if ( login_form_margin['top'] ) customizer_css.addCSS( selector_login_form, 'margin-top', login_form_margin['top'] + unit );
			if ( login_form_margin['right'] ) customizer_css.addCSS( selector_login_form, 'margin-right', login_form_margin['right'] + unit );
			if ( login_form_margin['bottom'] ) customizer_css.addCSS( selector_login_form, 'margin-bottom', login_form_margin['bottom'] + unit );
			if ( login_form_margin['left'] ) customizer_css.addCSS( selector_login_form, 'margin-left', login_form_margin['left'] + unit );
		}

		// Padding
		if ( login_form_padding ) {
			let unit = login_form_padding['unit'] || 'px';
			if ( login_form_padding['top'] ) customizer_css.addCSS( selector, 'padding-top', login_form_padding['top'] + unit  );
			if ( login_form_padding['right'] ) customizer_css.addCSS( selector, 'padding-right', login_form_padding['right'] + unit  );
			if ( login_form_padding['bottom'] ) customizer_css.addCSS( selector, 'padding-bottom', login_form_padding['bottom'] + unit  );
			if ( login_form_padding['left'] ) customizer_css.addCSS( selector, 'padding-left', login_form_padding['left'] + unit  );
		}

		// Border
		if ( login_form_border ) {
			let style = login_form_border['style'], color = login_form_border['color'];
			if ( color && style ) {
				if ( login_form_border['top'] ) customizer_css.addCSS( selector, 'border-top', `${login_form_border['top']}px ${style} ${color}` );
				if ( login_form_border['right'] ) customizer_css.addCSS( selector, 'border-right', `${login_form_border['right']}px ${style} ${color}` );
				if ( login_form_border['bottom'] ) customizer_css.addCSS( selector, 'border-bottom', `${login_form_border['bottom']}px ${style} ${color}` );
				if ( login_form_border['left'] ) customizer_css.addCSS( selector, 'border-left', `${login_form_border['left']}px ${style} ${color}` );
			}
		}

		// Border Radius
		if ( login_form_border_radius ) {

			let lf_br = login_form_border_radius,
				lf_br_unit = login_form_border_radius['unit'] || 'px',
				lf_br_borders = [ lf_br['top'], lf_br['right'], lf_br['bottom'], lf_br['left'] ];

			if ( lf_br_borders.some( br => br != '' ) ) {
				customizer_css.addCSS( selector, 'border-radius', lf_br_borders.map( _lf_br => (_lf_br || 0) + lf_br_unit ).join(' ') );
			}

		}

		// Box Shadow
		if ( login_form_box_shadow ) {

			let bs_color 		= login_form_box_shadow['bs_color'] + ' ',
				bs_hz 			= ( login_form_box_shadow['bs_hz'] || 0 ) + 'px ',
				bs_ver 			= ( login_form_box_shadow['bs_ver'] || 0 ) + 'px ',
				bs_blur 		= ( login_form_box_shadow['bs_blur'] || 0 ) + 'px ',
				bs_spread 		= ( login_form_box_shadow['bs_spread'] || 0 ) + 'px ',
				bs_spread_pos 	= login_form_box_shadow['bs_spread_pos'];

			if ( bs_color && bs_color.trim() ) customizer_css.addCSS( selector, 'box-shadow', bs_hz + bs_ver + bs_blur + bs_spread + bs_color + bs_spread_pos );

		}

		customizer_css.toDom();

	}

	function updateFormFields() {

		let login_form_fields = wp.customize( '_loginfy_[login_form_fields]' ).get();

		for ( let label of Object.keys(login_form_fields) ) {

			if ( label.startsWith( 'label_' ) ) {

				let _id = label.replaceAll('label_', 'wp_adminify_');
				let id = _id.replaceAll('_','-');
				$('#' + id ).text(login_form_fields[label]);

			} else {

				let selectors,
					selector_prefix = 'body.loginfy-login-customizer ',
					selectors_a = ['#loginfy-username', '#loginfy-password', '#loginfy-remember-me', '#loginfy-lost-password', '#backtoblog a'],
					selectors_b = ['#loginform input[type=text]', '#loginform input[type=email]', '#loginform textarea', '#loginform input[type=password]'],
					selectors_c = ['#loginform label', '#loginfy-lost-password', '#backtoblog a'];

				switch ( label ) {

					case 'fields_user_placeholder':
						$( '#loginform #user_login' ).attr( 'placeholder', login_form_fields['fields_user_placeholder'] );
						break;

					case 'fields_pass_placeholder':
						$( '#loginform #user_pass' ).attr( 'placeholder', login_form_fields['fields_pass_placeholder'] );
						break;

					case 'style_label_font_size':
						customizer_css.removeCSS( selectors_a.join(','), 'font-size' );
						if ( login_form_fields['style_label_font_size'] ) customizer_css.addCSS( selectors_a.join(','), 'font-size', login_form_fields['style_label_font_size'] + 'px' );
						break;

					case 'style_fields_height':
						selectors = selectors_b.map( sel => selector_prefix + sel );
						customizer_css.removeCSS( selectors.join(','), 'height' );
						if ( login_form_fields['style_fields_height'] ) customizer_css.addCSS( selectors.join(','), 'height', login_form_fields['style_fields_height'] + 'px' );
						break;

					case 'style_fields_font_size':
						selectors = selectors_b.map( sel => selector_prefix + sel );
						customizer_css.removeCSS( selectors.join(','), 'font-size' );
						if ( login_form_fields['style_fields_font_size'] ) customizer_css.addCSS( selectors.join(','), 'font-size', login_form_fields['style_fields_font_size'] + 'px' );
						break;

					case 'style_fields_bg':
						selectors = selectors_b.map( sel => selector_prefix + sel );
						customizer_css.removeCSS( selectors.join(','), 'background' );
						if ( login_form_fields['style_fields_bg']['color'] ) customizer_css.addCSS( selectors.join(','), 'background', login_form_fields['style_fields_bg']['color'] );

						selectors = selectors_b.map( sel => selector_prefix + sel + ':focus' );
						customizer_css.removeCSS( selectors.join(','), 'background' );
						if ( login_form_fields['style_fields_bg']['focus'] ) customizer_css.addCSS( selectors.join(','), 'background', login_form_fields['style_fields_bg']['focus'] + ' !important' );
						break;

					case 'style_label_color':
						selectors = selectors_c.map( sel => selector_prefix + sel );
						customizer_css.removeCSS( selectors.join(','), 'color' );
						if ( login_form_fields['style_label_color'] ) customizer_css.addCSS( selectors.join(','), 'color', login_form_fields['style_label_color'] );
						break;

					case 'style_fields_color':

						selectors = selectors_b.concat( selectors_b.map( sel => sel + '::placeholder' ) );
						selectors = selectors.map( sel => selector_prefix + sel );

						customizer_css.removeCSS( selectors.join(','), 'color' );
						if ( login_form_fields['style_fields_color']['color'] ) customizer_css.addCSS( selectors.join(','), 'color', login_form_fields['style_fields_color']['color'] );

						selectors = selectors_b.map( sel => sel + ':focus' );
						selectors = selectors.concat( selectors.map( function(sel) { return sel + '::placeholder' } ) );
						selectors = selectors.map( sel => selector_prefix + sel );

						customizer_css.removeCSS( selectors.join(','), 'color' );
						if ( login_form_fields['style_fields_color']['focus'] ) customizer_css.addCSS( selectors.join(','), 'color', login_form_fields['style_fields_color']['focus'] );

						break;

					case 'style_border':

						selectors = selectors_b.map( sel => selector_prefix + sel );
						let fl_sb = login_form_fields['style_border'];

						customizer_css.removeCSS( selectors.join(','), ['border-top', 'border-right', 'border-bottom', 'border-left'] );

						if ( fl_sb['color'] && fl_sb['style'] ) {
							if ( fl_sb['top'] ) customizer_css.addCSS( selectors.join(','), 'border-top', `${fl_sb['top']}px ${fl_sb['style']} ${fl_sb['color']}` );
							if ( fl_sb['right'] ) customizer_css.addCSS( selectors.join(','), 'border-right', `${fl_sb['right']}px ${fl_sb['style']} ${fl_sb['color']}` );
							if ( fl_sb['bottom'] ) customizer_css.addCSS( selectors.join(','), 'border-bottom', `${fl_sb['bottom']}px ${fl_sb['style']} ${fl_sb['color']}` );
							if ( fl_sb['left'] ) customizer_css.addCSS( selectors.join(','), 'border-left', `${fl_sb['left']}px ${fl_sb['style']} ${fl_sb['color']}` );
						}

						break;

					case 'style_border_radius':

						selectors = selectors_b.map( sel => selector_prefix + sel );
						let fl_br = login_form_fields['style_border_radius'], fl_br_unit = fl_br['unit'], fl_borders = [ fl_br['top'], fl_br['right'], fl_br['bottom'], fl_br['left'] ];

						customizer_css.removeCSS( selectors.join(','), 'border-radius' );

						if ( fl_borders.some( br => br != '' ) ) {
							customizer_css.addCSS( selectors.join(','), 'border-radius', fl_borders.map( _fl_br => (_fl_br || 0) + fl_br_unit ).join(' ') );
						}

						break;

					case 'fields_margin':

						selectors = selectors_b.map( sel => selector_prefix + sel );
						let fl_margin = login_form_fields['fields_margin'], fl_mr_unit = fl_margin['unit'];

						customizer_css.removeCSS( selectors.join(','), ['margin-top', 'margin-right', 'margin-bottom', 'margin-left'] );

						if ( fl_margin['top'] ) customizer_css.addCSS( selectors.join(','), 'margin-top', fl_margin['top'] + fl_mr_unit );
						if ( fl_margin['right'] ) customizer_css.addCSS( selectors.join(','), 'margin-right', fl_margin['right'] + fl_mr_unit );
						if ( fl_margin['bottom'] ) customizer_css.addCSS( selectors.join(','), 'margin-bottom', fl_margin['bottom'] + fl_mr_unit );
						if ( fl_margin['left'] ) customizer_css.addCSS( selectors.join(','), 'margin-left', fl_margin['left'] + fl_mr_unit );

						break;

					case 'fields_padding':

						selectors = selectors_b.map( sel => selector_prefix + sel );
						let fl_padding = login_form_fields['fields_padding'], fl_pd_unit = fl_padding['unit'];

						customizer_css.removeCSS( selectors.join(','), ['padding-top', 'padding-right', 'padding-bottom', 'padding-left'] );

						if ( fl_padding['top'] ) customizer_css.addCSS( selectors.join(','), 'padding-top', fl_padding['top'] + fl_pd_unit );
						if ( fl_padding['right'] ) customizer_css.addCSS( selectors.join(','), 'padding-right', fl_padding['right'] + fl_pd_unit );
						if ( fl_padding['bottom'] ) customizer_css.addCSS( selectors.join(','), 'padding-bottom', fl_padding['bottom'] + fl_pd_unit );
						if ( fl_padding['left'] ) customizer_css.addCSS( selectors.join(','), 'padding-left', fl_padding['left'] + fl_pd_unit );

						break;

					case 'fields_bs_color':

						selectors = selectors_b.map( sel => selector_prefix + sel );

						customizer_css.removeCSS( selectors, 'box-shadow' );

						let fl_bs_color 		= login_form_fields['fields_bs_color'] + ' ',
							fl_bs_hz 			= ( login_form_fields['fields_bs_hz'] || 0 ) + 'px ',
							fl_bs_ver 			= ( login_form_fields['fields_bs_ver'] || 0 ) + 'px ',
							fl_bs_blur 			= ( login_form_fields['fields_bs_blur'] || 0 ) + 'px ',
							fl_bs_spread 		= ( login_form_fields['fields_bs_spread'] || 0 ) + 'px ',
							fl_bs_spread_pos 	= login_form_fields['fields_bs_spread_pos'];

						if ( fl_bs_color && fl_bs_color.trim() ) customizer_css.addCSS( selectors.join(','), 'box-shadow', fl_bs_hz + fl_bs_ver + fl_bs_blur + fl_bs_spread + fl_bs_color + fl_bs_spread_pos );

						break;

					case 'input_login':
						$( '#loginform input[name="wp-submit"]' ).val( login_form_fields['input_login'] );
						break;

				}

			}

		}

		customizer_css.toDom();

	}

	function updateFormButton() {

		let button_size 		= wp.customize( '_loginfy_[button_size]' ),
			button_font_size 	= wp.customize( '_loginfy_[button_font_size]' ).get(),
			button_settings 	= wp.customize( '_loginfy_[login_form_button_settings]' ).get(),
			selector 			= 'body.loginfy-login-customizer #loginform #wp-submit';

		if ( button_size ) button_size = button_size.get();

		customizer_css.removeCSS( selector, ['background', 'color', 'text-shadow', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'border-top', 'border-right', 'border-bottom', 'border-left', 'border-radius', 'box-shadow'] );

		customizer_css.removeCSS( selector + ':hover', ['background', 'color', 'text-shadow'] );

		// Button Width Height
		if ( button_size && button_size['unit'] ) {
			if ( button_size['width'] ) customizer_css.addCSS( selector, 'width', button_size['width'] + button_size['unit'] );
			if ( button_size['height'] ) customizer_css.addCSS( selector, 'height', button_size['height'] + button_size['unit'] );
		}

		// Button Font Size
		if ( button_font_size ) customizer_css.addCSS( selector, 'font-size', button_font_size + 'px' );

		// Button Settings
		for ( let label of Object.keys(button_settings) ) {

			switch ( label ) {

				case 'button_bg':
					if ( button_settings[label] ) customizer_css.addCSS( selector, 'background', button_settings[label] );
					break;

				case 'button_text_color':
					if ( button_settings[label] ) customizer_css.addCSS( selector, 'color', button_settings[label] );
					break;

				case 'button_text_shadow':

					let btn_ts_color 	= button_settings[label]['ts_color'] + ' ',
						btn_ts_hz 		= ( button_settings[label]['ts_hz'] || 0 ) + 'px ',
						btn_ts_ver 		= ( button_settings[label]['ts_ver'] || 0 ) + 'px ',
						btn_ts_blur 	= ( button_settings[label]['ts_ver'] || 0 ) + 'px ';

					if ( btn_ts_color && btn_ts_color.trim() ) customizer_css.addCSS( selector, 'text-shadow', btn_ts_hz + btn_ts_ver + btn_ts_blur + btn_ts_color );

					break;

				case 'button_bg_hover':
					if ( button_settings[label] ) customizer_css.addCSS( selector + ':hover', 'background', button_settings[label] );
					break;

				case 'button_text_hover':
					if ( button_settings[label] ) customizer_css.addCSS( selector + ':hover', 'color', button_settings[label] );
					break;

				case 'button_text_shadow_hover':

					let btn_ts_color_hover 	= button_settings[label]['ts_hover'] + ' ',
						btn_ts_hz_hover 	= ( button_settings[label]['ts_hz_hover'] || 0 ) + 'px ',
						btn_ts_ver_hover 	= ( button_settings[label]['ts_ver_hover'] || 0 ) + 'px ',
						btn_ts_blur_hover 	= ( button_settings[label]['ts_blur_hover'] || 0 ) + 'px ';

					if ( btn_ts_color_hover && btn_ts_color_hover.trim() ) customizer_css.addCSS( selector + ':hover', 'text-shadow', btn_ts_hz_hover + btn_ts_ver_hover + btn_ts_blur_hover + btn_ts_color_hover );

					break;

				case 'button_margin':

					let btn_margin = button_settings[label], btn_mr_unit = btn_margin['unit'];

					if ( btn_margin['top'] ) customizer_css.addCSS( selector, 'margin-top', btn_margin['top'] + btn_mr_unit );
					if ( btn_margin['right'] ) customizer_css.addCSS( selector, 'margin-right', btn_margin['right'] + btn_mr_unit );
					if ( btn_margin['bottom'] ) customizer_css.addCSS( selector, 'margin-bottom', btn_margin['bottom'] + btn_mr_unit );
					if ( btn_margin['left'] ) customizer_css.addCSS( selector, 'margin-left', btn_margin['left'] + btn_mr_unit );

					break;

				case 'button_padding':

					let btn_padding = button_settings[label], btn_pd_unit = btn_padding['unit'];

					if ( btn_padding['top'] ) customizer_css.addCSS( selector, 'padding-top', btn_padding['top'] + btn_pd_unit );
					if ( btn_padding['right'] ) customizer_css.addCSS( selector, 'padding-right', btn_padding['right'] + btn_pd_unit );
					if ( btn_padding['bottom'] ) customizer_css.addCSS( selector, 'padding-bottom', btn_padding['bottom'] + btn_pd_unit );
					if ( btn_padding['left'] ) customizer_css.addCSS( selector, 'padding-left', btn_padding['left'] + btn_pd_unit );

					break;

				case 'button_border':

					let btn_border = button_settings[label];

					if ( btn_border['color'] && btn_border['style'] ) {
						if ( btn_border['top'] ) customizer_css.addCSS( selector, 'border-top', `${btn_border['top']}px ${btn_border['style']} ${btn_border['color']}` );
						if ( btn_border['right'] ) customizer_css.addCSS( selector, 'border-right', `${btn_border['right']}px ${btn_border['style']} ${btn_border['color']}` );
						if ( btn_border['bottom'] ) customizer_css.addCSS( selector, 'border-bottom', `${btn_border['bottom']}px ${btn_border['style']} ${btn_border['color']}` );
						if ( btn_border['left'] ) customizer_css.addCSS( selector, 'border-left', `${btn_border['left']}px ${btn_border['style']} ${btn_border['color']}` );
					}

					break;

				case 'button_border_radius':

					let btn_border_rd = button_settings[label], btn_border_rd_unit = btn_border_rd['unit'], btn_borders_rd = [ btn_border_rd['top'], btn_border_rd['right'], btn_border_rd['bottom'], btn_border_rd['left'] ];

					if ( btn_borders_rd.some( br => br != '' ) ) {
						customizer_css.addCSS( selector, 'border-radius', btn_borders_rd.map( _btn_border_rd => (_btn_border_rd || 0) + btn_border_rd_unit ).join(' ') );
					}

					break;

				case 'button_box_shadow':

					let btn_bs_color 		= button_settings[label]['bs_color'] + ' ',
						btn_bs_hz 			= ( button_settings[label]['bs_hz'] || 0 ) + 'px ',
						btn_bs_ver 			= ( button_settings[label]['bs_ver'] || 0 ) + 'px ',
						btn_bs_blur 		= ( button_settings[label]['bs_blur'] || 0 ) + 'px ',
						btn_bs_spread 		= ( button_settings[label]['bs_spread'] || 0 ) + 'px ',
						btn_bs_spread_pos 	= button_settings[label]['bs_spread_pos'];

					if ( btn_bs_color && btn_bs_color.trim() ) customizer_css.addCSS( selector, 'box-shadow', btn_bs_hz + btn_bs_ver + btn_bs_blur + btn_bs_spread + btn_bs_color + btn_bs_spread_pos );

					break;

			}

		}

		customizer_css.toDom();

	}

	function updateFormOthers() {

		let is_registration_open = LoginfyCustomizer.anyone_can_register;
		let form_disable_register;
		let form_button_remember_me        = wp.customize( '_loginfy_[login_form_button_remember_me]' ).get()              == ( 0 || false ) ? false : true,
		    form_disable_lost_pass         = wp.customize( '_loginfy_[login_form_disable_lost_pass]' ).get()               == ( 0 || false ) ? false : true,
		    form_disable_privacy_policy    = wp.customize( '_loginfy_[login_form_disable_privacy_policy]' ).get()    == ( 0 || false ) ? false : true,
		    form_disable_language_switcher = wp.customize( '_loginfy_[login_form_disable_language_switcher]' ).get() == ( 0 || false ) ? false : true,
		    form_disable_back_to_site      = wp.customize( '_loginfy_[login_form_disable_back_to_site]' ).get()            == ( 0 || false ) ? false : true;
		if( is_registration_open == 1 ){
			form_disable_register 		= wp.customize( '_loginfy_[login_form_disable_register]' ).get() == ( 0 || false ) ? false : true;
		}else{
			form_disable_register = false;
		}

		customizer_css.removeCSS( 'body.loginfy-login-customizer p.forgetmenot', 'display' );
		customizer_css.removeCSS( 'body.loginfy-login-customizer p#nav', 'display' );
		customizer_css.removeCSS( 'body.loginfy-login-customizer p#backtoblog', 'display' );
		

		if ( form_button_remember_me ) customizer_css.addCSS( 'body.loginfy-login-customizer p.forgetmenot', 'display', 'none' );

		if( is_registration_open == 1 ){
			customizer_css.addCSS( 'body.loginfy-login-customizer p#nav', 'color', 'revert' );
			if ( form_disable_register ) {
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav a:nth-child(1)', 'display', 'none' );
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav', 'color', 'transparent !important' );
			}else{
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav a:nth-child(1)', 'display', 'revert' );
			}
			if ( form_disable_lost_pass ) {
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav a:nth-child(2)', 'display', 'none' );
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav', 'color', 'transparent !important' );
			}else{
				customizer_css.addCSS( 'body.loginfy-login-customizer p#nav a:nth-child(2)', 'display', 'revert' );
			}
		}else{
			if ( form_disable_lost_pass ) customizer_css.addCSS( 'body.loginfy-login-customizer p#nav', 'display', 'none' );
		}

		// Back to website
		if ( form_disable_back_to_site ) customizer_css.addCSS( 'body.loginfy-login-customizer p#backtoblog', 'display', 'none' );

		

		customizer_css.toDom();

	}

	function updateFormCredits() {

		let adminify_credits 		= wp.customize( '_loginfy_[jlt_loginfy_credits]' ).get() == ( 0 || false ) ? false : true,
			credits_text_color 		= wp.customize( '_loginfy_[credits_text_color]' ).get(),
			credits_logo_position 	= wp.customize( '_loginfy_[credits_logo_position]' ).get();

		customizer_css.removeCSS( 'body.loginfy-login-customizer .loginfy-badge__text', 'color' );
		$('.loginfy-badge').removeClass( $('.loginfy-badge').attr('class') ).addClass('loginfy-badge');

		// Enable Credit
		if ( adminify_credits ) {
			$('.loginfy-badge').removeClass('is-hidden');
		} else {
			$('.loginfy-badge').addClass('is-hidden');
		}

		// Credit Color
		if ( credits_text_color ) customizer_css.addCSS( 'body.loginfy-login-customizer .loginfy-badge__text', 'color', credits_text_color );

		// Credit Position
		if ( credits_logo_position && credits_logo_position['background-position'] ) {
			let credit_logo_position = credits_logo_position['background-position'].replace(/\s+/g, '-').toLowerCase();
			$('.loginfy-badge').addClass( credit_logo_position );
		} else {
			$('.loginfy-badge').removeClass( $('.loginfy-badge').attr('class') ).addClass('loginfy-badge');
		}

		customizer_css.toDom();

	}

	wp.customize.bind('preview-ready', function () {

		// Initial
		updateLogoText();
		updateLogoImage();
		updateLoginTitleStyle();
		updateLoginBackground();
		updateLoginLayoutAndBG();
		updateFormStyles();
		updateFormFields();
		updateFormButton();
		updateFormOthers();
		updateFormCredits();

		// Change Template
		wp.customize( '_loginfy_[templates]', function (value) {
			value.bind(function ( template ) {
				// Pre-defined Templates Selction
				$.ajax({
					url : LoginfyCustomizer.ajaxurl,
					type: 'POST',
					data: {
						template_id: template,
						action: 'jlt_loginfy_adminify_presets',
						security: LoginfyCustomizer.preset_nonce
					},
					beforeSend: function() {
						$('.login').append('<div class="loginfy-login-preloader" style="position: fixed;top: 0;left: 0; height: 100%; width: 100%; background: rgba(255,255, 255, .5) url(' + LoginfyCustomizer.preset_loader + ') no-repeat center center; z-index: 9999999;"></div>');
					},
					success: function(response) {
						$('.loginfy-style-wp').remove();
						$('head').append(response);
						$('.loginfy-login-preloader').remove();

						updateLoginTitleStyle();
						updateLoginBackground();
						updateLoginLayoutAndBG();
						bgSizeHotfix();
						updateFormStyles();
						updateFormFields();
						updateFormButton();
						updateFormOthers();
						updateFormCredits();
					}
				});
			});
		});

		// Change Logo Type
		wp.customize( '_loginfy_[logo_settings]', function (value) {
			value.bind( function (value) {
				updateLogoType( value );
				updateLoginTitleStyle();
			});
		});

		// Change Logo Text
		wp.customize( '_loginfy_[logo_text]', function(value) {
			value.bind( function() {
				updateLogoText();
			});
		});

		// Change Logo Image
		wp.customize( '_loginfy_[logo_image]', function (value) {
			value.bind( function () {
				updateLogoImage();
			});
		});

		// logo Link
		wp.customize( '_loginfy_[logo_login_url]', function(value) {
			value.bind( function( link ) {
				if ( link.url ) {
					$('#login h1 a').attr( 'href', link.url );
				} else {
					$('#login h1 a').attr( 'href', window.location.origin );
				}
			});
		});

		// Login page title
		wp.customize( '_loginfy_[login_page_title]', function(value) {
			value.bind( function( title ) {
				$('head > title').text( title );
			});
		});

		// Text Logo Style
		wp.customize( '_loginfy_[login_title_style]', function(value) {
			value.bind( function( modules ) { eventMinimizer( function() { updateLoginTitleStyle( modules ) } ) });
		});

		// Background
		[
			'_loginfy_[jlt_loginfy_login_bg_type]',
			'_loginfy_[jlt_loginfy_login_bg_color_opt]',
			'_loginfy_[jlt_loginfy_login_bg_color]',
			'_loginfy_[jlt_loginfy_login_gradient_bg]',
			'_loginfy_[jlt_loginfy_login_bg_video_type]',
			'_loginfy_[jlt_loginfy_login_bg_video_self_hosted]',
			'_loginfy_[jlt_loginfy_login_bg_video_youtube]',
			'_loginfy_[jlt_loginfy_login_bg_video_loop]',
			'_loginfy_[jlt_loginfy_login_bg_video_poster]',
			'_loginfy_[jlt_loginfy_login_bg_slideshow]',
			'_loginfy_[jlt_loginfy_login_bg_overlay_type]',
			'_loginfy_[jlt_loginfy_login_bg_overlay_color]',
			'_loginfy_[jlt_loginfy_login_bg_overlay_gradient_color]',
			'_loginfy_[jlt_loginfy_login_overlay_opacity]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateLoginBackground ) });
			});
		});

		// Layout
		[
			'_loginfy_[alignment_login_width]',
			'_loginfy_[alignment_login_column]',
			'_loginfy_[alignment_login_horizontal]',
			'_loginfy_[alignment_login_vertical]',
			'_loginfy_[alignment_login_bg_type]',
			'_loginfy_[alignment_login_bg_color]',
			'_loginfy_[alignment_login_bg_gradient_color]',
			'_loginfy_[alignment_login_bg_skew]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateLoginLayoutAndBG ) });
			});
		});

		// Form Styles
		[
			'_loginfy_[login_form_bg_type]',
			'_loginfy_[login_form_bg_color]',
			'_loginfy_[login_form_bg_gradient]',
			'_loginfy_[login_form_height_width]',
			'_loginfy_[login_form_margin]',
			'_loginfy_[login_form_padding]',
			'_loginfy_[login_form_border_radius]',
			'_loginfy_[login_form_border]',
			'_loginfy_[login_form_box_shadow]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateFormStyles ) });
			});
		});

		// Form Fields
		wp.customize( '_loginfy_[login_form_fields]', function( value ) {
			value.bind( function() { updateFormFields() });
		});

		// Form Button
		[
			'_loginfy_[button_size]',
			'_loginfy_[button_font_size]',
			'_loginfy_[login_form_button_settings]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateFormButton ) });
			});
		});

		// Form Others
		[
			'_loginfy_[login_form_button_remember_me]',
			'_loginfy_[login_form_disable_register]',
			'_loginfy_[login_form_disable_lost_pass]',
			// '_loginfy_[login_form_disable_lost_pass]',
			'_loginfy_[login_form_disable_privacy_policy]',
			'_loginfy_[login_form_disable_language_switcher]',
			'_loginfy_[login_form_disable_back_to_site]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateFormOthers ) });
			});
		});

		// Credit Settings
		[
			'_loginfy_[jlt_loginfy_credits]',
			'_loginfy_[credits_text_color]',
			'_loginfy_[credits_logo_position]'
		].forEach( function( model ) {
			wp.customize( model, function( value ) {
				value.bind( function() { eventMinimizer( updateFormCredits ) });
			});
		});

	});

})(jQuery);
