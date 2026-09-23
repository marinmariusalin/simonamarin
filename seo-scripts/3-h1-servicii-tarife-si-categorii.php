<?php
/**
 * SEO, pasul 3 — NICIUN CUVANT SCHIMBAT.
 *
 *   192  Servicii  „Servicii psihologice": <h2 class="text2"> -> <h1> (pagina nu avea H1)
 *   215  Tarife    „Tarife servicii psihologice": <h2 class="text2"> -> <h1> (idem)
 *        Acelasi tratament ca Home si Contact la pasul 2; sablonul page_front-page
 *        nu tipareste titlul paginii, deci titlul din continut e singurul.
 *
 *   Rank Math `strip_category_base` -> off.
 *        Cu baza de categorie scoasa, categoria „cuplu" si articolul 1716
 *        („Cu relatia de cuplu la psiholog", slug `cuplu`) au aceeasi adresa,
 *        /cuplu/. Castiga categoria: arhiva „Cuplu Archives", cu noindex. Deci
 *        articolul e de negasit si neindexabil, desi e in sitemap, iar cele cinci
 *        articole care il citeaza ajung pe arhiva. Verificat si pe productie.
 *        Categoriile ajung la /category/<slug>/ (noindex, nu sunt in sitemap si
 *        niciun continut nu le citeaza), articolul isi recapata adresa.
 *
 * Rulare (din radacina site-ului):
 *   php -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/3-h1-servicii-tarife-si-categorii.php
 *
 * Copie a starii dinainte: E:/simonamarin/_backup-seo-2026-09-23-pas3.json
 */
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';
global $wpdb;

$bak_file = 'E:/simonamarin/_backup-seo-2026-09-23-pas3.json';
$general  = get_option( 'rank-math-options-general' );
file_exists( $bak_file ) || file_put_contents(
	$bak_file,
	json_encode(
		array(
			'posts'               => array(
				192 => get_post_field( 'post_content', 192, 'raw' ),
				215 => get_post_field( 'post_content', 215, 'raw' ),
			),
			'strip_category_base' => $general['strip_category_base'] ?? null,
		),
		JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
	)
);

$words = function ( $h ) {
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( strip_tags( $h ), ENT_QUOTES, 'UTF-8' ) ) );
};

$edits = array(
	array( 192, '/<h2 class="text2">(\s*Servicii psihologice)<\/h2>/', '<h1 class="text2">$1</h1>' ),
	array( 215, '/<h2 class="text2">(Tarife servicii psihologice)<\/h2>/', '<h1 class="text2">$1</h1>' ),
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
	$wpdb->update( $wpdb->posts, array( 'post_content' => $new ), array( 'ID' => $id ) );
	clean_post_cache( $id );
	echo "OK $id\n";
}

$general['strip_category_base'] = 'off';
update_option( 'rank-math-options-general', $general );
flush_rewrite_rules( false );
echo "strip_category_base off, rewrite reimprospatat\n";
