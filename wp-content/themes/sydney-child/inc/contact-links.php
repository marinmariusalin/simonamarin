<?php
/**
 * Banda de legaturi de contact din antet (MIG-01).
 *
 * ---------------------------------------------------------------------------
 * DE CE PRIN FILTRUL `wp_nav_menu_items` SI NU ALTFEL
 * ---------------------------------------------------------------------------
 * Aceste cinci legaturi au existat pe site ani de zile, scrise direct in
 * header.php al temei Sydney. Au disparut in momentul actualizarii temei la
 * versiunea 2.71, impreuna cu tot fisierul. Asta este exact problema pe care
 * acest fisier o rezolva definitiv, si de aceea alegerea mecanismului conteaza
 * mai mult decat rezultatul vizual.
 *
 * Variantele posibile si de ce au fost respinse:
 *
 *   1. Un header.php propriu in tema copil. Cel mai rapid, dar reintra exact in
 *      capcana: un sablon scris pentru markup-ul unei anumite versiuni, care se
 *      rupe tacut la urmatoarea. Am avut deja un astfel de fisier in aceasta
 *      tema si a trebuit sters dupa o ora.
 *
 *   2. Modulul "header builder" din Sydney 2.71, cu componentele `social` si
 *      `contact-info`. Este modul propriu al temei de a face asta si ar fi
 *      corect din punctul de vedere al temei. Dar leaga legaturile de
 *      arhitectura de module a lui Sydney, care se poate schimba la fel de bine
 *      ca restul, si cere reconstruirea intregului antet, inclusiv a culorilor
 *      abia readuse la design-ul original.
 *
 *   3. Filtrul `wp_nav_menu_items` - varianta aleasa. Este un filtru din
 *      NUCLEUL WordPress, nu din tema. Supravietuieste oricarui update al lui
 *      Sydney si, mai important, ar functiona identic si daca site-ul ar trece
 *      maine pe cu totul alta tema. Singura conditie este ca tema sa foloseasca
 *      wp_nav_menu() cu o locatie de meniu, ceea ce fac practic toate.
 *
 * ---------------------------------------------------------------------------
 * O SCHIMBARE DE POZITIE, ASUMATA
 * ---------------------------------------------------------------------------
 * In design-ul vechi iconitele stateau langa titlul site-ului, in stanga. Aici
 * apar la capatul meniului, in dreapta. Nu este o limitare tehnica: este
 * consecinta faptului ca ele devin elemente de meniu.
 * Pozitia noua este un tipar raspandit si cel putin la fel de descoperibil -
 * zona din dreapta a antetului este locul unde utilizatorii cauta actiuni. Daca
 * pozitia din stanga este ceruta explicit, atunci varianta corecta devine
 * modulul header builder, cu costurile de mai sus.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Datele de contact afisate in antet.
 *
 * Sunt informatii publice ale cabinetului, preluate exact cum erau in
 * header.php inainte de migrare - niciun numar si nicio adresa nu au fost
 * schimbate. Daca se modifica vreodata, se modifica AICI si in subsol, si se
 * verifica in paralel si datele din Rank Math (vezi MIG-02).
 *
 * @return array Lista de legaturi, in ordinea afisarii.
 */
function simonamarin_contact_links() {
	return array(
		array(
			'icon'  => 'facebook',
			'url'   => 'https://www.facebook.com/PsihologSimonaMarin',
			'label' => __( 'Facebook', 'sydney-child' ),
			'rel'   => 'nofollow',
			'prop'  => 'sameAs',
		),
		array(
			'icon'  => 'whatsapp',
			'url'   => 'https://wa.me/40747668204',
			'label' => __( 'WhatsApp', 'sydney-child' ),
			'rel'   => 'nofollow',
			'prop'  => 'sameAs',
		),
		array(
			'icon'  => 'instagram',
			'url'   => 'https://www.instagram.com/simonamarin.ro/',
			'label' => __( 'Instagram', 'sydney-child' ),
			'rel'   => 'nofollow',
			'prop'  => 'sameAs',
		),
		array(
			'icon'  => 'phone',
			'url'   => 'tel:0747668204',
			'label' => __( 'Telefon', 'sydney-child' ),
			'rel'   => '',
			'prop'  => 'telephone',
		),
		array(
			'icon'  => 'envelope',
			'url'   => 'mailto:psihologsimonamarin@gmail.com',
			'label' => __( 'Email', 'sydney-child' ),
			'rel'   => '',
			'prop'  => 'email',
		),
	);
}

/**
 * Adauga legaturile de contact la finalul meniului principal.
 *
 * `aria-label` pe fiecare legatura nu este optional: continutul vizibil este o
 * iconita SVG marcata `aria-hidden`, deci fara eticheta un cititor de ecran ar
 * anunta doar "link", fara sa spuna unde duce.
 *
 * Verificarea locatiei este importanta - fara ea, iconitele ar aparea in ORICE
 * meniu de pe site, inclusiv intr-unul viitor de subsol.
 *
 * @param string   $items HTML-ul elementelor de meniu.
 * @param stdClass $args  Argumentele apelului wp_nav_menu().
 * @return string HTML-ul, cu legaturile adaugate la final.
 */
function simonamarin_add_contact_links_to_menu( $items, $args ) {
	$location = isset( $args->theme_location ) ? $args->theme_location : '';

	if ( 'primary' !== $location ) {
		return $items;
	}

	if ( ! function_exists( 'simonamarin_icon' ) ) {
		return $items;
	}

	/*
	 * Toate cele cinci legaturi stau intr-un SINGUR <li>, nu in cinci.
	 *
	 * Prima varianta emitea cate un element de meniu pentru fiecare iconita.
	 * Rezultatul, vizibil imediat in browser: meniul devenea prea lat si se
	 * rupea pe doua randuri, cu doua iconite sus si trei jos, iar titlul
	 * site-ului se imparteau si el pe doua linii.
	 * Grupate intr-un element unic, ele nu mai pot fi despartite de mecanismul
	 * de incadrare al meniului si ocupa vizibil mai putin loc.
	 */
	$out = '';

	foreach ( simonamarin_contact_links() as $link ) {
		$rel  = $link['rel'] ? sprintf( ' rel="%s"', esc_attr( $link['rel'] ) ) : '';
		$prop = $link['prop'] ? sprintf( ' itemprop="%s"', esc_attr( $link['prop'] ) ) : '';

		$out .= sprintf(
			'<a class="sm-contact-link" href="%1$s"%2$s%3$s aria-label="%4$s" title="%4$s">%5$s</a>',
			esc_url( $link['url'] ),
			$rel,
			$prop,
			esc_attr( $link['label'] ),
			simonamarin_icon( $link['icon'] )
		);
	}

	return $items . '<li class="menu-item sm-contact-group">' . $out . '</li>';
}
add_filter( 'wp_nav_menu_items', 'simonamarin_add_contact_links_to_menu', 10, 2 );
