<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - content-none.php (rezultat gol / cautare fara hit)
 * Nota: textele afisate raman neschimbate - TODO-urile privesc indexarea si
 * structura, nu copy-ul.
 * ============================================================================
 *
 * TODO [SEO-49][CRITICAL]: Aceasta stare de "zero rezultate" este indexabila.
 * Google trateaza paginile de tip "Nothing Found" ca soft 404: pagini care
 * raspund HTTP 200 dar nu au continut util. In Search Console apar la
 * "Soft 404" si consuma crawl budget.
 * Fix: emite `<meta name="robots" content="noindex, follow">` cand loop-ul este
 *      gol. In practica: filtru `wp_robots` care adauga noindex pentru
 *      `is_search()` si pentru arhive fara rezultate. Rank Math are optiunea
 *      pentru cautare (Titles & Meta > Misc Pages) - de VERIFICAT ca e pornita.
 *
 * TODO [SEO-50][HIGH]: Pagina fara rezultate nu ofera NICIUN drum inainte in
 * afara formularului de cautare. Fara link-uri interne, utilizatorul iese din
 * site (bounce) si crawlerul intra intr-un capat mort.
 * Fix: adauga sub formular o lista generata programatic - ultimele articole,
 *      categoriile principale sau paginile de servicii. Este continut generat
 *      de tema, nu continut editat de client, deci permis.
 */
?>

<?php
/*
 * TODO [UX-56][MEDIUM]: "Nothing Found" / "Sorry, but nothing matched your
 * search terms" sunt in ENGLEZA, pe un site in romana. Textele vin din tema
 * (nu din continutul paginilor), deci pot fi traduse - fie prin fisierul de
 * limba din languages/, fie inlocuind sirurile in template.
 * Impact: un vizitator roman primeste, exact in momentul in care nu a gasit
 * ce cauta, un mesaj pe care nu il intelege.
 *
 * TODO [UX-57][MEDIUM]: Ecranul de "niciun rezultat" este o fundatura: ofera
 * doar un camp de cautare si sugestia de a incerca alte cuvinte. Nu are
 * link-uri catre paginile principale.
 * Fix: 2-3 link-uri catre destinatiile importante (Servicii, Despre,
 * Contact) sub campul de cautare. Regula: un ecran gol nu trebuie sa fie
 * niciodata si un ecran fara iesire.
 */
?>
<section class="no-results not-found">
	<header class="page-header">
		<?php
		/*
		 * TODO [SEO-51][MEDIUM]: Acest <h1> este generic si identic pe toate
		 * starile goale din site (cautare fara rezultate, arhiva goala,
		 * blog fara articole). Mai multe URL-uri cu acelasi H1 si acelasi
		 * continut = duplicate content clasic.
		 * Fix: H1 contextual, construit din interogare (nu din continut editat
		 *      de client), ex. "Niciun rezultat pentru <termen>" pe cautare.
		 *      Se combina obligatoriu cu noindex din SEO-49.
		 */
		?>
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'simonamarin' ); ?></h1>
	</header><!-- .page-header -->

	<div class="page-content">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) :

			printf(
				'<p>' . wp_kses(
					/* translators: 1: link to WP admin new post page. */
					__( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'simonamarin' ),
					array(
						'a' => array(
							'href' => array(),
						),
					)
				) . '</p>',
				esc_url( admin_url( 'post-new.php' ) )
			);

		elseif ( is_search() ) :
			?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'simonamarin' ); ?></p>
			<?php
			get_search_form();

		else :
			?>

			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'simonamarin' ); ?></p>
			<?php
			get_search_form();

		endif;
		?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
