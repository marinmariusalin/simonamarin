<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package simonamarin
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			<!-- AUDIT [CRITICAL]: These footer links are hardcoded
			 - Cannot be customized without code edit
			 - Bad for branding flexibility
			 TODO: Convert to theme options/customizer settings -->
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'simonamarin' ) ); ?>">
				<?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'simonamarin' ), 'WordPress' );
				?>
			</a>
			<span class="sep"> | </span>
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'simonamarin' ), 'simonamarin', '<a href="http://underscores.me/">Underscores.me</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
