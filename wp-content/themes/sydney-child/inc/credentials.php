<?php
/**
 * Afilierea si atestatele profesionale, la finalul fiecarei pagini.
 *
 * ---------------------------------------------------------------------------
 * DE CE
 * ---------------------------------------------------------------------------
 * Pe un site de psihoterapie (YMYL), cine scrie si ce drept de practica are
 * cantaresc si pentru vizitator, si pentru Google. Pana acum afilierea si
 * atestatele apareau doar pe „Despre mine"; cererea utilizatorului (23.09.2026)
 * este ca ele sa apara pe toate paginile, urmate de butonul WhatsApp.
 *
 * Textele sunt exact cele date de utilizator: randurile de afiliere de pe
 * „Despre mine" si denumirile atestatelor din Registrul Unic al Psihologilor.
 * Atestatele sunt aceleasi ca in datele structurate (simonamarin_credentials()
 * din inc/structured-data.php) - o singura lista, citita de ambele.
 *
 * ---------------------------------------------------------------------------
 * ORDINEA DIFERA DE LA O PAGINA LA ALTA, DELIBERAT
 * ---------------------------------------------------------------------------
 * Cerinta utilizatorului: acelasi bloc, dar cu ordinea putin schimbata pe
 * fiecare pagina, ca sa nu fie o copie identica repetata pe tot site-ul.
 * Ordinea se deduce din ID-ul paginii, nu la intamplare: aceeasi pagina arata
 * mereu la fel (cache-ul LiteSpeed ramane valid, Google vede o pagina stabila),
 * iar doua pagini vecine difera. Butonul WhatsApp ramane mereu ultimul.
 *
 * ---------------------------------------------------------------------------
 * DE CE PRIN `the_content` SI NU COPIAT IN FIECARE PAGINA
 * ---------------------------------------------------------------------------
 * Un singur loc de intretinut: un atestat nou se adauga o data, nu in 40 de
 * pagini. Si, ca inc/related-posts.php, filtrul de nucleu supravietuieste unui
 * update Sydney si chiar unei schimbari de tema.
 *
 * „Despre mine" (ID 28) e exclusa: are deja afilierea si atestatele in
 * continut, in contextul lor (sub formari, practica, voluntariat).
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Pagina care contine deja afilierea si atestatele in propriul text.
 */
const SIMONAMARIN_ABOUT_PAGE_ID = 28;

/**
 * Roteste o lista cu $by pozitii (primul element trece la coada).
 *
 * @param array $items Lista.
 * @param int   $by    Numarul de pozitii.
 * @return array
 */
function simonamarin_rotate( array $items, $by ) {
	$by = $items ? $by % count( $items ) : 0;
	return array_merge( array_slice( $items, $by ), array_slice( $items, 0, $by ) );
}

/**
 * Marcajul blocului, pentru o pagina data.
 *
 * Trei variatii independente, toate din ID: ordinea celor doua grupuri
 * (afiliere / atestate), ordinea celor doua afilieri si ordinea celor trei
 * atestate - 2 x 2 x 3 = 12 combinatii.
 *
 * @param int $post_id ID-ul paginii.
 * @return string
 */
function simonamarin_render_credentials( $post_id ) {
	$seed = absint( $post_id );

	$affiliations = array(
		sprintf(
			'Membru acreditat <a href="%s" rel="nofollow noopener" target="_blank">COPSI (Colegiul Psihologilor din Romania)</a>',
			esc_url( 'https://www.copsi.ro/' )
		),
		sprintf(
			'Membru <a href="%s" rel="nofollow noopener" target="_blank">Asociatia de Psihoterapie Experientiala din Romania, Institutul SPER</a>',
			esc_url( 'https://www.sper.ro' )
		),
	);
	$affiliations = simonamarin_rotate( $affiliations, intdiv( $seed, 2 ) );

	$atestate = simonamarin_rotate( simonamarin_credential_names(), $seed );

	$li = static function ( $items, $escape ) {
		$out = '';
		foreach ( $items as $item ) {
			$out .= '<li>' . ( $escape ? esc_html( $item ) : $item ) . '</li>';
		}
		return $out;
	};

	$groups = array(
		'<div class="sm-credentials__group">'
		. '<h2 class="sm-credentials__heading">Afiliere</h2>'
		. '<ul class="sm-credentials__list">' . $li( $affiliations, false ) . '</ul>'
		. '</div>',
		'<div class="sm-credentials__group">'
		. '<h2 class="sm-credentials__heading">Atestate de liberă practică</h2>'
		. '<p class="sm-credentials__source">Registrul Unic al Psihologilor – Comisia de psihologie clinică și psihoterapie</p>'
		. '<ul class="sm-credentials__list">' . $li( $atestate, true ) . '</ul>'
		. '</div>',
	);
	if ( $seed % 2 ) {
		$groups = array_reverse( $groups );
	}

	$whatsapp = sprintf(
		'<div class="sm-credentials__cta"><a class="sm-credentials__whatsapp" href="%1$s" rel="nofollow noopener" target="_blank" aria-label="%2$s" title="%2$s">%3$s</a></div>',
		esc_url( 'https://wa.me/40747668204' ),
		esc_attr__( 'WhatsApp', 'sydney-child' ),
		function_exists( 'simonamarin_icon' ) ? simonamarin_icon( 'whatsapp' ) : esc_html__( 'WhatsApp', 'sydney-child' )
	);

	return '<aside class="sm-credentials" aria-label="Afiliere și atestate profesionale">'
		. '<div class="sm-credentials__groups">' . implode( '', $groups ) . '</div>'
		. $whatsapp
		. '</aside>';
}

/**
 * Unde apare blocul: orice pagina sau articol, in afara de „Despre mine".
 *
 * @return bool
 */
function simonamarin_credentials_apply() {
	return is_singular( array( 'page', 'post' ) ) && ! is_page( SIMONAMARIN_ABOUT_PAGE_ID );
}

/**
 * Adauga blocul sub continut.
 *
 * Prioritatea 15: dupa wpautop (10) si shortcode-uri (11), ca marcajul sa nu
 * fie reimpachetat in <p>, si inainte de articolele conexe (20), ca atestatele
 * sa stea imediat sub textul semnat, iar recomandarile dupa. Verificarile de
 * bucla sunt aceleasi ca in inc/related-posts.php: `the_content` ruleaza si in
 * feed-uri, descrieri SEO si fragmente, unde blocul nu are ce cauta.
 *
 * @param string $content Continutul.
 * @return string
 */
function simonamarin_append_credentials( $content ) {
	if ( ! simonamarin_credentials_apply() || ! is_main_query() || ! in_the_loop() || is_feed() ) {
		return $content;
	}

	return $content . simonamarin_render_credentials( get_the_ID() );
}
add_filter( 'the_content', 'simonamarin_append_credentials', 15 );

/**
 * Stilul blocului, doar unde apare. Jetoanele sunt in redesign.css.
 */
function simonamarin_enqueue_credentials_style() {
	if ( ! simonamarin_credentials_apply() ) {
		return;
	}
	$asset = sydney_child_asset( 'assets/css/credentials.css' );
	if ( $asset ) {
		wp_enqueue_style( 'sydney-child-credentials', $asset[0], array( 'sydney-child-redesign' ), $asset[1] );
	}
}
add_action( 'wp_enqueue_scripts', 'simonamarin_enqueue_credentials_style', 20 );
