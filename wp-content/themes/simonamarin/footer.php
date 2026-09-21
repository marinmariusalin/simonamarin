<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package simonamarin
 */

?>

	<?php
	/*
	 * TODO [UX-43][HIGH]: Footerul este ultimul punct de decizie al vizitatorului
	 * pe mobil - locul unde ajunge dupa ce a citit tot si unde, daca nu gaseste
	 * nimic de facut, inchide pagina. Acum contine doar doua credite catre
	 * WordPress si Underscores (vezi SEO-16).
	 * Din perspectiva de UX lipsesc, in ordinea importantei:
	 *   1. un CTA clar (buton de contact / tel: / WhatsApp) - pe mobil, tel:
	 *      transforma un tap intr-un apel, cea mai scurta cale spre conversie;
	 *   2. date de contact vizibile (nu doar pe pagina Contact);
	 *   3. link-uri catre paginile legale (obligatorii, cookie-law-info e
	 *      instalat);
	 *   4. link-uri sociale.
	 * Nota: footerul temei NU este continut de pagina, deci se poate construi
	 * fara a atinge textul articolelor sau al paginilor.
	 *
	 * TODO [UX-44][MEDIUM]: Separatorul `<span class="sep"> | </span>` intre
	 * linkuri este un pattern de desktop. Pe 360px cele doua fraze se sparg pe
	 * mai multe randuri, iar bara verticala ramane suspendata la capat de rand.
	 * Fix la rescrierea footerului: elemente de tip lista/flex cu gap, nu
	 * separatoare textuale.
	 *
	 * TODO [UX-45][MEDIUM]: Nu exista buton "inapoi sus". Pe pagini lungi citite
	 * pe telefon, intoarcerea la navigatie inseamna zeci de swipe-uri. Devine
	 * inutil daca se implementeaza headerul sticky (UX-03) - alege UNA dintre
	 * cele doua solutii, nu pe amandoua.
	 */
	?>
	<footer id="colophon" class="site-footer">
		<?php
		/*
		 * TODO [PSY-04][CRITICAL]: Site-ul nu contine NICAIERI informatii pentru
		 * situatii de criza. Pe un site de psihoterapie, o parte din vizitatori
		 * ajung in momente de risc acut, iar un formular de contact cu raspuns
		 * "in 24-48 de ore" nu este un raspuns pentru ei.
		 * Fix: un bloc permanent in footer, pe toate paginile, cu:
		 *   - mesajul explicit ca site-ul si cabinetul NU ofera interventie in
		 *     criza si nu sunt monitorizate permanent;
		 *   - 112 pentru urgente;
		 *   - linia de prevenire a suicidului (TelVerde 0800 801 200, Alianta
		 *     Romana de Preventie a Suicidului) si 116 111 pentru copii.
		 * OBLIGATORIU: verifica numerele si programul de functionare INAINTE de
		 * publicare si recontroleaza-le periodic. Un numar de criza afisat gresit
		 * este mai rau decat absenta lui. Nu prelua numerele din acest comentariu
		 * fara verificare.
		 * Ton si forma: discret, nu alarmist - un rand de text si link-uri, nu un
		 * banner rosu. Rosul si urgenta vizuala activeaza suplimentar (vezi
		 * DS-03).
		 *
		 * TODO [PSY-05][HIGH]: Lipseste disclaimerul profesional: continutul
		 * site-ului are caracter informativ si nu inlocuieste evaluarea sau
		 * tratamentul de specialitate. Relevant mai ales daca exista articole
		 * despre simptome - un vizitator se poate autodiagnostica dupa ele.
		 *
		 * TODO [PSY-06][HIGH]: Lipseste orice mentiune despre confidentialitate.
		 * Este prima intrebare nerostita a oricui ia in calcul terapia.
		 * Fix: o fraza in footer + o pagina dedicata care explica pe scurt
		 * secretul profesional, limitele lui legale, cum sunt tratate datele
		 * trimise prin formular si cat timp sunt pastrate. Vezi PSY-08.
		 *
		 * TODO [PSY-07][MEDIUM]: Footerul este si locul potrivit pentru datele de
		 * atestare din PSY-01 (cod Colegiul Psihologilor, forma de exercitare a
		 * profesiei, CUI). Vezi si SEO-17, care cere acelasi lucru din alt motiv.
		 */
		?>
		<div class="site-info">
			<!-- AUDIT [CRITICAL]: These footer links are hardcoded
			 - Cannot be customized without code edit
			 - Bad for branding flexibility
			 TODO: Convert to theme options/customizer settings -->
			<?php
			/*
			 * TODO [SEO-16][CRITICAL]: Doua link-uri externe dofollow pe FIECARE
			 * pagina din site (wordpress.org si underscores.me).
			 * Impact: scurgere de link equity catre site-uri care nu au nicio
			 * legatura cu subiectul, pe absolut toate paginile. Este cel mai
			 * ieftin castig SEO de pe lista - se rezolva prin stergere.
			 * Fix recomandat: sterge complet ambele link-uri si pune in locul lor
			 * un copyright propriu (fara a modifica continutul paginilor - footerul
			 * temei nu este continut de pagina).
			 * Alternativa minima: rel="nofollow noopener" pe ambele.
			 *
			 * TODO [SEC-07][LOW]: CORECTAT 2026-09-21 - enuntul initial spunea ca
			 * link-ul http:// catre underscores.me genereaza mixed-content warning.
			 * Nu este adevarat: browserele raporteaza mixed content doar pentru
			 * subresurse incarcate de pagina (script, css, img, iframe), nu pentru
			 * destinatia unui <a href>. Un link http ramane doar un redirect in
			 * plus pentru cine il apasa si o scapare de igiena.
			 * In plus, problema dispare oricum daca se aplica SEO-16, care cere
			 * stergerea ambelor link-uri de credit. De rezolvat o singura data,
			 * acolo - aici nu e nimic separat de facut.
			 *
			 * TODO [SEO-17][HIGH]: Footerul nu contine NIMIC util pentru SEO local:
			 * lipsesc NAP (nume, adresa, telefon), program, link-uri catre paginile
			 * legale (Politica de confidentialitate, Termeni, Cookies - obligatorii
			 * si legal, avand cookie-law-info instalat) si link-uri sociale.
			 * Impact: fara NAP consistent nu exista semnal de Local SEO / Google
			 * Business Profile.
			 *
			 * TODO [SEO-18][MEDIUM]: Lipseste wp_nav_menu pentru o locatie
			 * 'menu-footer' - vezi SEO-10 din functions.php.
			 */
			?>
			<a href="<?php echo esc_url( __( 'https://wordpress.org/', 'simonamarin' ) ); ?>">
				<?php
				/* translators: %s: CMS name, i.e. WordPress. */
				printf( esc_html__( 'Proudly powered by %s', 'simonamarin' ), 'WordPress' );
				?>
			</a>
			<?php // TODO [DEAD-10][LOW]: Clasa CSS "sep" de mai jos nu este definita nicaieri - o cautare dupa ".sep" in style.css si style-rtl.css nu returneaza nimic. Separatorul se vede doar pentru ca este caracterul literal " | " din HTML. Fie se sterge atributul class (inutil), fie se adauga o regula reala in style.css. ?>
			<span class="sep"> | </span>
				<?php
				/* translators: 1: Theme name, 2: Theme author. */
				printf( esc_html__( 'Theme: %1$s by %2$s.', 'simonamarin' ), 'simonamarin', '<a href="http://underscores.me/">Underscores.me</a>' );
				?>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
