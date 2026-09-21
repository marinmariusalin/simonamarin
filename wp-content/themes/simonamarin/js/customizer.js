/* global wp, jQuery */
/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 *
 * TODO [CRITICAL]: REWRITE WITHOUT JQUERY - Vanilla JS version needed
 * - jQuery is deprecated in WordPress 6.2+
 * - Use wp.customize() API with native DOM methods
 * - Performance improvement: remove jQuery overhead
 *
 * --- AUDIT 2 (2026-09-21) - corectie de prioritate ---
 *
 * NOTA: acest fisier se incarca DOAR in previzualizarea din Customizer
 * (hook `customize_preview_init`), niciodata pe frontend-ul public.
 * Deci impactul asupra performantei reale si asupra SEO este ZERO, iar
 * jQuery este oricum incarcat de WordPress in ecranul de Customizer.
 * Rescrierea ramane utila ca igiena de cod, dar NU este CRITICAL -
 * reprioritizat la LOW. Nu consuma timp pe el inaintea SEO/PERF.
 *
 * TODO [BUG-02][LOW]: Handler-ul pentru header_textcolor scrie stiluri inline pe
 * .site-title / .site-description. Daca in viitor se adauga si un handler de
 * dimensiune sau font, stilurile inline vor bate CSS-ul din tema si vor
 * face previzualizarea sa nu mai corespunda cu rezultatul salvat.
 */

( function( $ ) {
	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$( '.site-title a' ).text( to );
		} );
	} );
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			$( '.site-description' ).text( to );
		} );
	} );

	/*
	 * TODO [DEAD-07][MEDIUM]: Handler-ul de mai jos previzualizeaza 'header_textcolor',
	 * o setare care vine de la add_theme_support( 'custom-header' ) din
	 * inc/custom-header.php. Functionalitatea custom-header nu este afisata nicaieri
	 * pe frontend. Daca se ia decizia de a sterge custom-header, acest bloc devine
	 * cod mort si se sterge odata cu el (impreuna cu linia 'header_textcolor' din
	 * inc/customizer.php).
	 */
	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				$( '.site-title, .site-description' ).css( {
					clip: 'rect(1px, 1px, 1px, 1px)',
					position: 'absolute',
				} );
			} else {
				$( '.site-title, .site-description' ).css( {
					clip: 'auto',
					position: 'relative',
				} );
				$( '.site-title a, .site-description' ).css( {
					color: to,
				} );
			}
		} );
	} );
}( jQuery ) );
