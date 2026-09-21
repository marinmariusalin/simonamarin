<?php
/**
 * simonamarin functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package simonamarin
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * PERF-01 [CRITICAL] - REZOLVAT 2026-09-21. Versiune de asset bazata pe filemtime.
 *
 * Problema: _S_VERSION era hardcodat '1.0.0' si nu crestea niciodata. Fiecare
 * fisier era servit cu `?ver=1.0.0`, deci browserul vizitatorului recurent si
 * cache-ul LiteSpeed continuau sa serveasca varianta veche dupa orice editare
 * de CSS sau JS. Practic, cache busting inexistent.
 *
 * Solutia: versiunea devine data ultimei modificari a fisierului. Se schimba
 * automat la fiecare editare si NU se schimba cand nu s-a modificat nimic -
 * exact comportamentul dorit, spre deosebire de time(), care ar dezactiva
 * complet cache-ul.
 *
 * Pe fallback se intoarce _S_VERSION: daca fisierul lipseste (cale gresita),
 * enqueue-ul tot trebuie sa produca un URL valid, nu `?ver=`.
 *
 * @param string $relative_path Cale relativa la radacina temei, ex. 'style.css'.
 * @return string Versiunea de folosit in wp_enqueue_style/script.
 */
function simonamarin_asset_version( $relative_path ) {
	$absolute_path = get_theme_file_path( $relative_path );

	if ( file_exists( $absolute_path ) ) {
		return (string) filemtime( $absolute_path );
	}

	return _S_VERSION;
}

// AUDIT COMMENTS - SEE audit-report.html for full details
// REZOLVAT 2026-09-21: PHP requirement ridicat la >=7.4 in composer.json si
//   "Requires PHP: 7.4" in antetul style.css (cele doua trebuie sa ramana egale).
// REZOLVAT 2026-09-21: Security headers - vezi inc/security.php.
//   CSP ramane deschis ca SEC-11, se implementeaza intai in mod Report-Only.
// TODO [CRITICAL]: Implement Performance Optimizations (lazy loading, caching)
// TODO: Add Block Editor support - add_theme_support( 'wp-block-styles' )
// TODO: Make content-width responsive instead of hardcoded 640px

/* =============================================================================
 * AUDIT 2 - SEO / PERFORMANCE / SECURITY (sesiune 2026-09-21)
 * Nimic din textul paginilor nu a fost modificat. Doar marcaje TODO.
 * =============================================================================
 *
 * --- SEO [CRITICAL] ---
 *
 * TODO [SEO-01][CRITICAL]: Zero date structurate (JSON-LD) in tema.
 *   Impact: Google nu poate genera rich results (Article, Person, BreadcrumbList,
 *           LocalBusiness). Pierdere directa de CTR in SERP.
 *   Context: Rank Math (seo-by-rank-math) este instalat si emite deja o parte
 *           din schema. Fix: NU dubla schema - mai intai verifica ce emite Rank
 *           Math (Rank Math > Titles & Meta > Schema), apoi completeaza in tema
 *           DOAR ce lipseste, via filtrul `rank_math/json_ld`.
 *   Fix minim: BreadcrumbList + Person (autor) pentru paginile de blog.
 *
 * TODO [SEO-02][CRITICAL]: Lipseste complet breadcrumb navigation in template-uri.
 *   Impact: fara breadcrumbs nu exista BreadcrumbList schema si nici semnal de
 *           ierarhie pentru crawler; utilizatorii nu au orientare in site.
 *   Fix: apeleaza `rank_math_the_breadcrumbs()` (exista in Rank Math) in
 *        header.php sau la inceputul lui <main>, protejat cu function_exists().
 *
 * TODO [SEO-03][CRITICAL]: `$content_width = 640` (vezi simonamarin_content_width
 *   mai jos) limiteaza latimea imaginilor inserate in continut la 640px.
 *   Impact: imaginea LCP e servita la rezolutie mica -> arata prost pe desktop /
 *           retina, iar Google penalizeaza calitatea vizuala si CLS.
 *   Fix: ridica la latimea reala a coloanei de continut (ex. 1200) si genereaza
 *        srcset corect prin add_image_size().
 *
 * TODO [SEO-04][HIGH]: Nu sunt inregistrate dimensiuni de imagine proprii
 *   (add_image_size). Tema foloseste doar 'post-thumbnail' default.
 *   Impact: srcset sarac -> browserul descarca imagini prea mari pe mobil.
 *   Fix: add_image_size( 'simonamarin-card', 600, 400, true ) etc. + regenerare.
 *
 * TODO [SEO-05][HIGH]: Lipseste suport pentru excerpt-uri controlate.
 *   Impact: content-search.php foloseste the_excerpt() cu lungimea default (55
 *           cuvinte) si "[...]" - meta description fallback slaba, snippet urat.
 *   Fix: filtre `excerpt_length` si `excerpt_more`.
 *
 * TODO [SEO-06][HIGH]: Nu exista suport 'responsive-embeds'.
 *   Impact: embed-urile (YouTube, Maps) sparg layout-ul pe mobil -> semnal
 *           negativ de mobile usability in Search Console.
 *   Fix: add_theme_support( 'responsive-embeds' ) in simonamarin_setup().
 *
 * TODO [SEO-07][MEDIUM]: Arhivele si paginarea nu au tratament de duplicate
 *   content (rel=next/prev, noindex pe /page/2+, noindex pe rezultate cautare).
 *   Fix: se configureaza din Rank Math, dar trebuie VERIFICAT - momentan
 *        search.php produce pagini indexabile de tip "Search Results for: ...".
 *
 * TODO [SEO-08][MEDIUM]: Site-ul nu declara limba/hreflang explicit pentru
 *   continut romanesc. Verifica Settings > General > Site Language = ro_RO,
 *   altfel `language_attributes()` din header.php scoate lang="en-US".
 *
 * --- PERFORMANCE [CRITICAL] ---
 *
 * PERF-01 [CRITICAL] - REZOLVAT 2026-09-21. Versiunile de asset vin acum din
 *   filemtime, prin simonamarin_asset_version(). Se schimba la fiecare editare
 *   de fisier si raman stabile cand nu s-a modificat nimic.
 *
 * PERF-02 [HIGH] - REZOLVAT 2026-09-21. navigation.js se incarca doar cand
 *   locatia 'menu-1' are un meniu atribuit, si cu strategy 'defer'.
 *   De reverificat pe mobil dupa orice modificare: scriptul deschide meniul.
 *
 * PERF-03 [HIGH] - REZOLVAT PARTIAL 2026-09-21. Scoase: detectia de emoji
 *   (script inline + wp-emoji-release.min.js + cererile catre s.w.org),
 *   wp-embed.min.js, rsd_link, wlwmanifest_link, shortlink si meta generator
 *   (ultimele trei prin inc/security.php - SEC-02/SEC-03).
 *
 *   NU s-au scos, deliberat, doua lucruri din lista initiala:
 *   - wp-block-library: vezi PUB-18 mai jos. Pe un site cu articole scrise in
 *     editorul de blocuri, scoaterea lui strica vizual coloanele, galeriile si
 *     tabelele din continut. Optimizarea se face selectiv sau deloc.
 *   - link-ul de discovery REST API (`<link rel="https://api.w.org/">`).
 *     Stergerea lui nu inchide REST API-ul, deci nu aduce securitate, dar rupe
 *     descoperirea oEmbed si unele integrari. Castig zero, risc real.
 *
 * TODO [PERF-04][HIGH]: Nu exista resource hints (preconnect / preload).
 *   Fix: preload pentru style.css si pentru imaginea LCP; preconnect catre
 *        fonts.gstatic.com / Google Tag Manager daca sunt folosite.
 *
 * PERF-05 [MEDIUM] - REZOLVAT 2026-09-21. editor-styles + add_editor_style(
 *   'style.css' ) sunt acum in simonamarin_setup(). Varianta mai curata, cu un
 *   fisier de continut dedicat incarcat in ambele contexte, ramane PUB-17.
 *
 * TODO [PERF-06][MEDIUM]: style-rtl.css (961 linii) este generat si livrat desi
 *   site-ul e in romana. Nu se incarca in browser (doar la locale RTL), dar se
 *   commiteaza si se sincronizeaza degeaba. Decizie: pastreaza sau sterge.
 *
 * --- SECURITY [CRITICAL] ---
 *
 * SEC-01 ... SEC-04 - REZOLVATE 2026-09-21, in inc/security.php.
 * Motivatia fiecarei decizii este documentata acolo, langa cod. Pe scurt:
 *
 *   SEC-01 [CRITICAL] Security headers - REZOLVAT PARTIAL. Se emit acum
 *     X-Content-Type-Options, Referrer-Policy, X-Frame-Options si
 *     Permissions-Policy pe frontend, prin filtrul `wp_headers`.
 *     HSTS este scris, dar dezactivat implicit: se porneste cu o constanta in
 *     wp-config.php, abia dupa ce HTTPS-ul de productie e stabil.
 *     Content-Security-Policy NU se emite inca si ramane deschis ca SEC-11:
 *     avertismentul din auditul initial era corect, o politica enforced
 *     netestata poate rupe pagini. Se face in doi pasi, Report-Only intai.
 *
 *   SEC-02 [HIGH] Versiunea WordPress - REZOLVAT pentru <meta generator> si
 *     pentru feed-uri. Partea cu `?ver=` pe assets NU a fost atinsa in mod
 *     deliberat: acele query stringuri sunt mecanismul de cache busting al
 *     WordPress, iar stergerea lor face ca vizitatorii sa ramana cu CSS/JS
 *     vechi dupa fiecare update. Este un compromis prost pentru un castig de
 *     securitate aproape nul.
 *
 *   SEC-03 [HIGH] Pingback/XML-RPC - REZOLVAT. Metodele pingback.* sunt scoase
 *     din XML-RPC si headerul X-Pingback nu mai este emis.
 *     Nota despre simonamarin_pingback_header() din inc/template-functions.php:
 *     functia este deja inofensiva, pentru ca este conditionata de pings_open(),
 *     iar simonamarin_disable_comments() forteaza acel filtru pe false. Nu emite
 *     nimic si a fost lasata neatinsa.
 *
 *   SEC-04 [MEDIUM] Enumerarea autorilor - REZOLVAT pentru forma `?author=N`.
 *     Arhivele `/author/slug/` raman accesibile intentionat, ca sa nu se schimbe
 *     structura de URL-uri; daca ar trebui sau nu indexate este SEO-30.
 *
 * TODO [SEC-05][INFO]: Verificat - tema nu proceseaza input de la utilizator
 *   ($_GET/$_POST/$_REQUEST absente) si nu face query-uri SQL directe.
 *   Nu exista vulnerabilitati de tip SQLi in cod propriu. OK.
 *
 * --- COD INUTIL / CURATENIE ---
 *
 * TODO [CLEAN-01][REZOLVAT in aceasta sesiune]: composer.json era JSON INVALID -
 *   continea un bloc de comentariu in stil C in interiorul obiectului "require".
 *   JSON nu accepta comentarii, deci `composer install` esua cu Parse error.
 *   Comentariul a fost mutat intr-o cheie de tip string. Vezi composer.json.
 *
 * TODO [CLEAN-02][HIGH]: Scripturile npm (watch / compile:css) citesc din
 *   directorul `sass/`, care NU EXISTA in tema. Build-ul e mort.
 *   Fix: ori se restaureaza sursele SCSS, ori se sterg scripturile din
 *        package.json si se editeaza style.css direct.
 *
 * TODO [CLEAN-03][MEDIUM]: Fisiere de dezvoltare livrate in productie:
 *   .eslintrc, .stylelintrc.json, phpcs.xml.dist, composer.json, package.json,
 *   README.md, readme.txt, LICENSE, languages/readme.txt.
 *   Impact: expun stack-ul si sunt accesibile public daca serverul nu le blocheaza.
 *
 * TODO [CLEAN-04][MEDIUM]: Tema 'sydney' (6.2 MB) este INACTIVA in
 *   wp-content/themes/ iar style.css al ei a fost suprascris - nu mai contine
 *   header-ul "Theme Name", deci e o tema corupta.
 *   Impact: teme inactive nu primesc update-uri si sunt vector clasic de atac.
 *   Fix: sterge-o complet (backup intai). Nu este in scope-ul git al temei.
 *
 * TODO [CLEAN-05][LOW]: inc/jetpack.php este cod mort - Jetpack nu este instalat.
 *   Include-ul e conditionat (defined JETPACK__VERSION), deci impactul e zero,
 *   dar CSS-ul aferent din style.css se livreaza oricum. Vezi style.css.
 *
 * TODO [CLEAN-06][LOW]: Metadate de tema neconfigurate - style.css inca declara
 *   "Theme Name: simonamarin / Author: Underscores.me / Description: Description"
 *   si Theme URI catre underscores.me.
 * =============================================================================
 */


/* =============================================================================
 * AUDIT 3 - COD MORT / FISIERE NEFOLOSITE (sesiune 2026-09-21)
 * Doar marcaje TODO. NU s-a sters nicio linie si nu s-a atins niciun continut.
 * Fiecare punct are explicatia completa la fata locului, in fisierul indicat.
 * =============================================================================
 *
 * FISIERE CARE POT DISPAREA COMPLET:
 *   DEAD-01 [MEDIUM] inc/jetpack.php           - Jetpack neinstalat, cod inaccesibil
 *   DEAD-03 [MEDIUM] languages/simonamarin.pot - domeniu de text gresit (_s), din 2020
 *   DEAD-04 [LOW]    languages/readme.txt      - instructiuni Underscores in productie
 *   DEAD-05 [MEDIUM] style-rtl.css             - 961 linii servite doar pe site-uri RTL
 *   DEAD-07 [MEDIUM] inc/custom-header.php     - feature neafisat in niciun template
 *   DEAD-11 [LOW]    readme.txt                - sablon Underscores cu informatii false
 *   DEAD-13 [LOW]    phpcs.xml.dist, .eslintrc, .stylelintrc.json - lint nerulabil
 *
 * LINII / BLOCURI DE STERS:
 *   DEAD-02 [MEDIUM] style.css        - sectiunea "Jetpack infinite scroll"
 *   DEAD-05 [MEDIUM] functions.php    - wp_style_add_data( ..., 'rtl', 'replace' )
 *   DEAD-06 [LOW]    package.json     - compile:rtl, lint:scss, bundle, rtlcssConfig
 *   DEAD-08 [LOW]    inc/template-tags.php - shim wp_body_open() pentru WP < 5.2
 *   DEAD-09 [LOW]    style.css        - reguli pentru <acronym> si <big> (HTML4)
 *   DEAD-10 [LOW]    footer.php       - class="sep" fara nicio regula CSS
 *   DEAD-12 [LOW]    inc/template-functions.php - clasa body "no-sidebar" nefolosita
 *
 * DEPENDENTE (se sterg impreuna, altfel raman referinte rupte):
 *   DEAD-01 -> functions.php (require jetpack) + DEAD-02
 *   DEAD-05 -> functions.php (wp_style_add_data) + DEAD-06 (compile:rtl, rtlcssConfig)
 *   DEAD-07 -> functions.php (require custom-header) + inc/customizer.php
 *              (transport header_textcolor) + js/customizer.js (handler)
 *   DEAD-03 -> composer.json (scriptul make-pot scrie inca in languages/_s.pot)
 *
 * NEVERIFICABIL STATIC - de confirmat inainte de stergere:
 *   Selectorii din style.css pentru markup generat de NUCLEU sau de PLUGIN-uri
 *   (.gallery-columns-*, .wp-caption, .bypostauthor, .sticky, .page-links,
 *   .comment-navigation, .wp-smiley) NU apar in template-urile temei, dar sunt
 *   emisi de WordPress in continut. NU se sterg pe baza unui grep prin tema.
 * =============================================================================
 */

/* =============================================================================
 * AUDIT 3 - PUBLICARE: "articol nou, afisat corect din prima" (sesiune 2026-09-21)
 * Prefix TODO: [PUB-xx]. Nimic nu a fost modificat in cod si nici in continut -
 * doar marcaje.
 * =============================================================================
 *
 * PROBLEMA, PE SCURT
 * Astazi, ca un articol nou sa arate corect, aspectul se obtine adaugand CSS
 * direct in articol (atribut style= pe elemente, bloc HTML custom sau un bloc de
 * cod). Consecintele sunt toate pe termen lung:
 *   - fiecare articol are propriul stil, deci doua articole nu arata la fel;
 *   - o schimbare de design cere reeditarea manuala a fiecarui articol;
 *   - stilul inline din continut bate orice regula a temei, deci tema nu mai
 *     poate corecta nimic global;
 *   - autorul trebuie sa stie CSS ca sa poata publica.
 *
 * CAUZA REALA nu este ca "lipsesc niste reguli CSS", ci ca tema nu declara catre
 * WordPress niciun contract de prezentare: nu exista theme.json, nu exista
 * editor-styles, latimea de continut este cea default, iar paleta de culori si
 * scara de fonturi nu sunt definite. In lipsa lor, editorul ii ofera autorului
 * unelte complet libere (color picker, latimi, spatieri arbitrare), iar fiecare
 * alegere se materializeaza ca stil inline in continut. Tema a delegat, fara sa
 * vrea, decizia de design catre fiecare articol in parte.
 *
 * DIRECTIA DE REZOLVARE (in aceasta ordine - pasii de sus fac inutili pasii de jos)
 *   1. theme.json               - contractul: latimi, paleta, fonturi, spatieri   PUB-01
 *   2. suporturi de tema        - editor-styles, block styles, align-wide, embeds PUB-02..PUB-06
 *   3. restrictii si sabloane   - paleta fixa, scara de fonturi, block patterns   PUB-07..PUB-09
 *   4. CSS pentru markup-ul pe care il genereaza efectiv editorul                 PUB-10..PUB-16
 *   5. abia la final: CSS-ul inline deja existent in articolele vechi             PUB-19
 * Pasul 5 se face ULTIMUL si separat, fiind singurul care atinge continutul.
 *
 * CRITERIU DE "GATA": un autor scrie un articol nou folosind doar blocurile
 * standard (paragraf, titlu, imagine, citat, lista, tabel, embed), nu deschide
 * niciodata editorul de cod, iar articolul arata identic cu celelalte.
 *
 * TODO [PUB-01][CRITICAL]: Tema NU are fisier theme.json. Este piesa lipsa
 *   centrala: theme.json este locul in care tema ii spune editorului ce latimi,
 *   ce culori, ce dimensiuni de font si ce spatieri sunt permise, iar WordPress
 *   genereaza singur CSS-ul corespunzator, si in editor si pe site. Fara el,
 *   fiecare autor reinventeaza stilul in fiecare articol.
 *   Fix: creeaza theme.json in radacina temei, cu minim:
 *     {
 *       "version": 3,
 *       "settings": {
 *         "appearanceTools": true,
 *         "layout":     { "contentSize": "720px", "wideSize": "1140px" },
 *         "color":      { "palette": [ ... culorile de brand ... ],
 *                         "defaultPalette": false, "custom": false,
 *                         "customGradient": false },
 *         "typography": { "fontSizes": [ ... scara temei ... ],
 *                         "customFontSize": false, "fluid": true },
 *         "spacing":    { "units": [ "px", "rem", "%" ],
 *                         "spacingSizes": [ ... ] }
 *       },
 *       "styles": { "typography": { ... }, "elements": { "link": { ... } } }
 *     }
 *   Valorile de latime trebuie sa fie latimile reale ale coloanei acestui site
 *   (masurate in browser), nu valori copiate de undeva. Vezi PUB-06 si UX-27.
 *   Atentie: theme.json schimba aspectul pe TOT site-ul dintr-o data. Se
 *   introduce pe staging, nu direct pe productie.
 *
 * TODO [PUB-19][HIGH]: DUPA ce PUB-01..PUB-16 sunt rezolvate, stilul inline
 *   existent deja in articolele vechi va continua sa suprascrie tema: articolele
 *   noi vor arata corect, cele vechi nu. Curatarea lui inseamna insa modificarea
 *   continutului, ceea ce NU se face fara decizie explicita.
 *   Pas pregatitor, inofensiv, care se poate face oricand: inventariaza, nu
 *   modifica. O cautare in continut dupa `style=` si `<style` arata cate
 *   articole sunt afectate si ce proprietati se repeta - exact acele proprietati
 *   trebuie sa ajunga in theme.json, ca stergerea inline-ului sa nu schimbe
 *   nimic vizual.
 *   Varianta prudenta, daca inventarul iese mare: lasa articolele vechi asa cum
 *   sunt si aplica regulile noi incepand cu urmatorul articol.
 * =============================================================================
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function simonamarin_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on simonamarin, use a find and replace
		* to change 'simonamarin' to the name of your theme in all the template files.
		*/
	/*
	 * TODO [UX-69][CRITICAL]: Tema este pregatita pentru traducere, dar folderul
	 * languages/ contine DOAR simonamarin.pot (sablonul gol) - nu exista niciun
	 * ro_RO.po / ro_RO.mo. Consecinta directa, vizibila pe site:
	 *   - butonul de meniu mobil scrie "Primary Menu";
	 *   - meta articolelor scrie "Posted on ... by ...";
	 *   - arhivele scriu "Category: ...";
	 *   - paginarea scrie "Older posts" / "Newer posts";
	 *   - 404 scrie "Oops! That page can't be found.";
	 *   - cautarea scrie "Search Results for:" si "Nothing Found".
	 * Pentru un vizitator roman, textul in engleza in interfata este cel mai
	 * rapid semnal ca site-ul e nefinisat - si apare fix in momentele de
	 * frustrare (eroare, zero rezultate).
	 * ACESTA ESTE CEL MAI MARE CASTIG DE UX PE EFORT MINIM din toata lista.
	 * Fix: genereaza ro_RO.po din .pot, tradu sirurile si compileaza .mo. NU
	 * este o modificare de continut - sunt siruri ale temei, nu texte din
	 * articole sau pagini.
	 */
	load_theme_textdomain( 'simonamarin', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	// TODO [SEO-09][LOW]: 'automatic-feed-links' adauga feed-uri si pentru comentarii,
	// categorii, tag-uri si autori. Daca blogul nu are trafic RSS, sunt URL-uri
	// crawl-uite degeaba (crawl budget). Decizie: pastreaza doar feed-ul principal.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// TODO [SEO-04][HIGH] / [UX-76][HIGH]: Aici lipsesc add_image_size() proprii.
	// Fara ele, srcset-ul contine doar marimile WP default, care rareori se
	// potrivesc cu grila temei -> imagini supradimensionate pe mobil, si carduri
	// de inaltimi diferite in listari.
	// NU s-a rezolvat in aceasta sesiune si este intentionat: dimensiunile
	// corecte se aleg DUPA ce grila reala a site-ului este decisa si masurata
	// (vezi PUB-01), altfel se regenereaza mediateca de doua ori degeaba.

	/*
	 * PUB-05 [HIGH] / SEO-06 [HIGH] - REZOLVAT 2026-09-21.
	 *
	 * Un YouTube sau o postare incorporata isi pastra dimensiunea fixa a
	 * iframe-ului si depasea coloana pe telefon. Autorul compensa punandu-i o
	 * latime inline in articol - exact tiparul pe care il descrie PUB-*.
	 * Cu acest suport, WordPress adauga singur wrapper-ul care pastreaza
	 * proportia, iar embed-ul se incadreaza in coloana pe orice ecran.
	 */
	add_theme_support( 'responsive-embeds' );

	/*
	 * PUB-03 [HIGH] - REZOLVAT 2026-09-21.
	 *
	 * Incarca stilurile de baza ale blocurilor core (citat, tabel, separator,
	 * galerie, buton). Fara ele blocurile arata nefinisat pe site, iar autorul
	 * compensa manual in articol.
	 *
	 * PUB-03 cerea o decizie inainte: 'wp-block-styles' SAU reguli proprii ale
	 * temei pentru aceleasi blocuri (PUB-10), nu amandoua, ca sa nu apara
	 * conflicte de specificitate. Decizia luata aici este 'wp-block-styles',
	 * pentru ca tema nu are in acest moment nicio regula proprie pentru blocuri.
	 * DACA se scriu vreodata reguli proprii (PUB-10), linia asta trebuie
	 * reevaluata atunci - nu lasata pur si simplu pe loc.
	 */
	add_theme_support( 'wp-block-styles' );

	/*
	 * PUB-02 [CRITICAL] / PERF-05 [MEDIUM] / UX-77 [MEDIUM] - REZOLVAT 2026-09-21.
	 *
	 * Pana acum, in editor textul aparea cu fontul, latimea si spatierile
	 * default ale WordPress, nu cu ale site-ului. Autorul nu vedea ce publica,
	 * incerca sa corecteze din editor si, negasind un control potrivit, ajungea
	 * sa scrie CSS direct in articol. Acum editorul incarca stilul temei.
	 *
	 * Se incarca style.css pentru ca este, deocamdata, singurul CSS al temei.
	 * PUB-17 ramane deschis si descrie varianta mai curata: un fisier separat,
	 * doar cu regulile care privesc continutul (elemente + .wp-block-*),
	 * incarcat si in editor si pe site, ca sa existe o singura sursa de adevar.
	 *
	 * Functioneaza si cu classic-editor instalat - TinyMCE respecta editor-styles.
	 *
	 * DE VERIFICAT DUPA ACEASTA MODIFICARE: deschide un articol in editor si
	 * compara-l cu aceeasi pagina pe site. Daca editorul arata acum ciudat (nu
	 * doar diferit), cauza este ca style.css contine reguli de layout global
	 * (body, .site) pe care WordPress le rescrie pe .editor-styles-wrapper - si
	 * atunci se trece la varianta din PUB-17.
	 */
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );

	/*
	 * TODO [PUB-04][HIGH]: add_theme_support( 'align-wide' ) NU a fost adaugat,
	 * si este o omisiune deliberata, nu una uitata.
	 * PUB-04 spune el insusi ca align-wide are efect real doar impreuna cu
	 * layout.contentSize / wideSize din theme.json (PUB-01) sau cu reguli
	 * .alignwide / .alignfull in CSS (PUB-13). Adaugat singur, i-ar da autorului
	 * doua butoane noi in editor care nu produc nimic vizibil pe site - ceea ce
	 * este mai rau decat lipsa lor, pentru ca il trimite inapoi la latimi inline.
	 * Se adauga in aceeasi sesiune cu theme.json.
	 */

	/*
	 * PUB-02 [CRITICAL] - REZOLVAT 2026-09-21, mai jos in aceasta functie.
	 * Textul original al constatarii, pastrat pentru context:
	 * Lipsesc add_theme_support( 'editor-styles' ) si
	 * add_editor_style(). Consecinta directa asupra publicarii: in editor textul
	 * apare cu fontul, latimea si spatierile default ale WordPress, nu cu ale
	 * site-ului. Autorul nu vede ce publica, incearca sa corecteze din editor si,
	 * negasind un control potrivit, ajunge sa scrie CSS in articol.
	 * Fix, doua linii, aici in simonamarin_setup():
	 *   add_theme_support( 'editor-styles' );
	 *   add_editor_style( 'style.css' );   // sau un assets/editor.css dedicat
	 * Conditie ca fix-ul sa aiba efect: regulile de continut trebuie sa fie
	 * scrise astfel incat sa se aplice si in editor (WordPress le rescrie pe
	 * .editor-styles-wrapper). Cel mai curat este un fisier separat, cu reguli pe
	 * elemente si pe .wp-block-*, incarcat in ambele contexte. Vezi PUB-10.
	 *
	 * PUB-03 [HIGH] - REZOLVAT 2026-09-21, mai jos in aceasta functie.
	 * Textul original al constatarii, pastrat pentru context:
	 * Lipseste add_theme_support( 'wp-block-styles' ). Fara
	 * el, stilurile de baza ale blocurilor core (citat, tabel, separator, galerie,
	 * buton) nu se incarca pe site; blocurile arata nefinisat, iar autorul
	 * compenseaza manual, in articol.
	 * De decis inainte: daca tema isi scrie propriile reguli pentru aceleasi
	 * blocuri (PUB-10), 'wp-block-styles' devine CSS in plus si sursa de
	 * conflicte de specificitate. Alege una dintre cele doua cai, nu amandoua.
	 *
	 * TODO [PUB-04][HIGH]: Lipseste add_theme_support( 'align-wide' ) (vezi si
	 * SEO-06). Fara el, optiunile "Latime mare" si "Latime completa" nici nu apar
	 * in editor. Autorul care vrea o imagine pe toata latimea nu are buton, deci
	 * o forteaza cu latime inline - este cea mai frecventa cauza de CSS in
	 * articol. align-wide are efect real doar impreuna cu layout.contentSize /
	 * wideSize din theme.json (PUB-01) sau cu reguli .alignwide / .alignfull in
	 * CSS (PUB-13).
	 *
	 * PUB-05 [HIGH] - REZOLVAT 2026-09-21, mai jos in aceasta functie.
	 * Textul original al constatarii, pastrat pentru context:
	 * Lipseste add_theme_support( 'responsive-embeds' ).
	 * Un YouTube sau o postare lipita in articol isi pastreaza dimensiunea fixa a
	 * iframe-ului si depaseste coloana pe telefon, asa ca autorul ii pune latime
	 * inline. Fix: add_theme_support( 'responsive-embeds' ) - WordPress adauga
	 * singur wrapper-ul care pastreaza proportia.
	 *
	 * TODO [PUB-07][HIGH]: Editorul ofera acum color picker liber si dimensiune de
	 * font libera, pentru ca tema nu declara nicio paleta si nicio scara
	 * tipografica. Orice culoare aleasa se scrie ca stil inline in articol si
	 * devine imposibil de schimbat global mai tarziu.
	 * Fix (in theme.json, PUB-01): settings.color.palette cu culorile de brand,
	 * "defaultPalette": false, "custom": false; settings.typography.fontSizes cu
	 * scara temei si "customFontSize": false.
	 * Efect: autorul alege dintr-un set fix, iar rezultatul in continut este o
	 * clasa (.has-<nume>-color, .has-<nume>-font-size), nu un stil inline - deci
	 * tema poate schimba culoarea dintr-un singur loc, pentru toate articolele
	 * deodata.
	 *
	 * TODO [PUB-08][MEDIUM]: Acelasi rationament pentru spatiere. Fara
	 * settings.spacing.spacingSizes si settings.spacing.units, controalele de
	 * margine si padding din editor produc valori arbitrare, inline, diferite de
	 * la un articol la altul. Fix: o scara de spatieri in theme.json si, daca nu
	 * e nevoie de control fin, dezactivarea spatierii custom.
	 *
	 * TODO [PUB-09][MEDIUM]: Nu exista niciun block pattern inregistrat si niciun
	 * sablon de articol. Chiar si dupa PUB-01..PUB-08, autorul porneste de la o
	 * pagina goala si reconstruieste structura din memorie, deci consistenta
	 * depinde de disciplina lui.
	 * Fix: register_block_pattern() cu una-doua structuri de articol (intro,
	 * imagine, subtitluri, citat, CTA final). Autorul insereaza sablonul si
	 * completeaza doar textul. Este cel mai eficient mod de a obtine articole
	 * consistente fara sa ceri cuiva sa tina minte reguli.
	 */

	/*
	 * TODO [UX-76][HIGH]: Fara add_image_size() proprii (vezi si SEO-04), nu
	 * exista o proportie consistenta pentru imaginile din listari. Din unghi de
	 * design asta e cauza problemei descrise in UX-37: cardurile au inaltimi
	 * diferite pentru ca imaginile au proportii diferite.
	 * Fix in doi pasi:
	 *   1. decide grila (ex. card 16:9 pe mobil, 4:3 pe desktop);
	 *   2. add_image_size( 'simonamarin-card', 720, 405, true ) si regenereaza
	 *      miniaturile.
	 * Taierea (crop) garantata din PHP e mai sigura decat object-fit din CSS,
	 * pentru ca livreaza si fisiere mai mici pe mobil.
	 *
	 * UX-77 [MEDIUM] - REZOLVAT 2026-09-21 odata cu PUB-02. Constatarea originala:
	 * Lipsesc add_theme_support( 'editor-styles' ) +
	 * add_editor_style(). Impact de UX pentru cel care administreaza site-ul:
	 * in editor textul arata altfel decat pe site, deci formatarea se face
	 * "pe ghicite" si rezultatul se verifica abia dupa publicare.
	 * Se aplica si cu classic-editor instalat (TinyMCE respecta editor-styles).
	 *
	 * TODO [UX-78][MEDIUM]: Nu exista camp de cautare in header sau in meniu.
	 * Formularul de cautare apare doar pe 404 si pe ecranul "niciun rezultat" -
	 * adica exact acolo unde utilizatorul a esuat deja. Pe mobil, cautarea
	 * este adesea calea preferata fata de navigarea prin meniu.
	 * DE DECIS INTAI: are site-ul destul continut incat cautarea sa fie utila?
	 * Pe un site de prezentare cu 6-8 pagini, raspunsul este de obicei nu - si
	 * atunci solutia corecta este un meniu bun, nu un camp de cautare.
	 */
	// This theme uses wp_nav_menu() in one location.
	// TODO [SEO-10][MEDIUM]: O singura locatie de meniu. Fara meniu de footer nu
	// exista internal linking secundar catre paginile importante (servicii,
	// contact, politica de confidentialitate) - link equity slab distribuita.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'simonamarin' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			// TODO [A11Y-01][LOW]: Lipseste 'navigation-widgets' - widget-urile de
			// navigatie ies fara <nav> semantic, deci fara landmark pentru
			// screen readers si fara semnal de navigatie pentru crawler.
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'simonamarin_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'simonamarin_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
/*
 * TODO [UX-79][MEDIUM]: $content_width = 640 este valoarea default Underscores
 * si nu corespunde niciunei latimi reale de coloana din acest site, pentru ca
 * site-ul nu are layout definit (vezi UX-27 din style.css).
 * Consecinta de design: WordPress foloseste aceasta valoare pentru a decide
 * ce dimensiuni de imagine sa ofere; 640px este prea putin pentru imaginea
 * principala pe un telefon modern (viewport 390px la DPR 3 = 1170px reali),
 * deci imaginile pot aparea neclare exact pe mobil.
 * Fix: stabileste intai latimea coloanei de continut (UX-27), apoi pune aici
 * exact acea valoare.
 */
function simonamarin_content_width() {
	/*
	 * TODO [PUB-06][HIGH]: Aceeasi valoare de 640px, privita din unghiul
	 * publicarii: $content_width este plafonul pe care editorul il foloseste
	 * pentru imaginile si embed-urile inserate in articol. Cand autorul insereaza
	 * o imagine mare si o vede limitata la 640px, reactia normala este sa ii puna
	 * latime manual in articol - inca o sursa de CSS inline.
	 * Fix: dupa ce se stabileste latimea reala a coloanei (UX-27), pune aici
	 * exact acea valoare SI aceeasi valoare in layout.contentSize din theme.json
	 * (PUB-01). Cele doua trebuie sa ramana sincronizate; daca difera, editorul
	 * arata o latime si site-ul alta, iar autorul va corecta iar manual.
	 */
	// TODO [SEO-03][CRITICAL]: 640px este valoarea default din Underscores, nu
	// latimea reala a coloanei acestui site. WordPress foloseste $content_width ca
	// plafon pentru imaginile si embed-urile inserate in continut.
	// Impact: imaginea principala (LCP) e servita mica -> blurata pe desktop.
	// Fix: masoara latimea reala a .site-main din style.css si pune valoarea aici.
	$GLOBALS['content_width'] = apply_filters( 'simonamarin_content_width', 640 );
}
add_action( 'after_setup_theme', 'simonamarin_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function simonamarin_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'simonamarin' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'simonamarin' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'simonamarin_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
/*
 * TODO [PSY-08][CRITICAL]: DATE DE SANATATE. Orice mesaj trimis prin formularul
 * de contact al unui cabinet de psihoterapie contine, aproape prin definitie,
 * date privind sanatatea - categorie speciala in GDPR (art. 9), cu regim mult
 * mai strict decat datele obisnuite. Pe acest site sunt instalate
 * contact-form-7 si post-smtp. DE VERIFICAT, in aceasta ordine:
 *   1. post-smtp are jurnal de email-uri activat implicit, iar jurnalul poate
 *      pastra CONTINUTUL mesajelor in baza de date WordPress. Asta inseamna
 *      relatari personale stocate in clar, accesibile oricui obtine acces la
 *      admin sau la un backup. Dezactiveaza logarea continutului sau
 *      limiteaza retentia la cateva zile.
 *   2. exista duplicator-backups in wp-content - backup-urile contin aceleasi
 *      date si nu trebuie sa ramana accesibile public.
 *   3. daca se instaleaza vreodata Flamingo (companion CF7), el stocheaza
 *      permanent toate mesajele - decizie constienta, nu implicita.
 *   4. formularul trebuie sa ceara minimul necesar (nume sau pseudonim + o
 *      cale de contact) si sa spuna EXPLICIT, langa buton, ce se intampla cu
 *      mesajul: cine il citeste, cat se pastreaza, in cat timp se raspunde.
 * Formularea "Nu scrie aici detalii pe care nu vrei sa le trimiti prin email"
 * este o masura de protectie reala si, in acelasi timp, un semn de
 * profesionalism.
 *
 * TODO [PSY-09][HIGH]: Google Site Kit este instalat, deci exista analytics.
 * Pe un site de sanatate mintala, URL-urile vizitate sunt ele insele date
 * sensibile: "cineva de la IP-ul X a citit pagina despre atacuri de panica".
 * DE VERIFICAT si de decis constient:
 *   - anonimizarea IP si dezactivarea oricaror semnale de publicitate
 *     (Google Signals, remarketing) - pe acest domeniu nu au ce cauta;
 *   - sa NU se adauge Meta Pixel sau alte pixeli publicitari; este exact
 *     scenariul pentru care au existat sanctiuni in sectorul de sanatate;
 *   - cookie-law-info trebuie sa blocheze efectiv scripturile pana la
 *     consimtamant, nu doar sa afiseze un banner. Testeaza cu DevTools >
 *     Network inainte de a accepta bannerul.
 *
 * TODO [PSY-10][MEDIUM]: Exista folderul wp-content/gravatars (cache local de
 * avatare). Avatarele Gravatar se incarca pe baza unui hash al adresei de
 * email a comentatorului si implica o cerere catre un serviciu tert de pe
 * pagina. Daca se pastreaza comentariile (vezi PSY-11), dezactiveaza
 * avatarele din Settings > Discussion.
 */
function simonamarin_scripts() {
	// PERF-01 [CRITICAL] - REZOLVAT: versiunea vine din filemtime, nu din
	// constanta hardcodata. Vezi simonamarin_asset_version() la inceputul
	// fisierului pentru motivatie.
	//
	// TODO [PERF-04][HIGH]: style.css este singurul CSS al temei si e render
	// blocking. Fara <link rel="preload"> si fara critical CSS inline, FCP/LCP
	// sufera. LiteSpeed Cache are optiune de CCSS - verifica daca e activata.
	wp_enqueue_style( 'simonamarin-style', get_stylesheet_uri(), array(), simonamarin_asset_version( 'style.css' ) );
	// TODO [DEAD-05][MEDIUM]: Linia de mai jos incarca style-rtl.css, un fisier de
	// 961 linii care nu este servit niciodata (site LTR, romana). Se sterge odata
	// cu style-rtl.css. Vezi antetul acelui fisier pentru detalii.
	wp_style_add_data( 'simonamarin-style', 'rtl', 'replace' );

	/*
	 * PERF-02 [HIGH] - REZOLVAT 2026-09-21.
	 *
	 * Inainte: navigation.js se incarca pe fiecare pagina, inclusiv pe cele
	 * unde nu exista niciun meniu de navigat. Acum se incarca doar daca locatia
	 * 'menu-1' chiar are un meniu atribuit in Appearance > Menus.
	 *
	 * ATENTIE daca se schimba ceva aici: scriptul gestioneaza butonul
	 * .menu-toggle, adica meniul pe mobil. Daca este incarcat conditionat
	 * gresit, meniul mobil ramane inchis si nu se poate deschide - o pagina
	 * fara navigatie, pe dispozitivul de pe care vine majoritatea traficului.
	 * De verificat dupa orice modificare: deschide site-ul pe un ecran
	 * < 600px si apasa efectiv butonul de meniu.
	 *
	 * 'strategy' => 'defer' (WP 6.3+) lasa parsarea HTML sa continue in timp ce
	 * scriptul se descarca. Scriptul isi ataseaza singur listenerele dupa
	 * incarcarea documentului, deci defer nu schimba comportamentul.
	 */
	if ( has_nav_menu( 'menu-1' ) ) {
		wp_enqueue_script(
			'simonamarin-navigation',
			get_template_directory_uri() . '/js/navigation.js',
			array(),
			simonamarin_asset_version( 'js/navigation.js' ),
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	/*
	 * MODIFICAT (2026-09-21): scriptul 'comment-reply' nu se mai incarca -
	 * comentariile sunt dezactivate pe tot site-ul (vezi
	 * simonamarin_disable_comments() mai jos). Conditia originala era:
	 *   if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
	 * Daca se revine vreodata asupra deciziei, se restaureaza de aici.
	 */

	/*
	 * TODO [PUB-17][MEDIUM]: Aici se incarca doar style.css, pentru site. Nu
	 * exista niciun stylesheet incarcat si in editor, deci nimic din ce se
	 * stabileste in PUB-10..PUB-16 nu va fi vizibil autorului in timp ce scrie.
	 * Fix, impreuna cu PUB-02: muta regulile care privesc continutul
	 * (elemente + .wp-block-*) intr-un fisier propriu, de exemplu
	 * assets/css/content.css, si incarca-l in ambele contexte - cu
	 * add_editor_style() pentru editor si cu wp_enqueue_style() aici pentru site.
	 * Asa exista o singura sursa de adevar si editorul nu mai poate minti.
	 *
	 * TODO [PUB-18][MEDIUM]: ATENTIE la interactiunea cu PERF-03 de mai jos.
	 * `wp_dequeue_style( 'wp-block-library' )` este o optimizare corecta doar pe
	 * un site fara blocuri. Pe un site unde autorul publica articole din editorul
	 * de blocuri, scoaterea acelui CSS strica exact ce incearca PUB-01..PUB-16 sa
	 * repare (coloane, galerii, butoane, tabele), iar simptomul reapare sub forma
	 * de CSS scris manual in articol.
	 * Regula: PUB-* are prioritate fata de acel dequeue. Daca se doreste
	 * optimizarea, se face selectiv (doar pe paginile fara blocuri) si se
	 * verifica dupa aceea un articol real, nu homepage-ul.
	 */

	/*
	 * PERF-03 [HIGH] - REZOLVAT PARTIAL 2026-09-21, vezi mai jos ce s-a scos si
	 * ce NU s-a scos, pentru ca diferenta este intentionata.
	 *
	 * SCOS: wp-embed (js/wp-embed.min.js). Acest script exista doar ca alte
	 * site-uri sa poata incorpora articolele acestui site intr-un iframe
	 * oEmbed. Nu afecteaza incorporarea de YouTube sau alt continut extern IN
	 * paginile noastre - aceea se face pe server. Este cerut pe fiecare pagina
	 * si nu are niciun consumator aici.
	 *
	 * SCOS: scriptul si stilurile de detectie a emoji. WordPress incarca un
	 * script inline plus wp-emoji-release.min.js ca sa converteasca emoji in
	 * imagini de pe s.w.org, pentru browsere care nu le redau nativ. In 2026
	 * toate browserele relevante le redau nativ, iar cererea catre s.w.org este
	 * si o cerere catre un domeniu tert de pe fiecare pagina - ceea ce, pentru
	 * un site de cabinet, este si o scurgere inutila de trafic catre exterior.
	 *
	 * NU S-A SCOS: wp-block-library. Auditul initial il sugera, dar PUB-18 de
	 * mai sus il contrazice explicit si PUB-18 are dreptate: daca articolele
	 * sunt scrise in editorul de blocuri, scoaterea acelui CSS strica vizual
	 * coloanele, galeriile, butoanele si tabelele din continut. Optimizarea
	 * corecta se face selectiv, pe paginile fara blocuri, si se verifica pe un
	 * articol real - nu se face "din oficiu" aici.
	 *
	 * DE VERIFICAT MANUAL: LiteSpeed Cache are propriile optiuni pentru emoji
	 * si embed. Daca sunt deja bifate acolo, liniile de mai jos sunt redundante
	 * (inofensive, dar redundante) si e mai curat sa existe intr-un singur loc.
	 */
	wp_deregister_script( 'wp-embed' );
}
add_action( 'wp_enqueue_scripts', 'simonamarin_scripts' );

/**
 * PERF-03 [HIGH] - REZOLVAT PARTIAL. Scoate detectia de emoji din <head>.
 *
 * Hook-urile de emoji sunt inregistrate de core pe 'init' si pe alte actiuni
 * decat 'wp_enqueue_scripts', deci nu pot fi scoase din simonamarin_scripts().
 * De aceea exista aceasta functie separata.
 *
 * Ce dispare concret din HTML-ul fiecarei pagini: un bloc de script inline de
 * ~1 KB, plus cererea catre wp-emoji-release.min.js si, la prima aparitie a
 * unui emoji, cereri de imagine catre s.w.org (domeniu tert).
 *
 * Emoji-urile scrise in continut raman vizibile - browserele le redau nativ de
 * ani buni. Se scoate doar polyfill-ul pentru browsere care nu mai sunt in uz.
 * Niciun text de articol sau de pagina nu este modificat.
 */
function simonamarin_disable_emoji_detection() {
	// Scriptul de detectie, pe frontend, in admin si in paginile de embed.
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'embed_head', 'print_emoji_detection_script' );

	/*
	 * Stilurile de emoji. Numele hook-ului conteaza: in WordPress-ul instalat
	 * aici (7.0.5, verificat in wp-includes/default-filters.php) stilurile sunt
	 * puse in coada de `wp_enqueue_emoji_styles`, iar vechiul
	 * `print_emoji_styles` este pastrat doar pentru compatibilitate si este
	 * dezlegat chiar de wp_enqueue_emoji_styles(). Un remove_action pe numele
	 * vechi, singur, NU ar face nimic - de aceea sunt scoase ambele.
	 */
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'admin_enqueue_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );

	// Si conversia emoji in imagini din feed-uri si din e-mailurile trimise.
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'simonamarin_disable_emoji_detection' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
// TODO [CLEAN-05][LOW]: Cod mort - Jetpack nu este instalat pe acest site.
// Include-ul e conditionat, deci nu costa nimic la runtime, dar inc/jetpack.php
// si sectiunea "Jetpack infinite scroll" din style.css pot fi sterse.
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Infrastructura SEO: robots.txt, sitemap, canonical, redirecturi, indexare.
 *
 * Fisierul este, deocamdata, exclusiv documentatie - tot codul din el este
 * comentat, deci include-ul nu are niciun efect la runtime. Este inclus totusi
 * de aici pentru ca TODO-urile SEO-72 ... SEO-93 sa apara in orice cautare
 * facuta peste fisierele incarcate efectiv de tema.
 */
require get_template_directory() . '/inc/seo.php';

/**
 * Hardening: headere de securitate, ascunderea versiunii, pingback, enumerare.
 *
 * Spre deosebire de inc/seo.php, acest fisier contine cod ACTIV - efectele lui
 * se vad in headerele de raspuns ale oricarei pagini de frontend. Verificare
 * rapida dupa modificari:
 *   curl -sSI https://<domeniu>/ | grep -i "x-content-type\|referrer\|x-frame\|permissions\|pingback"
 */
require get_template_directory() . '/inc/security.php';

/*
 * TODO [SEO-11][HIGH]: Structura de fisiere de extensie a temei este doar
 * partial creata. Exista inc/seo.php (infrastructura SEO, documentatie).
 * Mai lipsesc:
 *   inc/security.php   - send_headers, dezactivare enumerare autori, hide version.
 *   inc/performance.php- dequeue balast, resource hints, versionare prin filemtime.
 * Fiecare inclus aici cu require get_template_directory() . '/inc/<fisier>.php';
 * Partea de SEO on-page ramasa (JSON-LD complementar Rank Math, breadcrumbs,
 * excerpt) se implementeaza tot in inc/seo.php, sub blocul de documentatie.
 */


/**
 * Dezactiveaza complet comentariile pe front-end.
 *
 * DECIZIE (2026-09-21), la cererea clientei. Context - vezi PSY-11 din
 * comments.php: pe un site de cabinet de psihoterapie, comentariile publice
 * expun persoane care relateaza experiente personale, creeaza o relatie
 * publica terapeut-comentator cu limite ambigue si o obligatie implicita de a
 * raspunde unor mesaje care pot semnala risc - in afara oricarui cadru in care
 * riscul poate fi evaluat.
 *
 * Aceasta implementare NU sterge nimic: comentariile existente raman in baza de
 * date, doar nu mai sunt afisate si nu mai pot fi adaugate. Decizia este
 * complet reversibila prin stergerea acestui bloc.
 *
 * Acopera si: SEC-09 (spam fara Akismet), SEC-10 (cookie-uri de comentator si
 * GDPR), SEO-33 (link-uri fara rel="ugc nofollow"), SEO-34 (URL-uri
 * /comment-page-N/ indexabile), UX-72 (formular de comentarii nestilizat).
 */
function simonamarin_disable_comments() {
	// Inchide comentariile si ping-urile pentru toate tipurile de continut.
	add_filter( 'comments_open', '__return_false', 20 );
	add_filter( 'pings_open', '__return_false', 20 );

	// Ascunde comentariile deja existente, fara a le sterge.
	add_filter( 'comments_array', '__return_empty_array', 20 );

	// Nu mai raporta niciun comentariu, ca sa nu apara "1 thought on ...".
	add_filter( 'get_comments_number', '__return_zero', 20 );

	// Scoate feed-ul de comentarii din <head> (vezi si SEO-09).
	add_filter( 'feed_links_show_comments_feed', '__return_false' );
}
add_action( 'init', 'simonamarin_disable_comments' );

/**
 * Scoate din <head> link-urile rel="alternate" catre feed-urile suplimentare.
 *
 * feed_links_extra() emite, printre altele, feed-ul de comentarii al fiecarui
 * articol - inutil acum, cand comentariile sunt inchise. Elimina in acelasi
 * timp si feed-urile de categorie, tag, autor si cautare, ceea ce corespunde
 * recomandarii din SEO-09 (URL-uri crawl-uite degeaba).
 * Feed-ul principal al site-ului, emis de feed_links(), ramane neatins.
 */
remove_action( 'wp_head', 'feed_links_extra', 3 );
