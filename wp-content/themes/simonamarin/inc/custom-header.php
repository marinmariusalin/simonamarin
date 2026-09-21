<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package simonamarin
 */

/*
 * =============================================================================
 * AUDIT COD MORT (2026-09-21)
 * TODO [DEAD-07][MEDIUM]: FUNCTIONALITATE MOARTA PE FRONTEND - de decis stergere.
 *   Motiv: fisierul inregistreaza add_theme_support( 'custom-header' ), ceea ce
 *          adauga in Customizer panoul "Header Image", dar NICIUN template al
 *          temei nu afiseaza imaginea. `the_header_image_tag()` apare in tot
 *          proiectul o singura data: in exemplul din comentariul de mai sus
 *          (linia 7), care este text, nu cod executat. header.php nu o apeleaza.
 *   Consecinta: utilizatorul poate incarca o imagine de header care nu apare
 *          nicaieri pe site - un bug de UX raportat frecvent pe temele _s.
 *   Doua variante, ambele valide:
 *     (a) SE FOLOSESTE: adauga `the_header_image_tag()` in header.php, in
 *         <header id="masthead">, si atunci fisierul NU se sterge.
 *     (b) NU SE FOLOSESTE: sterge acest fisier + linia `require` din
 *         functions.php. Odata cu el devin moarte si:
 *           - simonamarin_header_style() (hook wp-head-callback de aici),
 *           - transportul postMessage pentru 'header_textcolor' din
 *             inc/customizer.php,
 *           - handler-ul wp.customize( 'header_textcolor' ) din js/customizer.js.
 *   Recomandare: (b), pentru ca site-ul foloseste custom-logo, nu header image.
 * =============================================================================
 */

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses simonamarin_header_style()
 */
/*
 * ============================================================================
 * AUDIT SEO (2026-09-21) - inc/custom-header.php
 * VERIFICA INTAI: header.php NU apeleaza the_header_image_tag() nicaieri, deci
 * imaginea de header nu este afisata in acest moment. Daca ramane asa, fisierul
 * este cod mort. TODO-urile devin relevante doar daca imaginea de header este
 * activata.
 * ============================================================================
 *
 * TODO [SEO-59][MEDIUM]: 'width' => 1000 este prea mic pentru un header
 * full-width pe ecrane retina (2x => 2000px reali). Daca imaginea de header
 * ajunge sa fie elementul LCP, va fi scalata in sus si va arata neclara -
 * Google evalueaza si calitatea vizuala a LCP-ului.
 * Fix: 1920x600 cu 'flex-height' => true, plus srcset prin
 *      the_header_image_tag() (suporta deja atribute custom).
 *
 * TODO [SEO-60][MEDIUM]: the_header_image_tag() emite <img> fara alt
 * descriptiv si fara fetchpriority. Daca imaginea e deasupra foldului, ii
 * trebuie loading="eager" + fetchpriority="high"; daca e decorativa, ii trebuie
 * alt="" explicit, ca sa nu fie citita inutil de screen readere.
 * Fix: filtrul `get_header_image_tag_attributes`.
 *
 * TODO [SEO-61][LOW]: simonamarin_header_style() emite un bloc <style> inline in
 * <head> la fiecare incarcare de pagina. Cu o politica CSP stricta (vezi
 * TODO-urile de securitate din functions.php) stilurile inline necesita
 * 'unsafe-inline' sau un nonce. Alternativa curata: wp_add_inline_style().
 */

function simonamarin_custom_header_setup() {
	add_theme_support(
		'custom-header',
		apply_filters(
			'simonamarin_custom_header_args',
			array(
				'default-image'      => '',
				'default-text-color' => '000000',
				'width'              => 1000,
				'height'             => 250,
				'flex-height'        => true,
				'wp-head-callback'   => 'simonamarin_header_style',
			)
		)
	);
}
add_action( 'after_setup_theme', 'simonamarin_custom_header_setup' );

if ( ! function_exists( 'simonamarin_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see simonamarin_custom_header_setup().
	 */
	function simonamarin_header_style() {
		$header_text_color = get_header_textcolor();

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( get_theme_support( 'custom-header', 'default-text-color' ) === $header_text_color ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
			?>
			.site-title,
			.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
				}
			<?php
			// If the user has set a custom color for the text use that.
		else :
			?>
			.site-title a,
			.site-description {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
		<?php endif; ?>
		</style>
		<?php
	}
endif;
