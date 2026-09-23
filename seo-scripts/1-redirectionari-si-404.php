<?php
/**
 * SEO, pasul 1 — configurare Rank Math (fara continut).
 *
 * 1. Opreste redirectionarea TUTUROR paginilor inexistente spre prima pagina
 *    (`redirections_fallback = homepage`). Google trateaza un 301 spre home ca
 *    pe o pagina disparuta (soft 404), iar linkurile externe spre URL-uri vechi
 *    isi pierd valoarea in loc sa fie redirectionate unde trebuie.
 * 2. Redirectionari 301 explicite pentru cele doua URL-uri vechi care apar in
 *    Search Console: /servicii/ si /terms-and-conditions-terms/.
 * 3. /despre-mine/ -> /consilier-psihologic/. Butonul „Despre mine" de pe prima
 *    pagina duce la /despre-mine/, care nu exista; pana acum fallback-ul il
 *    trimitea inapoi pe prima pagina. Fara redirectionarea asta, oprirea
 *    fallback-ului l-ar transforma intr-un 404 vizibil.
 *
 * Rulare (din radacina site-ului):
 *   php -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/1-redirectionari-si-404.php
 *
 * Copie a setarilor dinainte: E:/simonamarin/_backup-rankmath-general-2026-09-23b.json
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

$g = get_option( 'rank-math-options-general' );
file_put_contents( 'E:/simonamarin/_backup-rankmath-general-2026-09-23b.json', json_encode( $g, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
$g['redirections_fallback'] = 'default';
update_option( 'rank-math-options-general', $g );
echo "fallback=default\n";

$home = home_url( '/' );
foreach ( array( array( 'servicii', 'servicii-psihologice/' ), array( 'terms-and-conditions-terms', 'termeni-si-conditii/' ), array( 'despre-mine', 'consilier-psihologic/' ) ) as list( $from, $to ) ) {
	$r = \RankMath\Redirections\Redirection::from(
		array(
			'sources'     => array( array( 'pattern' => $from, 'comparison' => 'exact' ) ),
			'url_to'      => $home . $to,
			'header_code' => 301,
			'status'      => 'active',
		)
	);
	echo "redirect $from -> ", $r->save() ? 'ok' : 'eroare', "\n";
}
