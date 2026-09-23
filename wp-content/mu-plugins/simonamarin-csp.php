<?php
/**
 * Plugin Name: Simona Marin - Content-Security-Policy (doar raportare)
 * Description: Trimite Content-Security-Policy-Report-Only si colecteaza incalcarile raportate de browsere. Nu blocheaza nimic.
 * Version: 1.0.0
 * Author: Cabinet Individual de Psihologie Simona Marin
 *
 * ---------------------------------------------------------------------------
 * SEC-11 - DE CE REPORT-ONLY SI NU POLITICA APLICATA
 * ---------------------------------------------------------------------------
 * O politica CSP aplicata direct ar rupe tacut orice sursa pe care nu am
 * anticipat-o: un script de la Cloudflare pe productie, un widget de plugin
 * dupa update, iframe-ul reCAPTCHA. Vizitatorul ar vedea un formular care nu se
 * trimite, iar noi nu am afla. Varianta Report-Only lasa pagina sa functioneze
 * exact ca inainte si doar ne spune ce AR fi blocat. Dupa cateva zile de trafic
 * real fara rapoarte neasteptate, aceeasi politica se poate aplica schimband
 * numele headerului.
 *
 * ---------------------------------------------------------------------------
 * CE PERMITE POLITICA SI DE CE
 * ---------------------------------------------------------------------------
 * Sursele au fost inventariate din HTML-ul paginilor (Home, Contact, Despre
 * mine, Tarife, Articole) si din codul care incarca scripturi dupa pagina:
 *
 * - `'unsafe-inline'` la script-src si style-src: Contact Form 7, CookieYes,
 *   Rank Math, LiteSpeed si tema pun scripturi si stiluri inline. Fara nonce-uri
 *   in toate aceste pluginuri (nu le controlam), inline trebuie permis. Politica
 *   ramane utila: blocheaza scripturi de pe ORICE alt domeniu decat cele de mai
 *   jos, pluginuri Flash/`<object>`, deturnarea `<base>` si trimiterea
 *   formularelor catre alt domeniu - exact ce ar folosi un script injectat ca sa
 *   scoata datele din formularul de contact.
 * - www.google.com + www.gstatic.com: reCAPTCHA v3, doar pe paginile cu
 *   formular (mu-plugins/simonamarin-recaptcha.php). Scriptul, iframe-ul
 *   invizibil si cererile de verificare vin de acolo.
 * - www.googletagmanager.com + *.google-analytics.com +
 *   *.analytics.google.com: GA4, incarcat doar dupa acordul pentru analiza
 *   (mu-plugins/simonamarin-analytics.php).
 * - `data:` la imagini si fonturi: iconite SVG inline din CSS-ul pluginurilor.
 *
 * Linkurile catre WhatsApp, Facebook, Instagram, COPSI etc. sunt simple
 * navigari si nu intra sub CSP.
 *
 * ---------------------------------------------------------------------------
 * RAPOARTELE SI DATELE VIZITATORILOR
 * ---------------------------------------------------------------------------
 * Faptul ca cineva a vizitat un anumit articol este informatie sensibila pe un
 * site de psihoterapie. De aceea din fiecare raport se pastreaza doar ce ajuta
 * la reglarea politicii - directiva, originea resursei blocate si CALEA paginii,
 * fara query string - si NICIODATA IP-ul, user-agentul sau vreun identificator.
 * Rapoartele identice se agrega intr-un contor. Se vad in Unelte -> Raport CSP.
 *
 * @package simonamarin
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Numele optiunii in care se agrega rapoartele. Autoload dezactivat. */
const SIMONAMARIN_CSP_OPTION = 'simonamarin_csp_reports';

/**
 * Cate tipuri distincte de incalcari se pastreaza. Plafonul exista pentru ca
 * endpointul e public: oricine poate trimite rapoarte false, iar fara limita
 * optiunea ar putea fi umflata la nesfarsit.
 */
const SIMONAMARIN_CSP_MAX_ENTRIES = 200;

/**
 * Politica, ca lista de directive. Separata de header ca sa poata fi reutilizata
 * neschimbata cand se trece la `Content-Security-Policy` aplicat.
 *
 * @return string
 */
function simonamarin_csp_policy() {
	$google_analytics = 'https://www.googletagmanager.com https://*.google-analytics.com https://*.analytics.google.com';
	$recaptcha        = 'https://www.google.com https://www.gstatic.com';

	$directives = array(
		"default-src 'self'",
		"script-src 'self' 'unsafe-inline' https://www.googletagmanager.com {$recaptcha}",
		"style-src 'self' 'unsafe-inline'",
		"img-src 'self' data: {$google_analytics} {$recaptcha}",
		"font-src 'self' data:",
		"connect-src 'self' {$google_analytics} {$recaptcha}",
		"frame-src {$recaptcha}",
		"object-src 'none'",
		"base-uri 'self'",
		"form-action 'self'",
		"frame-ancestors 'self'",
	);

	return implode( '; ', $directives );
}

/**
 * Adauga headerul Report-Only pe paginile publice.
 *
 * Nu pe utilizatori autentificati: bara de admin si previzualizarile incarca
 * resurse (Gravatar, editor) pe care vizitatorii nu le vad, si ar umple raportul
 * cu zgomot care nu spune nimic despre site.
 *
 * Doua mecanisme de raportare, pentru ca browserele difera: `report-to` +
 * `Reporting-Endpoints` (Chrome, Edge) si `report-uri` (Firefox, Safari). Un
 * browser care il cunoaste pe primul il ignora pe al doilea, deci nu se
 * dubleaza rapoartele.
 *
 * @param array $headers Headerele pregatite de WordPress.
 * @return array
 */
function simonamarin_csp_headers( $headers ) {
	if ( is_user_logged_in() ) {
		return $headers;
	}

	$endpoint = rest_url( 'simonamarin/v1/csp-report' );

	$headers['Reporting-Endpoints']                 = 'csp="' . esc_url_raw( $endpoint ) . '"';
	$headers['Content-Security-Policy-Report-Only'] = simonamarin_csp_policy()
		. '; report-uri ' . esc_url_raw( $endpoint )
		. '; report-to csp';

	return $headers;
}
add_filter( 'wp_headers', 'simonamarin_csp_headers' );

/**
 * Endpointul care primeste rapoartele.
 */
function simonamarin_csp_register_route() {
	register_rest_route(
		'simonamarin/v1',
		'/csp-report',
		array(
			'methods'             => 'POST',
			'callback'            => 'simonamarin_csp_receive',
			// Browserul trimite rapoartele fara autentificare, deci ruta e publica.
			// Riscul e limitat de plafonul de intrari si de ce se pastreaza.
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'simonamarin_csp_register_route' );

/**
 * Reduce o adresa la origine (schema + domeniu). Pentru valorile speciale
 * (`inline`, `eval`, `data`) le pastreaza ca atare.
 *
 * @param string $uri Adresa din raport.
 * @return string
 */
function simonamarin_csp_origin( $uri ) {
	$uri = (string) $uri;
	if ( '' === $uri || false === strpos( $uri, '://' ) ) {
		return substr( sanitize_text_field( $uri ), 0, 40 );
	}
	$parts = wp_parse_url( $uri );
	if ( empty( $parts['host'] ) ) {
		return '';
	}
	return ( isset( $parts['scheme'] ) ? $parts['scheme'] . '://' : '' ) . strtolower( $parts['host'] );
}

/**
 * Primeste un raport (sau un lot) si il agrega in optiune.
 *
 * @param WP_REST_Request $request Cererea.
 * @return WP_REST_Response
 */
function simonamarin_csp_receive( WP_REST_Request $request ) {
	$body = $request->get_body();

	// Un raport real are cateva sute de octeti; un lot, cativa KB.
	if ( strlen( $body ) > 16384 ) {
		return new WP_REST_Response( null, 413 );
	}

	$data = json_decode( $body, true );
	if ( ! is_array( $data ) ) {
		return new WP_REST_Response( null, 400 );
	}

	// Normalizeaza cele doua formate intr-o lista de rapoarte.
	// `report-uri`: {"csp-report": {...}}, cu chei cu cratima.
	// `report-to`:  [{"type": "csp-violation", "body": {...}}], cu chei camelCase.
	$reports = array();
	if ( isset( $data['csp-report'] ) && is_array( $data['csp-report'] ) ) {
		$r         = $data['csp-report'];
		$reports[] = array(
			'directive' => $r['effective-directive'] ?? ( $r['violated-directive'] ?? '' ),
			'blocked'   => $r['blocked-uri'] ?? '',
			'document'  => $r['document-uri'] ?? '',
		);
	} else {
		foreach ( array_slice( $data, 0, 20 ) as $item ) {
			if ( ! is_array( $item ) || ( $item['type'] ?? '' ) !== 'csp-violation' || ! is_array( $item['body'] ?? null ) ) {
				continue;
			}
			$b         = $item['body'];
			$reports[] = array(
				'directive' => $b['effectiveDirective'] ?? '',
				'blocked'   => $b['blockedURL'] ?? '',
				'document'  => $b['documentURL'] ?? '',
			);
		}
	}

	if ( ! $reports ) {
		return new WP_REST_Response( null, 204 );
	}

	$stored = get_option( SIMONAMARIN_CSP_OPTION, array() );
	if ( ! is_array( $stored ) ) {
		$stored = array();
	}

	foreach ( $reports as $r ) {
		$directive = substr( sanitize_key( $r['directive'] ), 0, 40 );
		$blocked   = simonamarin_csp_origin( $r['blocked'] );
		// Doar calea paginii: query string-ul poate contine termeni de cautare.
		$path = wp_parse_url( (string) $r['document'], PHP_URL_PATH );
		$path = substr( sanitize_text_field( is_string( $path ) ? $path : '' ), 0, 120 );

		if ( '' === $directive ) {
			continue;
		}

		$key = md5( $directive . '|' . $blocked );
		$now = gmdate( 'Y-m-d H:i' );

		if ( isset( $stored[ $key ] ) ) {
			$stored[ $key ]['count']++;
			$stored[ $key ]['last'] = $now;
			$stored[ $key ]['page'] = $path;
		} elseif ( count( $stored ) < SIMONAMARIN_CSP_MAX_ENTRIES ) {
			$stored[ $key ] = array(
				'directive' => $directive,
				'blocked'   => $blocked,
				'page'      => $path,
				'count'     => 1,
				'first'     => $now,
				'last'      => $now,
			);
		}
	}

	update_option( SIMONAMARIN_CSP_OPTION, $stored, false );

	return new WP_REST_Response( null, 204 );
}

/**
 * Pagina Unelte -> Raport CSP: ce ar fi blocat politica, agregat.
 * Butonul de golire serveste la reluarea observatiei dupa o ajustare a politicii.
 */
function simonamarin_csp_admin_menu() {
	add_management_page( 'Raport CSP', 'Raport CSP', 'manage_options', 'simonamarin-csp', 'simonamarin_csp_admin_page' );
}
add_action( 'admin_menu', 'simonamarin_csp_admin_menu' );

/**
 * Afiseaza raportul.
 */
function simonamarin_csp_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['simonamarin_csp_clear'] ) && check_admin_referer( 'simonamarin_csp_clear' ) ) {
		delete_option( SIMONAMARIN_CSP_OPTION );
		echo '<div class="notice notice-success"><p>Raportul a fost golit.</p></div>';
	}

	$stored = get_option( SIMONAMARIN_CSP_OPTION, array() );
	$stored = is_array( $stored ) ? $stored : array();
	uasort(
		$stored,
		static function ( $a, $b ) {
			return $b['count'] <=> $a['count'];
		}
	);

	echo '<div class="wrap"><h1>Raport CSP (doar raportare)</h1>';
	echo '<p>Ce ar fi blocat politica <code>Content-Security-Policy</code> daca ar fi aplicata. Nimic nu este blocat acum. Politica actuala:</p>';
	echo '<p><code>' . esc_html( simonamarin_csp_policy() ) . '</code></p>';

	if ( ! $stored ) {
		echo '<p><strong>Niciun raport.</strong></p>';
	} else {
		echo '<table class="widefat striped"><thead><tr><th>Directiva</th><th>Sursa blocata</th><th>Ultima pagina</th><th>De cate ori</th><th>Prima data</th><th>Ultima data (UTC)</th></tr></thead><tbody>';
		foreach ( $stored as $row ) {
			printf(
				'<tr><td>%s</td><td>%s</td><td>%s</td><td>%d</td><td>%s</td><td>%s</td></tr>',
				esc_html( $row['directive'] ),
				esc_html( $row['blocked'] ),
				esc_html( $row['page'] ),
				(int) $row['count'],
				esc_html( $row['first'] ),
				esc_html( $row['last'] )
			);
		}
		echo '</tbody></table>';
	}

	echo '<form method="post" style="margin-top:1em">';
	wp_nonce_field( 'simonamarin_csp_clear' );
	echo '<button type="submit" name="simonamarin_csp_clear" class="button">Goleste raportul</button></form></div>';
}
