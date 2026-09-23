<?php
/**
 * SEO, pasul 7 — Dezindexare si redirectionare 301 pentru atelierele din 2018.
 *
 * Paginile:
 *   794  /grup-de-dezvoltare-personala-7-10-ani/  („Data limita 18 octombrie 2018")
 *   989  /grup-de-suport-pentru-viitoarele-mame/  („Incepand cu 6 noiembrie 2018")
 *
 * Actiuni:
 *   1. Seteaza `rank_math_robots` pe `noindex` (le scoate automat din sitemap-ul XML).
 *   2. Adauga redirectionare 301 explicita in Rank Math catre pagina generala /ateliere/,
 *      astfel incat Google sa le elimine din index si vizitatorii sa ajunga pe pagina curenta de ateliere.
 *
 * Rulare:
 *   php -d extension_dir="C:\Users\Marin Marius Alin\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\ext" -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/7-deindexare-ateliere-2018.php
 *
 * Copie de siguranta: E:/simonamarin/_backup-ateliere-2018-deindexare.json
 */
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';
global $wpdb;

$ids = array( 794, 989 );

$bak = array();
foreach ( $ids as $id ) {
	$bak[ $id ] = array(
		'title'             => get_the_title( $id ),
		'slug'              => get_post_field( 'post_name', $id ),
		'rank_math_robots'  => get_post_meta( $id, 'rank_math_robots', true ),
	);
}

$bak_file = 'E:/simonamarin/_backup-ateliere-2018-deindexare.json';
file_put_contents( $bak_file, json_encode( $bak, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE ) );
echo "Backup salvat in: $bak_file\n";

// 1. Seteaza noindex in Rank Math
foreach ( $ids as $id ) {
	update_post_meta( $id, 'rank_math_robots', array( 'noindex' ) );
	clean_post_cache( $id );
	echo "Post $id setat pe noindex\n";
}

// 2. Redirectionari 301 spre /ateliere/
$home = home_url( '/' );
$target_url = $home . 'ateliere/';

$sources_to_redirect = array(
	'grup-de-dezvoltare-personala-7-10-ani',
	'grup-de-suport-pentru-viitoarele-mame',
);

foreach ( $sources_to_redirect as $slug ) {
	// Verifica daca exista deja o redirectionare
	$existing = \RankMath\Redirections\DB::match_redirections( $slug );
	if ( ! empty( $existing ) ) {
		echo "Redirectionarea pentru $slug exista deja.\n";
		continue;
	}

	$r = \RankMath\Redirections\Redirection::from(
		array(
			'sources'     => array( array( 'pattern' => $slug, 'comparison' => 'exact' ) ),
			'url_to'      => $target_url,
			'header_code' => 301,
			'status'      => 'active',
		)
	);
	echo "Redirect 301 $slug -> /ateliere/: ", $r->save() ? 'OK' : 'EROARE', "\n";
}

// Goleste cache-ul sitemap Rank Math daca e activ
if ( class_exists( '\RankMath\Sitemap\Cache' ) ) {
	\RankMath\Sitemap\Cache::invalidate_storage();
	echo "Cache sitemap invalidat.\n";
}

echo "Finalizat cu succes.\n";
