<?php
/**
 * @package Sydney
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
	<?php do_action('sydney_inside_top_post'); ?>

	<!--<header class="entry-header"> -->
		
		<!--<div class="meta-post">-->
			<!--<?php sydney_all_cats(); ?>-->
		<!--</div>-->

		<!--<?php if (get_theme_mod('hide_meta_single') != 1 ) : ?>-->
		<!--<div class="single-meta">
		<!--	<?php sydney_posted_on(); ?>-->
		<!--</div><!-- .entry-meta -->
		<!--<?php endif; ?>-->
	<!--</header><!-- .entry-header -->

	<div class="entry-content">
		<div class="single-article-container">
			<div class="article-img">
				<?php the_post_thumbnail('large-thumb'); ?>
			</div>
			<?php the_title( '<h1 class="title-post entry-title">', '</h1>' ); ?>
					<h2 class="signature-font text3 article-post-signature">
				Psiholog Simona Marin</h2>
			<?php the_content(); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'sydney' ),
					'after'  => '</div>',
				) );
			?>
		</div>	
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php sydney_entry_footer(); ?>
	</footer><!-- .entry-footer -->

	<?php do_action('sydney_inside_bottom_post'); ?>

</article><!-- #post-## -->
