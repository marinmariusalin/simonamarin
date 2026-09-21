<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-38][HIGH]: Listarea foloseste template-parts/content.php, care
 * afiseaza the_content() integral - vezi SEO-24. Continut duplicat pe homepage.
 *
 * TODO [SEO-39][MEDIUM]: the_posts_navigation() produce doar link-uri
 * "Postari mai vechi / mai noi". Fara paginare numerotata, articolele de la
 * pagina 5+ sunt la 5 click-uri de homepage.
 * Impact: crawl depth mare -> Google le viziteaza rar sau deloc.
 * Fix: the_posts_pagination() in loc de the_posts_navigation().
 *
 * TODO [SEO-40][LOW]: Cand blogul este pe o pagina separata, titlul ei este
 * <h1 class="page-title screen-reader-text"> - adica ASCUNS vizual.
 * Nu e o problema de spam (textul corespunde titlului real), dar pagina de blog
 * ramane fara titlu vizibil pentru utilizatori.
 */
get_header();
?>

	<main id="primary" class="site-main">

		<?php
		if ( have_posts() ) :

			if ( is_home() && ! is_front_page() ) :
				?>
				<header>
					<h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
				</header>
				<?php
			endif;

			/*
			 * TODO [UX-70][MEDIUM]: Acest template intra in actiune doar daca
			 * homepage-ul este setat pe "ultimele articole" (Settings > Reading).
			 * VERIFICA INTAI aceasta setare - daca site-ul are o pagina statica pe
			 * prima pozitie, index.php se foloseste rar sau deloc, si atunci
			 * TODO-urile de aici nu merita efort.
			 * Daca ESTE folosit ca homepage: o lista cruda de articole, fara
			 * introducere si fara ierarhie, este cel mai slab tip de pagina de
			 * start pentru un site de prezentare. Pe mobil, primul ecran decide
			 * daca vizitatorul ramane, iar acum primul ecran contine doar
			 * titlul site-ului, meniul si inceputul primului articol.
			 */
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/*
				 * Include the Post-Type-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Type name) and that will be used instead.
				 */
				get_template_part( 'template-parts/content', get_post_type() );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
