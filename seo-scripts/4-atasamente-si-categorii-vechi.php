<?php
/**
 * SEO, pasul 4 — setari Rank Math, fara continut.
 *
 * 1. Paginile de atasament (47, cate una pentru fiecare imagine) erau
 *    indexabile: pagini fara text, doar o poza. Rank Math le redirectioneaza
 *    acum 301 spre articolul in care e imaginea (`attachment_redirect_urls`).
 *
 * 2. Adresele vechi ale categoriilor (/din-psihologie/ etc.) - devenite
 *    /category/<slug>/ la pasul 3 - primesc 301 spre adresa noua. Sunt citate
 *    din fiecare articol de pe productie si pot avea legaturi externe; fara
 *    redirectionare ar da 404 dupa deploy. /cuplu/ lipseste deliberat: e
 *    adresa articolului 1716.
 *
 * Rulare (din radacina site-ului):
 *   php -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/4-atasamente-si-categorii-vechi.php
 *
 * Copie a setarilor dinainte: E:/simonamarin/_backup-seo-2026-09-23-pas4.json
 */
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';

$general = get_option( 'rank-math-options-general' );
$bak     = 'E:/simonamarin/_backup-seo-2026-09-23-pas4.json';
file_exists( $bak ) || file_put_contents( $bak, json_encode( array( 'attachment_redirect_urls' => $general['attachment_redirect_urls'] ?? null ), JSON_PRETTY_PRINT ) );

$general['attachment_redirect_urls'] = 'on';
update_option( 'rank-math-options-general', $general );
echo "attachment_redirect_urls on\n";

foreach ( array( 'din-psihologie', 'din-viata-cotidiana', 'intrebari-frecvente', 'printre-randuri-de-carte', 'uncategorized' ) as $slug ) {
	$term = get_term_by( 'slug', $slug, 'category' );
	if ( ! $term ) {
		echo "SKIP $slug: categoria nu exista\n";
		continue;
	}
	$existing = \RankMath\Redirections\DB::match_redirections( $slug );
	if ( $existing ) {
		echo "SKIP $slug: exista deja\n";
		continue;
	}
	\RankMath\Redirections\DB::add(
		array(
			'url_to'      => get_category_link( $term ),
			'sources'     => array( array( 'pattern' => $slug, 'comparison' => 'exact' ) ),
			'header_code' => 301,
			'status'      => 'active',
		)
	);
	echo "OK $slug -> " . get_category_link( $term ) . "\n";
}
