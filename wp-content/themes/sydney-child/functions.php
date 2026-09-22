<?php
/**
 * Tema copil Sydney - functii.
 *
 * Scopul acestui fisier este sa incarce corect stilurile si sa devina locul in
 * care se scrie de acum inainte orice cod propriu al site-ului. Tema parinte
 * (../sydney/) trebuie tratata ca fiind cod al altcuiva: se poate actualiza
 * oricand, iar orice modificare facuta acolo dispare la primul update.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Incarca style.css-ul temei parinte.
 *
 * ACEASTA FUNCTIE NU ESTE OPTIONALA. Tema Sydney isi inregistreaza stilul cu
 * `wp_enqueue_style( 'sydney-style', get_stylesheet_uri(), ... )`, iar
 * `get_stylesheet_uri()` returneaza, intr-o tema copil, style.css-ul COPILULUI.
 * Fara linia de mai jos, CSS-ul original Sydney (~1720 de linii) nu s-ar mai
 * incarca deloc si site-ul ar aparea complet nestilizat.
 *
 * ORDINEA CONTEAZA, SI A FOST GRESITA LA PRIMA INCERCARE.
 *
 * Sydney tipareste, in ordine: bootstrap (hook la prioritatea 9), apoi propriul
 * style.css (prioritatea 10). Prima varianta a acestei functii rula tot pe
 * prioritatea 9 si ajungea sa fie inregistrata INAINTEA bootstrap-ului, deci
 * CSS-ul Sydney era tiparit primul si bootstrap il suprascria - exact invers
 * fata de cum functiona site-ul pana acum. Comparatia pixel cu pixel a
 * paginii de start a aratat 5.42% din pixeli modificati, adica un layout
 * deplasat, nu o diferenta de randare.
 *
 * De aceea ordinea nu se mai obtine din prioritati, ci din DEPENDENTE, care
 * sunt singurul mecanism pe care WordPress il respecta deterministic:
 *   sydney-bootstrap  ->  sydney-parent-style  ->  sydney-style (copilul)
 * Prima legatura o declar la enqueue; a doua o adaug pe stilul deja
 * inregistrat de tema parinte, dupa ce aceasta si-a rulat hook-ul (de aici
 * prioritatea 11).
 *
 * Consecinta practica ramane cea dorita: regulile din style.css-ul copilului
 * vin ultimele si castiga la specificitate egala, fara `!important`.
 *
 * Versiunea vine din filemtime, nu dintr-un numar scris de mana, ca fisierul sa
 * fie reluat din cache exact atunci cand se modifica si niciodata altfel.
 */
function sydney_child_enqueue_parent_style() {
	$parent_style = get_template_directory() . '/style.css';

	wp_enqueue_style(
		'sydney-parent-style',
		get_template_directory_uri() . '/style.css',
		array( 'sydney-bootstrap' ),
		file_exists( $parent_style ) ? (string) filemtime( $parent_style ) : null
	);

	/*
	 * Stilul copilului este inregistrat de tema parinte sub handle-ul
	 * 'sydney-style' (Sydney cere get_stylesheet_uri(), care intr-o tema copil
	 * este fisierul copilului). Nu il pot inregistra eu cu alte dependente, asa
	 * ca ii adaug dependenta pe obiectul deja inregistrat. Verificarea de
	 * existenta conteaza: daca o versiune viitoare a lui Sydney redenumeste
	 * handle-ul, aici nu crapa nimic - dar ordinea trebuie reverificata.
	 */
	$styles = wp_styles();
	if ( isset( $styles->registered['sydney-style'] ) ) {
		$styles->registered['sydney-style']->deps[] = 'sydney-parent-style';
	}
}
add_action( 'wp_enqueue_scripts', 'sydney_child_enqueue_parent_style', 11 );

/**
 * Pastreaza pentru stilul parintelui acelasi tratament de incarcare ca inainte.
 *
 * Tema parinte contine o optimizare (functia make_css_non_render_blocking din
 * sydney/functions.php) care transforma `sydney-style` si `sydney-bootstrap`
 * din <link rel="stylesheet"> in <link rel="preload" ... onload>, ca sa nu mai
 * blocheze randarea. Filtrul acela verifica lista de handle-uri pe nume si nu
 * stie nimic despre `sydney-parent-style`, care este nou.
 *
 * Fara functia de mai jos, CSS-ul principal al site-ului ar trece brusc de la
 * incarcare asincrona la incarcare blocanta - adica exact genul de schimbare
 * nedorita cand singurul scop al acestui pas este ca site-ul sa arate si sa se
 * comporte IDENTIC dupa mutarea pe tema copil.
 *
 * DE REEVALUAT SEPARAT: incarcarea asincrona a foii de stil principale este o
 * optimizare cu pret. Pagina incepe sa se randeze inainte ca stilul sa fie
 * aplicat, deci poate aparea un moment de continut nestilizat (FOUC), iar pe
 * conexiuni lente efectul este vizibil. Este o decizie de luat cu masuratori pe
 * trafic real, nu de schimbat aici din mers.
 *
 * @param string $html   Eticheta <link> generata.
 * @param string $handle Handle-ul stilului.
 * @param string $href   URL-ul fisierului.
 * @return string Eticheta, eventual rescrisa.
 */
function sydney_child_async_parent_style( $html, $handle, $href ) {
	if ( is_admin() || 'sydney-parent-style' !== $handle ) {
		return $html;
	}

	return sprintf(
		'<link rel="preload" href="%1$s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' .
		'<noscript><link rel="stylesheet" href="%1$s"></noscript>' . "\n",
		esc_url( $href )
	);
}
add_filter( 'style_loader_tag', 'sydney_child_async_parent_style', 10, 3 );

/**
 * Iconite SVG inline, in locul webfonturilor FontAwesome.
 *
 * Fisierul este generat din chiar webfonturile livrate cu tema parinte, deci
 * iconitele arata identic. Motivatia completa si instructiunile de regenerare
 * sunt in antetul lui inc/icons.php.
 */
require_once get_stylesheet_directory() . '/inc/icons.php';

/**
 * Legaturile de contact din antet (MIG-01).
 *
 * Se adauga la finalul meniului principal printr-un filtru din nucleul
 * WordPress, nu printr-un sablon propriu si nu prin modulul de constructie a
 * antetului din Sydney. Motivatia completa, si variantele respinse, sunt in
 * antetul fisierului.
 */
require_once get_stylesheet_directory() . '/inc/contact-links.php';

/**
 * Lista de articole de pe pagina /articole/ (MIG-07).
 *
 * Era produsa de o functie din tema Sydney 1.x, apelata din page.php al acelei
 * teme; ambele au disparut la actualizarea la 2.71, iar pagina a ramas doar cu
 * bannerul din continutul ei, fara nicio eroare vizibila.
 */
require_once get_stylesheet_directory() . '/inc/articles-list.php';

/**
 * Completari mici migrate din auditul initial (theme-color, aria-label pe
 * navigare, alt-text implicit pe imaginea reprezentativa, noindex pe cautari
 * fara rezultate). Detaliile "de ce" sunt langa fiecare functie in fisier.
 */
require_once get_stylesheet_directory() . '/inc/meta-enhancements.php';

/*
 * ============================================================================
 * MIGRAREA LA SYDNEY 2.71 - CE A RAMAS
 * ============================================================================
 *
 * Lista completa si prioritizata este in TODO.md din radacina proiectului.
 * Aici raman doar punctele care privesc direct fisierele acestei teme.
 *
 * REZOLVAT, ca sa nu fie reluat:
 *   - Culorile antetului, readuse la valorile masurate pe productie prin
 *     optiunile proprii ale temei. In 2.71 fiecare culoare are doua setari,
 *     una `global_*` care o leaga de paleta globala si una cu valoarea
 *     proprie; cat timp `global_*` are valoare, ea castiga, deci ambele
 *     trebuie scrise.
 *   - Iconitele de contact din antet (era MIG-01), refacute prin filtrul de
 *     nucleu `wp_nav_menu_items` - vezi inc/contact-links.php.
 *   - Componenta de cautare scoasa din antet; nu exista in design-ul original.
 *   - FontAwesome inlocuit cu SVG inline - vezi inc/icons.php.
 *
 * TODO [MIG-03][HIGH]: Subsolul personalizat (contact, retele sociale, lista
 *   de servicii) se afiseaza acum in varianta implicita Sydney. Se
 *   reconstruieste prin widgeturi sau prin hook-urile `sydney_before_footer` /
 *   `sydney_footer`, NU printr-un footer.php copiat in aceasta tema.
 *   Motivul e acelasi pentru care MIG-01 a fost facut prin filtru: un sablon
 *   copiat este scris pentru markup-ul unei anumite versiuni si se rupe tacut
 *   la urmatoarea. Datele de contact exista deja structurate in
 *   inc/contact-links.php, in simonamarin_contact_links().
 *
 * TODO [MIG-04][MEDIUM]: Semnatura "Psiholog Simona Marin" de sub titlul
 *   articolelor statea in content-single.php. Se reface prin hook-ul
 *   `sydney_before_single_entry` sau `sydney_inside_top_post`.
 *
 * TODO [MIG-05][MEDIUM]: De reverificat tipografia pe toate paginile. Fonturile
 *   sunt setate (Poppins), dar pe unele titluri apare alt font decat pe
 *   productie. Cauza probabila nu este in aceasta tema, ci in CSS-ul aditional
 *   al site-ului (optiunea custom_css_post_id, ID 171), care tinteste clase din
 *   Sydney 1.x ce nu mai exista.
 *
 * TODO [MIG-06][LOW]: Pozitia iconitelor de contact este acum la capatul
 *   meniului, in dreapta; in design-ul vechi stateau langa titlu, in stanga.
 *   Este consecinta directa a faptului ca devin elemente de meniu, si a fost
 *   asumata. Daca pozitia din stanga este ceruta, singura varianta curata este
 *   pornirea modulului header builder, care cere insa reconstruirea intregului
 *   antet - inclusiv a culorilor abia readuse la design-ul original.
 */
