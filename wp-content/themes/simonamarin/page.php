<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

get_header();

/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - page.php (template pentru toate paginile statice)
 * Nota: NU se modifica titlurile sau textul paginilor. Toate TODO-urile de mai
 * jos privesc doar marcajul, ierarhia si semnalele tehnice din template.
 * ============================================================================
 *
 * TODO [SEO-41][CRITICAL]: Paginile statice sunt cele mai importante din punct
 * de vedere comercial (servicii, despre, contact), dar acest template nu emite
 * niciun fel de date structurate. Pe pagini Rank Math genereaza implicit doar
 * WebPage, fara context.
 * Fix: in inc/seo.php (vezi SEO-11 din functions.php), adauga JSON-LD
 *      contextual pe pagini: Service / AboutPage / ContactPage in functie de
 *      sablonul paginii, plus `mainEntityOfPage` cu permalink-ul canonic.
 *      Se ataseaza programatic - fara a atinge continutul editat de client.
 *
 * TODO [SEO-42][HIGH]: Lipsesc breadcrumbs pe pagini (vezi SEO-02). Pe paginile
 * copil (ex. /servicii/consultanta/) absenta lor inseamna ca Google nu primeste
 * niciun semnal de ierarhie parinte-copil, iar in SERP se afiseaza URL-ul brut
 * in loc de calea de navigare.
 * Fix: `if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { rank_math_the_breadcrumbs(); }`
 *      imediat dupa deschiderea <main>.
 *
 * TODO [SEO-43][MEDIUM]: Comentariile sunt incarcate pe pagini statice
 * (`comments_open() || get_comments_number()`). Pe pagini de prezentare
 * comentariile sunt de obicei spam-magnet si adauga URL-uri ?replytocom
 * crawl-uite degeaba.
 * Fix: dezactiveaza comentariile pe post_type 'page' din Settings > Discussion
 *      sau filtreaza `comments_open` pentru pagini. Verifica intai daca vreo
 *      pagina are comentarii legitime.
 *
 * TODO [SEO-44][MEDIUM]: get_sidebar() este apelat si pe pagini. Daca sidebar-ul
 * contine widget-uri de tip "Articole recente" / "Arhive" / "Meta", fiecare
 * pagina primeste zeci de link-uri interne identice catre continut de valoare
 * mica, diluand link equity catre paginile comerciale.
 * VERIFICA INTAI: Appearance > Widgets. Daca 'sidebar-1' e gol, sidebar.php se
 * intoarce imediat si acest TODO nu se aplica.
 * Fix: sidebar contextual (fara sidebar pe landing / contact) sau widget-uri
 *      curatate de "Meta" si "Arhive".
 */
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

			// If comments are open or we have at least one comment, load up the comment template.
			/*
			 * TODO [UX-71][MEDIUM]: Comentariile sunt incarcate si pe PAGINI, nu
			 * doar pe articole. Pe un site de prezentare, o sectiune de comentarii
			 * sub pagina "Servicii" sau "Despre" este zgomot: adauga inaltime pe
			 * mobil, invita spam (vezi SEC-09 din comments.php) si nu aduce nimic.
			 * Fix: dezactiveaza comentariile pe pagini din Settings > Discussion
			 * (setare de administrare, nu de cod) sau conditioneaza aici pe tipul
			 * de continut.
			 */
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
