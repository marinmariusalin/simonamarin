<?php
/**
 * Subsolul site-ului (MIG-03).
 *
 * ---------------------------------------------------------------------------
 * CE S-A PIERDUT SI CE SE REFACE
 * ---------------------------------------------------------------------------
 * Pana la migrarea la Sydney 2.71, subsolul era scris direct in footer.php al
 * temei parinte: linia cabinetului, iconitele de contact, lista de servicii si
 * legatura spre termeni si conditii. La update a disparut tot, iar in locul lui
 * a ramas creditul implicit al temei („Propulsat cu mandrie de Sydney"), cu o
 * legatura iesita spre athemes.com care nu are nicio legatura cu cabinetul.
 *
 * Subsolul e singurul loc prezent pe FIECARE pagina. Paginile de servicii au in
 * Search Console impresii de ordinul zecilor, in timp ce articolele au mii; o
 * legatura interna din subsol le leaga de toate paginile site-ului.
 *
 * ---------------------------------------------------------------------------
 * NICIUN TEXT NOU
 * ---------------------------------------------------------------------------
 * Textele vizibile sunt exclusiv cele care exista deja pe site: etichetele
 * legaturilor sunt titlurile paginilor, citite din baza de date la randare (nu
 * scrise aici), iar linia de copyright foloseste denumirea si formula din
 * subsolul vechi. Singurul element adaugat este anul, generat dinamic.
 *
 * ---------------------------------------------------------------------------
 * DE CE O FUNCTIE PE HOOK-UL `sydney_footer` SI NU UN footer.php COPIAT
 * ---------------------------------------------------------------------------
 * Sydney 2.71 construieste subsolul intr-o singura functie legata de
 * `sydney_footer` (sydney_footer_area). O inlocuiesc pe hook, pastrand exact
 * clasele ei (`site-footer`, `site-info`), deci CSS-ul existent se aplica fara
 * modificari. Un footer.php copiat ar reintra in capcana de la migrare.
 * Daca vreodata se porneste constructorul de antet/subsol al temei, acesta
 * sterge toate actiunile de pe `sydney_footer` - inclusiv pe aceasta - si
 * subsolul trebuie refacut acolo.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Paginile legate din subsol, grupate, prin ID.
 *
 * Prin ID si nu prin URL: un slug schimbat in admin nu rupe legatura, iar o
 * pagina trecuta in ciorna sau stearsa dispare singura din subsol.
 *
 * Criteriul de selectie: paginile din meniul principal, plus paginile despre
 * terapie si terapia online, pe care Search Console le arata aproape invizibile
 * si care sunt formatul principal de lucru. NU sunt incluse:
 *   - Ateliere (1279), scoasa deliberat din meniul principal;
 *   - Terapia EMDR (3575): pagina exista, dar calificarea pentru EMDR nu apare in
 *     lista specializarilor verificate; o legatura sitewide ar prezenta-o ca
 *     serviciu. Se adauga dupa confirmare.
 *
 * @return array[] Grupuri de ID-uri de pagini, cu eticheta pentru cititoarele de ecran.
 */
function simonamarin_footer_groups() {
	return array(
		array(
			'label' => __( 'Servicii', 'sydney-child' ),
			'pages' => array( 192, 1537, 1512, 215 ),
		),
		array(
			'label' => __( 'Cabinet', 'sydney-child' ),
			'pages' => array( 28, 1229, 186, 579, 582 ),
		),
	);
}

/**
 * Linia de copyright.
 *
 * Sydney citeste textul din theme_mod-ul `footer_credits`, cu un implicit care
 * contine creditul temei. Filtrul `theme_mod_footer_credits` e din nucleu, deci
 * supravietuieste update-urilor, iar placeholder-ele {copyright} si {year} sunt
 * inlocuite tot de Sydney - anul nu ramane niciodata inghetat.
 *
 * Formula e cea din subsolul de dinainte de migrare („Toate drepturile rezervate
 * © Cabinet Individual de Psihologie Simona Marin"), cu anul adaugat.
 *
 * Daca in Customizer se scrie vreodata explicit alt text, acela castiga: filtrul
 * inlocuieste doar valoarea implicita, nu o alegere facuta in admin.
 *
 * @param mixed $value Valoarea salvata sau implicita.
 * @return mixed
 */
function simonamarin_footer_credits( $value ) {
	if ( function_exists( 'sydney_get_footer_credits_default' ) && sydney_get_footer_credits_default() !== $value && '' !== (string) $value ) {
		return $value;
	}

	return '{copyright} {year} Cabinet Individual de Psihologie Simona Marin. Toate drepturile rezervate.';
}
add_filter( 'theme_mod_footer_credits', 'simonamarin_footer_credits' );

/**
 * Randeaza subsolul.
 */
function simonamarin_footer_area() {
	// Zona de widgeturi a temei ramane, exact ca in sydney_footer_area().
	if ( is_active_sidebar( 'footer-1' ) ) {
		get_sidebar( 'footer' );
	}

	$container = get_theme_mod( 'footer_credits_container', 'container' );
	$credits   = function_exists( 'sydney_footer_credits' ) ? sydney_footer_credits() : '';
	?>
	<footer id="colophon" class="site-footer">
		<div class="<?php echo esc_attr( $container ); ?>">
			<div class="sm-footer-nav">
				<?php foreach ( simonamarin_footer_groups() as $group ) : ?>
					<?php
					$links = '';
					foreach ( $group['pages'] as $page_id ) {
						if ( 'publish' !== get_post_status( $page_id ) ) {
							continue;
						}
						$links .= sprintf(
							'<li><a href="%1$s">%2$s</a></li>',
							esc_url( get_permalink( $page_id ) ),
							esc_html( get_the_title( $page_id ) )
						);
					}
					if ( '' === $links ) {
						continue;
					}
					?>
					<nav aria-label="<?php echo esc_attr( $group['label'] ); ?>">
						<ul><?php echo $links; // phpcs:ignore WordPress.Security.EscapeOutput -- escapat mai sus. ?></ul>
					</nav>
				<?php endforeach; ?>

				<?php if ( function_exists( 'simonamarin_contact_links' ) && function_exists( 'simonamarin_icon' ) ) : ?>
					<div class="sm-footer-contact">
						<?php
						// Aceleasi date ca in antet, din aceeasi functie: o singura sursa.
						foreach ( simonamarin_contact_links() as $link ) {
							printf(
								'<a href="%1$s"%2$s aria-label="%3$s" title="%3$s">%4$s</a>',
								esc_url( $link['url'] ),
								$link['rel'] ? sprintf( ' rel="%s"', esc_attr( $link['rel'] ) ) : '',
								esc_attr( $link['label'] ),
								simonamarin_icon( $link['icon'] ) // phpcs:ignore WordPress.Security.EscapeOutput -- SVG generat local.
							);
						}
						?>
					</div>
				<?php endif; ?>
			</div>

			<div class="site-info">
				<div class="row">
					<div class="col-md-12">
						<?php echo wp_kses_post( $credits ); ?>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<?php
}

/**
 * Inlocuieste functia de subsol a temei cu cea de mai sus.
 *
 * Pe `after_setup_theme`, adica dupa ce Sydney si-a inregistrat actiunea
 * (fisierele temei parinte se incarca inaintea acestui hook). Verificarea cu
 * has_action face inlocuirea sigura: daca o versiune viitoare redenumeste
 * functia, subsolul temei ramane si nu apar doua subsoluri.
 */
function simonamarin_replace_footer() {
	if ( false !== has_action( 'sydney_footer', 'sydney_footer_area' ) ) {
		remove_action( 'sydney_footer', 'sydney_footer_area' );
		add_action( 'sydney_footer', 'simonamarin_footer_area' );
	}
}
add_action( 'after_setup_theme', 'simonamarin_replace_footer', 20 );
