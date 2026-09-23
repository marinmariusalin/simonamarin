<?php
/**
 * SEO, pasul 2 — marcaj in continutul a sase pagini. NICIUN CUVANT SCHIMBAT.
 *
 * Fiecare modificare e verificata inainte de scriere: textul paginii, fara
 * etichete HTML, trebuie sa fie identic inainte si dupa. Daca difera, sau daca
 * tiparul nu se gaseste exact o data, pagina e sarita.
 *
 *   31   Home      titlul „Cabinet Individual de Psihologie": <h2> -> <h1> (pagina nu avea H1)
 *   186  Contact   „Contact": <h2> -> <h1> (pagina nu avea H1)
 *   2095 Schimbare al doilea H1, din corpul articolului -> <h2>
 *   1512 Terapia online    „psihoterapia online" devine legatura spre articolul 2197
 *   2197 Psihoterapia la distanta  „Terapia online" devine legatura spre pagina 1512
 *        (cele doua concureaza pe aceleasi interogari, pozitia ~33,6)
 *   108  Gelozia in cuplu  „cuplurilor" devine legatura spre /cuplu/
 *
 * Plus: sterge canonicalul scris manual pe 2197 (identic cu cel implicit; la un
 * transfer de baza de date ar ramane cu domeniul vechi).
 *
 * Rulare (din radacina site-ului):
 *   php -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/2-h1-si-legaturi-interne.php
 *
 * Copie a continutului dinainte: E:/simonamarin/_backup-seo-2026-09-23-continut.json
 */
// Doar din linia de comanda: fisierul sta in radacina web si nu trebuie sa
// poata fi rulat prin browser, nici local, nici daca ajunge vreodata pe productie.
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';
global $wpdb;

$ids = array( 31, 186, 2095, 1512, 2197, 108 );
$bak = array(
	'posts'          => array(),
	'canonical_2197' => get_post_meta( 2197, 'rank_math_canonical_url', true ),
);
foreach ( $ids as $id ) {
	$bak['posts'][ $id ] = get_post_field( 'post_content', $id, 'raw' );
}
file_put_contents( 'E:/simonamarin/_backup-seo-2026-09-23-continut.json', json_encode( $bak, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );

$words = function ( $h ) {
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( strip_tags( $h ), ENT_QUOTES, 'UTF-8' ) ) );
};

$edits = array(
	array( 31, '/<h2 class="homepage-title text2">(.*?)<\/h2>/s', '<h1 class="homepage-title text2">$1</h1>' ),
	array( 186, '/<h2 class="text2" itemprop="headline">Contact<\/h2>/', '<h1 class="text2" itemprop="headline">Contact</h1>' ),
	array( 2095, '/<h1 class="text4">(Cum implementezi o schimbare\?)<\/h1>/', '<h2 class="text4">$1</h2>' ),
	array( 1512, '/(Odata cu schimbarea stilului de viata\. )(psihoterapia online)/', '$1<a href="' . get_permalink( 2197 ) . '">$2</a>' ),
	array( 2197, '/(?<![>\w])(Terapia online)( a cunoscut o creștere rapidă)/u', '<a href="' . get_permalink( 1512 ) . '">$1</a>$2' ),
	array( 108, '/(În cadrul )(cuplurilor)/u', '$1<a href="' . get_permalink( 1716 ) . '">$2</a>' ),
);

foreach ( $edits as list( $id, $re, $rep ) ) {
	$old = get_post_field( 'post_content', $id, 'raw' );
	$n   = preg_match_all( $re, $old );
	if ( 1 !== $n ) {
		echo "SKIP $id: $n potriviri\n";
		continue;
	}
	$new = preg_replace( $re, $rep, $old, 1 );
	if ( $words( $old ) !== $words( $new ) ) {
		echo "SKIP $id: textul s-ar schimba\n";
		continue;
	}
	// Data „ultimei actualizari" se schimba doar pentru ca pagina chiar se schimba.
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $new, 'post_modified' => current_time( 'mysql' ), 'post_modified_gmt' => current_time( 'mysql', 1 ) ),
		array( 'ID' => $id )
	);
	clean_post_cache( $id );
	echo "OK $id\n";
}

delete_post_meta( 2197, 'rank_math_canonical_url' );
echo "canonical 2197 sters\n";
