<?php
/*
Template Name: Front Page
*/

get_header(); ?>

	<div id="primary" class="fp-content-area">
		<main id="main" class="site-main" role="main">

			<div class="entry-content">
				<?php while ( have_posts() ) : the_post(); ?>
					<?php the_content(); ?>
				<?php endwhile; ?>
			</div><!-- .entry-content -->
			<!--<span>page 2 page_front-page</span>-->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
