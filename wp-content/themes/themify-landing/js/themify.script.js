;// Themify Theme Scripts - http://themify.me/

// Initialize object literals
var FixedHeader = {},
	ThemifyTabs = {},
	ThemifySlider = {};

/////////////////////////////////////////////
// jQuery functions					
/////////////////////////////////////////////
(function($){

	// Fixed Header /////////////////////////
	FixedHeader = {
		headerHeight: 0,
		init: function() {
			if( '' != themifyScript.fixedHeader ) {
				FixedHeader.headerHeight = $('#headerwrap').height();
				this.activate();
				$(window).on('scroll touchstart.touchScroll touchmove.touchScroll', this.activate);
			}
			$('#pagewrap').css('paddingTop', Math.floor( FixedHeader.headerHeight ));
		},
		activate: function() {
			var $window = $(window),
				scrollTop = $window.scrollTop(),
				hwHeight = $('#headerwrap').height();
			$('#pagewrap').css('paddingTop', Math.floor( hwHeight ));
			if( scrollTop >= FixedHeader.headerHeight ) {
				FixedHeader.scrollEnabled();
			} else {
				FixedHeader.scrollDisabled();
			}
		},
		scrollDisabled: function() {
			$('#headerwrap').removeClass('fixed-header');
			$('#header').removeClass('header-on-scroll');
			$('body').removeClass('fixed-header-on');
		},
		scrollEnabled: function() {
			$('#headerwrap').addClass('fixed-header');
			$('#header').addClass('header-on-scroll');
			$('body').addClass('fixed-header-on');
		}
	};

	ThemifyTabs = {
		init: function( tabset, suffix ) {
			$( tabset ).each(function(){
				var $tabset = $(this);

				$('.htab-link:first', $tabset).addClass('current');
				$('.btab-panel:first', $tabset).show();

				$( $tabset ).on( 'click', '.htab-link', function(e){
					e.preventDefault();

					var $a = $(this),
						tab = '.' + $a.data('tab') + suffix,
						$tab = $( tab, $tabset );

					$( '.htab-link', $tabset ).removeClass('current');
					$a.addClass('current');

					$( '.btab-panel', $tabset ).hide();
					$tab.show();

					$(document.body).trigger( 'themify-tab-switched', $tab );
				});
			});
		}
	};

	ThemifySlider = {
		recalcHeight: function(items, $obj) {
			var heights = [], height;
			$.each( items, function() {
				heights.push( $(this).outerHeight(true) );
			});
			height = Math.max.apply( Math, heights );
			$obj.closest('.carousel-wrap').find( '.caroufredsel_wrapper, .slideshow' ).each(function(){
				$(this).outerHeight( height );
			});
		},
		didResize: false,
		// Initialize carousels
		create: function(obj) {
			var self = this;
			obj.each(function() {
				var $this = $(this),
					$sliderWrapper = $this.closest('.loops-wrapper.slider');
				// Start Carousel
				$this.carouFredSel({
					responsive : true,
					prev : $this.data('slidernav') && 'yes' == $this.data('slidernav') ? '#' + $this.data('id') + ' .carousel-prev' : '',
					next: $this.data('slidernav') && 'yes' == $this.data('slidernav') ? '#' + $this.data('id') + ' .carousel-next' : '',
					pagination : {
						container : $this.data('pager') && 'yes' == $this.data('pager') ? '#' + $this.data('id') + ' .carousel-pager' : ''
					},
					circular : true,
					infinite : true,
					swipe: true,
					scroll : {
						items : $this.data('scroll')? parseInt( $this.data('scroll'), 10 ) : 1,
						fx : $this.data('effect'),
						duration : parseInt($this.data('speed')),
						onBefore: function() {
							var pos = $(this).triggerHandler( 'currentPosition' );
							$('#' + $this.data('thumbsid') + ' a').removeClass( 'selected' );
							$('#' + $this.data('thumbsid') + ' a.itm'+pos).addClass( 'selected' );
							var page = Math.floor( pos / 3 );
							$('#' + $this.data('thumbsid')).trigger( 'slideToPage', page );
						}
					},
					auto : {
						play : ('off' != $this.data('autoplay')),
						timeoutDuration : 'off' != $this.data('autoplay') ? parseInt($this.data('autoplay')) : 0
					},
					items : {
						visible : {
							min : 1,
							max : $this.data('visible')? parseInt( $this.data('visible'), 10 ) : 1
						},
						width : 222,
						height: 'variable'
					},
					onCreate : function( items ) {

						$(window).resize(function() {
							self.didResize = true;
						});
						setInterval(function() {
							if ( self.didResize ) {
								self.didResize = false;
								self.recalcHeight(items.items, $this);
							}
						}, 250);

						$this.closest('.slideshow-wrap').css({
							'visibility' : 'visible',
							'height' : 'auto'
						});
						$this.closest('.loops-wrapper.slider').css({
							'visibility' : 'visible',
							'height' : 'auto'
						});

						if ( $this.data('slidernav') && 'yes' != $this.data('slidernav') ) {
							$('#' + $this.data('id') + ' .carousel-next').remove();
							$('#' + $this.data('id') + ' .carousel-prev').remove();
						}
						$(window).resize();
						$('.slideshow-slider-loader', $this.closest('.slider')).remove(); // remove slider loader
					}
				});
				// End Carousel

			});
		}
	};

	// DOCUMENT READY
	$(document).ready(function() {

		var $body = $('body');

		/////////////////////////////////////////////
		// Scroll to top
		/////////////////////////////////////////////
		$('.back-top a').click(function() {
			$('body,html').animate({ scrollTop: 0 }, 800);
			return false;
		});

		/////////////////////////////////////////////
		// Toggle main nav on mobile
		/////////////////////////////////////////////
		$('#menu-icon').themifySideMenu({
			close: '#menu-icon-close'
		});

		if ( $(window).width() < 780 ) {
			$('#main-nav').addClass('scroll-nav');
		}

		// Reset slide nav width
		$(window).resize(function(){
		    var viewport = $(window).width();
		    if (viewport > 780) {
			    $body.removeAttr('style');
			    $('#main-nav').removeClass('scroll-nav');
		    } else {
			    $('#main-nav').addClass('scroll-nav');
		    }
		});

		// Initialize Tabs for Widget ///////////////
		ThemifyTabs.init( '.event-posts', '-events' );

	});

	// WINDOW LOAD
	$(window).load(function() {
		var $body = $('body');

		// Single Gallery Layout
		var $gallery = $('.gallery-wrapper.masonry');
		if ( $gallery.length > 0 && 'undefined' !== typeof $.fn.isotope ) {
			$gallery.isotope({
				layoutMode: 'packery',
				itemSelector: '.item'
			}).isotope( 'once', 'layoutComplete', function() {
				$(window).trigger('resize');
			});
			this.masonryActive = true;
		}

		// Carousel initialization //////////////////
		if ( typeof $.fn.carouFredSel !== 'undefined' ) {
			var carouselInit = function( $context ) {
				$context = $context || $body;
				ThemifySlider.create( $( '.loops-wrapper.event .slideshow', $context ) );
			};
			carouselInit();
			// Front Builder
			$body.on('builder_toggle_frontend', function(event, is_edit){
				carouselInit($(this));
			}).on('builder_load_module_partial', function(event, $newElems){
				carouselInit($newElems);
			});
		}

		// Lightbox / Fullscreen initialization ///////////
		if(typeof ThemifyGallery !== 'undefined') {
			ThemifyGallery.init({'context': $(themifyScript.lightboxContext)});
		}

		// Fixed header ///////////////////////////////////
		FixedHeader.init();

	});
	
})(jQuery);