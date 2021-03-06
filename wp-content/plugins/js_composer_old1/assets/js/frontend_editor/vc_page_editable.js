var vc_iframe = {
	scripts_to_wait: 0,
	time_to_call: false,
	ajax: false,
	activities_list: [],
	scripts_to_load: false,
	loaded_script: {},
	loaded_styles: {},
	inline_scripts: [],
	inline_scripts_body: []
};
(function ( $ ) {
	vc_iframe.showNoContent = function ( show ) {
		var $vc_no_content_helper_el = $( '#vc_no-content-helper' );
		(false === show && $vc_no_content_helper_el.addClass( 'vc_not-empty' )) || $vc_no_content_helper_el.removeClass( 'vc_not-empty' );
	};
	vc_iframe.scrollTo = function ( id ) {
		var $el, el_height, hidden = true, position_y, position,
			window_height = $( window ).height(),
			window_scroll_top = $( window ).scrollTop();
		if ( id && ($el = $( '[data-model-id=' + id + ']' )) ) {
			position = $el.offset();
			if ( false === (position_y = position ? position.top : false) ) {
				return false;
			}
			el_height = $el.height();
			if ( (position_y > window_scroll_top + window_height) ||
				(position_y + el_height < window_scroll_top) ) {
				$.scrollTo( $el, 500, { offset: - 50 } );
			}
		}
	};
	vc_iframe.startSorting = function () {
		$( 'body' ).addClass( 'vc_sorting' );
	};
	vc_iframe.stopSorting = function () {
		$( 'body' ).removeClass( 'vc_sorting' )
	};
	vc_iframe.initDroppable = function () {
		$( 'body' ).addClass( 'vc_dragging' );
		$( '.vc_container-block' ).bind( 'mouseenter.vcDraggable', function () {
			$( this ).addClass( 'vc_catcher' );
		} ).bind( 'mouseout.vcDraggable', function () {
			$( this ).removeClass( 'vc_catcher' );
		} );
	};
	vc_iframe.killDroppable = function () {
		$( 'body' ).removeClass( 'vc_dragging' );
		$( '.vc_container-block' ).unbind( 'mouseover.vcDraggable mouseleave.vcDraggable' );
	};
	vc_iframe.addActivity = function ( callback ) {
		this.activities_list.push( callback );
	};
	vc_iframe.renderPlaceholder = function ( event, element ) {
		var tag = $( element ).data( 'tag' ),
			is_container = parent.vc.map[ tag ] === Object( parent.vc.map[ tag ] ) && ( ( (true === parent.vc.map[ tag ].is_container || false === parent.vc.map[ tag ].is_container || '[object Boolean]' === toString.call( parent.vc.map[ tag ].is_container )) && true === parent.vc.map[ tag ].is_container ) || (null != parent.vc.map[ tag ].as_parent && '[object Array]' === Object.prototype.toString.call( parent.vc.map[ tag ].as_parent ) && false != parent.vc.map[ tag ].as_parent) ),
			$helper = $( '<div class="vc_helper vc_helper-' + tag + '"><i class="vc_general vc_element-icon'
			+ ( parent.vc.map[ tag ].icon ? ' ' + parent.vc.map[ tag ].icon : '' )
			+ '"'
			+ ( is_container ? ' data-is-container="true"' : '' )
			+ '></i> ' + parent.vc.map[ tag ].name + '</div>' ).prependTo( 'body' );
		return $helper;
	};
	vc_iframe.setSortable = function ( app ) {
		var $vc_row_el = $( '[data-tag=vc_row]' );
		$vc_row_el.parent().sortable( {
			forcePlaceholderSize: false,
			items: '[data-tag=vc_row]',
			handle: '.vc_move-vc_row',
			cursor: 'move',
			cursorAt: { top: 20, left: 16 },
			placeholder: "vc_placeholder-row",
			helper: this.renderPlaceholder,
			start: function ( event, ui ) {
				vc_iframe.startSorting();
				ui.placeholder.height( 30 );
			},
			stop: this.stopSorting,
			tolerance: "pointer",
			update: function ( event, ui ) {
				parent.vc.app.saveRowOrder();
			}
		} );
		$( '.vc_element-container' ).sortable( {
			forcePlaceholderSize: true,
			helper: this.renderPlaceholder,
			distance: 3,
			scroll: true,
			scrollSensitivity: 70,
			cursor: 'move',
			cursorAt: { top: 20, left: 16 },
			connectWith: '.vc_element-container',
			items: '> [data-model-id]',
			handle: '.vc_element-move',
			start: this.startSorting,
			update: app.saveElementOrder,
			change: function ( event, ui ) {
				ui.placeholder.height( 30 );
				ui.placeholder.width( ui.placeholder.parent().width() );
			},
			placeholder: 'vc_placeholder',
			tolerance: "pointer",
			over: function ( event, ui ) {
				var tag = ui.item.data( 'tag' ),
					vc_map = window.parent.vc.map || false,
					parent_tag = ui.placeholder.closest( '[data-tag]' ).data( 'tag' ),
					allowed_container_element = 'undefined' === typeof(vc_map[ parent_tag ].allowed_container_element) ? true : vc_map[ parent_tag ].allowed_container_element;
				ui.placeholder.removeClass( 'vc_hidden-placeholder' );
				ui.placeholder.css( { maxWidth: ui.placeholder.parent().width() } );
				if ( tag && vc_map ) {
					if ( ! window.parent.vc.checkRelevance( parent_tag, tag ) ) {
						ui.placeholder.addClass( 'vc_hidden-placeholder' );
					}
					if ( ui.sender ) {
						var $sender_column = ui.sender.closest( '.vc_element' ).removeClass( 'vc_sorting-over' );
						1 > $sender_column.find( '.vc_element' ).length && $sender_column.addClass( 'vc_empty' );
					}
					ui.placeholder.closest( '.vc_element' ).addClass( 'vc_sorting-over' );
					var is_container = vc_map[ tag ] === Object( vc_map[ tag ] ) && ( ( (true === vc_map[ tag ].is_container || false === vc_map[ tag ].is_container || '[object Boolean]' === toString.call( vc_map[ tag ].is_container )) && true === vc_map[ tag ].is_container ) || (null != vc_map[ tag ].as_parent && '[object Array]' === Object.prototype.toString.call( vc_map[ tag ].as_parent ) && false != vc_map[ tag ].as_parent) );
					if ( is_container && ! (true === allowed_container_element || allowed_container_element === tag.replace( /_inner$/,
							'' )) ) {
						ui.placeholder.addClass( 'vc_hidden-placeholder' );
					}
				}
			},
			out: function ( event, ui ) {
				ui.placeholder.removeClass( 'vc_hidden-placeholder' );
			},
			stop: function ( event, ui ) {
				var tag = ui.item.data( 'tag' ),
					vc_map = window.parent.vc.map || false,
					parent_tag = ui.item.parents( '[data-tag]:first' ).data( 'tag' ),
					allowed_container_element = vc_map[ parent_tag ].allowed_container_element ? vc_map[ parent_tag ].allowed_container_element : true,
					trig_changed = true,
					item_model;
				if ( ! window.parent.vc.checkRelevance( parent_tag, tag ) ) {
					ui.placeholder.removeClass( 'vc_hidden-placeholder' );
					$( this ).sortable( 'cancel' );
					trig_changed = false;
				}
				var is_container = vc_map[ tag ] === Object( vc_map[ tag ] ) && ( ( (true === vc_map[ tag ].is_container || false === vc_map[ tag ].is_container || '[object Boolean]' === toString.call( vc_map[ tag ].is_container )) && true === vc_map[ tag ].is_container ) || (null != vc_map[ tag ].as_parent && '[object Array]' === Object.prototype.toString.call( vc_map[ tag ].as_parent ) && false != vc_map[ tag ].as_parent) );
				if ( is_container && ! (true === allowed_container_element || allowed_container_element === tag.replace( /_inner$/,
						'' )) ) {
					ui.placeholder.removeClass( 'vc_hidden-placeholder' );
					$( this ).sortable( 'cancel' );
					trig_changed = false
				}
				if ( trig_changed ) {
					item_model = parent.vc.shortcodes.get( ui.item.data( 'modelId' ) );
					item_model.view.parentChanged();
				}
				vc_iframe.stopSorting();
			}
		} );
		$( '.wpb_row' ).sortable( {
			forcePlaceholderSize: true,
			tolerance: "pointer",
			items: '> [data-tag=vc_column], > [data-tag=vc_column_inner]',
			handle: '> .vc_controls .vc_move-vc_column',
			start: function ( event, ui ) {
				vc_iframe.startSorting();
				var id = ui.item.data( 'modelId' ),
					model = parent.vc.shortcodes.get( id ),
					css_class = model.view.convertSize( model.getParam( 'width' ) );
				ui.item.appendTo( ui.item.parent().parent() );
				ui.placeholder.addClass( css_class );
				ui.placeholder.width( ui.placeholder.width() - 4 );
			},
			cursor: 'move',
			cursorAt: { top: 20, left: 16 },
			stop: function ( event, ui ) {
				vc_iframe.stopSorting( event, ui );
			},
			update: app.saveColumnOrder,
			placeholder: 'vc_placeholder-column',
			helper: this.renderPlaceholder
		} );
		$vc_row_el.disableSelection();
		$vc_row_el.delegate( 'select', 'mouseenter', function () {
			$vc_row_el.enableSelection();
		} );
		$vc_row_el.delegate( 'select', 'mouseleave', function () {
			$vc_row_el.disableSelection();
		} );
		$vc_row_el.delegate( 'input[type="text"],textarea', "focus", function () {
			$vc_row_el.enableSelection();
		} );
		$vc_row_el.delegate( 'input[type="text"],textarea', "blur", function () {
			$vc_row_el.disableSelection();
		} );

		app.setFrameSize();
		$( '#vc_load-new-js-block' ).appendTo( 'body' );
	};
	vc_iframe.loadCustomCss = function ( css ) {
		if ( ! vc_iframe.$custom_style ) {
			$( '[data-type=vc_custom-css]' ).remove();
			vc_iframe.$custom_style = $( '<style class="vc_post_custom_css_style"></style>' ).appendTo( 'body' );
		}
		vc_iframe.$custom_style.html( css );
	};
	vc_iframe.setCustomShortcodeCss = function ( css ) {
		this.$shortcodes_custom_css = $( 'body > [data-type=vc_shortcodes-custom-css]' );
		if ( ! this.$shortcodes_custom_css.length ) {
			this.$shortcodes_custom_css = $( '<style data-type="vc_shortcodes-custom-css"></style>' ).prependTo( 'body' );
		}
		this.$shortcodes_custom_css.append( css );
	};
	vc_iframe.addInlineScript = function ( script ) {
		return this.inline_scripts.push( script ) - 1;
	};
	vc_iframe.addInlineScriptBody = function ( script ) {
		return this.inline_scripts_body.push( script ) - 1;
	};
	vc_iframe.loadInlineScripts = function () {
		var i = 0;
		while ( this.inline_scripts[ i ] ) {
			$( this.inline_scripts[ i ] ).insertAfter( '.js_placeholder_' + i );
			$( '.js_placeholder_' + i ).remove();
			i ++;
		}
		this.inline_scripts = [];
	};
	vc_iframe.loadInlineScriptsBody = function () {
		var i = 0;
		while ( this.inline_scripts_body[ i ] ) {
			$( this.inline_scripts_body[ i ] ).insertAfter( '.js_placeholder_inline_' + i );
			$( '.js_placeholder_inline_' + i ).remove();
			i ++;
		}
		this.inline_scripts_body = [];
	};
	vc_iframe.allowedLoadScript = function ( src ) {
		var script_url, i, scripts_string, scripts = [], scripts_to_add = [], ls_rc;
		if ( src.match( /load\-scripts\.php/ ) ) {
			scripts_string = src.match( /load%5B%5D=([^&]+)/ )[ 1 ];
			if ( scripts_string ) {
				scripts = scripts_string.split( ',' );
			}
			for ( i in
				scripts ) {
				ls_rc = 'load-script:' + scripts[ i ];
				if ( ! vc_iframe.loaded_script[ window.parent.vc_globalHashCode( ls_rc ) ] ) {
					vc_iframe.loaded_script[ window.parent.vc_globalHashCode( ls_rc ) ] = ls_rc;
					scripts_to_add.push( scripts[ i ] );
				}
			}
			return ! scripts_to_add.length ? false : src.replace( /load%5B%5D=[^&]+/,
				'load%5B%5D=' + scripts_to_add.join( ',' ) );
		} else if ( ! vc_iframe.loaded_script[ window.parent.vc_globalHashCode( src ) ] ) {
			vc_iframe.loaded_script[ window.parent.vc_globalHashCode( src ) ] = src;
			return src;
		}
		return false;
	};
	vc_iframe.collectScriptsData = function () {
		$( 'script[src]' ).each( function () {
			vc_iframe.allowedLoadScript( $( this ).attr( 'src' ) );
		} );
		$( 'link[href]' ).each( function () {
			var href = $( this ).attr( 'href' );
			vc_iframe.loaded_styles[ window.parent.vc_globalHashCode( href ) ] = href;
		} );
	};
	$( 'body' ).removeClass( 'admin-bar' );
	$( document ).ready( function () {
		$( '#wpadminbar' ).hide();
		$( '.edit-link' ).hide();
		window.parent.vc && ! window.parent.vc.loaded && window.parent.vc.build && window.parent.vc.build();
	} );
	vc_iframe.reload = function () {
		vc_iframe.reload_safety_call = false;
		$( 'a:not(.control-btn),form' ).each( function () {
			$( this ).attr( 'target', '_blank' );
		} );

		this.collectScriptsData();
		this.loadInlineScripts();
		this.loadInlineScriptsBody();
		for ( var i in
			this.activities_list ) {
			this.activities_list[ i ].call( window );
		}
		this.activities_list = [];
		window.setTimeout( function () {
			window.vc_twitterBehaviour();
			window.vc_teaserGrid();
			window.vc_carouselBehaviour();
			window.vc_prettyPhoto();
			window.vc_googleplus();
			window.vc_pinterest();
			window.vc_progress_bar();
			window.vc_rowBehaviour();
			window.vc_waypoints();
			window.vc_google_fonts();
			window.vc_gridBehaviour();

			$( window ).trigger( 'vc_reload' );
			$( window ).trigger( 'resize' );
		}, 10 ); // just a small timeout to _.defer task
		return true;
	};
	vc_iframe.addScripts = function ( $elements ) {
		vc_iframe.scripts_to_wait = $elements.length;
		vc_iframe.scripts_to_load = $elements;
	};
	vc_iframe.addStyles = function ( $elements ) {
		$elements.each( function () {
			$( '<style/>', {
				type: 'text/css'
			} ).html( $( this ).html() ).appendTo( 'body' );
		} );
	};
	vc_iframe.loadScripts = function () {
		if ( ! vc_iframe.scripts_to_wait || ! vc_iframe.scripts_to_load ) {
			vc_iframe.reload();
			return;
		}
		vc_iframe.scripts_to_load.each( function () {
			var $element = $( this );
			vc_iframe.reload_safety_call = true;
			if ( $element.is( 'script' ) ) {
				var src = $element.attr( 'src' );
				if ( src ) {
					src = vc_iframe.allowedLoadScript( src );
					if ( src ) {
						$.getScript( src, function () {
							vc_iframe.scripts_to_wait -= 1;
							1 > vc_iframe.scripts_to_wait && vc_iframe.reload()
						} );
					} else {
						// remove not allowed script from queue
						vc_iframe.scripts_to_wait -= 1;
						1 > vc_iframe.scripts_to_wait && vc_iframe.reload()
					}
				} else if ( $element ) {
					// TODO: possible this one is not required. Better to add internal js inside shortcode element content.
					try {
						$( '<script/>', {
							type: 'text/javascript'
						} ).html( $element.html() ).appendTo( 'body' );
					} catch ( e ) {
						window.console && window.console.error && console.error( e );
					}
					vc_iframe.scripts_to_wait -= 1;
					1 > vc_iframe.scripts_to_wait && vc_iframe.reload()
				}
			} else {
				// this must be a <link> element with href
				var href = $element.attr( 'href' );
				if ( href && ! vc_iframe.loaded_styles[ window.parent.vc_globalHashCode( href ) ] ) {
					$( '<link/>', {
						rel: 'stylesheet',
						type: 'text/css',
						href: href
					} ).appendTo( 'body' );
				}
				vc_iframe.scripts_to_wait -= 1;
				1 > vc_iframe.scripts_to_wait && vc_iframe.reload();
			}
		} );
		vc_iframe.scripts_to_load = false;
		$( document ).ajaxComplete( function ( e ) {
			$( e.currentTarget ).unbind( 'ajaxComplete' );
			! vc_iframe.scripts_to_wait && vc_iframe.reload();
		} );
		window.setTimeout( function () {
			true === vc_iframe.reload_safety_call && vc_iframe.reload();
		}, 14000 );
	};
	vc_iframe.destroyTabs = function ( $tabs ) {
		$tabs.each( function () {
			var $t = $( this ).find( '.wpb_tour_tabs_wrapper' );
			$t.tabs( 'destroy' );
		} );
	};
	vc_iframe.buildTabs = function ( $tab, active ) {
		var ver = $.ui.version.split( '.' ),
			old_version = 1 === parseInt( ver[ 0 ] ) && 9 > parseInt( ver[ 1 ] );
		$tab.each( function ( index ) {
			var $tabs,
				interval = $( this ).attr( "data-interval" ),
				tabs_array = [],
				$wrapper = $( this ).find( '.wpb_tour_tabs_wrapper' );
			if ( $wrapper.hasClass( 'ui-widget' ) ) {
				active = false !== active ? active : $wrapper.tabs( 'option', 'active' );
				$tabs = $wrapper.tabs( 'refresh' );
				$wrapper.tabs( 'option', 'active', active );
			} else {
				$tabs = $( this ).find( '.wpb_tour_tabs_wrapper' ).tabs( {
					active: 0,
					show: function ( event, ui ) {
						wpb_prepare_tab_content( event, ui );
					},
					activate: function ( event, ui ) {
						wpb_prepare_tab_content( event, ui );
					}
				} );
			}
			$( this ).find( '.vc_element' ).each( function () {
				tabs_array.push( this.id );
			} );
			$( this ).find( '.wpb_prev_slide a, .wpb_next_slide a' ).unbind( 'click' ).click( function ( e ) {
				e.preventDefault();
				if ( old_version ) {
					var index = $tabs.tabs( 'option', 'selected' );
					if ( $( this ).parent().hasClass( 'wpb_next_slide' ) ) {
						index ++;
					}
					else {
						index --;
					}
					if ( 0 > index ) {
						index = $tabs.tabs( "length" ) - 1;
					}
					else if ( index >= $tabs.tabs( "length" ) ) {
						index = 0;
					}
					$tabs.tabs( "select", index );
				} else {
					var index = $tabs.tabs( "option", "active" ),
						length = $tabs.find( '.wpb_tab' ).length;

					if ( $( this ).parent().hasClass( 'wpb_next_slide' ) ) {
						index = (index + 1) >= length ? 0 : index + 1;
					} else {
						index = 0 > index - 1 ? length - 1 : index - 1;
					}
					$tabs.tabs( "option", "active", index );
				}
			} );

		} );
		return true;
	};
	vc_iframe.setActiveTab = function ( $tabs, index ) {
		$tabs.each( function () {
			$( this ).find( '.wpb_tour_tabs_wrapper' ).tabs( 'refresh' );
			$( this ).find( '.wpb_tour_tabs_wrapper' ).tabs( 'option', 'active', index );
		} );
	};
	vc_iframe.setTabsSorting = function ( view ) {
		var $controls = $( view.tabsControls().get( 0 ) );
		if ( $controls.hasClass( 'ui-sortable' ) ) {
			$controls.sortable( 'destroy' );
		}
		$controls.sortable( {
			axis: ('vc_tour' === view.model.get( 'shortcode' ) ? 'y' : 'x'),
			update: view.stopSorting,
			items: "> li:not(.add_tab_block)"/*,
			 start: function (event, ui) { ui.item.css('margin-top', $(window).scrollTop() ); },
			 beforeStop: function (event, ui) { ui.item.css('margin-top', 0 ); }*/
		} );
		// fix: #1019, from http://stackoverflow.com/questions/2451528/jquery-ui-sortable-scroll-helper-element-offset-firefox-issue
		var userAgent = navigator.userAgent.toLowerCase();

		if ( userAgent.match( /firefox/ ) ) {
			$controls.bind( "sortstart", function ( event, ui ) {
				ui.helper.css( 'margin-top', $( window ).scrollTop() );
			} );
			$controls.bind( "sortbeforestop", function ( event, ui ) {
				ui.helper.css( 'margin-top', 0 );
			} );
		}
	};
	vc_iframe.buildAccordion = function ( $el, active ) {
		$el.each( function ( index ) {
			var $this = $( this ),
				$tabs,
				$wrapper = $this.find( '.wpb_accordion_wrapper' ),
				interval = $this.attr( "data-interval" ),
				active_tab = ! isNaN( $this.data( 'active-tab' ) ) && 0 < parseInt( $this.data( 'active-tab' ) ) ? parseInt( $this.data( 'active-tab' ) ) - 1 : false,
				collapsible = false === active_tab || 'yes' === $this.data( 'collapsible' );
			//
			if ( $wrapper.hasClass( 'ui-widget' ) ) {
				if ( false === active ) {
					active = $wrapper.accordion( "option", 'active' );
				}
				$wrapper.accordion( "refresh" );
				$wrapper.accordion( 'option', 'active', active );
			} else {
				$tabs = $this.find( '.wpb_accordion_wrapper' ).accordion( {
					create: function ( event, ui ) {
						ui.panel.parent().parent().addClass( 'vc_active-accordion-tab' );
					},
					header: "> .vc_element > div > h3",
					autoHeight: false,
					heightStyle: "content",
					active: active_tab,
					collapsible: collapsible,
					navigation: true,
					activate: function ( event, ui ) {
						vc_accordionActivate( event, ui );
						ui.oldPanel.parent().parent().removeClass( 'vc_active-accordion-tab' );
						ui.newPanel.parent().parent().addClass( 'vc_active-accordion-tab' );
					},
					change: function ( event, ui ) {
						if ( 'undefined' !== typeof($.fn.isotope) ) {
							ui.newContent.find( '.isotope' ).isotope( "layout" );
						}
						window.vc_carouselBehaviour();
					}

				} );
			}
		} );
	};
	vc_iframe.setAccordionSorting = function ( view ) {
		$( view.$accordion.find( '> .wpb_accordion_wrapper' ).get( 0 ) ).sortable( {
			handle: '.vc_move-vc_accordion_tab',
			update: view.stopSorting
		} );
	};
	vc_iframe.vc_imageCarousel = function ( model_id ) {
		var $el = $( '[data-model-id=' + model_id + ']' ),
			images_count = $el.find( 'img' ).length,
			$carousel = $el.find( '[data-ride="vc_carousel"]' );
		if ( ! $carousel.find( 'img:first' ).length ) {
			$carousel.carousel( $carousel.data() );
			return;
		}
		if ( ! $carousel.find( 'img:first' ).prop( 'complete' ) ) {
			window.setTimeout( function () {
				vc_iframe.vc_imageCarousel( model_id );
			}, 500 );
			return;
		}
		$carousel.carousel( $carousel.data() );
	};
	vc_iframe.vc_gallery = function ( model_id ) {
		var $el = $( '[data-model-id=' + model_id + ']' ),
			$gallery = $el.find( '.wpb_gallery_slides' );
		if ( ! $gallery.find( 'img:first' ).prop( 'complete' ) ) {
			window.setTimeout( function () {
				vc_iframe.vc_gallery( model_id );
			}, 500 );
			return;
		}
		this.gallerySlider( $gallery );
	};
	vc_iframe.vc_postsSlider = function ( model_id ) {
		var $el = $( '[data-model-id=' + model_id + ']' ),
			$gallery = $el.find( '.wpb_gallery_slides' );
		this.gallerySlider( $gallery );
	};
	vc_iframe.gallerySlider = function ( $gallery ) {
		var $imagesGrid;
		if ( $gallery.hasClass( 'wpb_flexslider' ) ) {
			var sliderSpeed = 800,
				sliderTimeout = parseInt( $gallery.attr( 'data-interval' ) ) * 1000,
				sliderFx = $gallery.attr( 'data-flex_fx' ),
				slideshow = true;
			if ( 0 === sliderTimeout ) {
				slideshow = false;
			}
			$gallery.flexslider( {
				animation: sliderFx,
				slideshow: slideshow,
				slideshowSpeed: sliderTimeout,
				sliderSpeed: sliderSpeed,
				smoothHeight: true
			} );
			$gallery.addClass( 'loaded' );
		} else if ( $gallery.hasClass( 'wpb_slider_nivo' ) ) {
			var sliderSpeed = 800,
				sliderTimeout = $gallery.attr( 'data-interval' ) * 1000;

			if ( 0 === sliderTimeout ) {
				sliderTimeout = 9999999999;
			}

			$gallery.find( '.nivoSlider' ).nivoSlider( {
				effect: 'boxRainGrow,boxRain,boxRainReverse,boxRainGrowReverse', // Specify sets like: 'fold,fade,sliceDown'
				slices: 15, // For slice animations
				boxCols: 8, // For box animations
				boxRows: 4, // For box animations
				animSpeed: sliderSpeed, // Slide transition speed
				pauseTime: sliderTimeout, // How long each slide will show
				startSlide: 0, // Set starting Slide (0 index)
				directionNav: true, // Next & Prev navigation
				directionNavHide: true, // Only show on hover
				controlNav: true, // 1,2,3... navigation
				keyboardNav: false, // Use left & right arrows
				pauseOnHover: true, // Stop animation while hovering
				manualAdvance: false, // Force manual transitions
				prevText: 'Prev', // Prev directionNav text
				nextText: 'Next' // Next directionNav text
			} );
		} else if ( $gallery.hasClass( 'wpb_image_grid' ) ) {
			if ( $.fn.imagesLoaded ) {
				$imagesGrid = $gallery.find( '.wpb_image_grid_ul' ).imagesLoaded( function () {
					$imagesGrid.isotope( {
						itemSelector: '.isotope-item',
						layoutMode: 'fitRows'
					} );
				} );
			} else {
				$gallery.find( '.wpb_image_grid_ul' ).isotope( {
					// options
					itemSelector: '.isotope-item',
					layoutMode: 'fitRows'
				} );
			}

		}
	};
	vc_iframe.vc_Flickr = function ( $placeholder ) {
		var link = $placeholder.data( 'link' );
		$.getScript( link, function () {
			$( window.b_txt ).insertAfter( $placeholder );
		} );
	};
	vc_iframe.vc_toggle = function ( model_id ) {
		var $el = $( '[data-model-id=' + model_id + ']' );
		window.vc_toggleBehaviour( $el );
	};
	vc_iframe.gridInit = function ( model_id ) {
		var $grid = $( '[data-model-id=' + model_id + '] [data-vc-grid-settings]' ),
			vcGrid;
		if ( ! $grid.find( '.vc_grid-loading:visible' ).length ) {
			vcGrid = $grid.data( 'vcGrid' );
			if ( vcGrid ) {
				$grid.empty(); // TODO: need to add reinit in plugin
				vcGrid.init();
			} else {
				$grid.vcGrid();
			}
		}
	};
	vc_iframe.updateChildGrids = function ( model_id ) {
		var $grid = $( '[data-model-id=' + model_id + '] [data-vc-grid-settings]' );
		$grid.each( function () {
			var $grid = $( this );
			var vcGrid = $( this ).data( 'vcGrid' );
			if ( ! $grid.find( '.vc_grid-loading:visible' ).length && vcGrid ) {
				$grid.empty(); // TODO: need to add reinit in plugin
				vcGrid.init();
			}
		} );
	};
	vc_iframe.buildTTA = function () {
		$( '[data-vc-accordion]:not(.vc_is-ready-fe)' ).on( 'show.vc.accordion', function ( e ) {
			var ui = {};
			ui.newPanel = $( this ).data( 'vc.accordion' ).getTarget();
			window.wpb_prepare_tab_content( e, ui );
		} ).addClass( 'vc_is-ready-fe' );
	};
	vc_iframe.vc_pieChart = function () {
		window.vc_pieChart();
		window.setTimeout( function () {
			$( window ).unbind( 'resize.vcPieChartEditable' ).bind( 'resize.vcPieChartEditable', function () {
				$( '.vc_pie_chart.vc_ready' ).vcChat( 'resize' );
			} );
		}, 500 );

	};
	$( document ).ready( function () {
		if ( parent && parent.vc && ! parent.vc.loaded ) {
			window.setTimeout( function () {
				parent.vc.build();
			}, 10 );
		}
	} );
})( window.jQuery );