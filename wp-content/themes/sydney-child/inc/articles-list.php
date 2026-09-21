<?php
/**
 * Lista de articole de pe pagina /articole/ (MIG-07).
 *
 * ---------------------------------------------------------------------------
 * CE S-A INTAMPLAT
 * ---------------------------------------------------------------------------
 * Pagina /articole/ nu este pagina de articole a WordPress-ului: optiunea
 * `page_for_posts` este 0, iar /articole/ este o pagina obisnuita al carei
 * continut contine DOAR un banner cu titlu. Lista propriu-zisa nu a fost
 * niciodata in continutul paginii.
 *
 * Ea era produsa de o functie `getArticles()` din functions.php-ul temei
 * Sydney 1.x, apelata direct din page.php al acelei teme. Actualizarea la
 * Sydney 2.71 a inlocuit ambele fisiere, asa ca lista a disparut fara nicio
 * eroare: pagina raspunde in continuare cu 200 si arata bannerul, doar ca
 * dedesubt nu mai e nimic.
 *
 * Este exact tipul de regresie tacuta pentru care exista aceasta tema copil.
 *
 * ---------------------------------------------------------------------------
 * DE CE PRIN `the_content` SI NU PRINTR-UN page.php COPIAT
 * ---------------------------------------------------------------------------
 * `the_content` este un filtru din nucleul WordPress. Un page.php copiat in
 * tema copil ar functiona azi si s-ar rupe la urmatorul update al lui Sydney,
 * adica ar reproduce fix cauza acestei probleme. Aceeasi alegere a fost facuta
 * si pentru legaturile de contact din antet - vezi inc/contact-links.php.
 *
 * ---------------------------------------------------------------------------
 * MARCAJUL ESTE PASTRAT IDENTIC, INTENTIONAT
 * ---------------------------------------------------------------------------
 * Clasele `articles-container`, `articole-box`, `card-content`, `text4`,
 * `textP` si `article-img-content` nu vin din tema, ci din CSS-ul aditional al
 * site-ului (optiunea `custom_css_post_id`, ID 171), care a supravietuit
 * migrarii neatins. Reproducand exact acelasi marcaj, stilurile existente se
 * aplica mai departe si pagina arata ca inainte, fara sa fie nevoie de CSS nou.
 *
 * Structura originala, pastrata:
 *   <div class="articles-container row">
 *     <a class="col-md-5 col-xs-10 articole-box">
 *       <div class="row card-content">
 *         <div class="col-md-12 text4">titlu</div>
 *         <div class="col-md-12">
 *           <div class="article-img-content">imagine</div>
 *           <div class="textP">rezumat</div>
 *         </div>
 *       </div>
 *     </a>
 *   </div>
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Numarul de caractere din rezumatul fiecarui articol.
 *
 * 350 este valoarea din implementarea originala, pastrata ca sa nu se schimbe
 * inaltimea cardurilor.
 */
const SIMONAMARIN_ARTICLE_EXCERPT_LENGTH = 350;

/**
 * Construieste lista de articole.
 *
 * TREI CORECTURI FATA DE IMPLEMENTAREA ORIGINALA, toate cu motiv:
 *
 * 1. Detectarea paginii. Originalul folosea
 *    `strpos( $_SERVER['REQUEST_URI'], 'articole' )`, ceea ce inseamna ca lista
 *    aparea pe ORICE adresa care continea sirul "articole" - inclusiv, de
 *    exemplu, pe un articol al carui slug l-ar fi continut intamplator. Aici se
 *    foloseste is_page(), care verifica pagina reala.
 *
 * 2. Taierea rezumatului. Originalul folosea `substr()`, care numara OCTETI, nu
 *    caractere. Pe un text romanesc, unde fiecare diacritica ocupa doi octeti,
 *    taietura putea cadea in mijlocul unui caracter si producea un simbol
 *    invalid la finalul fiecarui rezumat. `mb_substr()` numara caractere.
 *
 * 3. Escapare. Originalul concatena titlul si continutul direct in HTML. Sunt
 *    texte scrise de proprietarul site-ului, deci riscul practic e mic, dar
 *    escaparea nu costa nimic si opreste orice marcaj scapat din continut sa
 *    strice structura paginii.
 *
 * @return string Marcajul listei, sau sir gol daca nu exista articole.
 */
function simonamarin_render_articles_list() {
	$posts = get_posts(
		array(
			'posts_per_page' => -1,
			'post_type'      => 'post',
			'post_status'    => 'publish',
		)
	);

	if ( ! $posts ) {
		return '';
	}

	$out = '<div class="articles-container row">';

	foreach ( $posts as $post_item ) {
		/*
		 * Rezumatul se construieste MEREU din continut, niciodata din
		 * `post_excerpt`, desi articolele au si rezumate proprii.
		 *
		 * Prima varianta prefera post_excerpt cand exista. Rezultatul, vizibil
		 * imediat pe pagina: rezumatele de pe acest site contin marcaj HTML
		 * scris de mana, iar escapandu-l corect obtineam exact ce vrea
		 * escaparea sa faca - marcajul aparea ca text, cu tot cu
		 * `<a href="...">` in mijlocul propozitiei.
		 *
		 * Implementarea originala folosea tot continutul, deci asta reproduce
		 * si comportamentul de dinainte de migrare. Daca se doreste vreodata
		 * folosirea rezumatelor proprii, ele trebuie intai curatate de marcaj -
		 * dar aceea e o schimbare de aspect, nu o reparatie.
		 *
		 * strip_shortcodes inainte de eliminarea tagurilor: altfel un shortcode
		 * neexecutat ar aparea ca text brut in rezumat.
		 */
		$excerpt = wp_strip_all_tags( strip_shortcodes( $post_item->post_content ) );
		$excerpt = trim( preg_replace( '/\s+/u', ' ', $excerpt ) );

		if ( mb_strlen( $excerpt ) > SIMONAMARIN_ARTICLE_EXCERPT_LENGTH ) {
			$excerpt = mb_substr( $excerpt, 0, SIMONAMARIN_ARTICLE_EXCERPT_LENGTH ) . '...';
		}

		/*
		 * Dimensiunea implicita a miniaturii, ca in implementarea originala.
		 * Cerand explicit 'medium' am obtinut o imagine de alta latime, iar
		 * CSS-ul site-ului - care asaza imaginea in stanga si lasa textul sa
		 * curga in dreapta - producea alt rezultat: imaginea trecea deasupra
		 * textului, pe toata latimea cardului.
		 */
		$thumbnail = get_the_post_thumbnail( $post_item );

		$out .= sprintf(
			'<a href="%1$s" class="col-md-5 col-xs-10 articole-box">'
			. '<div class="row card-content">'
			. '<div class="col-md-12 text4">%2$s</div>'
			. '<div class="col-md-12">'
			. '<div class="article-img-content">%3$s</div>'
			. '<div class="textP">%4$s</div>'
			. '</div></div></a>',
			esc_url( get_permalink( $post_item ) ),
			esc_html( $post_item->post_title ),
			$thumbnail, // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- marcaj generat de WordPress.
			esc_html( $excerpt )
		);
	}

	$out .= '</div>';

	return $out;
}

/**
 * Adauga lista sub continutul paginii /articole/.
 *
 * Cele trei verificari de la inceput nu sunt exces de prudenta. `the_content`
 * se executa ori de cate ori este randat continutul unui articol sau al unei
 * pagini, inclusiv in afara buclei principale - de exemplu cand un plugin de
 * SEO construieste o descriere, cand se genereaza un feed sau cand un widget
 * afiseaza un fragment. Fara ele, lista de articole ar fi fost inserata si
 * acolo.
 *
 * @param string $content Continutul paginii.
 * @return string Continutul, cu lista adaugata la final pe pagina potrivita.
 */
function simonamarin_append_articles_list( $content ) {
	if ( ! is_page( 'articole' ) || ! is_main_query() || ! in_the_loop() ) {
		return $content;
	}

	// `carousel-container-home` este wrapperul pe care il avea si page.php-ul
	// vechi in jurul apelului. Pastrat pentru ca CSS-ul site-ului il foloseste.
	return $content . '<div class="row carousel-container-home">' . simonamarin_render_articles_list() . '</div>';
}
add_filter( 'the_content', 'simonamarin_append_articles_list' );
