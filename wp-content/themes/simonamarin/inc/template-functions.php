<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package simonamarin
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
/*
 * TODO [UX-80][LOW]: Functia adauga clasa 'no-sidebar' cand sidebar-ul e gol,
 * dar tema nu are niciun stil legat de ea (vezi sectiunea goala "# Layouts").
 * Este un carlig util pe care il vei folosi cand implementezi layoutul din
 * UX-27: .no-sidebar poate trece continutul pe o singura coloana centrata,
 * iar restul paginilor pe doua coloane peste breakpoint.
 * Merita adaugate si clase pentru template-ul/slug-ul paginii, ca sa poti
 * stiliza landing page-urile diferit fara a atinge continutul lor.
 */
function simonamarin_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// TODO [DEAD-12][LOW]: Clasa "no-sidebar" adaugata mai jos nu este consumata de
	// nimic: nu exista nicio regula ".no-sidebar" in style.css sau style-rtl.css si
	// niciun template / script al temei nu o citeste. In starea actuala doar umfla
	// atributul class de pe <body>. Se sterge, SAU se foloseste asa cum a fost
	// gandita - ca hook de layout, pentru a intinde continutul pe toata latimea
	// cand sidebar-ul este gol. Verifica intai daca vreun plugin sau CSS-ul din
	// Customizer > Additional CSS nu se bazeaza deja pe ea.
	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'simonamarin_body_classes' );

/*
 * TODO [SEO-23][MEDIUM]: Aici lipsesc filtrele minime de igiena SEO:
 *   - excerpt_length / excerpt_more  (vezi SEO-05 din functions.php);
 *   - 'nav_menu_link_attributes' pentru a marca link-urile externe din meniu cu
 *     rel="noopener" si target sigur;
 *   - 'the_content' pentru a adauga loading="lazy" + decoding="async" pe iframe-uri
 *     (embed-uri YouTube) - fiecare embed costa ~500KB si blocheaza main thread.
 */

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
/*
 * TODO [SEC-03][LOW]: ACTUALIZAT 2026-09-21 - hook-ul nu mai emite nimic.
 * Enuntul initial (publica URL-ul XML-RPC in <head> pe fiecare articol/pagina
 * cu ping-uri deschise, deci invitatie la trackback spam si semnal catre
 * xmlrpc.php, vector clasic de brute-force si de amplificare DDoS prin
 * pingback reflection) nu mai este valabil in cod: simonamarin_disable_comments()
 * din functions.php adauga add_filter( 'pings_open', '__return_false', 20 ),
 * deci conditia `is_singular() && pings_open()` este intotdeauna falsa si
 * <link rel="pingback"> nu mai ajunge niciodata in <head>.
 * Ce ramane de facut, redus la curatenie: sterge functia de mai jos impreuna
 * cu add_action( 'wp_head', ... ) de sub ea - este cod care nu se mai executa.
 * Separat de tema, si independent de acest hook: verifica daca LiteSpeed sau
 * hosting-ul blocheaza xmlrpc.php, pentru ca fisierul ramane accesibil chiar
 * daca tema nu il mai anunta. Loginizer acopera doar login-ul clasic.
 */
function simonamarin_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'simonamarin_pingback_header' );
