<?php
/**
 * Plugin Name: Simona Marin - securitate si headere
 * Description: Headere de securitate, corectarea cache-ului pe HTML si reducerea suprafetei expuse. Independent de tema.
 * Version: 1.0.0
 * Author: Cabinet Individual de Psihologie Simona Marin
 *
 * ---------------------------------------------------------------------------
 * DE CE ESTE UN MU-PLUGIN SI NU COD DE TEMA
 * ---------------------------------------------------------------------------
 * Nimic din ce urmeaza nu tine de aspectul site-ului. Sunt reguli care trebuie
 * sa fie valabile indiferent ce tema este activa si care nu au voie sa dispara
 * cand tema se schimba sau se actualizeaza.
 *
 * Lectia care a dus aici: aceleasi reguli fusesera scrise mai intai in tema
 * `simonamarin`, care - s-a descoperit ulterior - NU este tema activa a
 * site-ului. Aveau, deci, efect zero. Un mu-plugin se incarca automat, inaintea
 * pluginurilor obisnuite, nu poate fi dezactivat din panou si nu depinde de
 * nicio tema.
 *
 * Al doilea motiv, la fel de important: urmeaza migrarea temei Sydney la
 * versiunea 2.71, care inlocuieste complet fisierele temei parinte. Orice
 * corectie scrisa acolo ar fi fost stearsa de acea migrare. Aici nu.
 *
 * ---------------------------------------------------------------------------
 * DOMENIU: cabinet de psihoterapie
 * ---------------------------------------------------------------------------
 * Vizitatorul ajunge pe site cautand ajutor pentru o problema personala, iar
 * faptul ca a vizitat site-ul este in sine o informatie sensibila. De aici doua
 * alegeri deliberate mai jos: Referrer-Policy strict, ca adresa paginii citite
 * sa nu se scurga catre alte domenii, si Permissions-Policy care refuza explicit
 * camera, microfonul si geolocatia, chiar daca nimic din site nu le cere.
 *
 * @package simonamarin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CHILD-01 [CRITICAL] - REZOLVAT. Cache-Control corect pentru documentul HTML.
 *
 * PROBLEMA GASITA IN TEMA
 * `sydney/functions.php` contine functia set_efficient_browser_caching(), legata
 * la hook-ul `send_headers`, care trimite pe FIECARE pagina de frontend:
 *
 *     Cache-Control: public, max-age=2592000
 *
 * adica 30 de zile, pe documentul HTML. Numele functiei sugereaza fisiere
 * statice, dar hook-ul se aplica paginii, nu fisierelor.
 *
 * DE CE CONTEAZA, CONCRET
 * Un vizitator care a deschis o pagina primeste exact aceeasi versiune timp de o
 * luna, fara sa mai intrebe serverul. Daca intre timp se corecteaza un pret, un
 * orar sau un numar de telefon, el continua sa vada varianta veche. `public`
 * permite in plus si cache-urilor intermediare sa pastreze pagina. Pe un site de
 * cabinet, unde informatiile de contact si tarifele trebuie sa fie corecte,
 * asta este o problema de continut, nu una de performanta.
 *
 * SOLUTIA
 * Se scoate functia temei cu totul (ceea ce rezolva si CHILD-02, mai jos) si se
 * trimite in loc `no-cache`. Numele induce in eroare: NU dezactiveaza cache-ul.
 * Inseamna "pastreaza pagina, dar intreaba serverul daca s-a schimbat inainte sa
 * o refolosesti". Serverul raspunde cu 304 daca nu s-a schimbat nimic, deci nu
 * se retransmite continut - se pastreaza castigul de viteza, fara continut
 * invechit.
 *
 * Cache-ul lung ramane corect si util pentru CSS, JS, imagini si fonturi, dar
 * acela se configureaza din server sau din LiteSpeed Cache, nu de aici, si nu
 * este afectat de aceasta schimbare.
 *
 * DACA SE FOLOSESTE UN CACHE DE PAGINA (LiteSpeed este instalat): pluginul isi
 * gestioneaza propriile headere pentru paginile pe care le serveste din cache.
 * Regula de mai jos acopera raspunsurile generate de PHP.
 */
function simonamarin_fix_html_cache_headers() {
	// Nu atinge adminul, feed-urile sau cererile REST.
	if ( is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	// Utilizatorii logati nu trebuie sa primeasca nimic din cache.
	if ( is_user_logged_in() ) {
		header( 'Cache-Control: no-store, no-cache, must-revalidate, max-age=0' );
		return;
	}

	header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
}

/**
 * CHILD-02 [HIGH] - REZOLVAT. Scoate marker-ul de depanare din fiecare pagina.
 *
 * Aceeasi functie a temei facea, pe langa headerul de cache:
 *
 *     echo "<!-- FUNCTIONS.PHP IS WORKING 1 -->";
 *
 * pe hook-ul `send_headers`. Rezultatul se vede ca primii octeti ai fiecarei
 * pagini de pe site, inaintea lui <!DOCTYPE html>. Este un marker lasat din
 * depanare, ajuns in productie.
 *
 * In afara de faptul ca nu are ce cauta acolo, a scrie in output pe
 * `send_headers` este si o practica riscanta: din acel moment raspunsul a
 * inceput, iar orice cod care ar incerca ulterior sa trimita un header esueaza.
 *
 * Ambele se rezolva printr-un singur remove_action, pentru ca provin din aceeasi
 * functie. Se ruleaza pe `init`: mu-pluginurile se incarca INAINTEA temei, deci
 * la momentul incarcarii acestui fisier hook-ul temei nu exista inca si nu ar
 * avea ce sa fie scos.
 */
function simonamarin_replace_theme_cache_headers() {
	remove_action( 'send_headers', 'set_efficient_browser_caching' );
	add_action( 'send_headers', 'simonamarin_fix_html_cache_headers' );
}
add_action( 'init', 'simonamarin_replace_theme_cache_headers' );

/**
 * SEC-01 - Headere de securitate pe frontend.
 *
 * Filtrul `wp_headers` ruleaza in WP::send_headers(), deci acopera cererile de
 * frontend si NU atinge wp-admin. Este intentionat: un header gresit in admin
 * poate rupe editorul sau mediateca, iar WordPress isi trimite oricum propriile
 * headere acolo.
 *
 * @param array $headers Headerele pregatite de WordPress.
 * @return array Headerele, completate.
 */
function simonamarin_security_headers( $headers ) {

	// Interzice browserului sa "ghiceasca" alt tip de continut decat cel
	// declarat. Fara el, un fisier incarcat in mediateca si servit cu tip gresit
	// poate ajunge sa fie interpretat ca HTML sau ca script.
	$headers['X-Content-Type-Options'] = 'nosniff';

	// Catre alte domenii trimite doar schema si domeniul, niciodata calea, si
	// nimic la coborarea de la HTTPS la HTTP. Concret: daca vizitatorul da clic
	// pe un link extern dintr-un articol, site-ul de destinatie NU afla ce
	// articol citea.
	$headers['Referrer-Policy'] = 'strict-origin-when-cross-origin';

	// Impiedica incarcarea site-ului intr-un <iframe> de pe alt domeniu.
	// SAMEORIGIN si nu DENY, pentru ca previzualizarea din Customizer si din
	// editor incarca frontend-ul intr-un iframe de pe acelasi domeniu.
	$headers['X-Frame-Options'] = 'SAMEORIGIN';

	// Refuza API-uri de browser pe care site-ul nu le foloseste. Daca se adauga
	// vreodata o harta incorporata care cere locatia, `geolocation` se scoate.
	$headers['Permissions-Policy'] = 'camera=(), microphone=(), geolocation=()';

	/*
	 * HSTS - scris, dar INTENTIONAT dezactivat implicit.
	 *
	 * Odata primit, browserul refuza sa mai deschida site-ul prin HTTP pana la
	 * expirarea lui max-age, indiferent ce se schimba pe server intre timp. Daca
	 * certificatul expira, vizitatorii primesc o eroare pe care nu o pot ocoli.
	 * Se porneste abia dupa ce HTTPS-ul de productie este stabil si reinnoirea
	 * certificatului este automata, adaugand in wp-config.php:
	 *     define( 'SIMONAMARIN_ENABLE_HSTS', true );
	 * Fara `preload`, care este practic ireversibil.
	 */
	if ( is_ssl() && defined( 'SIMONAMARIN_ENABLE_HSTS' ) && SIMONAMARIN_ENABLE_HSTS ) {
		$headers['Strict-Transport-Security'] = 'max-age=15552000'; // 180 de zile.
	}

	// Endpointul XML-RPC isi anunta singur adresa prin acest header.
	unset( $headers['X-Pingback'] );

	/*
	 * TODO [SEC-11][MEDIUM]: Content-Security-Policy nu este emis aici, si nu
	 * este o omisiune. Pe acest site ruleaza pluginuri care injecteaza scripturi
	 * inline (cache, formulare, SEO, Google Tag Manager), iar o politica
	 * aplicata direct ar rupe pagini in productie fara avertisment.
	 * Ordinea corecta, intr-o sesiune dedicata: intai
	 * `Content-Security-Policy-Report-Only` cu raportare, cateva zile pe trafic
	 * real, si abia apoi politica aplicata. Pasul Report-Only nu blocheaza nimic.
	 */

	return $headers;
}
add_filter( 'wp_headers', 'simonamarin_security_headers' );

/**
 * SEC-02 - Nu mai anunta versiunea de WordPress.
 *
 * Versiunea exacta apare implicit in `<meta name="generator">` si in feed-uri.
 * Nu este o vulnerabilitate in sine, dar scuteste scanerele automate de pasul
 * de identificare: ele cauta versiuni cu probleme cunoscute si trec mai departe
 * daca nu gasesc una. Nu inlocuieste actualizarea la zi, este un strat in plus.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );

/*
 * TODO [SEC-14][LOW]: Raman in <head> sapte etichete `<meta name="generator">`
 * puse de pluginuri, nu de WordPress: dominant-color-images, webp-uploads,
 * performance-lab, speculation-rules, embed-optimizer, image-prioritizer si
 * Site Kit by Google. Fiecare isi anunta numele SI versiunea exacta, ceea ce
 * este, ca informatie pentru un scaner, mai util decat versiunea de WordPress.
 *
 * Nu le-am scos aici pentru ca fiecare plugin foloseste propriul hook si
 * propriul nume de functie; un remove_action pentru fiecare s-ar rupe tacut la
 * primul lor update, iar un filtru care curata tot <head> printr-un buffer de
 * iesire este disproportionat fata de castig.
 * Varianta serioasa, daca se doreste: o singura functie care ruleaza pe
 * `wp_head` cu prioritate mare si care sterge etichetele dintr-un buffer,
 * insotita de un test care verifica periodic ca inca functioneaza.
 */

/**
 * SEC-03 - Pingback XML-RPC.
 *
 * Pingback-ul permite oricui sa ceara serverului sa deschida o conexiune catre
 * un URL ales de el, ceea ce transforma site-ul in unealta pentru alte atacuri
 * si ii consuma resursele.
 *
 * Se scot DOAR metodele de pingback, nu tot xmlrpc.php: restul este folosit de
 * aplicatia mobila WordPress. Dezactivarea completa este o decizie a
 * proprietarului site-ului, nu o presupunere facuta de aici.
 *
 * @param array $methods Metodele XML-RPC inregistrate.
 * @return array Metodele, fara pingback.
 */
function simonamarin_remove_pingback_methods( $methods ) {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'simonamarin_remove_pingback_methods' );

/*
 * Relicve din <head> fara niciun consumator in 2026: RSD (pentru clienti de
 * blogging desktop), wlwmanifest (Windows Live Writer, produs retras din 2017)
 * si shortlink-ul, care expune ID-ul numeric al fiecarui articol.
 * Permalink-urile normale raman, evident, neatinse.
 */
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );

/**
 * SEC-04 - Blocheaza enumerarea autorilor.
 *
 * `/?author=1` este redirectat de WordPress catre arhiva autorului, iar slugul
 * din URL-ul rezultat este, in instalarile implicite, chiar numele de
 * utilizator. Asta ofera gratuit jumatate dintr-o pereche de credentiale.
 *
 * Se blocheaza doar forma `?author=N`. Arhivele `/author/slug/` raman
 * functionale, ca sa nu se schimbe structura de URL-uri a site-ului.
 * Utilizatorii logati sunt exceptati, ca sa nu se strice navigarea din admin.
 */
function simonamarin_block_author_enumeration() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}

	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- citire read-only a unui parametru public de URL.
	if ( isset( $_GET['author'] ) ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'simonamarin_block_author_enumeration' );

/**
 * SEC-12 - Mesaj generic la esecul autentificarii.
 *
 * Implicit, WordPress raspunde diferit dupa ce anume e gresit: "utilizatorul
 * nu exista" cand numele e gresit, "parola gresita" cand numele e corect.
 * Diferenta ii spune unui atacator, gratuit, daca un nume de utilizator
 * ghicit este valid, inainte sa incerce parole. Un singur mesaj, identic in
 * ambele cazuri, scoate acest semnal fara sa afecteze un utilizator legitim,
 * care oricum trebuie sa reincerce cu datele corecte.
 *
 * @return string Mesajul generic, in locul celui original al WordPress.
 */
function simonamarin_generic_login_error() {
	return __( 'Autentificare esuata. Verifica numele de utilizator si parola.', 'sydney-child' );
}
add_filter( 'login_errors', 'simonamarin_generic_login_error' );

/*
 * TODO [SEC-13][HIGH]: Formularul de contact transmite date de sanatate, care
 * intra sub articolul 9 din GDPR. De verificat, in afara codului: transportul
 * (exista post-smtp instalat - de confirmat ca foloseste TLS), cat timp raman
 * mesajele in baza de date, si daca exista un temei legal si o informare
 * afisata langa formular. Niciunul nu se rezolva din cod, dar toate cantaresc
 * mai mult decat orice header de mai sus.
 */
