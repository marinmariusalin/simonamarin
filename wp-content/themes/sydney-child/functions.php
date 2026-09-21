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
 * CE URMEAZA SA FIE MUTAT AICI DIN TEMA PARINTE
 * ============================================================================
 *
 * Acest commit face DOAR mutarea structurala: site-ul ruleaza pe tema copil si
 * arata identic. Personalizarile de mai jos sunt inca in ../sydney/ si vor fi
 * sterse de primul update al temei. Fiecare are nevoie de verificare proprie in
 * browser, de aceea nu au fost mutate toate deodata.
 *
 * TODO [CHILD-01][CRITICAL]: sydney/functions.php, functia
 *   set_efficient_browser_caching() trimite pe FIECARE pagina de frontend
 *   `Cache-Control: public, max-age=2592000`, adica 30 de zile.
 *   Nu este un header pentru fisiere statice, cum sugereaza numele - se aplica
 *   documentului HTML. Consecinte reale: un vizitator care a citit o pagina
 *   primeste aceeasi versiune 30 de zile, orice corectura de text ramane
 *   invizibila pentru el, iar `public` permite si cache-urilor intermediare sa
 *   pastreze pagina. Pe un site unde se schimba preturi, orare sau informatii
 *   de contact, asta e o problema de continut, nu de performanta.
 *   Corect pentru HTML: `no-cache` (revalidare la fiecare cerere) sau un
 *   max-age de ordinul minutelor. Cache-ul lung se aplica CSS/JS/imagini, si
 *   acolo se seteaza din server sau din LiteSpeed, nu din tema.
 *
 * TODO [CHILD-02][HIGH]: aceeasi functie face `echo "<!-- FUNCTIONS.PHP IS
 *   WORKING 1 -->"` pe hook-ul `send_headers`. Este un marker de depanare
 *   ajuns in productie - se vede ca primii octeti ai fiecarei pagini, inaintea
 *   lui <!DOCTYPE html>. In afara de faptul ca nu are ce cauta acolo, a scrie
 *   in output pe `send_headers` este si o practica riscanta: orice cod care ar
 *   incerca sa trimita un header dupa acel moment esueaza.
 *
 * TODO [CHILD-03][HIGH]: sydney/header.php, liniile 55-62, incarca
 *   fontawesome.min.css, solid.min.css si brands.min.css de la
 *   `https://simonamarin.ro/wp-content//themes/sydney/css/...`.
 *   Trei probleme intr-o singura linie: domeniul de productie este scris de
 *   mana (pe Local fisierele nu se incarca deloc, de unde patratele goale in
 *   locul iconitelor din header), calea contine un dublu slash, iar fisierele
 *   ocolesc complet sistemul de enqueue, deci nu pot fi nici depuse in cache,
 *   nici combinate, nici dezactivate de un plugin.
 *   Corect: `wp_enqueue_style` cu `get_theme_file_uri()`, din acest fisier.
 *   ATENTIE la diferenta: blocul JSON-LD din acelasi header.php foloseste tot
 *   `https://simonamarin.ro` si ACOLO este corect - datele structurate trebuie
 *   sa indice identitatea canonica a cabinetului, nu adresa de pe care se
 *   intampla sa fie servita pagina.
 *
 * TODO [CHILD-04][MEDIUM]: restul blocului de optimizari adaugat la finalul lui
 *   sydney/functions.php (defer pe scripturi, CSS non-blocking, font-display,
 *   scoaterea lui jquery-migrate) trebuie mutat aici ca sa supravietuiasca unui
 *   update. De verificat in paralel ce face deja LiteSpeed Cache, ca sa nu
 *   existe doua straturi care se bat pe aceeasi optimizare.
 *
 * TODO [CHILD-05][MEDIUM]: sydney/style.css nu mai are antet de tema - blocul
 *   cu "Theme Name: Sydney" a fost suprascris. WordPress tolereaza asta (tema
 *   este gasita dupa numele directorului), dar o afiseaza fara nume in
 *   Appearance > Themes si nu ii cunoaste versiunea.
 *   NU am rescris antetul: nu exista nicaieri in fisierele temei versiunea
 *   reala a lui Sydney (nici readme.txt, nici changelog.txt nu o contin), iar
 *   un numar de versiune inventat ar face WordPress sa ofere sau sa ascunda
 *   gresit update-urile. Solutia curata, acum ca personalizarile se muta aici:
 *   se reinstaleaza Sydney curat de pe wordpress.org, ceea ce readuce si
 *   antetul, si versiunea corecta.
 */
