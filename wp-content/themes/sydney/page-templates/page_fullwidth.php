<?php

/*

Template Name: Full width

*/
	get_header();
?>



	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'page' ); ?>

				<?php

					if ( comments_open() || '0' != get_comments_number() ) :

						comments_template();

					endif;
				?>

			<?php endwhile; // end of the loop. ?>
        	<?php if (is_front_page() ) : ?>
        
        		<!--<div class="row carousel-container-home">
        			<?php getPageCards() ?>
        		</div>-->
			<!--<span>page 3 2Fpage_fullwidth</span>-->
        	<?php endif; ?>
		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>

