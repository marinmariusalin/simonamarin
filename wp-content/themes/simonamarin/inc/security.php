<?php
/**
 * Hardening la nivel de tema: headere de securitate si reducerea suprafetei expuse.
 *
 * Rezolva TODO-urile SEC-01 (fara security headers), SEC-02 (versiunea WordPress
 * expusa), SEC-03 (pingback/XML-RPC activ) si SEC-04 (enumerarea autorilor) din
 * functions.php.
 *
 * ---------------------------------------------------------------------------
 * DE CE ACEST FISIER SI NU .htaccess
 * ---------------------------------------------------------------------------
 * Headerele pot fi emise si din configuratia serverului. Tinandu-le in tema,
 * ele calatoresc odata cu ea: raman valabile dupa o migrare cu Duplicator, dupa
 * o schimbare de hosting sau dupa o resetare a .htaccess de catre un plugin de
 * cache. Dezavantajul este ca nu acopera fisierele servite fara sa treaca prin
 * PHP (imagini, CSS, JS). Pentru headerele de mai jos asta nu conteaza: toate
 * se aplica documentului HTML.
 *
 * ---------------------------------------------------------------------------
 * DOMENIU: cabinet de psihoterapie
 * ---------------------------------------------------------------------------
 * Vizitatorul ajunge pe site cautand ajutor pentru o problema personala.
 * Faptul ca a vizitat acest site este in sine o informatie sensibila, iar
 * formularul de contact transmite date de sanatate (GDPR art. 9). De aici
 * doua alegeri deliberate mai jos:
 *   - Referrer-Policy strict, ca URL-ul paginii citite sa nu se scurga catre
 *     alte domenii prin antetul Referer;
 *   - Permissions-Policy care refuza explicit camera, microfonul si
 *     geolocatia, chiar daca tema nu le cere - un script tert injectat nu
 *     trebuie sa le poata cere nici el.
 *
 * @package simonamarin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SEC-01 [CRITICAL] - REZOLVAT 2026-09-21. Headere de securitate pe frontend.
 *
 * Filtrul `wp_headers` ruleaza in WP::send_headers(), deci acopera cererile de
 * frontend si NU atinge wp-admin. Este intentionat: un header gresit in admin
 * (mai ales CSP) poate rupe editorul de blocuri sau mediateca, iar WordPress
 * isi trimite oricum propriile headere acolo.
 *
 * @param array $headers Headerele pregatite de WordPress.
 * @return array Headerele, completate.
 */
function simonamarin_security_headers( $headers ) {

	/*
	 * Interzice browserului sa "ghiceasca" alt tip de continut decat cel
	 * declarat in Content-Type. Fara el, un fisier incarcat in mediateca si
	 * servit cu tip gresit poate fi interpretat ca HTML sau ca script.
	 */
	$headers['X-Content-Type-Options'] = 'nosniff';

	/*
	 * Trimite URL-ul complet ca Referer doar catre acelasi origin; catre alte
	 * domenii trimite doar schema si domeniul, si nimic daca se coboara de la
	 * HTTPS la HTTP. Concret: daca vizitatorul da clic pe un link extern dintr-un
	 * articol, site-ul de destinatie NU afla ce articol citea.
	 */
	$headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';

	/*
	 * Impiedica incarcarea site-ului intr-un <iframe> de pe alt domeniu
	 * (clickjacking). SAMEORIGIN, nu DENY, pentru ca previzualizarea din
	 * Customizer si cea din editor incarca frontend-ul intr-un iframe de pe
	 * acelasi domeniu si DENY le-ar rupe.
	 */
	$headers['X-Frame-Options'] = 'SAMEORIGIN';

	/*
	 * Refuza API-urile de browser pe care tema nu le foloseste. Lista este
	 * scurta si cu directive larg suportate: un nume de directiva necunoscut
	 * este ignorat de browser, dar o lista lunga si speculativa e greu de
	 * intretinut. Daca in viitor se adauga o harta incorporata (Google Maps,
	 * OpenStreetMap) care cere locatia, `geolocation` trebuie scos de aici.
	 */
	$headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';

	/*
	 * HSTS - optional, si INTENTIONAT dezactivat implicit.
	 *
	 * Odata ce un browser a primit acest header, refuza sa mai deschida site-ul
	 * prin HTTP pana la expirarea lui max-age, INDIFERENT ce se schimba pe
	 * server intre timp. Daca certificatul expira sau hostingul e mutat gresit,
	 * vizitatorii primesc o eroare pe care nu o pot ocoli. De aceea nu se
	 * porneste "din oficiu", ci abia dupa ce HTTPS-ul in productie este stabil
	 * si reinnoirea certificatului este automata.
	 *
	 * Activare: adauga in wp-config.php, pe mediul de productie,
	 *   define( 'SIMONAMARIN_ENABLE_HSTS', true );
	 * Nu se adauga `preload` fara o decizie separata - inscrierea in lista de
	 * preload a browserelor este practic ireversibila.
	 */
	if ( is_ssl() && defined( 'SIMONAMARIN_ENABLE_HSTS' ) && SIMONAMARIN_ENABLE_HSTS ) {
		$headers['Strict-Transport-Security'] = 'max-age=15552000'; // 180 de zile.
	}

	/*
	 * TODO [SEC-11][MEDIUM]: Content-Security-Policy nu este emis aici.
	 *
	 * Nu este o omisiune: pe acest site ruleaza pluginuri care injecteaza
	 * scripturi si stiluri inline (cache, formulare, SEO), iar o politica
	 * enforced scrisa "din birou" ar rupe pagini in productie fara avertisment.
	 * Ordinea corecta de lucru, intr-o sesiune dedicata:
	 *   1. emite intai `Content-Security-Policy-Report-Only` cu o politica
	 *      stricta si un `report-uri` catre un colector;
	 *   2. las-o sa ruleze cateva zile pe trafic real si aduna violarile;
	 *   3. abia apoi transforma politica in `Content-Security-Policy`.
	 * Pasul 1 este inofensiv - Report-Only nu blocheaza nimic.
	 */

	return $headers;
}
add_filter( 'wp_headers', 'simonamarin_security_headers' );

/**
 * SEC-02 [HIGH] - REZOLVAT 2026-09-21. Nu mai anunta versiunea de WordPress.
 *
 * Versiunea exacta este publicata implicit in doua locuri: `<meta name="generator">`
 * din <head> si elementul <generator> din feed-uri. Nu este o vulnerabilitate in
 * sine, dar scuteste un atacator automat de pasul de fingerprinting: scanerele
 * cauta versiuni cu CVE-uri cunoscute si trec mai departe daca nu gasesc una.
 *
 * Obscuritatea NU inlocuieste actualizarea la zi a WordPress-ului si a
 * pluginurilor - ramane doar un strat in plus.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/**
 * SEC-03 [HIGH] - REZOLVAT 2026-09-21. Pingback si XML-RPC.
 *
 * Pingback-ul XML-RPC permite oricui sa ceara serverului sa deschida o conexiune
 * catre un URL ales de el. Asta transforma site-ul in unealta pentru alte
 * atacuri si ii consuma resursele. Comentariile si ping-urile sunt deja inchise
 * la nivel de continut (vezi simonamarin_disable_comments din functions.php),
 * dar endpointul XML-RPC ramanea accesibil independent de acea setare.
 *
 * Mai jos se scot DOAR metodele de pingback, nu tot xmlrpc.php. Diferenta
 * conteaza: restul XML-RPC este folosit de aplicatia mobila WordPress si de
 * Jetpack. Jetpack nu este instalat aici (vezi CLEAN-05), deci daca se confirma
 * ca nici aplicatia mobila nu este folosita, se poate adauga si
 * `add_filter( 'xmlrpc_enabled', '__return_false' );` - dar aceea este o decizie
 * care trebuie luata de proprietarul site-ului, nu presupusa din tema.
 *
 * @param array $methods Metodele XML-RPC inregistrate.
 * @return array Metodele, fara pingback.
 */
function simonamarin_remove_pingback_methods( $methods ) {
	unset( $methods['pingback.ping'] );
	unset( $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'simonamarin_remove_pingback_methods' );

/**
 * Scoate headerul X-Pingback, care anunta adresa endpointului XML-RPC.
 *
 * @param array $headers Headerele pregatite de WordPress.
 * @return array Headerele, fara X-Pingback.
 */
function simonamarin_remove_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'simonamarin_remove_pingback_header' );

/*
 * Scoate din <head> doua relicve care nu au niciun consumator in 2026 si care
 * indica unelte de editare la distanta:
 *   - RSD (Really Simple Discovery), folosit de clientii de blogging desktop;
 *   - wlwmanifest, pentru Windows Live Writer, produs retras din 2017.
 * Ambele sunt pur informative pentru un scaner; eliminarea lor nu afecteaza
 * nimic din site.
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

/*
 * Scoate si shortlink-ul (`<link rel="shortlink" href="...?p=123">`). Expune
 * ID-ul numeric al fiecarui articol si nu este folosit de nimic pe acest site;
 * permalink-ul normal ramane, evident, neatins. Face parte din PERF-03.
 */
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * SEC-04 [MEDIUM] - REZOLVAT 2026-09-21. Blocheaza enumerarea autorilor.
 *
 * Cererea `/?author=1` este redirectata de WordPress catre arhiva autorului, iar
 * slugul din URL-ul rezultat este, in instalarile implicite, chiar user_login-ul.
 * Asta ofera gratuit jumatate dintr-o pereche de credentiale, iar cealalta
 * jumatate se ghiceste prin brute force.
 *
 * Se blocheaza doar forma `?author=N` de pe frontend. Arhivele de autor
 * accesate prin URL-ul lor normal (`/author/slug/`) raman functionale, ca sa nu
 * se schimbe structura de URL-uri a site-ului - vezi SEO-30 pentru decizia
 * separata daca aceste arhive ar trebui sau nu indexate.
 *
 * Utilizatorii logati sunt exceptati, ca sa nu se strice navigarea din admin.
 */
function simonamarin_block_author_enumeration() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- citire read-only a unui parametru public de URL, fara efect de scriere.
	if ( isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'simonamarin_block_author_enumeration' );

/*
 * TODO [SEC-12][MEDIUM]: Mesajele de eroare la autentificare spun daca numele de
 * utilizator exista ("The password you entered for the username X is incorrect").
 * Este a doua cale de enumerare, independenta de SEC-04. Se rezolva cu filtrul
 * `login_errors`, dar acela apartine mai degraba unui mu-plugin decat temei:
 * daca tema este schimbata, protectia nu trebuie sa dispara odata cu ea.
 *
 * TODO [SEC-13][HIGH]: Formularul de contact trimite date de sanatate (GDPR
 * art. 9). De verificat, in afara temei: transportul (SMTP cu TLS - exista
 * post-smtp pe site), retentia mesajelor in baza de date, si daca exista un
 * temei legal si o informare afisata langa formular. Niciunul dintre aceste
 * puncte nu se rezolva din tema, dar sunt mai importante decat orice header.
 */
