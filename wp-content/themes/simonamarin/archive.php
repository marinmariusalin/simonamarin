<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-29][HIGH]: Arhivele nu au tratament de paginare pentru SEO.
 * Impact: /categorie/pagina/2/ are acelasi <title> si aceeasi descriere ca
 * pagina 1 -> continut duplicat raportat in Search Console.
 * Fix: Rank Math adauga "Pagina X" in title daca e configurat - de verificat.
 *
 * TODO [SEO-30][MEDIUM]: Arhivele de autor, de data (zi/luna/an) si de tag sunt
 * active si indexabile. Pe un site cu un singur autor, arhiva de autor este
 * duplicat aproape perfect al homepage-ului.
 * Fix: dezactiveaza arhivele de autor si de data din Rank Math > Titles & Meta.
 *
 * TODO [SEO-31][MEDIUM]: the_archive_description() afiseaza descrierea
 * categoriei. Daca ea e goala (cazul implicit), arhivele nu au text unic.
 * Nota: completarea descrierilor se face din admin, NU din cod.
 */
/*
 * TODO [PSY-18][HIGH]: Arhivele sunt organizate cronologic - dupa cand a
 * scris terapeuta, nu dupa ce cauta cititorul. Pe un site de cabinet,
 * oamenii nu navigheaza dupa data, ci dupa problema: anxietate, depresie,
 * relatie de cuplu, doliu, burnout, parenting.
 * Fix: categoriile devin structura principala de navigare (in meniu si in
 * pagini de tip "ghid"), iar arhivele de data si de autor se dezactiveaza -
 * vezi SEO-30, care cere acelasi lucru din motive de indexare.
 * Nota: reorganizarea categoriilor existente se face din admin, impreuna cu
 * terapeuta; tema doar expune structura rezultata.
 */
get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<?php
			/*
			 * TODO [UX-61][MEDIUM]: Titlul de arhiva generat de the_archive_title()
			 * include prefixul in engleza ("Category: ", "Tag: ", "Author: ").
			 * Pe un site in romana arata ca o scapare tehnica.
			 * Fix: filtru pe 'get_the_archive_title' care scoate prefixul. Nu
			 * atinge numele categoriei in sine - doar eticheta adaugata de tema.
			 *
			 * TODO [UX-62][MEDIUM]: Arhiva nu spune cate rezultate exista si nici
			 * la ce pagina din cate se afla utilizatorul. Pe mobil, unde nu vezi
			 * decat un articol o data, lipsa acestui reper face parcurgerea
			 * dezorientanta.
			 */
			?>
			<header class="page-header">
				<?php
				the_archive_title( '<h1 class="page-title">', '</h1>' );
				the_archive_description( '<div class="archive-description">', '</div>' );
				?>
			</header><!-- .page-header -->

			<?php
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

			/*
			 * TODO [UX-63][HIGH]: the_posts_navigation() afiseaza doar doua link-uri
			 * text: "Older posts" / "Newer posts" - in engleza, fara numar de
			 * pagina si fara stil de buton. Pe mobil sunt doua link-uri minuscule
			 * la capatul unei liste lungi de articole.
			 * Fix: the_posts_pagination() cu numere de pagina, stilizate ca tinte
			 * de minim 44px, si etichete in romana prin 'prev_text'/'next_text'.
			 * Alternativa moderna pe mobil: buton "Incarca mai multe".
			 */
			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
