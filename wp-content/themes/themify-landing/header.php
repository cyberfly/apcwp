<?php
/**
 * Template for site header
 * @package themify
 * @since 1.0.0
 */
?>
<!doctype html>
<html <?php echo themify_get_html_schema(); ?> <?php language_attributes(); ?>>

<head>
	<?php
	/** Themify Default Variables
	 *  @var object */
	global $themify; ?>
	<meta charset="<?php bloginfo( 'charset' ); ?>">

	<title><?php wp_title(); ?></title>

	<?php
	/**
	 *  Stylesheets and Javascript files are enqueued in theme-functions.php
	 */
	?>

	<!-- wp_head -->
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php themify_body_start(); // hook ?>

<div id="pagewrap" class="hfeed site">

	<?php if ( themify_theme_show_area( 'header' ) && themify_theme_do_not_exclude_all( 'header' ) ) : ?>
		<div id="headerwrap">

			<?php themify_header_before(); // hook ?>

			<header id="header" class="pagewidth clearfix">

	            <?php themify_header_start(); // hook ?>

	            <?php if ( themify_theme_show_area( 'site_logo' ) ) : ?>
					<?php echo themify_logo_image(); ?>
				<?php endif; ?>

				<?php if ( themify_theme_show_area( 'site_tagline' ) ) : ?>
					<?php echo themify_site_description(); ?>
				<?php endif; ?>

				<?php if ( themify_theme_do_not_exclude_all( 'mobile-menu' ) ) : ?>
					<a id="menu-icon" href="#mobile-menu"></a>
					<div id="mobile-menu" class="sidemenu sidemenu-off">
					
						<?php if ( themify_theme_show_area( 'search_form' ) ) : ?>
							<div id="searchform-wrap">
								<?php get_search_form(); ?>
							</div>
							<!-- /searchform-wrap -->
						<?php endif; // exclude search form ?>

						<?php if ( themify_theme_show_area( 'social_widget' ) ) : ?>
							<div class="social-widget">
								<?php dynamic_sidebar( 'social-widget' ); ?>

								<?php if ( themify_theme_show_area( 'rss' ) ) : ?>
									<div class="rss">
										<a href="<?php echo esc_url( themify_get( 'setting-custom_feed_url' ) != '' ? themify_get( 'setting-custom_feed_url' ) : get_bloginfo( 'rss2_url' ) ); ?>"></a>
									</div>
								<?php endif; // exclude rss ?>
							</div>
							<!-- /.social-widget -->
						<?php endif; // exclude social widget ?>

						<?php if ( themify_theme_show_area( 'menu_navigation' ) ) : ?>
							<?php themify_theme_menu_nav(); ?>
							<!-- /#main-nav -->
						<?php endif; // exclude menu navigation ?>

						<a id="menu-icon-close" href="#"></a>
					</div>
					<!-- /#mobile-menu -->
				<?php endif; // do not exclude all this ?>

				<?php themify_header_end(); // hook ?>

			</header>
			<!-- /#header -->

	        <?php themify_header_after(); // hook ?>

		</div>
		<!-- /#headerwrap -->
	<?php endif; // exclude header ?>

	<div id="body" class="clearfix">

		<?php themify_layout_before(); //hook ?>