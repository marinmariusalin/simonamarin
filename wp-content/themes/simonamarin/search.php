<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-28][HIGH]: Paginile de rezultate ale cautarii interne trebuie sa fie
 * noindex, follow. Altfel Google le indexeaza si le trateaza ca soft 404 /
 * "thin content", iar in cazuri de spam de cautare devin vector de SEO poisoning
 * (cineva linkuieste /?s=<text spam> si textul ajunge indexat pe domeniul tau).
 * Fix: verifica Rank Math > Titles & Meta > Misc Pages > Search Results = noindex.
 * Daca nu, adauga wp_robots filter pe is_search().
 *
 * NOTA [SEC-08][INFO]: verificat, nu este nimic de reparat. get_search_query()
 * aplica esc_attr pe termenul cautat inainte de a-l returna, deci textul
 * introdus de utilizator nu poate injecta HTML in <h1> sau in <title>.
 * Ramane de retinut doar ca acel text ajunge vizibil in pagina si in titlu -
 * motivul pentru care rezultatele cautarii trebuie sa fie noindex (SEO-28).
 * Pastrat ca nota, nu ca TODO: nu genera task din el.
 */
get_header();
?>

	<main id="primary" class="site-main">

		<?php if ( have_posts() ) : ?>

			<?php
			/*
			 * TODO [UX-64][HIGH]: Pagina de rezultate nu contine formularul de
			 * cautare. Utilizatorul care vrea sa rafineze cautarea trebuie sa se
			 * intoarca de unde a plecat - iar pe mobil nici nu are de unde, pentru
			 * ca formularul de cautare nu apare in header sau in meniu.
			 * Fix: get_search_form() imediat sub titlu, cu termenul curent
			 * precompletat.
			 *
			 * TODO [UX-65][MEDIUM]: Titlul "Search Results for: X" este in engleza
			 * si nu spune cate rezultate s-au gasit. "5 rezultate pentru X" este
			 * mai util si mai scurt - pe mobil, titlul actual poate ocupa doua-trei
			 * randuri.
			 */
			?>
			<header class="page-header">
				<h1 class="page-title">
					<?php
					/* translators: %s: search query. */
					printf( esc_html__( 'Search Results for: %s', 'simonamarin' ), '<span>' . get_search_query() . '</span>' );
					?>
				</h1>
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
				 * Run the loop for the search to output the results.
				 * If you want to overload this in a child theme then include a file
				 * called content-search.php and that will be used instead.
				 */
				get_template_part( 'template-parts/content', 'search' );

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
