<?php
/**
 * SEO & CRO, pasul 8 — Schimbare butoane WhatsApp pagina Servicii (ID 192) in stilul de pe homepage (sm-hero-whatsapp).
 *
 * Inlocuieste butoanele late cu text cu butonul circular, compact si delicat cu iconita WhatsApp,
 * identic cu cel din hero-ul de pe homepage (.sm-hero-whatsapp).
 *
 * Rulare:
 *   php -d extension_dir="C:\Users\Marin Marius Alin\AppData\Local\Programs\Local\resources\extraResources\lightning-services\php-8.2.29+0\bin\win64\ext" -d extension=php_mysqli.dll seo-scripts/8-servicii-whatsapp-stil-homepage.php
 *
 * Backup salvat in: E:/simonamarin/_backup-servicii-whatsapp-stil-homepage-2026-09-23.json
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

$old_btn = '<div class="sm-whatsapp-cta"><a href="https://wa.me/40747668204" class="sm-whatsapp-cta-link" rel="nofollow"><i class="sm-icon"><svg viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation"><g transform="translate(0,448) scale(1,-1)" fill="currentColor"><path d="M381 351Q448 284 448 194Q448 102 382.0 37.0Q316 -28 224 -28Q167 -28 118 -1L0 -32L32 83Q2 134 2 194Q2 286 67.0 351.0Q132 416 224 416Q316 416 381 351ZM224 9Q300 9 355.0 63.5Q410 118 410 194Q411 268 354 324Q300 378 224.0 378.0Q148 378 93.5 324.0Q39 270 39 194Q39 141 68 96L72 89L53 21L123 39L130 35Q173 9 224 9ZM325 148Q335 143 336 141Q339 135 332 114Q329 106 316.5 97.5Q304 89 295 88Q269 84 228 102Q219 106 210.5 111.0Q202 116 194.5 122.0Q187 128 180.0 134.0Q173 140 167.0 146.0Q161 152 156.0 157.5Q151 163 147.0 168.0Q143 173 140.5 176.5Q138 180 136.0 183.0Q134 186 134 186Q111 217 111 243Q111 268 130 290Q137 296 145 296Q153 296 156 296Q165 296 168 287Q185 246 185 246Q188 240 186 236Q182 227 176.0 220.5Q170 214 169.0 213.0Q168 212 170 206Q184 180 200.5 167.0Q217 154 245 140Q253 136 257 141Q270 156 275 163Q279 169 287 166Q295 163 325 148Z"/></g></svg></i><span>WhatsApp</span></a></div>';

$new_btn = '<div class="sm-whatsapp-cta"><a href="https://wa.me/40747668204" class="sm-hero-whatsapp" rel="nofollow noopener" aria-label="WhatsApp" title="WhatsApp" target="_blank"><i class="sm-icon"><svg viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation"><g transform="translate(0,448) scale(1,-1)" fill="currentColor"><path d="M381 351Q448 284 448 194Q448 102 382.0 37.0Q316 -28 224 -28Q167 -28 118 -1L0 -32L32 83Q2 134 2 194Q2 286 67.0 351.0Q132 416 224 416Q316 416 381 351ZM224 9Q300 9 355.0 63.5Q410 118 410 194Q411 268 354 324Q300 378 224.0 378.0Q148 378 93.5 324.0Q39 270 39 194Q39 141 68 96L72 89L53 21L123 39L130 35Q173 9 224 9ZM325 148Q335 143 336 141Q339 135 332 114Q329 106 316.5 97.5Q304 89 295 88Q269 84 228 102Q219 106 210.5 111.0Q202 116 194.5 122.0Q187 128 180.0 134.0Q173 140 167.0 146.0Q161 152 156.0 157.5Q151 163 147.0 168.0Q143 173 140.5 176.5Q138 180 136.0 183.0Q134 186 134 186Q111 217 111 243Q111 268 130 290Q137 296 145 296Q153 296 156 296Q165 296 168 287Q185 246 185 246Q188 240 186 236Q182 227 176.0 220.5Q170 214 169.0 213.0Q168 212 170 206Q184 180 200.5 167.0Q217 154 245 140Q253 136 257 141Q270 156 275 163Q279 169 287 166Q295 163 325 148Z"/></g></svg></i></a></div>';

$count_matches = substr_count( $old_content, $old_btn );
echo "S-au gasit $count_matches aparitii ale butonului anterior pe pagina $id.\n";

if ( 9 !== $count_matches ) {
	echo "ATENTIE: Se asteptau 9 aparitii, dar s-au gasit $count_matches!\n";
	if ( 0 === $count_matches ) {
		echo "Nicio aparitie nu se potriveste exact. Iesire fara modificari.\n";
		exit( 1 );
	}
}

// Backup inainte de modificare
$backup_file = 'E:/simonamarin/_backup-servicii-whatsapp-stil-homepage-2026-09-23.json';
$backup_data = array(
	'timestamp' => date( 'c' ),
	'page_id'   => $id,
	'content'   => $old_content,
);
file_put_contents( $backup_file, json_encode( $backup_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
echo "Backup salvat in: $backup_file\n";

$new_content = str_replace( $old_btn, $new_btn, $old_content );

$wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array( 'ID' => $id ) );
clean_post_cache( $id );
echo "Pagina 192 (Servicii) a fost actualizata in baza de date cu butonul circular WhatsApp stil homepage.\n";
