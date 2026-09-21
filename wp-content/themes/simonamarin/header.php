<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package simonamarin
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	// TODO [CLEAN-07][LOW]: Link-ul XFN catre gmpg.org este un rest din 2003.
	// Niciun motor de cautare nu il mai foloseste. Se poate sterge.
	?>
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<?php
	/*
	 * TODO [SEO-12][CRITICAL]: <head> gol pe partea de tema. Verifica in
	 * "View Source" pe site-ul live ce emite Rank Math si completeaza ce lipseste:
	 *   - <link rel="canonical">            (Rank Math: DA, de verificat)
	 *   - og:title / og:description / og:image / og:type / og:locale
	 *   - twitter:card = summary_large_image
	 *   - JSON-LD Organization/Person + BreadcrumbList
	 * NU dubla nimic din ce emite deja Rank Math - dublurile de canonical sau OG
	 * sunt mai daunatoare decat lipsa lor.
	 *
	 * TODO [PERF-07][HIGH]: Lipsesc resource hints inainte de wp_head():
	 *   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	 *   <link rel="preload" as="image" href="<imaginea LCP>">
	 * Fara ele, fonturile si imaginea principala se descopera tarziu -> LCP mare.
	 *
	 * TODO [PERF-08][MEDIUM]: Lipseste <meta name="theme-color"> pentru mobil.
	 */
	wp_head();
	?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'simonamarin' ); ?></a>

	<?php
	/*
	 * ============================================================================
	 * AUDIT UX (2026-09-21) - abordare MOBILE-FIRST
	 * Nota de context: tema este un schelet Underscores care a suprascris tema
	 * anterioara. Inainte de a implementa orice TODO de mai jos, verifica in
	 * browser DACA template-ul respectiv este efectiv atins de vreun URL real
	 * (Appearance > Editor nu ajuta aici - foloseste Query Monitor, care arata
	 * "Template: header.php / page.php" pentru fiecare pagina incarcata).
	 * Un TODO pe un template mort nu merita efort.
	 * ============================================================================
	 *
	 * TODO [UX-01][HIGH]: Headerul nu are container cu latime maxima si nici
	 * padding orizontal (vezi si CSS-06 din style.css - sectiunea "# Layouts"
	 * este goala). Pe mobil asta inseamna ca logo-ul, titlul si meniul ating
	 * marginea ecranului, fara "gutter".
	 * Regula mobile-first: minim 16px padding lateral pe viewport < 480px,
	 * 20-24px peste. Textul lipit de rama ecranului este primul semnal vizual
	 * ca un site "nu e facut pentru telefon".
	 * Fix: un wrapper .site-header > .container cu max-width + padding inline.
	 *
	 * TODO [UX-02][HIGH]: Headerul nu contine niciun CTA. Pe un site de
	 * prezentare, peste 70% din trafic este mobil, iar actiunea principala
	 * (suna / scrie / programare) trebuie sa fie vizibila fara scroll.
	 * Fix recomandat: un buton tel: in header, vizibil DOAR pe mobil, sau o
	 * bara fixa jos (thumb zone) - zona de jos a ecranului este cel mai usor
	 * de atins cu degetul mare, headerul de sus este cel mai greu.
	 *
	 * TODO [UX-03][MEDIUM]: Headerul nu este sticky. Pe pagini lungi, pe mobil,
	 * utilizatorul ramane fara acces la navigatie dupa primul scroll si e
	 * obligat sa deruleze inapoi pana sus.
	 * Fix: position: sticky; top: 0 pe #masthead, cu inaltime redusa la scroll.
	 * Atentie: sticky header consuma din viewportul mobil - maxim ~56-64px.
	 *
	 * TODO [UX-04][MEDIUM]: Nu exista ierarhie vizuala intre .site-branding si
	 * .main-navigation - ambele sunt blocuri stivuite, la aceeasi greutate
	 * vizuala. Pe mobil utilizatorul nu distinge instant "cine e site-ul" de
	 * "unde pot merge".
	 */
	?>
	<?php
	/*
	 * ============================================================================
	 * AUDIT PSY (2026-09-21) - UX pentru un site de cabinet de psihoterapie
	 * Premisa: o parte din vizitatori ajung aici in perioade de suferinta, iar
	 * decizia de a scrie primul mesaj este ea insasi anevoioasa. Design-ul fie
	 * coboara pragul acestui prim pas, fie il ridica. Nu exista varianta neutra.
	 * ============================================================================
	 *
	 * TODO [PSY-01][CRITICAL]: Headerul afiseaza doar numele site-ului si
	 * descrierea (tagline). Lipseste identitatea profesionala verificabila:
	 * titlul profesional exact (psiholog clinician / psihoterapeut, treapta de
	 * specializare) si codul din Registrul Colegiului Psihologilor din Romania.
	 * Doua motive, ambele serioase:
	 *   - increderea: vizitatorul poate verifica atestarea in registrul public;
	 *   - deontologia: prezentarea corecta a calificarii este o obligatie
	 *     profesionala, nu un element de marketing.
	 * Fix: titlul profesional in header (sau imediat sub el) si datele complete
	 * de atestare in footer. Textul exact se cere terapeutei - NU se inventeaza
	 * si nu se aproximeaza titulatura.
	 *
	 * TODO [PSY-02][CRITICAL]: CTA-ul principal (vezi UX-02) trebuie formulat cu
	 * grija - pe acest tip de site, eticheta butonului este o interventie in
	 * sine:
	 *   - prag jos, nu angajament mare: "Scrie-mi" / "Hai sa vorbim" /
	 *     "Programeaza o prima discutie" functioneaza mai bine decat
	 *     "Rezerva sedinta", care presupune ca decizia e deja luata;
	 *   - persoana intai ("Scrie-mi") umanizeaza si aminteste ca de cealalta
	 *     parte e o persoana, nu o institutie.
	 * INTERZIS pe acest tip de site, chiar daca cresc conversia:
	 * numaratoare inversa, "ultimele locuri", pop-up de exit-intent, mesaje de
	 * culpabilizare la inchidere ("Nu, prefer sa raman asa"). Pe un public
	 * vulnerabil, tiparele astea nu sunt doar dark patterns, sunt o problema
	 * etica.
	 *
	 * TODO [PSY-03][MEDIUM]: Titlul paginii (<title>) apare in istoricul
	 * browserului, in taburi si in sugestiile din bara de adresa - inclusiv pe
	 * un dispozitiv partajat cu familia. Pentru cineva care nu a spus nimanui
	 * ca cauta terapie, un titlu de tipul "Terapie pentru depresie | ..." poate
	 * fi un motiv real de a nu reveni.
	 * Nu exista solutie tehnica completa (istoricul apartine browserului), dar
	 * se poate: mentine titlurile sobre, si adauga pe pagina de contact o nota
	 * scurta despre navigarea privata. Se configureaza in Rank Math, nu aici.
	 */
	?>
	<header id="masthead" class="site-header">
		<div class="site-branding">
			<?php
			/*
			 * TODO [PERF-09][HIGH]: the_custom_logo() emite <img> fara width/height
			 * explicite daca logo-ul e SVG sau daca metadatele lipsesc -> CLS
			 * (Cumulative Layout Shift), metrica Core Web Vitals penalizata de Google.
			 * In plus, logo-ul din header este aproape mereu deasupra foldului si
			 * NU trebuie sa aiba loading="lazy" (WP il adauga automat din 5.5).
			 * Fix: filtru `get_custom_logo` care forteaza loading="eager" +
			 *      fetchpriority="high" si adauga dimensiunile.
			 *
			 * TODO [SEO-13][MEDIUM]: Alt-textul logo-ului vine din campul "Alt Text"
			 * al atasamentului din Media Library. Verifica sa nu fie gol - logo-ul
			 * este principalul semnal de brand din pagina.
			 */
			the_custom_logo();
			/*
			 * TODO [SEO-14][CRITICAL]: Pe homepage, <h1> este numele site-ului.
			 * Impact: homepage-ul nu are un H1 descriptiv cu cuvintele-cheie reale
			 * (ex. serviciul + orasul). Numele brandului ca H1 nu aduce rankings
			 * pentru interogari non-brand.
			 * Atentie: NU se modifica textul paginii. Fix corect = H1 semantic sa
			 * vina din continutul paginii de front page, iar aici sa ramana un <p>
			 * sau un <div> cu clasa .site-title (asa cum e deja pe restul paginilor).
			 * Conditia de mai jos se aplica doar cand front page = lista de articole;
			 * daca site-ul are o pagina statica pe prima pozitie, ramura asta nu se
			 * executa oricum - de verificat in Settings > Reading.
			 */
			if ( is_front_page() && is_home() ) :
				?>
				<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
				<?php
			else :
				?>
				<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
				<?php
			endif;
			$simonamarin_description = get_bloginfo( 'description', 'display' );
			if ( $simonamarin_description || is_customize_preview() ) :
				?>
				<p class="site-description">
					<?php
					/*
					 * SEC-06 [CRITICAL] - REZOLVAT 2026-09-21.
					 *
					 * Inainte: `echo $simonamarin_description;` cu un phpcs:ignore care
					 * masca avertismentul in loc sa rezolve problema. Valoarea vine din
					 * optiunea 'blogdescription', scriptibila de orice cont cu
					 * capability 'manage_options' sau de orice plugin - deci un XSS
					 * stocat daca acel cont este compromis.
					 *
					 * Acum: wp_kses_post() filtreaza output-ul prin lista de etichete
					 * permise in continutul unui articol. Descrierea site-ului poate
					 * contine in continuare <em>, <strong> sau un link (get_bloginfo cu
					 * 'display' trece deja valoarea prin filtrul 'bloginfo'), dar <script>
					 * si atributele de tip on* sunt eliminate.
					 *
					 * NU se inlocuieste cu esc_html(): ar afisa literal orice tag din
					 * descriere. Aceasta este o schimbare de escapare, nu de continut -
					 * textul descrierii ramane exact cel din setari.
					 */
					echo wp_kses_post( $simonamarin_description );
					?>
				</p>
			<?php endif; ?>
		</div><!-- .site-branding -->

		<?php
		/*
		 * TODO [A11Y-02][MEDIUM]: <nav> fara aria-label. Cand pagina va avea si
		 * breadcrumbs sau meniu de footer, screen readerele nu le pot deosebi.
		 * Fix: <nav id="site-navigation" class="main-navigation"
		 *          aria-label="<?php esc_attr_e( 'Primary', 'simonamarin' ); ?>">
		 *
		 * TODO [SEO-02][CRITICAL]: Locul potrivit pentru breadcrumbs este imediat
		 * DUPA acest </nav>, inainte de </header>. Rank Math ofera:
		 *   if ( function_exists( 'rank_math_the_breadcrumbs' ) ) { rank_math_the_breadcrumbs(); }
		 * Trebuie activat din Rank Math > General Settings > Breadcrumbs.
		 */
		?>
		<nav id="site-navigation" class="main-navigation">
			<?php
			/*
			 * TODO [UX-07][MEDIUM]: Meniul desktop se deschide pe hover
			 * (.main-navigation ul li:hover > ul in style.css) si are submeniuri
			 * cu latime fixa `width: 200px`. Pe mobil hover nu exista, iar in
			 * meniul deschis submeniurile raman pozitionate absolut, cu `left`
			 * manipulat prin -999em. Rezultat probabil pe telefon: submeniurile
			 * fie nu sunt accesibile, fie ies din ecran si provoaca scroll
			 * orizontal.
			 * VERIFICA INTAI: deschide meniul pe un telefon real (sau DevTools,
			 * 360px) pe o intrare de meniu cu copii. Daca meniul site-ului e
			 * plat (fara submeniuri), acest TODO nu se aplica - nu investi in el.
			 * Fix daca exista submeniuri: pe mobil, submeniul devine `position:
			 * static` si se deschide in flux (acordeon), cu un buton separat de
			 * expandare langa link - ca link-ul parinte sa ramana navigabil
			 * (vezi UX-08 din navigation.js).
			 */
			?>
			<?php
			// TODO [A11Y-03][LOW]: Butonul de meniu nu isi schimba textul/starea
			// vizibila la deschidere si nu are aria-label descriptiv. Textul
			// "Primary Menu" e si eticheta, si continut - confuz la citire vocala.
			?>
			<?php
			/*
			 * TODO [UX-05][CRITICAL]: Butonul de meniu mobil este singurul punct de
			 * intrare in navigatie sub 600px si are trei probleme de UX:
			 *   1. Zona de atingere. In style.css, `button` are `line-height: 1` si
			 *      `padding: 0.6em 1em 0.4em` => inaltime reala ~30px. Minimul
			 *      recomandat este 44x44px (Apple HIG) / 48x48px (Material).
			 *      Sub acest prag rata de ratare a tap-ului creste vizibil.
			 *   2. Nu are pictograma (hamburger). Textul "Primary Menu", in engleza,
			 *      pe un site in romana, este si netradus si neintuitiv. Conventia
			 *      recunoscuta instant pe mobil este icon-ul cu trei linii.
			 *   3. Nu isi schimba forma la deschidere (hamburger -> X). Utilizatorul
			 *      nu are indiciu vizual ca acelasi buton inchide meniul.
			 * Fix: icon SVG inline + label text ascuns vizual, min-height 48px,
			 *      si o clasa .is-open pentru starea deschisa (JS seteaza deja
			 *      aria-expanded - stilizeaza pe [aria-expanded="true"], fara clasa
			 *      noua).
			 *
			 * TODO [UX-06][MEDIUM]: Butonul este plasat SUB branding, deci in partea
			 * de sus a ecranului - cea mai greu accesibila cu degetul mare pe
			 * telefoanele mari. Daca traficul mobil e dominant, evalueaza o bara de
			 * navigatie jos (bottom nav) cu 3-4 destinatii principale.
			 */
			?>
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'simonamarin' ); ?></button>
			<?php
			// TODO [SEO-15][MEDIUM]: wp_nav_menu() fara 'fallback_cb' => false.
			// Daca meniul 'menu-1' nu e asignat, WordPress afiseaza automat
			// wp_page_menu() = TOATE paginile publicate, inclusiv cele care nu
			// trebuie linkuite (thank-you, landing, pagini de test).
			// Impact: link-uri interne nedorite pe fiecare pagina din site.
			// Fix: adauga 'fallback_cb' => false si 'container_class' propriu.
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					'menu_id'        => 'primary-menu',
				)
			);
			?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->
