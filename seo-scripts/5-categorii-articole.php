<?php
/**
 * SEO, pasul 5 — articolele din „Uncategorized" trec in categoriile existente.
 *
 * 21 de articole erau doar (sau si) in „Uncategorized". Nu se creeaza, nu se
 * redenumesc si nu se sterg categorii; nu se atinge textul niciunui articol.
 * Se schimba doar legatura articol-categorie.
 *
 * Categoriile nu sunt afisate vizitatorilor (widgetul din subsol e ascuns prin
 * CSS, sub titlu au fost scoase). Rostul lor e pentru Google: ajung in
 * `articleSection` din datele structurate (structured-data.php, 3b).
 *
 * Criteriul urmeaza incadrarea pe care site-ul o avea deja:
 * - cuplu / relatie        -> Din psihologie + Cuplu (ca 61, 64, 66, 89, 108)
 * - copii / parinti / teme psihologice -> Din psihologie (ca 428, 438, 443, 513)
 * - schimbare, decizii, echilibru personal -> Din viata cotidiana (ca 1022, 1870)
 *
 * Rulare (din radacina site-ului):
 *   php -d extension=php_mysqli.dll -d mysqli.default_port=10005 seo-scripts/5-categorii-articole.php
 *
 * Copie a incadrarii dinainte: E:/simonamarin/_backup-seo-2026-09-23-pas5.json
 */
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';

$psi    = 'din-psihologie';
$cuplu  = 'cuplu';
$zilnic = 'din-viata-cotidiana';

$plan = array(
	1213 => array( $psi ),          // Recomandari pentru parintii adolescentilor
	1385 => array( $psi ),          // De ce au copiii comportamente considerate neadecvate?
	1537 => array( $psi ),          // Psihoterapia
	1742 => array( $psi ),          // Meditatie, mit si realitate
	1760 => array( $psi ),          // Cand parintii devin ... parinti
	2076 => array( $psi ),          // Relaxarea progresiva
	2117 => array( $psi ),          // Ce povesti spunem copiilor
	3590 => array( $psi ),          // Dieta si sanatatea mintala
	3601 => array( $psi ),          // Relaxarea fizica si psihologica
	1716 => array( $psi, $cuplu ),  // Cu relatia de cuplu la psiholog
	1725 => array( $psi, $cuplu ),  // Destramarea unei relatii
	1731 => array( $psi, $cuplu ),  // Portretul omului gelos
	1752 => array( $psi, $cuplu ),  // Dragostea - model parental
	2131 => array( $psi, $cuplu ),  // Cuplul in perioada pandemiei
	1574 => array( $zilnic ),       // Alege acum sa fii bine cu tine
	1589 => array( $zilnic ),       // Cand stii ca se apropie o perioada a schimbarilor
	1620 => array( $zilnic ),       // Punct de cotitura
	1993 => array( $zilnic ),       // 7 stalpi ...
	2095 => array( $zilnic ),       // Cum implementezi o schimbare
	// Aveau deja categorii reale; li se scoate doar „Uncategorized".
	2197 => array( $psi, 'intrebari-frecvente' ), // Psihoterapia la distanta
	1870 => array( $zilnic ),                      // Ce este stresul?
);

$bak = 'E:/simonamarin/_backup-seo-2026-09-23-pas5.json';
if ( ! file_exists( $bak ) ) {
	$before = array();
	foreach ( array_keys( $plan ) as $id ) {
		$before[ $id ] = wp_get_post_categories( $id, array( 'fields' => 'slugs' ) );
	}
	file_put_contents( $bak, json_encode( $before, JSON_PRETTY_PRINT ) );
}

foreach ( $plan as $id => $slugs ) {
	$post = get_post( $id );
	if ( ! $post || 'post' !== $post->post_type ) {
		echo "SKIP $id: nu exista\n";
		continue;
	}
	$ids = array();
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( ! $term ) {
			echo "STOP $id: categoria $slug nu exista\n";
			continue 2;
		}
		$ids[] = (int) $term->term_id;
	}
	wp_set_post_categories( $id, $ids, false );
	echo "OK $id -> " . implode( ', ', wp_get_post_categories( $id, array( 'fields' => 'names' ) ) ) . "\n";
}

$uncat = get_term_by( 'slug', 'uncategorized', 'category' );
echo 'Ramase in Uncategorized: ' . ( $uncat ? (int) $uncat->count : 0 ) . "\n";
