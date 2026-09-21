<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package simonamarin
 */

if ( ! function_exists( 'simonamarin_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	/*
	 * TODO [UX-73][MEDIUM]: Sirul "Posted on %s" si "by %s" (functia urmatoare)
	 * sunt in engleza. Vezi UX-69 din functions.php - tema nu are traducere in
	 * romana incarcata, deci toate etichetele generate de tema apar in engleza
	 * pe un site in romana. Este cea mai vizibila si mai ieftin de reparat
	 * problema de perceptie a calitatii.
	 *
	 * TODO [UX-74][LOW]: Data este afisata in formatul configurat in WordPress,
	 * ca data absoluta. Pentru articole recente, o forma relativa ("acum 3
	 * zile") este mai usor de interpretat; pentru continut vechi, data absoluta
	 * e mai onesta. Alege in functie de cat de des se publica.
	 * Atentie: continutul vechi cu data vizibila poate reduce increderea -
	 * solutia corecta nu este ascunderea datei, ci actualizarea articolului.
	 */
	function simonamarin_posted_on() {
		/*
		 * TODO [SEO-21][MEDIUM]: Data publicarii este linkuita catre permalink-ul
		 * propriului articol (vezi $posted_on mai jos) - un self-link inutil, care
		 * nu aduce nimic si polueaza graful de link-uri interne.
		 * Fix: scoate <a> si lasa doar <time>.
		 *
		 * TODO [SEO-22][MEDIUM]: Marcajele hentry/published/updated sunt
		 * microformate vechi (hAtom). Google foloseste azi JSON-LD.
		 * datePublished/dateModified trebuie sa existe in schema Article - verifica
		 * ce emite Rank Math si asigura-te ca dateModified reflecta modificarile
		 * reale (semnal de prospetime a continutului).
		 */
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( DATE_W3C ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( DATE_W3C ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date. */
			esc_html_x( 'Posted on %s', 'post date', 'simonamarin' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		echo '<span class="posted-on">' . $posted_on . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'simonamarin_posted_by' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function simonamarin_posted_by() {
		$byline = sprintf(
			/* translators: %s: post author. */
			esc_html_x( 'by %s', 'post author', 'simonamarin' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
endif;

if ( ! function_exists( 'simonamarin_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function simonamarin_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ', ', 'simonamarin' ) );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'simonamarin' ) . '</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'simonamarin' ) );
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'simonamarin' ) . '</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'simonamarin' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					wp_kses_post( get_the_title() )
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'simonamarin' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'simonamarin_post_thumbnail' ) ) :
	/**
	 * Displays an optional post thumbnail.
	 *
	 * Wraps the post thumbnail in an anchor element on index views, or a div
	 * element when on single views.
	 */
	function simonamarin_post_thumbnail() {
		if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
			return;
		}

		if ( is_singular() ) :
			?>

			<div class="post-thumbnail">
				<?php
				/*
				 * TODO [PERF-10][CRITICAL]: Imaginea reprezentativa de pe single/page
				 * este de regula elementul LCP. the_post_thumbnail() fara argumente:
				 *   - lasa WordPress sa adauge loading="lazy" (din WP 5.5), ceea ce
				 *     INTARZIE fix imaginea care ar trebui incarcata prima;
				 *   - nu seteaza fetchpriority="high";
				 *   - foloseste marimea 'post-thumbnail' (de obicei 1568px sau
				 *     dimensiunea cropata default), nu una potrivita layout-ului.
				 * Impact direct pe Core Web Vitals (LCP) => factor de ranking.
				 * Fix: the_post_thumbnail( 'large', array(
				 *          'loading' => 'eager', 'fetchpriority' => 'high',
				 *          'decoding' => 'async', 'sizes' => '(max-width: 768px) 100vw, 1200px'
				 *      ) );
				 *
				 * TODO [PERF-11][MEDIUM]: Pluginurile image-prioritizer /
				 * optimization-detective / webp-uploads sunt instalate si pot deja
				 * corecta fetchpriority si formatul WebP. Verifica in HTML-ul live
				 * inainte de a implementa manual, ca sa nu intre in conflict.
				 *
				 * TODO [SEO-19][HIGH]: Alt-textul imaginii vine din Media Library.
				 * Fa un audit al atasamentelor - imaginile fara alt nu apar in
				 * Google Images si sunt problema de accesibilitate.
				 */
				the_post_thumbnail();
				?>
			</div><!-- .post-thumbnail -->

		<?php else : ?>

			<?php
			/*
			 * TODO [UX-75][MEDIUM]: In listari, imaginea este invelita intr-un link
			 * cu aria-hidden="true" si tabindex="-1" - corect pentru screen
			 * readers (evita link duplicat), dar inseamna ca imaginea NU are niciun
			 * indiciu vizual de interactivitate: fara cursor: pointer, fara
			 * hover/zoom, fara focus. Pe mobil hover nu conteaza, dar utilizatorii
			 * de telefon apasa instinctiv pe imagine - deci link-ul e bun, doar
			 * afisarea nu confirma ca se poate apasa.
			 * Fix (CSS, vezi UX-37): aspect-ratio + object-fit + o stare de
			 * apasare (:active) vizibila.
			 */
			?>
			<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
				<?php
					/*
					 * TODO [SEO-20][HIGH]: Argumentul 'alt' de mai jos SUPRASCRIE
					 * alt-textul real al imaginii cu titlul articolului.
					 * Impact: fiecare imagine din listare primeste un alt generic,
					 * identic cu titlul deja prezent in <h2> de deasupra. Se pierde
					 * alt-textul descriptiv setat in Media Library -> pierdere de
					 * relevanta in Google Images, si continut duplicat pentru
					 * screen readers (link-ul e oricum aria-hidden).
					 * Fix: scoate complet array-ul cu 'alt' si lasa WordPress sa
					 *      foloseasca _wp_attachment_image_alt.
					 */
					the_post_thumbnail(
						'post-thumbnail',
						array(
							'alt' => the_title_attribute(
								array(
									'echo' => false,
								)
							),
						)
					);
				?>
			</a>

			<?php
		endif; // End is_singular().
	}
endif;

/*
 * TODO [DEAD-08][LOW]: Shim-ul wp_body_open() de mai jos este cod mort - DE STERS.
 *   Motiv: functia wp_body_open() exista in nucleul WordPress incepand cu
 *          versiunea 5.2 (mai 2019). Site-ul ruleaza WordPress 6.x, deci
 *          conditia `! function_exists( 'wp_body_open' )` este intotdeauna
 *          falsa si corpul nu se defineste niciodata.
 *   Atentie: se sterge DOAR blocul de shim de aici (if ... endif), NU si
 *          apelul `wp_body_open()` din header.php linia 47 - acela ruleaza
 *          functia din nucleu si este obligatoriu pentru plugin-uri.
 */

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Shim for sites older than 5.2.
	 *
	 * @link https://core.trac.wordpress.org/ticket/12563
	 */
	function wp_body_open() {
		do_action( 'wp_body_open' );
	}
endif;
