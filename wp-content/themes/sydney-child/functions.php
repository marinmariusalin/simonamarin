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

/**
 * Sistemul de design (redesign 2026).
 *
 * Incarca, in ordine explicita prin dependente: fonturile gazduite local,
 * CSS-ul istoric mutat din Customizer si foaia noua de design. Motivatia
 * ordinii - si de ce nu se mai obtine din prioritati de hook - e in antetul
 * fisierului.
 */
require_once get_stylesheet_directory() . '/inc/design-system.php';

/**
 * Reguli pentru imagini: calitate de compresie, plafon de dimensiune la
 * incarcare si atributul `sizes`, prin care browserul alege varianta potrivita.
 * Sunt filtre de nucleu, ca livrarea imaginilor sa nu mai depinda de un plugin
 * de optimizare.
 */
require_once get_stylesheet_directory() . '/inc/images.php';

/**
 * Subsolul site-ului (MIG-03): legaturi sitewide spre servicii si paginile
 * cabinetului, iconitele de contact si copyright cu an dinamic, fara creditul
 * temei. Doar texte care exista deja pe site; motivatia in antetul fisierului.
 */
require_once get_stylesheet_directory() . '/inc/footer.php';

/**
 * Completari la graful JSON-LD al lui Rank Math: zona deservita, autorul real
 * al articolelor, Service pe paginile de servicii. Prin filtru, nu bloc propriu,
 * ca sa nu apara o a doua entitate pentru acelasi cabinet.
 */
require_once get_stylesheet_directory() . '/inc/structured-data.php';

/**
 * Fara autor, data si categorie sub titlul articolelor, prin filtrul Sydney.
 * Motivatia in antetul fisierului.
 */
require_once get_stylesheet_directory() . '/inc/post-meta.php';

/*
 * ============================================================================
 * MIGRAREA LA SYDNEY 2.71 - CE A RAMAS
 * ============================================================================
 *
 * Toate sarcinile active si rezolvate sunt centralizate exclusiv in TODO.md din radacina proiectului.
 */

