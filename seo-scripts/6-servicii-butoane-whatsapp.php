<?php
/**
 * SEO & CRO, pasul 6 — Butoane WhatsApp la fiecare sectiune/titlu de pe pagina Servicii (ID 192).
 *
 * Adauga la finalul fiecarei sectiuni de servicii un buton discret si elegant de WhatsApp
 * (acelasi component .sm-whatsapp-cta folosit pe pagina de Contact).
 *
 * Rulare:
 *   php -d extension_dir="C:\Users\Marin Marius Alin\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\ext" -d extension=php_mysqli.dll seo-scripts/6-servicii-butoane-whatsapp.php
 *
 * Copie a starii dinainte: E:/simonamarin/_backup-servicii-whatsapp-2026-09-23.json
 */
if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

$_SERVER['HTTP_HOST'] = 'simonamarin.local';
define( 'WP_USE_THEMES', false );
require __DIR__ . '/../wp-load.php';
global $wpdb;

$id = 192;
$old_content = get_post_field( 'post_content', $id, 'raw' );
$old_content = str_replace( "\r\n", "\n", $old_content );

$btn = '<div class="sm-whatsapp-cta"><a href="https://wa.me/40747668204" class="sm-whatsapp-cta-link" rel="nofollow"><i class="sm-icon"><svg viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation"><g transform="translate(0,448) scale(1,-1)" fill="currentColor"><path d="M381 351Q448 284 448 194Q448 102 382.0 37.0Q316 -28 224 -28Q167 -28 118 -1L0 -32L32 83Q2 134 2 194Q2 286 67.0 351.0Q132 416 224 416Q316 416 381 351ZM224 9Q300 9 355.0 63.5Q410 118 410 194Q411 268 354 324Q300 378 224.0 378.0Q148 378 93.5 324.0Q39 270 39 194Q39 141 68 96L72 89L53 21L123 39L130 35Q173 9 224 9ZM325 148Q335 143 336 141Q339 135 332 114Q329 106 316.5 97.5Q304 89 295 88Q269 84 228 102Q219 106 210.5 111.0Q202 116 194.5 122.0Q187 128 180.0 134.0Q173 140 167.0 146.0Q161 152 156.0 157.5Q151 163 147.0 168.0Q143 173 140.5 176.5Q138 180 136.0 183.0Q134 186 134 186Q111 217 111 243Q111 268 130 290Q137 296 145 296Q153 296 156 296Q165 296 168 287Q185 246 185 246Q188 240 186 236Q182 227 176.0 220.5Q170 214 169.0 213.0Q168 212 170 206Q184 180 200.5 167.0Q217 154 245 140Q253 136 257 141Q270 156 275 163Q279 169 287 166Q295 163 325 148Z"/></g></svg></i><span>WhatsApp</span></a></div>';

$targets = array(
	// 1. Adult
	'dificultăți în luarea deciziilor, etc.</li>' . "\n" . '</ul>' => 'dificultăți în luarea deciziilor, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 2. Copil
	'experiențe traumatice, separare, etc.</li>' . "\n" . '</ul>' => 'experiențe traumatice, separare, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 3. Adolescent
	'probleme somatice cu influenta psihică, etc.</li>' . "\n" . '</ul>' => 'probleme somatice cu influenta psihică, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 4. Cuplu/Familie
	'indecizia de a rămâne în relație, etc.</li>' . "\n" . '</ul>' => 'indecizia de a rămâne în relație, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 5. Grup
	'teme: adicții, pierderi, separări, etc.</li>' . "\n" . '</ul>' => 'teme: adicții, pierderi, separări, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 6. Parinti & Terapie online (spre finalul sectiunii parinti/ateliere/online)
	'<p class="textP">Ședințe de terapie online în cadrul unor <a href="http://simonamarin.local/tarife-servicii-psihologice/" target="_blank" rel="noopener">servicii psihologice</a> dedicate.</p>' => '<p class="textP">Ședințe de terapie online în cadrul unor <a href="http://simonamarin.local/tarife-servicii-psihologice/" target="_blank" rel="noopener">servicii psihologice</a> dedicate.</p>' . "\n" . $btn,

	// 7. Corporate training
	'Strategii de adaptare la schimbare</li>' . "\n" . '</ul>' => 'Strategii de adaptare la schimbare</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 8. Pachete corporate
	'Solicită oferta în funcție de nevoile tale.</li>' . "\n" . '</ul>' => 'Solicită oferta în funcție de nevoile tale.</li>' . "\n" . '</ul>' . "\n" . $btn,

	// 9. Vocational / orientare in cariera
	'redactare CV, redactare scrisoare de intenție, etc.</li>' . "\n" . '</ul>' => 'redactare CV, redactare scrisoare de intenție, etc.</li>' . "\n" . '</ul>' . "\n" . $btn,
);

$new_content = $old_content;
$count = 0;
foreach ( $targets as $search => $replace ) {
	if ( false === strpos( $new_content, $search ) ) {
		echo "EROARE: Subsirul nu a fost gasit: " . substr( $search, 0, 50 ) . "...\n";
		exit( 1 );
	}
	$new_content = str_replace( $search, $replace, $new_content );
	$count++;
}

echo "Toate cele $count puncte de inserare au fost validate cu succes.\n";

$wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array( 'ID' => $id ) );
clean_post_cache( $id );
echo "Pagina 192 (Servicii) a fost actualizata in baza de date.\n";
