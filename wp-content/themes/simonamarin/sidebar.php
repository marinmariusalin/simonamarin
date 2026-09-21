<?php
/**
 * The sidebar containing the main widget area
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package simonamarin
 */

/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - sidebar.php
 * ============================================================================
 *
 * TODO [SEO-45][HIGH]: Sidebar-ul este identic pe TOATE tipurile de continut
 * (articole, pagini, arhive, cautare). Continut boilerplate repetat pe fiecare
 * URL creste raportul cod/continut util si poate declansa evaluari de
 * "thin content" pe paginile scurte.
 * Fix: sidebar conditionat - `if ( is_singular( 'post' ) )` pentru blog,
 *      fara sidebar pe paginile de conversie (contact, servicii).
 *
 * TODO [SEO-46][MEDIUM]: <aside> nu are aria-label si nici rol semantic
 * explicit. Pentru Google, un <aside> etichetat ajuta la separarea continutului
 * principal de cel secundar (util si pentru extragerea snippet-urilor).
 * Fix: <aside id="secondary" class="widget-area"
 *          aria-label="<?php esc_attr_e( 'Sidebar', 'simonamarin' ); ?>">
 *
 * TODO [SEO-47][MEDIUM]: Widget-ul "Meta" (default in WordPress) publica
 * link-uri catre /wp-login.php si feed-uri RSS pe fiecare pagina. Sunt link-uri
 * interne inutile, crawl budget irosit si o expunere gratuita a paginii de
 * login.
 * VERIFICA: Appearance > Widgets - daca "Meta" este activ, sterge-l.
 *
 * TODO [SEO-48][LOW]: Daca sidebar-ul contine un widget de tip lista de
 * articole cu imagini, acestea sunt inserate fara `loading="lazy"` controlat de
 * tema si fara dimensiuni potrivite (vezi SEO-04 - lipsesc add_image_size).
 * Impact: imagini supradimensionate incarcate sub fold pe mobil.
 */

/*
 * TODO [UX-41][MEDIUM]: Sidebar-ul este randat dupa <main>, la finalul paginii.
 * Pe mobil (unde nu exista coloana laterala, vezi UX-27 din style.css) asta
 * inseamna ca tot ce pui aici apare dupa continut, unde ajung foarte putini
 * vizitatori. Nu este o greseala de cod - este prioritizarea corecta
 * mobile-first - dar are o consecinta de design care trebuie asumata:
 * SIDEBAR-UL NU ESTE UN LOC BUN PENTRU CTA, TELEFON SAU FORMULAR.
 * Verifica intai daca 'sidebar-1' are widget-uri asignate (Appearance >
 * Widgets). Daca e gol, functia iese imediat si nu se randeaza nimic - caz in
 * care intreaga discutie despre sidebar e teoretica si nu merita efort.
 * Daca este folosit: muta informatia de conversie in continut sau in footer.
 */
if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<aside id="secondary" class="widget-area">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside><!-- #secondary -->
