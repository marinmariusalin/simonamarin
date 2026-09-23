<?php
/**
 * Reguli pentru imagini: calitate, plafon de dimensiune si atributul `sizes`.
 *
 * Toate patru sunt filtre de nucleu, ca site-ul sa nu mai depinda de un plugin
 * de optimizare. Pana acum imaginile erau livrate in WebP pentru ca LiteSpeed
 * Cache rescria adresele la fiecare cerere; daca plugin-ul era oprit, in pagina
 * ajungeau fisierele originale, dintre care unele de peste un megaoctet. Acum
 * fisierele din biblioteca media sunt comprimate ca atare, iar regulile de mai
 * jos fac ca si cele incarcate de acum inainte sa fie la fel, fara niciun
 * plugin.
 *
 * @package sydney-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Calitatea la care WordPress scrie JPEG si WebP.
 *
 * Implicit WordPress foloseste 82 pentru JPEG, dar plugin-urile de optimizare
 * instalate de-a lungul timpului au schimbat valoarea, iar unele au lasat-o
 * schimbata si dupa dezinstalare. Fixarea ei aici scoate subiectul din zona de
 * configurare a plugin-urilor: 82 pentru JPEG e pragul de la care ochiul nu mai
 * distinge diferenta pe fotografii, 80 pentru WebP e echivalentul lui.
 *
 * @param int $quality Calitatea propusa.
 * @return int Calitatea folosita.
 */
function simonamarin_jpeg_quality( $quality ) {
	unset( $quality );

	return 82;
}
add_filter( 'jpeg_quality', 'simonamarin_jpeg_quality' );

/**
 * Acelasi prag, pe filtrul care primeste si tipul MIME.
 *
 * @param int    $quality   Calitatea propusa.
 * @param string $mime_type Tipul MIME al fisierului scris.
 * @return int Calitatea folosita.
 */
function simonamarin_editor_quality( $quality, $mime_type ) {
	unset( $quality );

	return 'image/webp' === $mime_type ? 80 : 82;
}
add_filter( 'wp_editor_set_quality', 'simonamarin_editor_quality', 10, 2 );

/**
 * Plafonul peste care WordPress redimensioneaza imaginea incarcata.
 *
 * Implicit e 2560 px. Cel mai mare loc in care o imagine chiar se vede pe acest
 * site este antetul articolului: 1092 px CSS pe un ecran de 1440. Pe un ecran
 * cu densitate dubla asta inseamna 2184 px, dar de la 1600 in sus diferenta nu
 * se mai vede pe o fotografie comprimata, in timp ce fisierul creste liniar cu
 * suprafata. 1600 acopera si antetul la densitate dubla, si cardurile din
 * arhiva la densitate tripla (326 px CSS x 3 = 978).
 *
 * @param int $threshold Plafonul propus.
 * @return int Plafonul folosit.
 */
function simonamarin_big_image_threshold( $threshold ) {
	unset( $threshold );

	return 1600;
}
add_filter( 'big_image_size_threshold', 'simonamarin_big_image_threshold' );

/**
 * Nu se mai genereaza varianta 2048x2048.
 *
 * Este o dimensiune pe care WordPress o produce pentru toate imaginile mari si
 * pe care acest site nu o cere niciodata: nici sablonul, nici srcset-ul nu
 * ajung la ea, pentru ca originalul e oricum plafonat la 1600. Ramanea pe disc
 * ca fisier mort. Varianta 1536x1536 se pastreaza - ea chiar e aleasa de
 * browser pentru antetul articolului pe ecrane cu densitate dubla.
 *
 * @param array $sizes Dimensiunile intermediare de generat.
 * @return array Dimensiunile ramase.
 */
function simonamarin_drop_unused_subsizes( $sizes ) {
	unset( $sizes['2048x2048'] );

	return $sizes;
}
add_filter( 'intermediate_image_sizes_advanced', 'simonamarin_drop_unused_subsizes' );

/**
 * Corecteaza atributul `sizes`, care spunea browserului o latime gresita.
 *
 * `sizes` e promisiunea facuta browserului despre cat de lata va fi imaginea in
 * pagina; pe baza ei alege din `srcset`. Valoarea implicita a WordPress este
 * `(max-width: Npx) 100vw, Npx`, unde N e latimea fisierului - adica "imaginea
 * ocupa toata latimea ecranului". Pe cardurile din arhiva, care au 331 px, asta
 * e fals de trei ori, iar browserul descarca varianta cea mai mare.
 *
 * Masurat inainte de aceasta corectie, pagina Articole trimitea 2868 KB pe
 * mobil, din care 1326 KB doar pentru primele trei carduri: fiind deasupra
 * pliului nu primesc `loading="lazy"`, deci nu primesc nici `sizes="auto"`
 * (care exista din WordPress 6.7, dar functioneaza doar pe imaginile lenese) si
 * raman cu `100vw`. Cardurile de mai jos, lenese, cereau corect variante de
 * 350 px si 3-19 KB fiecare.
 *
 * Latimile de mai jos sunt masurate in browser cu emulare de dispozitiv, nu
 * estimate:
 *   - card de arhiva: 331 px la 1440, 326 px la 820 si la 390;
 *   - antet de articol: 1092 px la 1440, 688 px la 820, 328 px la 390.
 *
 * Se agata de `wp_get_attachment_image_attributes`, nu de
 * `wp_calculate_image_sizes`, desi al doilea pare filtrul evident. Motivul e in
 * wp-includes/media.php: la linia 1163 nucleul cheama
 * `wp_calculate_image_sizes()` cu un `$size_array` de forma `array(1000, 1000)`,
 * deci acolo numele dimensiunii nu mai exista si nu am dupa ce sa deosebesc un
 * card de arhiva de un antet de articol. Numele ajunge nealterat abia la
 * `wp_get_attachment_image_attributes` (linia 1201), care ruleaza ultimul si
 * poate suprascrie ce s-a calculat inainte. Prima varianta a acestei functii
 * era pe filtrul evident si pur si simplu nu se aplica.
 *
 * Prefixul `auto,` se pastreaza cand exista: din WordPress 6.7 el lasa
 * browserul sa foloseasca latimea reala din pagina pentru imaginile lenese,
 * ceea ce e mai bun decat orice valoare scrisa de mana. Regulile de mai jos
 * conteaza mai ales pentru imaginile care NU sunt lenese - cele de deasupra
 * pliului, unde `auto` nu functioneaza.
 *
 * @param array   $attr       Atributele HTML ale imaginii.
 * @param WP_Post $attachment Atasamentul.
 * @param mixed   $size       Dimensiunea ceruta, ca nume sau ca pereche.
 * @return array Atributele, cu `sizes` corectat.
 */
function simonamarin_image_sizes_attr( $attr, $attachment, $size ) {
	unset( $attachment );

	if ( ! is_string( $size ) ) {
		return $attr;
	}

	$slots = array(
		// Cardurile din grila de articole au aceeasi latime la orice ecran.
		'post-thumbnail'     => '(min-width: 992px) 331px, 326px',
		/*
		 * Antetul articolului. Sub 1200 px imaginea nu ocupa chiar toata
		 * latimea ecranului, ci latimea minus marginile containerului:
		 * masurat, 688 px la un ecran de 820 si 328 px la unul de 390, adica
		 * 132 px si 62 px de margine. `100vw` ar supraevalua cu pana la 19%
		 * si ar face browserul sa ceara o varianta mai mare decat ii trebuie.
		 */
		'sydney-large-thumb' => '(min-width: 1200px) 1100px, (min-width: 768px) calc(100vw - 132px), calc(100vw - 62px)',
	);

	if ( ! isset( $slots[ $size ] ) ) {
		return $attr;
	}

	$auto = isset( $attr['sizes'] ) && 0 === strpos( ltrim( $attr['sizes'] ), 'auto,' );
	$attr['sizes'] = ( $auto ? 'auto, ' : '' ) . $slots[ $size ];

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'simonamarin_image_sizes_attr', 10, 3 );

/**
 * Preincarcarea portretului din hero-ul paginii de start.
 *
 * Portretul este `background-image` intr-un stil inline din continutul paginii,
 * deci browserul il descopera abia dupa ce a construit arborele de stiluri; ca
 * `<img>` l-ar fi gasit din primul pas. Este cel mai mare element vizibil la
 * incarcare (LCP), asa ca intarzierea se vede direct in viteza perceputa.
 *
 * Pana la 23.09.2026 preincarcarea o punea Optimization Detective + Image
 * Prioritizer, pluginuri beta scoase atunci pentru ca adunau date de la
 * vizitatori ca sa ajunga la aceeasi concluzie. Adresa se citeste din
 * continutul paginii, nu e scrisa aici: daca se schimba poza din editor,
 * preincarcarea o urmeaza singura, iar daca dispare, nu se mai emite nimic.
 */
function simonamarin_preload_hero_portrait() {
	if ( ! is_front_page() ) {
		return;
	}

	$post = get_queried_object();
	if ( ! $post instanceof WP_Post ) {
		return;
	}

	// Primul `background-image` din blocul `.profile-picture`; ghilimelele pot fi codificate ca &quot;.
	if ( ! preg_match( '/class="profile-picture"[^>]*background-image:\s*url\((?:&quot;|["\'])?([^"\')&]+)/', $post->post_content, $m ) ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s" fetchpriority="high">' . "\n",
		esc_url( $m[1] )
	);
}
add_action( 'wp_head', 'simonamarin_preload_hero_portrait', 2 );
