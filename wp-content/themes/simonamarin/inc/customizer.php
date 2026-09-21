<?php
/**
 * simonamarin Theme Customizer
 *
 * @package simonamarin
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - inc/customizer.php
 * ============================================================================
 *
 * TODO [SEO-62][HIGH]: Customizer-ul nu expune niciun camp pentru datele de
 * business. Fara ele, footerul nu poate afisa NAP (nume, adresa, telefon) si
 * nu se poate genera JSON-LD LocalBusiness / Organization fara hardcodare.
 * Fix: o sectiune noua "Date firma" cu campuri: nume legal, adresa, oras,
 *      telefon, email, program, latitudine/longitudine, profiluri sociale
 *      (sameAs). Aceste valori alimenteaza apoi SEO-01 si SEO-17.
 *      Toate campurile se sanitizeaza cu sanitize_text_field / esc_url_raw.
 *
 * TODO [SEO-63][MEDIUM]: Footerul are link-uri externe hardcodate (vezi SEO-16
 * din footer.php). Inlocuirea lor cu un copyright propriu are nevoie de un camp
 * de Customizer, altfel orice modificare cere editare de cod.
 *
 * TODO [SEO-64][LOW]: Partial-ul de selective refresh pentru 'blogdescription'
 * randeaza `bloginfo( 'description' )` neescapat, la fel ca in header.php
 * (vezi SEO/SEC-06). Fixul de escapare trebuie aplicat in AMBELE locuri,
 * altfel preview-ul din Customizer ramane vulnerabil.
 */

function simonamarin_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	// TODO [DEAD-07][MEDIUM]: Linia 'header_textcolor' de mai jos depinde de
	// functionalitatea custom-header, care nu este afisata nicaieri pe frontend
	// (vezi inc/custom-header.php). Daca se ia decizia de a sterge custom-header,
	// se sterge si aceasta linie, impreuna cu handler-ul wp.customize(
	// 'header_textcolor' ) din js/customizer.js.
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial(
			'blogname',
			array(
				'selector'        => '.site-title a',
				'render_callback' => 'simonamarin_customize_partial_blogname',
			)
		);
		$wp_customize->selective_refresh->add_partial(
			'blogdescription',
			array(
				'selector'        => '.site-description',
				'render_callback' => 'simonamarin_customize_partial_blogdescription',
			)
		);
	}
}
add_action( 'customize_register', 'simonamarin_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function simonamarin_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function simonamarin_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function simonamarin_customize_preview_js() {
	wp_enqueue_script( 'simonamarin-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), _S_VERSION, true );
}
add_action( 'customize_preview_init', 'simonamarin_customize_preview_js' );
