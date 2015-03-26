<?php
/**
 * Template for single post view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify, $themify_event;
?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<div class="featured-area <?php echo themify_theme_featured_area_style(); ?>">
		<div class="featured-background"></div>
		<div class="pagewidth clearfix">

			<div class="portfolio-post-wrap">

				<?php if ( $themify->hide_meta != 'yes' ): ?>
					<p class="post-meta entry-meta">
						<?php the_terms( get_the_ID(), get_post_type() . '-category', '<span class="post-category">', ' <span class="separator">/</span> ', ' </span>' ) ?>
					</p>
				<?php endif; //post meta ?>
				<!-- /post-meta -->

				<?php if ( $themify->hide_title != 'yes' ): ?>
					<?php themify_before_post_title(); // Hook ?>
					<h1 class="post-title entry-title" itemprop="name">
						<?php if ( $themify->unlink_title == 'yes' ): ?>
							<?php the_title(); ?>
						<?php else: ?>
							<a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
						<?php endif; //unlink post title ?>
					</h1>
					<?php themify_after_post_title(); // Hook ?>
				<?php endif; //post title ?>

				<div class="event-single-wrap clearfix">

					<div class="event-single-details clearfix">

						<div class="event-info-wrap">

							<?php if ( themify_check( 'start_date' ) ) : ?>
								<time class="post-date entry-date updated" itemprop="datePublished">
									<?php if( $start_date = themify_get( 'start_date' ) ) : ?>
										<?php $parts = explode( '@', $start_date ); ?>
										<span class="day"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $parts[0] ) ); ?></span>
										<span class="time"><?php echo date_i18n( get_option( 'time_format' ), strtotime( $parts[1] ) ); ?></span>
									<?php endif; ?>
								</time>
								<!-- / .post-date -->
							<?php endif; ?>

							<?php if ( themify_check( 'location' ) ) : ?>
								<span class="location"><?php echo themify_get( 'location' ); ?></span>
							<?php endif; ?>

							<?php if ( is_singular( 'event' ) && themify_check( 'map_address' ) ) : ?>
								<div class="address"><?php echo wpautop( themify_get( 'map_address' ) ); ?></div>
								<!-- /address -->
							<?php endif; ?>

						</div>
						<!-- / .event-info-wrap -->

					</div>
					<!-- /.event-single-details -->

					<?php if ( themify_check( 'buy_tickets' ) ) : ?>
						<a href="<?php echo themify_get( 'buy_tickets' ); ?>" class="button buy"><?php _e( 'Buy Tickets', 'themify' ); ?></a>
					<?php endif; ?>

					<div class="event-single-image-map-wrap clearfix">
						<?php get_template_part( 'includes/post-media',	'event' ); ?>
						<div class="event-map">
							<?php echo $themify_event->get_map( themify_get( 'map_address' ) ); ?>
						</div><!-- /.event-map -->
					</div>
					<!-- /.event-single-image-map-wrap -->

				</div>
				<!-- /.event-single-wrap -->
			</div>
			<!-- /.portfolio-post-wrap -->
		</div>
	</div>
	<!-- /.featured-area -->

	<!-- layout-container -->
	<div id="layout" class="pagewidth clearfix">

		<?php themify_content_before(); // hook ?>
		<!-- content -->
		<div id="content" class="list-post">
			<?php themify_content_start(); // hook ?>

			<?php get_template_part( 'includes/loop', get_post_type()); ?>

			<?php wp_link_pages( array( 'before' => '<p class="post-pagination"><strong>' . __( 'Pages:', 'themify' ) . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>

			<?php get_template_part( 'includes/author-box', 'single' ); ?>

			<?php get_template_part( 'includes/post-nav' ); ?>

			<?php comments_template(); ?>

			<?php themify_content_end(); // hook ?>
		</div>
		<!-- /content -->
		<?php themify_content_after(); // hook ?>

		<?php
		/////////////////////////////////////////////
		// Sidebar
		/////////////////////////////////////////////
		if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

	</div>
	<!-- /layout-container -->

<?php endwhile; ?>

<?php get_footer(); ?>