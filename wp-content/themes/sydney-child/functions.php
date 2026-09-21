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

/*
 * ============================================================================
 * MIGRAREA LA SYDNEY 2.71 - STARE SI DECIZII RAMASE
 * ============================================================================
 *
 * Tema parinte a fost actualizata de la o versiune din 2020 la 2.71. Nu a fost
 * un update obisnuit: din 150 de fisiere instalate doar 42 erau identice cu
 * originalul, iar arhitectura temei s-a schimbat (style.css a trecut de la 1726
 * la 18 linii, stilurile se genereaza acum din inc/styles.php).
 * Analiza completa: sydney-update-analiza.md din radacina proiectului.
 *
 * REZOLVAT PANA ACUM
 *   - Culorile antetului, readuse la design-ul original prin optiunile proprii
 *     ale temei, nu prin CSS peste: antet alb (#ffffff), text de meniu #20292F,
 *     titlu #443F3F. Valorile sunt masurate pe site-ul de productie, nu alese.
 *     Sydney 2.71 tine fiecare culoare in doua setari - una `global_*` care o
 *     leaga de paleta globala si una cu valoarea proprie. Cat timp `global_*`
 *     are o valoare, ea castiga, deci ambele trebuie scrise.
 *   - Sabloanele header.php si footer.php scrise pentru Sydney 1.x au fost
 *     SCOASE din tema copil. Erau construite pe alte clase CSS decat cele din
 *     2.71 si ar fi produs un amestec intre doua generatii de tema. Raman in
 *     istoric, in commit-ul 3d661cc, daca e nevoie de continutul lor.
 *   - Iconitele SVG (inc/icons.php) au supravietuit migrarii neatinse, fiind
 *     independente de versiunea temei.
 *
 * TODO [MIG-01][HIGH]: Banda de iconite de contact din antet (Facebook,
 *   WhatsApp, Instagram, telefon, email) nu mai este afisata. In Sydney 1.x
 *   statea scrisa direct in header.php, langa titlu.
 *
 *   NU am reintrodus-o printr-un nou sablon copil, desi ar fi fost rapid:
 *   ar insemna sa reintru exact in situatia din care tocmai am iesit, cu un
 *   sablon copiat care se rupe la urmatorul update al temei.
 *
 *   Sydney 2.71 are raspunsul propriu: modulul "header builder", cu o
 *   componenta `social` si una `contact-info` exact pentru asta. Modulul este
 *   momentan OPRIT (optiunea `sydney-modules` este false). Pornirea lui schimba
 *   complet modul in care se construieste antetul, deci este o decizie
 *   separata, de luat cu verificare vizuala dupa, nu un efect secundar al
 *   acestui pas.
 *
 * TODO [MIG-02][HIGH]: Date structurate - situatia e alta decat parea.
 *   Vechiul header.php continea un bloc JSON-LD scris de mana, de tip
 *   MedicalBusiness, cu telefon, interval de pret, program si specialitate.
 *   Prima concluzie a fost ca disparitia lui este o pierdere SEO.
 *
 *   Verificarea paginii dupa migrare arata altceva: Rank Math emite deja date
 *   structurate - Organization + Person, WebSite, WebPage, cu nume, email,
 *   profiluri sociale, logo si adresa. Deci site-ul NU a ramas fara schema.
 *
 *   Ce lipseste efectiv fata de blocul vechi: `telephone`, `priceRange`,
 *   programul de lucru, tipul MedicalBusiness si specialitatea medicala.
 *
 *   Fix-ul corect NU este readaugarea blocului hardcodat. Ar rezulta doua
 *   entitati concurente pentru aceeasi afacere, iar dublurile de date
 *   structurate sunt mai daunatoare decat lipsa lor. Completarea se face in
 *   Rank Math, la Titles & Meta > Local SEO: tip de afacere, telefon, interval
 *   de pret, program. Asa ramane o singura sursa, iar datele se mentin din
 *   panou, nu din cod.
 *
 * TODO [MIG-03][MEDIUM]: Subsolul personalizat (contact, retele sociale, lista
 *   de servicii) este afisat acum in varianta implicita Sydney. Aceeasi decizie
 *   ca la MIG-01: se reconstruieste prin footer builder sau prin widgeturi, nu
 *   printr-un sablon copiat.
 *
 * TODO [MIG-04][MEDIUM]: Semnatura "Psiholog Simona Marin" de sub titlul
 *   articolelor, care statea in content-single.php, nu mai apare.
 *   Se poate reface curat prin hook-ul `sydney_before_single_entry` sau
 *   `sydney_inside_top_post`, fara sablon copiat.
 *
 * TODO [MIG-05][MEDIUM]: De reverificat tipografia pe toate paginile. Setarile
 *   de font existau deja in format nou (`sydney_body_font`,
 *   `sydney_headings_font`, ambele Poppins), dar in capturile de dupa migrare
 *   unele titluri apar cu alt font decat pe productie - foarte probabil din
 *   CSS-ul aditional al site-ului (optiunea custom_css_post_id), care tintea
 *   clase din Sydney 1.x.
 */
