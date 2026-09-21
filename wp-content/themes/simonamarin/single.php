<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-35][HIGH]: Articolele nu au sectiune de articole similare (related
 * posts). Fiecare articol este o "frunza" in graful de link-uri interne -
 * primeste link din arhiva, dar nu trimite mai departe catre continut relevant.
 * Impact: crawl depth mare, autoritate interna prost distribuita, timp pe site mic.
 * Fix: WP_Query pe aceleasi categorii/taguri, excluzand articolul curent, afisata
 * dupa .entry-content. NU modifica textul articolelor - adauga doar o lista de
 * link-uri sub continut.
 *
 * TODO [SEO-36][MEDIUM]: the_post_navigation() de mai jos genereaza link-uri
 * Anterior/Urmator pe ordine CRONOLOGICA, nu tematica. Pe un blog cu subiecte
 * diverse asta creeaza legaturi fara relevanta semantica.
 * Fix: adauga 'in_same_term' => true, 'taxonomy' => 'category'.
 *
 * TODO [SEO-37][MEDIUM]: Lipseste schema Article/BlogPosting completa
 * (headline, image, datePublished, dateModified, author, publisher).
 * Verifica ce emite Rank Math cu Google Rich Results Test inainte de a adauga
 * ceva in tema - dublarea schemei este mai rea decat lipsa ei. Vezi SEO-01.
 */
/*
 * TODO [PSY-21][HIGH]: Daca pe site exista sau vor exista articole care
 * ating subiectul suicidului sau al autovatamarii, prezentarea lor trebuie
 * sa respecte recomandarile de raportare responsabila (OMS / ghidurile de
 * media). Pe partea care tine de tema si de prezentare:
 *   - resursele de criza la INCEPUTUL articolului (vezi PSY-13), nu la
 *     final si nu doar in footer;
 *   - fara imagine reprezentativa ilustrativa pe astfel de articole -
 *     imaginea apare si in preview-ul de social media, unde ajunge la
 *     oameni care nu au cautat subiectul;
 *   - fara extras automat in listari pentru aceste articole, din acelasi
 *     motiv: primele 55 de cuvinte pot scoate din context exact fraza
 *     gresita (vezi UX-47 - excerptul manual rezolva si asta).
 * Partea de continut editorial (formulari, evitarea descrierii metodelor)
 * este decizia si responsabilitatea terapeutei.
 *
 * TODO [PSY-22][MEDIUM]: Butoanele de distribuire sugerate in UX-67 trebuie
 * regandite aici: a distribui public un articol despre depresie este, pentru
 * multi, o declaratie pe care nu vor sa o faca. Web Share API (un singur
 * buton, care deschide sheet-ul sistemului si include si optiuni private -
 * mesaje, salvare) este net preferabil unui rand de iconite Facebook /
 * X / LinkedIn afisate permanent.
 * Optiunea cea mai utila si cea mai discreta ramane copierea link-ului.
 */
get_header();
?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			/*
			 * TODO [UX-66][MEDIUM]: Navigarea anterior/urmator se face intre
			 * articolele vecine cronologic, ceea ce rareori inseamna "articolul
			 * urmator relevant". Pentru un cititor pe mobil, mai utile sunt 2-3
			 * articole inrudite (aceeasi categorie), cu imagine si titlu.
			 * Vezi si UX-35 din style.css pentru problema de layout a acestei
			 * navigari pe ecran ingust.
			 *
			 * TODO [UX-67][MEDIUM]: Articolul nu are, pe mobil, niciunul dintre
			 * elementele care sustin citirea si distribuirea:
			 *   - timp estimat de citire (seteaza asteptarea inainte de scroll);
			 *   - butoane de distribuire native (Web Share API - un singur buton,
			 *     care deschide sheet-ul sistemului; net superior pe telefon fata
			 *     de un rand de iconite sociale);
			 *   - un CTA la finalul articolului.
			 * Toate sunt elemente de interfata adaugate din template, fara a
			 * modifica textul articolului.
			 *
			 * TODO [UX-68][LOW]: Etichetele "Previous:" / "Next:" de mai jos sunt
			 * in engleza si trec prin functiile de traducere, dar fisierele din
			 * languages/ nu contin traducere in romana. Vezi UX-69 din
			 * functions.php - problema este generala pe toata tema.
			 */
			the_post_navigation(
				array(
					'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'simonamarin' ) . '</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'simonamarin' ) . '</span> <span class="nav-title">%title</span>',
				)
			);

			/*
			 * DECIZIE (2026-09-21): comentariile sunt dezactivate pe tot site-ul,
			 * la cererea clientei. Motivele sunt descrise in PSY-11 (comments.php):
			 * pe un site de cabinet de psihoterapie, comentariile publice expun
			 * persoane care relateaza experiente personale si creeaza un cadru in
			 * care nu se poate raspunde responsabil.
			 * Apelul la comments_template() a fost eliminat de aici. Blocarea
			 * efectiva se face prin filtrele din functions.php - vezi
			 * simonamarin_disable_comments(). Comentariile deja existente NU au
			 * fost sterse din baza de date, doar nu mai sunt afisate.
			 * REZOLVATE prin aceasta modificare: PSY-11, SEC-09, SEC-10, UX-72,
			 * SEO-33, SEO-34.
			 */

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_sidebar();
get_footer();
