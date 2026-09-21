<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 *
 * ============================================================================
 * FISIER NEFOLOSIT incepand cu 2026-09-21.
 *
 * Comentariile au fost dezactivate pe tot site-ul, la cererea clientei.
 * Apelurile la comments_template() au fost eliminate din single.php si
 * page.php, iar inchiderea efectiva se face prin simonamarin_disable_comments()
 * din functions.php.
 *
 * Fisierul este pastrat intentionat, nu sters: daca decizia se schimba, se
 * restaureaza apelurile din cele doua template-uri si se sterge functia din
 * functions.php. Underscores il considera oricum un template obligatoriu.
 *
 * TODO-urile de mai jos raman ca documentatie a motivelor deciziei, dar NU mai
 * sunt de implementat: PSY-11, PSY-12, SEC-09, SEC-10, SEO-33, SEO-34, UX-72.
 * ============================================================================
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

/*
 * TODO [SEO-33][MEDIUM]: Comentariile nu au rel="ugc nofollow" pe link-urile din
 * corpul lor si nici pe link-ul de autor. WordPress adauga nofollow pe URL-ul
 * autorului, dar nu si pe link-urile puse in textul comentariului.
 * Impact: spam de link-uri care scurge autoritate catre site-uri terte.
 * Fix: filtru pe 'comment_text' care adauga rel="nofollow ugc" pe fiecare <a>.
 *
 * TODO [SEO-34][LOW]: Comentariile paginate (the_comments_navigation) creeaza
 * URL-uri /comment-page-2/ indexabile, duplicat al articolului.
 * Fix: verifica in Rank Math sa fie noindex sau dezactiveaza paginarea
 * comentariilor din Settings > Discussion.
 *
 * TODO [SEC-09][MEDIUM]: Formularul de comentarii (comment_form) nu are protectie
 * anti-spam in tema. Akismet NU este instalat (vezi lista de plugin-uri).
 * Impact: comentarii spam cu link-uri = risc de penalizare pentru link-uri de
 * proasta calitate + munca de moderare.
 * Fix: daca articolele nu au nevoie de comentarii, dezactiveaza-le complet din
 * Settings > Discussion - e cea mai sigura optiune pentru un site de prezentare.
 *
 * TODO [SEC-10][LOW]: Comentatorii nelogati primesc cookie-uri de identificare
 * (comment_author_*). Cu cookie-law-info instalat, acestea trebuie declarate in
 * politica de cookies - altfel e o problema GDPR.
 * WordPress ofera deja checkbox-ul de consimtamant (WP 4.9.6+) - verifica sa fie
 * activ in Settings > Discussion.
 */
?>

<?php
/*
 * TODO [PSY-11][CRITICAL]: Comentariile deschise sub articole despre sanatate
 * mintala sunt o problema, nu o functionalitate. Pe un articol despre
 * anxietate sau depresie, oamenii relateaza public experiente personale,
 * adesea sub numele lor real si fara sa realizeze ca raman indexate de Google
 * pe termen nelimitat. Consecinte concrete pentru cabinet:
 *   - expunerea celui care scrie, pe care el nu o poate anticipa;
 *   - o relatie publica terapeut-comentator, cu ambiguitati de rol si de
 *     limite;
 *   - obligatia implicita de a raspunde, inclusiv unor mesaje care pot
 *     semnala risc - iar un comentariu NU este un cadru in care se poate
 *     evalua sau gestiona riscul;
 *   - a raspunde public la o descriere de simptome se apropie periculos de
 *     consiliere in afara cadrului.
 * RECOMANDARE FERMA: dezactiveaza comentariile pe tot site-ul (Settings >
 * Discussion) si inchide-le pe articolele existente. Daca exista deja
 * comentarii cu continut personal, evalueaza retragerea lor - vezi
 * PSY-12 inainte de a sterge ceva.
 * Coincide cu SEC-09 si UX-72, din motive diferite - toate trei converg spre
 * aceeasi decizie.
 *
 * TODO [PSY-12][HIGH]: DACA se sterg comentarii existente, asta inseamna
 * atingerea unui continut creat de utilizatori - se face doar cu acordul
 * explicit al terapeutei si din interfata WordPress, niciodata direct din
 * baza de date. Conform regulii de proiect, continutul nu se modifica din
 * cod. Aici, decizia este a ei, nu a implementarii.
 */
?>
<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$simonamarin_comment_count = get_comments_number();
			if ( '1' === $simonamarin_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'simonamarin' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf( 
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $simonamarin_comment_count, 'comments title', 'simonamarin' ) ),
					number_format_i18n( $simonamarin_comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
				)
			);
			?>
		</ol><!-- .comment-list -->

		<?php
		the_comments_navigation();

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'simonamarin' ); ?></p>
			<?php
		endif;

	endif; // Check for have_comments().

	/*
	 * TODO [UX-72][MEDIUM]: comment_form() este afisat cu stilurile default
	 * Underscores, deci mosteneste campurile de 24px inaltime descrise in
	 * UX-20 (style.css). Formularul implicit cere nume, email si site web -
	 * trei campuri pe mobil inainte de a putea scrie ceva.
	 * Daca se pastreaza comentariile: scoate campul "website" (atrage spam si
	 * nu foloseste nimanui), pune textarea prima si campurile de identificare
	 * dupa.
	 * Daca nu se pastreaza (recomandat pentru un site de prezentare - vezi
	 * SEC-09), intreaga zona dispare si nu mai e nimic de stilizat.
	 */
	comment_form();
	?>

</div><!-- #comments -->
