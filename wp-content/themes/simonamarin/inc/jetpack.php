<?php
/**
 * Jetpack Compatibility File
 *
 * @link https://jetpack.com/
 *
 * @package simonamarin
 */

/*
 * =============================================================================
 * AUDIT COD MORT (2026-09-21)
 * TODO [DEAD-01][MEDIUM]: INTREGUL FISIER ESTE MORT - DE STERS.
 *   Motiv: fisierul este inclus din functions.php doar in interiorul
 *          `if ( defined( 'JETPACK__VERSION' ) )`. Plugin-ul Jetpack NU este
 *          instalat in wp-content/plugins/, deci constanta nu este niciodata
 *          definita si nicio linie de aici nu se executa vreodata.
 *   Ce moare odata cu el: simonamarin_jetpack_setup(), suportul pentru
 *          infinite-scroll / jetpack-responsive-videos / jetpack-content-options
 *          si simonamarin_infinite_scroll_render().
 *   De sters impreuna cu: blocul `require` din functions.php si sectiunea
 *          "# Plugins / Jetpack infinite scroll" din style.css (vezi DEAD-02).
 *   Daca Jetpack se instaleaza vreodata, fisierul se poate recupera din
 *          Underscores (_s) - nu contine nimic personalizat.
 * =============================================================================
 */

/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 * See: https://jetpack.com/support/content-options/
 */
/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - inc/jetpack.php
 * VERIFICA INTAI: este pluginul Jetpack instalat si activ? Daca nu, tot acest
 * fisier este cod mort si TODO-urile de mai jos au prioritate zero - in acest
 * caz fixul corect este sa nu mai fie incarcat din functions.php.
 * ============================================================================
 *
 * TODO [SEO-56][HIGH]: Infinite Scroll inlocuieste paginarea clasica. Fara
 * link-uri <a href="/page/2/"> in HTML-ul initial, crawlerul nu poate ajunge la
 * articolele de dupa primul lot - ele raman descoperibile doar prin sitemap.
 * Impact: articolele vechi primesc zero link-uri interne si sunt crawl-uite rar.
 * Fix: daca Infinite Scroll ramane activ, pastreaza OBLIGATORIU paginarea
 *      clasica in HTML ca fallback (Jetpack o suporta cu 'type' => 'click' si
 *      link "Load more" care are in spate un <a> real catre /page/2/).
 *
 * TODO [SEO-57][MEDIUM]: Infinite Scroll injecteaza articole fara a schimba
 * URL-ul si fara a actualiza <title> sau canonical. Fiecare lot incarcat este
 * continut care nu are adresa proprie - nu poate fi partajat si nu poate fi
 * indexat separat.
 *
 * TODO [SEO-58][LOW]: 'jetpack-responsive-videos' rezolva partial ce ar trebui
 * sa faca add_theme_support( 'responsive-embeds' ) din core (vezi SEO-06).
 * Dependenta de plugin pentru o functie de core este fragila: daca Jetpack e
 * dezactivat, embed-urile sparg layout-ul pe mobil.
 */

function simonamarin_jetpack_setup() {
	// Add theme support for Infinite Scroll.
	add_theme_support(
		'infinite-scroll',
		array(
			'container' => 'main',
			'render'    => 'simonamarin_infinite_scroll_render',
			'footer'    => 'page',
		)
	);

	// Add theme support for Responsive Videos.
	add_theme_support( 'jetpack-responsive-videos' );

	// Add theme support for Content Options.
	add_theme_support(
		'jetpack-content-options',
		array(
			'post-details' => array(
				'stylesheet' => 'simonamarin-style',
				'date'       => '.posted-on',
				'categories' => '.cat-links',
				'tags'       => '.tags-links',
				'author'     => '.byline',
				'comment'    => '.comments-link',
			),
			'featured-images' => array(
				'archive' => true,
				'post'    => true,
				'page'    => true,
			),
		)
	);
}
add_action( 'after_setup_theme', 'simonamarin_jetpack_setup' );

if ( ! function_exists( 'simonamarin_infinite_scroll_render' ) ) :
	/**
	 * Custom render function for Infinite Scroll.
	 */
	function simonamarin_infinite_scroll_render() {
		while ( have_posts() ) {
			the_post();
			if ( is_search() ) :
				get_template_part( 'template-parts/content', 'search' );
			else :
				get_template_part( 'template-parts/content', get_post_type() );
			endif;
		}
	}
endif;
