<?php
/**
 * Articole conexe la finalul fiecarui articol.
 *
 * ---------------------------------------------------------------------------
 * DE CE
 * ---------------------------------------------------------------------------
 * Articolele nu se leaga intre ele: cine termina de citit un text nu are pe
 * pagina niciun drum spre altul, iar pentru Google multe articole sunt
 * „izolate" - la ele se ajunge doar din lista de pe /articole/. Trei legaturi
 * spre articole de pe acelasi subiect rezolva ambele probleme fara sa atinga
 * textul vreunui articol.
 *
 * ---------------------------------------------------------------------------
 * NICIUN TEXT NOU
 * ---------------------------------------------------------------------------
 * Ca in subsol (inc/footer.php): pe pagina apar doar texte care exista deja.
 * Titlul sectiunii este numele categoriei comune, citit din baza de date, iar
 * cardurile au titlul articolului si imaginea lui. Fara rezumat: la finalul
 * unui text lung, trei rezumate de cate un paragraf ar fi inca o pagina de
 * citit, nu o recomandare.
 *
 * ---------------------------------------------------------------------------
 * DE CE NU FUNCTIA DIN SYDNEY
 * ---------------------------------------------------------------------------
 * Sydney are `sydney_related_posts()`, oprita din Customizer. Afiseaza data
 * fiecarui articol (scoasa intentionat de pe site, vezi inc/post-meta.php),
 * are titlul implicit in engleza si alege primele trei articole din ORICARE
 * categorie comuna - adica, pe acest site, aproape mereu aceleasi trei din
 * „Din psihologie", care cuprinde majoritatea articolelor.
 *
 * Ca la lista de pe /articole/ (inc/articles-list.php), sectiunea se adauga
 * prin filtrul de nucleu `the_content`, nu printr-un sablon sau un hook al
 * temei: supravietuieste unui update Sydney si chiar unei schimbari de tema.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cate articole se recomanda.
 */
const SIMONAMARIN_RELATED_POSTS_COUNT = 3;

/**
 * Alege articolele conexe si categoria dupa care au fost alese.
 *
 * Categoria de referinta este cea mai specifica dintre categoriile articolului
 * (cele mai putine articole): pentru un articol din „Cuplu" si „Din
 * psihologie", recomandarile vin din „Cuplu". In interiorul categoriei,
 * articolele se ordoneaza dupa apropierea datei de publicare - texte scrise in
 * aceeasi perioada, adesea in serie (Gelozia I, Portretul omului gelos...).
 *
 * Ordinea e determinista, nu aleatorie: aceleasi legaturi la fiecare vizita,
 * deci cache-ul LiteSpeed ramane valid, iar Google vede o structura stabila.
 *
 * Daca in categoria cea mai specifica nu sunt destule articole, se completeaza
 * din celelalte categorii ale articolului, in aceeasi ordine a specificitatii.
 *
 * @param WP_Post $post Articolul curent.
 * @return array{0: WP_Term|null, 1: WP_Post[]} Categoria si articolele.
 */
function simonamarin_related_posts( $post ) {
	$categories = array_filter(
		get_the_category( $post->ID ),
		static function ( $category ) {
			return 'uncategorized' !== $category->slug && $category->count > 1;
		}
	);

	if ( ! $categories ) {
		return array( null, array() );
	}

	usort(
		$categories,
		static function ( $a, $b ) {
			return $a->count - $b->count;
		}
	);

	$post_time = get_post_timestamp( $post );
	$chosen    = array();

	foreach ( $categories as $category ) {
		$candidates = get_posts(
			array(
				'post_type'        => 'post',
				'post_status'      => 'publish',
				'cat'              => $category->term_id,
				'post__not_in'     => array_merge( array( $post->ID ), array_keys( $chosen ) ),
				'posts_per_page'   => -1,
				'no_found_rows'    => true,
				'suppress_filters' => false,
			)
		);

		usort(
			$candidates,
			static function ( $a, $b ) use ( $post_time ) {
				return abs( get_post_timestamp( $a ) - $post_time ) - abs( get_post_timestamp( $b ) - $post_time );
			}
		);

		foreach ( $candidates as $candidate ) {
			$chosen[ $candidate->ID ] = $candidate;
			if ( count( $chosen ) >= SIMONAMARIN_RELATED_POSTS_COUNT ) {
				break 2;
			}
		}
	}

	return array( $categories[0], array_values( $chosen ) );
}

/**
 * Marcajul sectiunii.
 *
 * `<aside>` si nu `<section>`: e continut inrudit, nu parte din articol.
 * Titlul e `<h2>` - pagina are un singur H1, titlul articolului.
 *
 * Imaginea are `alt=""`: titlul articolului urmeaza imediat in aceeasi
 * legatura, iar un cititor de ecran l-ar anunta altfel de doua ori.
 *
 * @param WP_Post $post Articolul curent.
 * @return string Marcajul, sau sir gol daca nu exista recomandari.
 */
function simonamarin_render_related_posts( $post ) {
	list( $category, $related ) = simonamarin_related_posts( $post );

	if ( ! $related ) {
		return '';
	}

	$items = '';
	foreach ( $related as $item ) {
		$thumbnail = get_the_post_thumbnail(
			$item,
			'medium_large',
			array(
				'alt'     => '',
				'loading' => 'lazy',
				'sizes'   => '(max-width: 600px) 100vw, 240px',
			)
		);

		$items .= sprintf(
			'<li><a class="sm-related__link" href="%1$s">%2$s<span class="sm-related__title">%3$s</span></a></li>',
			esc_url( get_permalink( $item ) ),
			$thumbnail ? '<span class="sm-related__img">' . $thumbnail . '</span>' : '', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- marcaj generat de WordPress.
			esc_html( wp_strip_all_tags( html_entity_decode( get_the_title( $item ), ENT_QUOTES, 'UTF-8' ) ) )
		);
	}

	return sprintf(
		'<aside class="sm-related" aria-labelledby="sm-related-title">'
		. '<h2 id="sm-related-title" class="sm-related__heading">%1$s</h2>'
		. '<ul class="sm-related__list">%2$s</ul>'
		. '</aside>',
		esc_html( html_entity_decode( $category->name, ENT_QUOTES, 'UTF-8' ) ),
		$items
	);
}

/**
 * Adauga sectiunea sub continutul articolului.
 *
 * Aceleasi trei verificari ca la lista de pe /articole/: `the_content` ruleaza
 * si in afara paginii propriu-zise (feed, descrieri generate de pluginul de
 * SEO, fragmente), iar acolo sectiunea nu are ce cauta.
 *
 * @param string $content Continutul articolului.
 * @return string Continutul, cu recomandarile la final.
 */
function simonamarin_append_related_posts( $content ) {
	if ( ! is_singular( 'post' ) || ! is_main_query() || ! in_the_loop() || is_feed() ) {
		return $content;
	}

	return $content . simonamarin_render_related_posts( get_post() );
}
add_filter( 'the_content', 'simonamarin_append_related_posts', 20 );

/**
 * Stilul sectiunii, doar pe articole.
 *
 * Fisier separat si nu o sectiune in redesign-pages.css: se incarca doar unde
 * exista sectiunea. Depinde de redesign.css, unde sunt declarate jetoanele.
 */
function simonamarin_enqueue_related_posts_style() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}
	$asset = sydney_child_asset( 'assets/css/related-posts.css' );
	if ( $asset ) {
		wp_enqueue_style( 'sydney-child-related-posts', $asset[0], array( 'sydney-child-redesign-pages' ), $asset[1] );
	}
}
add_action( 'wp_enqueue_scripts', 'simonamarin_enqueue_related_posts_style', 13 );
