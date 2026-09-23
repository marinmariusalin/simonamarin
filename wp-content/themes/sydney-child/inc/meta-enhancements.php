<?php
/**
 * Completari mici, independente de continut, migrate din auditul initial
 * (facut din greseala pe tema `simonamarin/`, care nu ruleaza - vezi TODO.md).
 *
 * Fiecare functie de aici e un hook de nucleu WordPress sau al temei parinte,
 * nu un sablon copiat, ca sa nu se rupa tacut la urmatorul update Sydney -
 * acelasi principiu explicat pe larg in inc/contact-links.php.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PERF-08: `<meta name="theme-color">` pentru bara de sistem pe mobil.
 *
 * Fara ea, Android/Chrome coloreaza bara de stare cu negru sau alb implicit,
 * ceea ce pe un fundal de antet colorat arata ca o discontinuitate intre
 * pagina si sistemul de operare. Valoarea vine din optiunea de culoare a
 * antetului Sydney, ca sa nu existe doua surse de adevar pentru aceeasi
 * culoare - daca cineva schimba culoarea din Customizer, eticheta se schimba
 * automat cu ea.
 */
function simonamarin_theme_color_meta() {
	$header_bg = get_theme_mod( 'header_bg_color' );

	if ( empty( $header_bg ) ) {
		return;
	}

	printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $header_bg ) );
}
add_action( 'wp_head', 'simonamarin_theme_color_meta' );

/**
 * A11Y-02: eticheta `aria-label` pe navigarea principala.
 *
 * `wp_nav_menu_args` este filtrat in loc de a rescrie markup-ul antetului:
 * argumentul `container_aria_label` este sustinut nativ de `wp_nav_menu()`
 * din nucleu (din WP 5.9), deci Sydney il primeste si il aplica singur pe
 * elementul <nav>, indiferent cum arata restul markup-ului lui.
 * Fara eticheta, un cititor de ecran anunta doar "navigation", fara sa spuna
 * despre ce navigare e vorba, pe o pagina care poate avea si o a doua (de
 * subsol, in viitor - vezi MIG-03).
 *
 * @param array $args Argumentele apelului wp_nav_menu().
 * @return array Argumentele, cu eticheta adaugata pe meniul principal.
 */
function simonamarin_nav_aria_label( $args ) {
	if ( isset( $args['theme_location'] ) && 'primary' === $args['theme_location'] ) {
		$args['container_aria_label'] = __( 'Meniu principal', 'sydney-child' );
	}

	return $args;
}
add_filter( 'wp_nav_menu_args', 'simonamarin_nav_aria_label' );

/**
 * SEO-19 / SEO-20: text alternativ implicit pentru imaginea reprezentativa.
 *
 * Cand campul "Alt Text" din Media Library e lasat gol, WordPress emite
 * `alt=""` - corect tehnic pentru o imagine pur decorativa, dar imaginile
 * reprezentative de pe articole si pagini NU sunt decorative, sunt continut.
 * Filtrul completeaza doar cand alt-textul lipseste (nu suprascrie niciodata
 * un text scris de utilizator) si foloseste titlul articolului ca aproximare
 * rezonabila - nu inventeaza o descriere a imaginii, ceea ce ar fi mai gresit
 * decat lipsa ei.
 *
 * @param array        $attr       Atributele HTML ale imaginii.
 * @param WP_Post      $attachment Atasamentul.
 * @param string|array $size       Dimensiunea ceruta.
 * @return array Atributele, cu alt completat daca lipsea.
 */
function simonamarin_fallback_thumbnail_alt( $attr, $attachment, $size ) {
	unset( $size );

	if ( ! empty( $attr['alt'] ) ) {
		return $attr;
	}

	$post_id = get_the_ID();
	if ( $post_id && has_post_thumbnail( $post_id ) && (int) get_post_thumbnail_id( $post_id ) === (int) $attachment->ID ) {
		$attr['alt'] = get_the_title( $post_id );
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'simonamarin_fallback_thumbnail_alt', 10, 3 );

/**
 * SEO-49: pagina de "zero rezultate" la cautare nu trebuie sa fie indexabila.
 *
 * O cautare interna fara rezultate produce un URL unic per termen cautat
 * (`?s=orice`), care altfel intra in indexul Google ca pagina fara continut
 * util - potential mii de asemenea pagini, generate de vizitatori, nu de
 * site. `wp_robots` e filtrul de nucleu pentru asta din WP 5.7; Rank Math il
 * respecta si il combina cu propriile lui reguli, deci nu se creeaza o a doua
 * sursa de adevar pentru robots (vezi si MIG-02 despre acelasi principiu la
 * schema JSON-LD).
 *
 * @param array $robots Directivele robots pregatite de WordPress/Rank Math.
 * @return array Directivele, cu noindex adaugat cand cautarea nu are rezultate.
 */
function simonamarin_noindex_empty_search( $robots ) {
	if ( is_search() && ! have_posts() ) {
		$robots['noindex'] = true;
	}

	return $robots;
}
add_filter( 'wp_robots', 'simonamarin_noindex_empty_search' );

/**
 * Un singur H1 pe articol sau pagina.
 *
 * Sydney tipareste deja titlul articolului ca `<h1 class="title-post">`. 15
 * articole mai au un `<h1>` si in corpul lor (de obicei primul subtitlu, cu
 * clasa `text4`), deci Google vede doua titluri principale concurente pe
 * aceeasi pagina. Aici `<h1>` din corp devine `<h2 data-sm-h1>` - acelasi
 * text, aceleasi clase. Stilul vine aproape tot din clasa (`.entry-content
 * .text4` in redesign-pages.css); singura diferenta, marginea de sus pe care
 * Sydney o da lui h2 (1.2em fata de 1em), e anulata in style.css prin
 * atributul `data-sm-h1`. Verificat pixel cu pixel: pagina arata identic.
 *
 * De ce filtru si nu modificare in baza de date: continutul ramane neatins,
 * data „ultimei actualizari" a articolelor nu se schimba doar pentru marcaj,
 * iar regula prinde si articolele scrise de acum inainte.
 *
 * @param string $content Continutul articolului.
 * @return string
 */
function simonamarin_single_h1_in_posts( $content ) {
	if ( ! is_singular() || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	// Paginile pe sablonul page_front-page (Home, Contact, Servicii, Tarife)
	// NU tiparesc titlul Sydney: acolo H1-ul din continut e singurul si ramane.
	// Pe celelalte pagini, Sydney tipareste titlul - acelasi caz ca la articole
	// (de ex. „Despre mine", cu doua H1 identice).
	if ( is_page() && 'page-templates/page_front-page.php' === get_page_template_slug( get_queried_object_id() ) ) {
		return $content;
	}

	return preg_replace( array( '/<h1(\s|>)/i', '/<\/h1>/i' ), array( '<h2 data-sm-h1$1', '</h2>' ), $content );
}
add_filter( 'the_content', 'simonamarin_single_h1_in_posts', 20 );
