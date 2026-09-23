<?php
/**
 * Plugin Name: Simona Marin - livrarea mesajelor din formularul de contact
 * Description: Fixeaza serverul SMTP folosit de Post SMTP si destinatarul formularului de contact. Pe site-ul local, mailurile raman in Mailpit.
 * Version: 1.0.0
 * Author: Cabinet Individual de Psihologie Simona Marin
 *
 * DE CE EXISTA ACEST FISIER
 *
 * Intre 01.02.2025 si momentul copierii bazei de date pe local (21.09.2026),
 * toate mailurile trimise din site au esuat cu "Connection timed out". Asa s-au
 * pierdut si mesajele trimise din formularul de contact. Cauza: Post SMTP era
 * setat pe gazda "simonamarin.ro", portul 465. Domeniul trece de atunci prin
 * Cloudflare, iar Cloudflare transmite doar trafic web, nu SMTP. Conexiunea
 * pleca spre Cloudflare si nu primea niciun raspuns.
 *
 * Serverul de mail real al gazduirii este hv112.c-f.ro. MX-ul domeniului
 * indica spre el, iar certificatul lui TLS este emis pe acest nume. Autentificarea
 * cu contul contactform@simonamarin.ro a fost verificata pe 23.09.2026.
 *
 * DE CE IN COD SI NU DOAR IN SETARI
 *
 * Deploy-ul se face cu Duplicator: baza de date locala inlocuieste complet pe
 * cea de productie. O setare corectata doar din panoul de administrare de pe
 * productie s-ar pierde la urmatorul deploy. O setare corectata doar local ar
 * trimite mailuri reale in inboxul cabinetului la fiecare test facut local.
 * Filtrul de mai jos alege serverul dupa domeniul pe care ruleaza site-ul si
 * are prioritate peste ce este salvat in baza de date.
 *
 * DOMENIU
 *
 * Mesajele din formular sunt date de sanatate (GDPR art. 9) si vin de la
 * oameni care cer ajutor. Un mesaj pierdut inseamna o persoana care asteapta
 * un raspuns care nu vine. De aceea destinatarul este fixat si in cod.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Adresa la care trebuie sa ajunga mesajele din formularul de contact. */
const SIMONAMARIN_CONTACT_RECIPIENT = 'psihologsimonamarin@gmail.com';

/**
 * True doar pe site-ul public. Orice alt domeniu (simonamarin.local, o copie
 * de test) este tratat ca mediu local, ca sa nu trimita mailuri reale.
 */
function simonamarin_is_production() {
	$host = wp_parse_url( home_url(), PHP_URL_HOST );
	return in_array( $host, array( 'simonamarin.ro', 'www.simonamarin.ro' ), true );
}

/*
 * Serverul SMTP folosit de Post SMTP. Post SMTP isi citeste setarile cu
 * get_option( 'postman_options' ), deci filtrul option_ se aplica peste tot:
 * la trimitere, in jurnal si in ecranul de setari. Utilizatorul si parola
 * raman cele salvate in baza de date.
 */
add_filter(
	'option_postman_options',
	function ( $options ) {
		if ( ! is_array( $options ) ) {
			return $options;
		}

		if ( simonamarin_is_production() ) {
			$options['transport_type'] = 'smtp';
			$options['hostname']       = 'hv112.c-f.ro';
			$options['port']           = 465;
			$options['enc_type']       = 'ssl';
			$options['auth_type']      = 'login';
		} else {
			// Mailpit-ul din Local (portul SMTP din sites.json). Prinde
			// mailurile local si le arata la http://localhost:10000/,
			// fara sa le trimita mai departe.
			$options['transport_type'] = 'smtp';
			$options['hostname']       = '127.0.0.1';
			$options['port']           = 10001;
			$options['enc_type']       = 'none';
			$options['auth_type']      = 'none';
		}

		// Nu exista un al doilea server valid configurat. Un fallback gol ar
		// ascunde erorile in loc sa le rezolve.
		$options['fallback_smtp_enabled'] = 'no';

		return $options;
	}
);

/*
 * Destinatarul mailului principal al formularelor Contact Form 7 (nu si al
 * mailului de confirmare catre vizitator, "mail_2", care e dezactivat).
 * Adresa este deja salvata corect in formular. Filtrul o pastreaza asa si
 * daca formularul e editat sau inlocuit din greseala.
 */
add_filter(
	'wpcf7_mail_components',
	function ( $components, $contact_form, $mail ) {
		if ( $mail instanceof WPCF7_Mail && 'mail' === $mail->name() ) {
			$components['recipient'] = SIMONAMARIN_CONTACT_RECIPIENT;
		}
		return $components;
	},
	10,
	3
);

/*
 * SEC-13 - Jurnalul Post SMTP nu mai pastreaza continutul mesajelor din formular.
 *
 * GASIT PE 23.09.2026: Post SMTP salveaza in tabelul `post_smtp_logs` fiecare
 * mail INTEGRAL - subiectul, textul, headerele, adresa de raspuns. Pentru
 * mesajele din formular inseamna ca tot ce a scris un om care cere ajutor
 * (date de sanatate, GDPR art. 9) ramane in baza de date a site-ului, fara
 * termen, si pleaca odata cu ea in fiecare copie de siguranta si pachet
 * Duplicator. Mesajul ajunge oricum in inboxul cabinetului; copia din site nu
 * foloseste la nimic.
 *
 * Jurnalul in sine ramane pornit: el a aratat ca mailurile esuau din 2025
 * ("Connection timed out"). Pentru un mesaj din formular se pastreaza doar ce
 * trebuie ca sa se vada daca livrarea a reusit - data, starea, eroarea,
 * destinatarul cabinetului - si se golesc subiectul (e scris de vizitator),
 * textul, headerele, adresa de raspuns si transcrierea SMTP.
 *
 * Mesajul din formular se recunoaste prin momentul trimiterii, nu dupa
 * continut: Contact Form 7 anunta inceputul (`wpcf7_before_send_mail`) si
 * sfarsitul (`wpcf7_mail_sent` / `wpcf7_mail_failed`), iar orice mail salvat
 * in jurnal intre ele este al formularului.
 */
function simonamarin_cf7_sending( $set = null ) {
	static $sending = false;
	if ( null !== $set ) {
		$sending = (bool) $set;
	}
	return $sending;
}
add_action( 'wpcf7_before_send_mail', static function () { simonamarin_cf7_sending( true ); }, 1 );
add_action( 'wpcf7_mail_sent', static function () { simonamarin_cf7_sending( false ); } );
add_action( 'wpcf7_mail_failed', static function () { simonamarin_cf7_sending( false ); } );

add_action(
	'post_smtp_after_email_log_saved',
	function ( $log_id ) {
		global $wpdb;

		if ( ! simonamarin_cf7_sending() || ! $log_id ) {
			return;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- tabelul propriu al Post SMTP, fara API de actualizare.
		$wpdb->update(
			$wpdb->prefix . 'post_smtp_logs',
			array(
				'original_subject'   => 'Mesaj din formularul de contact (continut nesalvat)',
				'original_message'   => '',
				'original_headers'   => '',
				'reply_to_header'    => '',
				'session_transcript' => '',
			),
			array( 'id' => (int) $log_id ),
			'%s',
			'%d'
		);
	}
);
