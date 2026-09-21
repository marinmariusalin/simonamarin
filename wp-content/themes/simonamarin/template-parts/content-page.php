<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-27][HIGH]: Titlul paginii este emis automat ca <h1>, chiar si pe
 * paginile construite cu blocuri/page builder care au deja propriul H1 in
 * continut (foarte frecvent pe homepage si pe landing pages).
 * Impact: doua <h1> pe aceeasi pagina -> ierarhie de titluri ambigua.
 * Fix (fara a atinge continutul): optiune in customizer sau conditie pe slug/
 * template pentru a ascunde .entry-header pe paginile care isi au propriul H1.
 * Verifica intai in browser, pagina cu pagina, unde exista dublura.
 */
?>

<?php
/*
 * TODO [UX-50][HIGH]: Paginile nu au breadcrumbs. Pe mobil, unde meniul e
 * ascuns in spatele unui buton, breadcrumb-ul este principalul indiciu de
 * "unde ma aflu" si singura cale rapida catre nivelul superior - mai ales
 * pentru vizitatorii care intra direct dintr-o cautare Google, nu prin
 * homepage. Vezi SEO-02 din header.php pentru implementare (Rank Math).
 *
 * TODO [UX-51][HIGH]: Pagina se termina brusc, dupa continut, fara niciun pas
 * urmator. Pentru un site de prezentare, fiecare pagina ar trebui sa se
 * incheie cu o actiune (contact, programare, pagina urmatoare din flux).
 * Fix: o sectiune de CTA in template, dupa .entry-content - element de
 * interfata, deci NU se atinge textul paginii.
 *
 * TODO [UX-52][MEDIUM]: Link-ul de editare (.edit-link) se afiseaza pentru
 * utilizatorii logati direct in footerul paginii publice, fara stil. Pe
 * mobil, cand esti logat ca admin, apare ca un link razlet, usor de apasat
 * din greseala. Fix cosmetic: stilizeaza-l ca buton discret sau bazeaza-te
 * doar pe bara de admin.
 *
 * TODO [UX-53][LOW]: wp_link_pages afiseaza "Pages:" in engleza, netradus,
 * si numerele nu au stil de buton (vezi UX-40 din style.css). Se aplica doar
 * daca exista pagini impartite cu <!--nextpage--> - verifica inainte.
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<?php simonamarin_post_thumbnail(); ?>

	<?php
	/*
	 * TODO [PUB-22][MEDIUM]: Acelasi lucru ca la articole (vezi PUB-21 din
	 * template-parts/content.php): .entry-content nu are container cu latime
	 * declarata, deci o pagina noua construita din blocuri nu poate folosi
	 * "Latime mare" / "Latime completa" si nu are o masura de citire garantata.
	 * Pe pagini problema este chiar mai vizibila decat pe articole, pentru ca
	 * paginile de prezentare sunt cele construite cu sectiuni late.
	 * Fix: acelasi - latime pe .entry-content in CSS (PUB-12), corelata cu
	 * layout.contentSize / wideSize din theme.json (PUB-01).
	 */
	?>
	<?php
	/*
	 * TODO [PSY-15][CRITICAL]: Din perspectiva cuiva care ia in calcul terapia,
	 * intrebarile care blocheaza primul contact sunt mereu aceleasi, si toate
	 * tin de NECUNOSCUT, nu de competenta terapeutului:
	 *   - cum decurge concret o prima sedinta, ce se intampla acolo;
	 *   - cat dureaza, cat costa, cum se plateste;
	 *   - ce se intampla daca anulez sau intarzii;
	 *   - online sau la cabinet, si care e diferenta;
	 *   - cat timp dureaza, in general, un proces terapeutic;
	 *   - ce inseamna confidentialitatea, concret.
	 * VERIFICA in Pages ce exista deja si ce lipseste. Ce lipseste se scrie de
	 * catre terapeuta - dar structura de navigatie catre ele este treaba temei
	 * si trebuie pregatita (meniu, link-uri din pagini, CTA-uri).
	 *
	 * TODO [PSY-16][HIGH]: Afisarea transparenta a tarifelor reduce anxietatea
	 * de prag mai mult decat orice alt element de pe site si scuteste ambele
	 * parti de o conversatie incomoda. Absenta pretului este citita, aproape
	 * intotdeauna, ca "probabil prea scump pentru mine".
	 * Decizia de a publica tarifele apartine terapeutei; rolul temei este sa
	 * existe un loc bine plasat pentru ele.
	 *
	 * TODO [PSY-17][MEDIUM]: Pagina se termina fara pas urmator (vezi UX-51).
	 * Pe acest tip de site, pasul urmator trebuie sa fie mereu cel mai mic
	 * posibil: nu "programeaza 10 sedinte", ci "scrie-mi un rand". Un singur
	 * CTA per pagina - doua optiuni alaturate produc ezitare exact acolo unde
	 * ezitarea e deja mare.
	 */
	?>
	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'simonamarin' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
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
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
