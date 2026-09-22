<?php
/**
 * Sistemul de design - incarcarea fisierelor.
 *
 * Aici se aseaza, intr-o singura ordine explicita, cele trei straturi de CSS pe
 * care le are site-ul de acum inainte:
 *
 *   1. fonts.css              - @font-face, fonturi gazduite local
 *   2. legacy-customizer.css  - CSS-ul istoric, mutat din Customizer ca atare
 *   3. redesign.css           - sistemul de design nou, scris sa castige
 *
 * ORDINEA E INTREG SCOPUL ACESTUI FISIER.
 *
 * Pana acum, CSS-ul de design statea in Customizer. WordPress il tipareste
 * inline in <head> prin `wp_custom_css_cb`, la `wp_head` prioritatea 101, adica
 * dupa toate foile de stil puse in coada (care se tiparesc la prioritatea 8).
 * Consecinta: orice regula noua adaugata intr-un fisier al temei pierdea in
 * fata lui la specificitate egala, si singura scapare ramanea `!important` -
 * exact felul in care CSS-ul acela a ajuns sa aiba 27 KB.
 *
 * Mutat in fisier, intra in lantul de dependente al lui `wp_enqueue_style`,
 * care este singurul mecanism pe care WordPress il respecta deterministic:
 *
 *   sydney-bootstrap -> sydney-parent-style -> sydney-style (copilul)
 *     -> sydney-child-legacy -> sydney-child-redesign
 *
 * Aceeasi lectie ca la mutarea pe tema copil (vezi comentariul lung din
 * functions.php): ordinea nu se obtine din prioritati de hook, ci din
 * dependente declarate.
 *
 * Versiunile vin din `filemtime`, nu dintr-un numar scris de mana, ca browserul
 * sa reia fisierul din cache exact cand se schimba si niciodata altfel. Pe acest
 * site s-a pierdut deja timp cu un CSS care parea ca nu se aplica, dar era doar
 * servit din cache.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Intoarce URL-ul si versiunea unui fisier din assets/, sau null daca lipseste.
 *
 * Verificarea de existenta nu e formalitate: daca un fisier lipseste (mutare,
 * deploy partial), `filemtime` emite warning si versiunea devine `false`, ceea
 * ce inseamna versiunea WordPress in URL - adica exact genul de cache gresit pe
 * care mecanismul asta ar trebui sa il previna.
 *
 * @param string $rel Cale relativa la directorul temei copil.
 * @return array{0:string,1:string}|null Perechea URL + versiune.
 */
function sydney_child_asset( $rel ) {
	$path = get_stylesheet_directory() . '/' . ltrim( $rel, '/' );

	if ( ! file_exists( $path ) ) {
		return null;
	}

	return array(
		get_stylesheet_directory_uri() . '/' . ltrim( $rel, '/' ),
		(string) filemtime( $path ),
	);
}

/**
 * Pune in coada straturile de design.
 *
 * Prioritatea 12 vine dupa 11, unde `sydney_child_enqueue_parent_style` adauga
 * dependenta pe `sydney-style`. Daca ar rula mai devreme, handle-ul copilului
 * nu ar fi inca legat de cel al parintelui si lantul s-ar rupe tacut.
 */
function sydney_child_enqueue_design_system() {
	$deps = array();

	/*
	 * Fonturile nu depind de nimic si nu depinde nimeni de ele ca ordine - un
	 * @font-face se aplica indiferent unde e declarat. Le pun primele doar ca
	 * browserul sa le vada cat mai devreme si sa porneasca descarcarea.
	 */
	$fonts = sydney_child_asset( 'assets/css/fonts.css' );
	if ( $fonts ) {
		wp_enqueue_style( 'sydney-child-fonts', $fonts[0], array(), $fonts[1] );
	}

	/*
	 * CSS-ul istoric. Depinde de stilul temei copil ('sydney-style' - Sydney
	 * inregistreaza style.css-ul copilului sub acest handle), ca sa pastreze
	 * exact pozitia pe care o avea cand era tiparit inline: dupa tot restul.
	 */
	$legacy = sydney_child_asset( 'assets/css/legacy-customizer.css' );
	if ( $legacy ) {
		wp_enqueue_style( 'sydney-child-legacy', $legacy[0], array( 'sydney-style' ), $legacy[1] );
		$deps[] = 'sydney-child-legacy';
	}

	/*
	 * Sistemul de design nou. Vine ultimul, deci castiga la specificitate egala
	 * fara `!important`. Acolo unde tot nu castiga, cauza e o regula `!important`
	 * ramasa in CSS-ul istoric - si aceea se sterge din fisierul istoric, nu se
	 * contra-atacata cu un al doilea `!important`.
	 */
	$redesign = sydney_child_asset( 'assets/css/redesign.css' );
	if ( $redesign ) {
		wp_enqueue_style( 'sydney-child-redesign', $redesign[0], $deps, $redesign[1] );
		$deps[] = 'sydney-child-redesign';
	}

	/*
	 * Paginile interioare. Fisier separat de redesign.css nu din motive tehnice
	 * - ar merge si intr-unul singur - ci pentru ca sunt doua etape diferite de
	 * lucru: prima pagina s-a verificat si s-a inchis, restul site-ului se
	 * verifica pagina cu pagina. Doua fisiere inseamna doua diff-uri separate.
	 * Jetoanele (`:root`) sunt declarate in redesign.css, deci depinde de el.
	 */
	$pages = sydney_child_asset( 'assets/css/redesign-pages.css' );
	if ( $pages ) {
		wp_enqueue_style( 'sydney-child-redesign-pages', $pages[0], $deps, $pages[1] );
	}

	/*
	 * Antetul si subsolul. Al treilea fisier, si nu o sectiune in unul dintre
	 * cele doua de mai sus, pentru ca sunt singurele piese care apar pe TOATE
	 * paginile: nu pot fi scopate nici pe `.home`, nici pe `body:not(.home)`.
	 */
	$chrome = sydney_child_asset( 'assets/css/redesign-chrome.css' );
	if ( $chrome ) {
		wp_enqueue_style( 'sydney-child-redesign-chrome', $chrome[0], $deps, $chrome[1] );
	}
}
add_action( 'wp_enqueue_scripts', 'sydney_child_enqueue_design_system', 12 );

/**
 * Scoate din coada legatura catre Google Fonts pusa de tema parinte.
 *
 * Sydney cere `fonts.googleapis.com/css2?family=Poppins:wght@400&family=Poppins:wght@600`.
 * Doua motive pentru care nu mai are ce cauta acolo:
 *
 * 1. GDPR. Fiecare incarcare de pagina trimite adresa IP a vizitatorului catre
 *    Google. Pe un site care colecteaza date de sanatate prin formularul de
 *    contact (art. 9), transferul nu are temei si nici nu e nevoie de el.
 * 2. Dublura. Exact aceleasi doua greutati de Poppins sunt acum in
 *    assets/fonts/, servite de pe acelasi domeniu.
 *
 * Se face pe prioritatea 100, ca sa ruleze dupa ce tema parinte si-a pus
 * stilurile in coada - un `wp_dequeue_style` pe un handle neinregistrat inca nu
 * face nimic.
 */
function sydney_child_dequeue_google_fonts() {
	wp_dequeue_style( 'sydney-google-fonts' );
	wp_deregister_style( 'sydney-google-fonts' );
}
add_action( 'wp_enqueue_scripts', 'sydney_child_dequeue_google_fonts', 100 );

/**
 * Scriptul care repara butonul principal „Contact" de pe prima pagina.
 *
 * Motivatia completa e in antetul lui assets/js/cta-fix.js. Pe scurt: butonul e
 * scris in continutul paginii ca `<div ... href="contact-form">`, iar `href` pe
 * un `<div>` nu inseamna nimic - actiunea principala a site-ului nu face nimic.
 * Reparatia se face aici si nu prin editarea continutului din baza de date,
 * pentru ca marcajul paginilor apartine utilizatorului.
 *
 * Se incarca doar pe prima pagina, singurul loc unde exista `#purple-button`.
 * URL-ul tintei vine din PHP, nu scris de mana in JS: daca pagina de contact e
 * mutata sau redenumita, linkul o urmeaza.
 */
function sydney_child_enqueue_cta_fix() {
	if ( ! is_front_page() && ! is_home() ) {
		return;
	}

	$script = sydney_child_asset( 'assets/js/cta-fix.js' );
	if ( ! $script ) {
		return;
	}

	wp_enqueue_script( 'sydney-child-cta-fix', $script[0], array(), $script[1], true );

	/*
	 * `get_page_by_path` cauta dupa slug, deci supravietuieste unei redenumiri a
	 * titlului. Daca pagina lipseste cu totul, ramane calea implicita din JS.
	 */
	$contact = get_page_by_path( 'contact' );
	$url     = $contact ? get_permalink( $contact ) : home_url( '/contact/' );

	wp_localize_script( 'sydney-child-cta-fix', 'smCtaFix', array( 'contactUrl' => $url ) );
}
add_action( 'wp_enqueue_scripts', 'sydney_child_enqueue_cta_fix', 12 );
