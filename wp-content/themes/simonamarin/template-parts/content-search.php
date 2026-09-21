<?php
/**
 * Template part for displaying results in search pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

?>

<?php
/*
 * TODO [UX-54][MEDIUM]: Rezultatul de cautare afiseaza the_excerpt(), deci
 * rezumatul generat automat de WordPress (primele 55 de cuvinte), fara sa
 * evidentieze termenul cautat. Utilizatorul nu vede de ce un rezultat este
 * relevant si trebuie sa deschida fiecare pagina ca sa afle.
 * Fix: evidentiaza termenul cautat in titlu si in rezumat (highlight cu
 * <mark> - style.css are deja un stil pentru mark, #fff9c0).
 * Nota: aceasta este o transformare de AFISARE a rezultatelor, nu o
 * modificare a continutului stocat.
 *
 * TODO [UX-55][LOW]: Rezultatele nu arata tipul de continut (pagina vs.
 * articol). Pe un site mic asta conteaza putin; pe unul cu multe pagini,
 * ajuta la orientare.
 * DE VERIFICAT: cautarea interna este efectiv folosita? Daca formularul de
 * cautare nu apare nicaieri in interfata (acum apare doar in 404 si in
 * "niciun rezultat"), aceste TODO-uri au prioritate minima.
 *
 * ============================================================================
 * AUDIT SEO (2026-09-21) - content-search.php
 * ============================================================================
 *
 * TODO [SEO-52][HIGH]: Fiecare rezultat este marcat ca <article> complet, cu
 * header, entry-meta si footer - exact acelasi marcaj ca un articol de sine
 * statator. Pentru crawler, pagina de cautare arata ca o colectie de articole
 * aproape identice cu originalele.
 * Impact: risc de duplicate content si canibalizare intre /?s=... si articolul
 * real, cu atat mai mult cu cat rezultatele de cautare sunt momentan
 * indexabile (vezi SEO-49 din content-none.php).
 * Fix: pastreaza <article> doar ca wrapper de card (fara entry-footer cu
 *      taguri si categorii) si asigura noindex pe is_search().
 *
 * TODO [SEO-53][MEDIUM]: Titlul rezultatului foloseste rel="bookmark", un
 * atribut fara valoare pentru motoarele de cautare moderne.
 * ATENTIE: se schimba doar atributul HTML - textul titlului ramane neatins.
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

		<?php if ( 'post' === get_post_type() ) : ?>
		<div class="entry-meta">
			<?php
			simonamarin_posted_on();
			simonamarin_posted_by();
			?>
		</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php
	/*
	 * TODO [SEO-54][HIGH]: Imaginea din lista de rezultate este randata la
	 * marimea 'post-thumbnail' (vezi SEO-04 din functions.php - tema nu
	 * inregistreaza add_image_size proprii). Pe o listare de 10 rezultate se
	 * descarca 10 imagini mari pentru carduri mici.
	 * Impact: LCP si consum de date pe mobil.
	 * Fix: marime dedicata 'simonamarin-card' + atribut sizes corect in srcset.
	 */
	simonamarin_post_thumbnail();
	?>

	<div class="entry-summary">
		<?php
		/*
		 * TODO [SEO-55][HIGH]: the_excerpt() fara excerpt manual taie primele 55
		 * de cuvinte din continut si adauga "[&hellip;]". Acelasi text este
		 * folosit de Rank Math ca fallback de meta description pe articolele
		 * fara descriere setata.
		 * Impact: snippet-uri trunchiate la mijlocul frazei in SERP -> CTR mic.
		 * Fix (fara a atinge textul articolelor): filtrele `excerpt_length` si
		 *      `excerpt_more` in inc/seo.php (vezi SEO-05), plus completarea
		 *      campului Excerpt din admin pe articolele importante.
		 */
		the_excerpt();
		?>
	</div><!-- .entry-summary -->

	<footer class="entry-footer">
		<?php simonamarin_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
