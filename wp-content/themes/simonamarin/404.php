<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-32][MEDIUM]: Sub formularul de cautare (get_search_form(), care
 * EXISTA deja in acest template), pagina 404 afiseaza 4 widget-uri generate
 * dinamic: Recent Posts, Most Used Categories, Archives dropdown, Tag Cloud.
 * Probleme:
 *   - pe un site de prezentare cu putine articole sunt complet inutile;
 *   - Tag Cloud si Archives ruleaza query-uri suplimentare pe fiecare 404, iar
 *     boturile genereaza multe 404-uri -> consum de resurse degeaba;
 *   - link-urile catre arhive de data contrazic SEO-30 din archive.php.
 * Fix: pastreaza formularul de cautare si inlocuieste cele patru widget-uri cu
 * link-uri statice catre paginile importante (home, servicii, contact). Asta
 * imbunatateste si experienta reala.
 *
 * TODO [PERF-12][LOW]: WordPress trimite corect status 404 aici, dar verifica pe
 * live cu curl -I ca LiteSpeed Cache nu cache-uieste 404-urile cu status 200.
 */
get_header();
?>

	<main id="primary" class="site-main">

		<?php
		/*
		 * TODO [UX-58][HIGH]: Mesajul "Oops! That page can't be found." este in
		 * engleza, cu un ton jucaus care nu se potriveste unui site de prezentare
		 * in romana. Fiind text din tema (nu continut de pagina), poate fi
		 * schimbat: formulare scurta, in romana, care spune ce s-a intamplat si
		 * ce poate face vizitatorul mai departe.
		 *
		 * TODO [UX-59][HIGH]: Dupa formularul de cautare (deja prezent in template -
		 * vezi SEO-32), pagina 404 mai afiseaza patru widget-uri: articole recente,
		 * categorii, arhive pe luni si tag cloud. Pe mobil asta inseamna un ecran
		 * lung de liste nesortate, din care niciuna nu raspunde la intrebarea reala
		 * a utilizatorului: "unde e pagina pe care o cautam?".
		 * Fix orientat pe utilizator: un titlu clar, formularul de cautare pastrat si
		 * mutat imediat sub titlu, si 3-4 butoane mari catre destinatiile
		 * principale. Cele patru widget-uri se sterg.
		 * Tag cloud-ul in special este un pattern mort din 2010 si, pe mobil, o
		 * gramada de link-uri minuscule lipite - exact opusul unei tinte de 44px.
		 *
		 * TODO [UX-60][MEDIUM]: Pagina 404 nu afiseaza headerul de navigatie ca
		 * alternativa vizibila (meniul e ascuns dupa butonul hamburger si aici).
		 * Pe o pagina de eroare, navigatia ar trebui sa fie explicit deschisa /
		 * vizibila, nu ascunsa dupa inca un tap.
		 */
		?>
		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'simonamarin' ); ?></h1>
			</header><!-- .page-header -->

			<?php
			/*
			 * TODO [PSY-19][MEDIUM]: Tonul mesajelor de eroare conteaza mai mult
			 * pe acest site decat pe altele. "Oops! That page can't be found."
			 * este jucaus si, in engleza, si de neinteles - vezi UX-58. Pentru
			 * cineva care tocmai a facut efortul de a cauta ajutor, un ton
			 * glumet este disonant.
			 * Principiu general, valabil si pentru mesajele formularului de
			 * contact si pentru ecranul "niciun rezultat": scurt, calm, la
			 * obiect, fara umor si fara vina ("Ai gresit adresa"). Spune ce s-a
			 * intamplat si ofera o iesire - cel putin un link catre contact.
			 */
			?>
			<div class="page-content">
				<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'simonamarin' ); ?></p>

					<?php
					get_search_form();

					the_widget( 'WP_Widget_Recent_Posts' );
					?>

					<div class="widget widget_categories">
						<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'simonamarin' ); ?></h2>
						<ul>
							<?php
							wp_list_categories(
								array(
									'orderby'    => 'count',
									'order'      => 'DESC',
									'show_count' => 1,
									'title_li'   => '',
									'number'     => 10,
								)
							);
							?>
						</ul>
					</div><!-- .widget -->

					<?php
					/* translators: %1$s: smiley */
					$simonamarin_archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'simonamarin' ), convert_smilies( ':)' ) ) . '</p>';
					the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$simonamarin_archive_content" );

					the_widget( 'WP_Widget_Tag_Cloud' );
					?>

			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
