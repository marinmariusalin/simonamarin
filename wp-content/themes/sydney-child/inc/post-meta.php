<?php
/**
 * Fara linia de meta sub titlul articolelor ("De simonamarin_admin",
 * data, "Uncategorized").
 *
 * De ce: decizia utilizatorului. In plus, linia afisa numele de utilizator
 * al contului de administrare (cu legatura spre /author/...), data de
 * publicare a unor texte care nu sunt legate de o data, si categoria
 * implicita "Uncategorized" - nimic din ele nu ajuta un vizitator.
 *
 * De ce prin filtru si nu prin Customizer (`hide_meta_single`) sau un
 * content-single.php copiat: optiunea din Customizer sta in baza de date si
 * poate fi resetata la o reimportare de setari; un sablon copiat se rupe la
 * urmatorul update Sydney. Filtrul e cel folosit chiar de Sydney pentru
 * integrarile LearnDash/LifterLMS.
 *
 * Atentie la nume: `sydney_single_post_meta_enable` DEZACTIVEAZA meta cand
 * intoarce true (vezi sydney_single_post_meta() in inc/template-tags.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'sydney_single_post_meta_enable', '__return_true' );
