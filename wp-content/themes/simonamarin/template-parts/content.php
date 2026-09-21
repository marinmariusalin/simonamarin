<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package simonamarin
 */

/*
 * TODO [SEO-24][HIGH]: Acest template part este folosit si in listari (index,
 * archive), unde afiseaza the_content() COMPLET, nu un excerpt.
 * Impact: continut duplicat masiv - acelasi text apare pe homepage, pe arhiva de
 * categorie, pe arhiva de tag si pe pagina articolului. Google trebuie sa aleaga
 * singur canonicalul, si de multe ori alege gresit.
 * Fix: in listari foloseste the_excerpt(); pastreaza the_content() doar pentru
 * is_singular(). Nota: asta NU modifica textul articolelor, doar cat se afiseaza.
 *
 * TODO [SEO-25][MEDIUM]: Nu exista niciun marcaj de autor/data in afara textului
 * simplu (.entry-meta). Fara schema Article + author Person, articolele nu pot
 * castiga rich results si nu beneficiaza de semnalele E-E-A-T.
 */
?>

<?php
/*
 * TODO [UX-46][HIGH]: In listare, ordinea vizuala este: titlu -> meta (data,
 * autor) -> imagine -> continut integral. Pe mobil, meta apare INAINTEA
 * imaginii si a textului, adica informatia cea mai putin utila ocupa spatiul
 * de deasupra foldului. Scanarea unei liste pe telefon se face pe imagine +
 * titlu; data si autorul sunt informatie de context, nu de decizie.
 * Fix: in listare, ordinea recomandata este imagine -> titlu -> rezumat ->
 * meta discreta la final. Este o reordonare de template, nu de continut.
 *
 * TODO [UX-47][HIGH]: Legat de SEO-24 de mai sus, dar din unghi de UX:
 * afisarea continutului integral in listare face homepage-ul/arhivele
 * imposibil de scanat pe telefon - nu poti compara articole cand fiecare
 * ocupa 10 ecrane. Chiar daca SEO nu ar fi o problema, tot ar trebui
 * inlocuit cu excerpt + buton "Citeste mai mult".
 *
 * TODO [UX-48][MEDIUM]: Link-ul "Continue reading" generat de the_content()
 * apare doar daca articolul contine tag-ul <!--more-->. Pe articolele fara
 * el (majoritatea, daca au fost scrise fara aceasta conventie) nu exista
 * niciun element pe care sa apesi in afara titlului. Trecerea la the_excerpt()
 * (UX-47) rezolva si asta, pentru ca permite un buton constant.
 * Nota: butonul nu este continut de articol, este element de interfata.
 *
 * TODO [UX-49][LOW]: .entry-footer afiseaza categorii, tag-uri, link de
 * comentarii si link de editare unul dupa altul, fara separare. Pe mobil,
 * pe un articol cu 6-8 tag-uri, blocul acesta poate fi mai lung decat
 * rezumatul. Considera ascunderea tag-urilor in listare.
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php
		// TODO [SEO-26][MEDIUM]: In listari titlul este <h2>, corect. Dar articolul
		// nu are un <h1> de pagina pe arhive - vezi archive.php, unde titlul de
		// arhiva este deja <h1>. Verifica sa nu existe doua <h1> pe aceeasi pagina
		// dupa ce se rezolva SEO-14 din header.php.
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
			<div class="entry-meta">
				<?php
				simonamarin_posted_on();
				simonamarin_posted_by();
				?>
			</div><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<?php simonamarin_post_thumbnail(); ?>

	<?php
	/*
	 * TODO [PUB-21][MEDIUM]: .entry-content este un simplu <div>, fara nicio
	 * clasa de layout si fara constrangere de latime. Tot ce insereaza autorul
	 * in articol mosteneste latimea parintelui, oricare ar fi ea, iar blocurile
	 * "Latime mare" / "Latime completa" nu au fata de ce sa fie mai late.
	 * Aici se vede efectul lipsei contractului descris in PUB-01: articolul nu
	 * are un container cu latime cunoscuta, deci fiecare articol si-o defineste
	 * singur, prin stil inline.
	 * Fix (fara a atinge textul articolelor): defineste latimea pe .entry-content
	 * in CSS (PUB-12) si aliniaz-o cu layout.contentSize / wideSize din
	 * theme.json. Daca se opteaza pentru layout-ul de blocuri, se poate folosi si
	 * clasa generata de WordPress (wp_get_layout_style / suportul de layout), dar
	 * nu inainte ca theme.json sa existe.
	 */
	?>
	<div class="entry-content">
		<?php
		the_content(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'simonamarin' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				wp_kses_post( get_the_title() )
			)
		);

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'simonamarin' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php
	/*
	 * TODO [PSY-13][HIGH]: Articolele despre depresie, anxietate, trauma sau
	 * suicid au nevoie de un tratament editorial pe care tema nu il sustine
	 * acum in niciun fel. Elemente de template (nu de continut) care lipsesc:
	 *   - un bloc de resurse de criza AFISAT LA INCEPUTUL articolului, nu la
	 *     final, pe articolele din categoriile sensibile. Cine citeste in
	 *     suferinta rareori ajunge la final;
	 *   - un sumar / cuprins pentru textele lungi, ca cititorul sa poata sari
	 *     direct la ce il priveste si sa evite ce nu vrea sa citeasca - control
	 *     asupra expunerii, nu doar comoditate;
	 *   - timpul estimat de citire, care seteaza asteptarea inainte de scroll.
	 * Implementare curata: conditionat pe categorie, in template. Textul
	 * articolelor ramane neatins.
	 *
	 * TODO [PSY-14][MEDIUM]: Tag-urile si categoriile afisate in .entry-footer
	 * devin, pe acest site, etichete de tipul "depresie", "atac de panica".
	 * Ele sunt utile pentru navigare (vezi PSY-18, categoriile ca structura
 * principala de navigare), dar afisate ca lista lunga
	 * la finalul fiecarui articol arata ca o taxonomie de blog, nu ca un ghid.
	 * Fix: in listari ascunde tag-urile (vezi UX-49); pe articol, prezinta-le
	 * ca "Citeste si despre:" cu 2-3 legaturi relevante.
	 */
	?>
	<footer class="entry-footer">
		<?php simonamarin_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
