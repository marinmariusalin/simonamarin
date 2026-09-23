<?php
/**
 * Completari la datele structurate emise de Rank Math.
 *
 * ---------------------------------------------------------------------------
 * DE CE PRIN FILTRUL `rank_math/json_ld` SI NU UN BLOC JSON-LD PROPRIU
 * ---------------------------------------------------------------------------
 * Rank Math emite deja un graf complet (entitatea cabinetului, WebSite,
 * WebPage, Article). Un al doilea bloc scris de mana ar crea o a doua entitate
 * pentru aceeasi afacere, pe care Google trebuie s-o impace cu prima - mai rau
 * decat lipsa datelor. Filtrul modifica graful existent, in acelasi <script>.
 *
 * Ce se poate seta din admin (adresa, profilurile sociale, breadcrumbs) s-a
 * setat in Rank Math, nu aici. Aici e doar ce Rank Math gratuit nu stie.
 *
 * Nicio valoare de aici nu apare ca text pe pagina.
 *
 * ---------------------------------------------------------------------------
 * CE NU CONTINE, DELIBERAT
 * ---------------------------------------------------------------------------
 * - Adresa stradala: cabinetul nu o publica. Adresa din Rank Math contine doar
 *   localitatea, judetul si tara.
 * - Specializari, titulaturi, calificari (de ex. EMDR): se verifica la sursa
 *   inainte de publicare, vezi CLAUDE.md. Serviciile de mai jos poarta exact
 *   titlul paginii pe care il publica deja site-ul, nimic in plus.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Paginile care descriu un serviciu al cabinetului, prin ID.
 *
 * Doar /servicii-psihologice/ descrie ce ofera cabinetul. /psihoterapia/ si
 * /terapia-emdr/ sunt, in WordPress, ARTICOLE (post, nu page) - texte
 * informative despre o metoda - si raman Article; EMDR in plus nu apare in lista
 * specializarilor verificate.
 *
 * @return int[]
 */
function simonamarin_service_page_ids() {
	return array( 192 );
}

/**
 * Readuce graful de baza pe pagini.
 *
 * GASIT PE PRODUCTIE, 23.09.2026: /servicii-psihologice/, /tarife-servicii-
 * psihologice/, /terapia-online-avantaje-si-dezavantaje/ si /ateliere/ nu emit
 * NICIUN JSON-LD. Cauza e in Rank Math: tipul implicit de schema pentru pagini
 * este setat la „Service", iar Rank Math adauga entitatile globale (WebSite,
 * WebPage, cabinetul) doar cand tipul implicit e un Article sau Product
 * (Helper::get_default_schema_type cu $return_valid). Service fara date
 * completate in fiecare pagina nu produce nici el nimic - deci pagina ramane
 * fara nimic.
 *
 * Filtrul e cel documentat de Rank Math chiar pentru aceasta decizie.
 *
 * @param bool $can_add Decizia Rank Math.
 * @return bool
 */
function simonamarin_add_global_entities_on_pages( $can_add ) {
	return is_page() ? true : $can_add;
}
add_filter( 'rank_math/schema/add_global_entities', 'simonamarin_add_global_entities_on_pages' );

/**
 * Zona deservita.
 *
 * Sedintele sunt online; localitatea ramane pentru ca „psiholog popesti
 * leordeni" este interogarea non-brand cu cele mai multe clicuri din Search
 * Console. Tara acopera clientii online de oriunde.
 *
 * @return array[]
 */
function simonamarin_area_served() {
	return array(
		array(
			'@type' => 'City',
			'name'  => 'Popești-Leordeni',
		),
		array(
			'@type' => 'AdministrativeArea',
			'name'  => 'Ilfov',
		),
		array(
			'@type' => 'Country',
			'name'  => 'România',
		),
	);
}

/**
 * Tarifele, ca oferte in datele structurate ale serviciului.
 *
 * Copiate exact de pe /tarife-servicii-psihologice/ (pagina 215): aceleasi
 * denumiri, aceleasi preturi. Confirmate de utilizator pe 23.09.2026 ca fiind
 * cele corecte. Rank Math avea in Local SEO un interval vechi (200-350), pe
 * care nu il emitea oricum.
 *
 * Scrise aici, nu citite din pagina: marcajul paginii nu e structurat (titlu
 * si pret in blocuri separate, trecute prin wpautop), iar o extragere din el
 * s-ar rupe tacut la prima editare. DACA SE SCHIMBA UN TARIF PE PAGINA, SE
 * SCHIMBA SI AICI - altfel Google afiseaza alt pret decat site-ul.
 *
 * @return array
 */
function simonamarin_offer_catalog() {
	$tarife = array(
		'Ședință individuală consiliere / psihoterapie (50 min)'         => 350,
		'Ședință cuplu / familie consiliere / psihoterapie (75 min)'     => 450,
		'Ședință de psihonutritie (50 min)'                              => 350,
		'Ședință individuală consiliere / psihoterapie ONLINE (50 min)'  => 350,
		'Ședință cuplu / familie consiliere / psihoterapie ONLINE (75 min)' => 450,
		'Parenting și educație parentală ONLINE (75 min)'                => 450,
	);

	$offers = array();
	foreach ( $tarife as $name => $price ) {
		$offers[] = array(
			'@type'         => 'Offer',
			'name'          => $name,
			'price'         => (string) $price,
			'priceCurrency' => 'RON',
			'url'           => get_permalink( 215 ),
		);
	}

	return array(
		'@type'           => 'OfferCatalog',
		'name'            => wp_strip_all_tags( html_entity_decode( get_the_title( 215 ), ENT_QUOTES, 'UTF-8' ) ),
		'url'             => get_permalink( 215 ),
		'itemListElement' => $offers,
	);
}

/**
 * Modifica graful Rank Math.
 *
 * Prioritatea 99: dupa toate modulele Rank Math (Local SEO ruleaza pe 9,
 * schema pe 10-11), ca nodurile sa existe deja.
 *
 * @param array $data Nodurile grafului, indexate dupa cheie.
 * @return array
 */
function simonamarin_json_ld( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}

	$home      = trailingslashit( home_url() );
	$entity_id = null;

	// 1. Entitatea cabinetului: zona deservita.
	if ( isset( $data['publisher'] ) && is_array( $data['publisher'] ) ) {
		$data['publisher']['areaServed'] = simonamarin_area_served();
		$entity_id                       = isset( $data['publisher']['@id'] ) ? $data['publisher']['@id'] : null;

		// Telefonul: Rank Math il are in Local SEO, dar il emite doar pentru tipul
		// „companie", iar entitatea e setata ca persoana. E acelasi numar care e
		// deja public in antetul fiecarei pagini (tel:0747668204, vezi
		// inc/contact-links.php) - nimic nou nu devine public.
		if ( empty( $data['publisher']['telephone'] ) ) {
			$data['publisher']['telephone'] = '+40747668204';
		}

		// Programul, exact cel afisat pe pagina Tarife (confirmat de utilizator pe
		// 23.09.2026; Rank Math avea unul vechi, 9-17 / 9-12). Sta pe un
		// ContactPoint, nu direct pe entitate: openingHoursSpecification e valid
		// doar pe LocalBusiness/Place, iar entitatea e Organization + Person.
		// DACA SE SCHIMBA PROGRAMUL PE SITE, SE SCHIMBA SI AICI.
		$data['publisher']['contactPoint'] = array(
			'@type'             => 'ContactPoint',
			'contactType'       => 'customer service',
			'telephone'         => '+40747668204',
			'availableLanguage' => 'ro',
			'hoursAvailable'    => array(
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ),
					'opens'     => '10:00',
					'closes'    => '19:00',
				),
				array(
					'@type'     => 'OpeningHoursSpecification',
					'dayOfWeek' => 'Saturday',
					'opens'     => '10:00',
					'closes'    => '15:00',
				),
			),
		);
	}

	/*
	 * 2. Autorul articolelor.
	 *
	 * Rank Math il construieste din contul WordPress, al carui nume afisat este
	 * numele de autentificare - „simonamarin_admin". Doua probleme: fiecare
	 * articol spune Google-ului ca autorul e un cont tehnic (semnal slab de
	 * expertiza pe un subiect de sanatate), si numele de autentificare al
	 * administratorului e public in sursa fiecarei pagini.
	 *
	 * Nu schimb numele afisat al contului: e o setare a utilizatorului, care
	 * poate aparea si in alte locuri. Il inlocuiesc doar in date.
	 */
	$about_url = get_permalink( 28 );
	if ( isset( $data['ProfilePage'] ) && is_array( $data['ProfilePage'] ) ) {
		$data['ProfilePage']['name'] = 'Simona Marin';
		$data['ProfilePage']['url']  = $about_url ? $about_url : $home;
		unset( $data['ProfilePage']['image'], $data['ProfilePage']['description'] );
		if ( $entity_id ) {
			$data['ProfilePage']['worksFor'] = array( '@id' => $entity_id );
		}
	}

	if ( isset( $data['richSnippet'] ) && is_array( $data['richSnippet'] ) ) {
		$snippet = &$data['richSnippet'];

		if ( isset( $snippet['author']['name'] ) ) {
			$snippet['author']['name'] = 'Simona Marin';
		}

		/*
		 * 3. Titlul articolului.
		 *
		 * Rank Math pune in `headline` titlul SEO complet, cu numele site-ului
		 * lipit la coada („... – Cabinet Individual de Psihologie"). Titlul unui
		 * articol este titlul articolului, exact cum e publicat.
		 */
		if ( is_singular() && isset( $snippet['headline'] ) ) {
			$title               = wp_strip_all_tags( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) );
			$snippet['headline'] = $title;
			$snippet['name']     = $title;
		}

		unset( $snippet );
	}

	// 4. Paginile de servicii: Service. Pe pagini nu exista nod richSnippet (vezi mai sus).
	if ( is_page( simonamarin_service_page_ids() ) ) {
		$snippet = isset( $data['richSnippet'] ) && is_array( $data['richSnippet'] ) ? $data['richSnippet'] : array();
		if ( empty( $snippet['description'] ) ) {
			$description = class_exists( '\RankMath\Post' ) ? \RankMath\Post::get_meta( 'description', get_the_ID() ) : '';
			if ( $description ) {
				$snippet['description'] = $description;
			}
		}
		if ( isset( $data['WebPage']['@id'] ) ) {
			$snippet['isPartOf'] = array( '@id' => $data['WebPage']['@id'] );
		}
		$service = array(
			'@type'            => 'Service',
			'@id'              => isset( $snippet['@id'] ) ? $snippet['@id'] : get_permalink() . '#richSnippet',
			'name'             => wp_strip_all_tags( html_entity_decode( get_the_title(), ENT_QUOTES, 'UTF-8' ) ),
			'url'              => get_permalink(),
			'areaServed'       => simonamarin_area_served(),
			'availableChannel' => array(
				'@type'             => 'ServiceChannel',
				'name'              => 'Online',
				'serviceUrl'        => get_permalink( 186 ),
				'availableLanguage' => 'ro',
			),
		);
		if ( ! empty( $snippet['description'] ) ) {
			$service['description'] = $snippet['description'];
		}
		if ( $entity_id ) {
			$service['provider'] = array( '@id' => $entity_id );
		}
		if ( isset( $snippet['isPartOf'] ) ) {
			$service['mainEntityOfPage'] = $snippet['isPartOf'];
		}
		$service['hasOfferCatalog'] = simonamarin_offer_catalog();
		$data['richSnippet']        = $service;
	}

	return $data;
}
add_filter( 'rank_math/json_ld', 'simonamarin_json_ld', 99 );
