<?php
/**
 * Plugin Name: Simona Marin - reCAPTCHA doar langa formular
 * Description: Scriptul Google reCAPTCHA se incarca doar pe paginile care afiseaza efectiv un formular Contact Form 7, nu pe tot site-ul.
 *
 * De ce exista:
 *
 * Contact Form 7 pune reCAPTCHA v3 pe FIECARE pagina (hook-ul
 * wpcf7_recaptcha_enqueue_scripts nu verifica daca pagina are formular). Pe
 * 23.09.2026, Home, Servicii si Blog il incarcau fara sa aiba niciun formular.
 *
 * 1. Greutatea. api.js aduce ~350 KB de JavaScript de la Google, pentru fiecare
 *    vizitator, pe fiecare pagina.
 *
 * 2. Confidentialitatea. reCAPTCHA v3 trimite la Google date despre vizita
 *    (IP, cookie-uri, comportament) de indata ce se incarca, inainte de orice
 *    alegere in bannerul de cookie-uri. Pe un site de psihoterapie, faptul ca
 *    cineva citeste o pagina anume e deja o informatie sensibila. Langa
 *    formular, protectia anti-spam are un temei; pe restul site-ului, nu.
 *
 * Cum: scriptul ramane inregistrat, dar e scos din coada la incarcare si pus
 * inapoi doar cand CF7 randeaza un formular (filtrul wpcf7_form_elements
 * ruleaza numai la afisare). Scripturile reCAPTCHA sunt in subsol, deci sunt
 * tiparite dupa continut - cand formularul a fost deja randat. Asa functioneaza
 * oriunde apare formularul (pagina, articol, widget), fara lista de pagini
 * scrisa de mana care s-ar dezactualiza.
 *
 * Nu am folosit wpcf7_contact_form: acela ruleaza in constructorul formularului,
 * deci si cand formularul e doar citit din baza de date, nu afisat.
 *
 * @package simonamarin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scoate reCAPTCHA din coada; CF7 il pune la prioritatea 20.
 */
function simonamarin_recaptcha_defer_until_form() {
	wp_dequeue_script( 'wpcf7-recaptcha' );
}
add_action( 'wp_enqueue_scripts', 'simonamarin_recaptcha_defer_until_form', 21 );

/**
 * Pune reCAPTCHA inapoi in coada cand pe pagina se randeaza un formular.
 *
 * `google-recaptcha` vine automat, ca dependenta a lui `wpcf7-recaptcha`.
 * wp_enqueue_script nu face nimic daca scriptul nu e inregistrat (reCAPTCHA
 * neconfigurat in CF7), deci nu e nevoie de alta verificare.
 *
 * @param string $html Campurile formularului.
 * @return string Neschimbat.
 */
function simonamarin_recaptcha_enqueue_with_form( $html ) {
	wp_enqueue_script( 'wpcf7-recaptcha' );
	return $html;
}
add_filter( 'wpcf7_form_elements', 'simonamarin_recaptcha_enqueue_with_form' );
