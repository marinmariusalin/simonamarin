<?php
/**
 * Iconite SVG inline - inlocuiesc FontAwesome.
 *
 * GENERAT AUTOMAT din webfonturile FontAwesome livrate cu tema parinte
 * (sydney/webfonts/fa-brands-400.woff2 si fa-solid-900.woff2), pe 21.09.2026.
 * Conturile sunt exact aceleasi pe care le desena fontul, deci iconitele arata
 * identic - nu sunt inlocuitori aproximativi dintr-un alt set.
 *
 * DE CE: site-ul incarca 155 KB de webfonturi (fa-solid-900.woff2 78 KB +
 * fa-brands-400.woff2 77 KB) plus trei fisiere CSS, ca sa afiseze CINCI iconite.
 * Inline, aceleasi cinci ocupa sub 3 KB si nu mai genereaza nicio cerere.
 * In plus, fisierele CSS erau cerute de la https://simonamarin.ro, adresa
 * scrisa de mana - deci pe orice alt domeniu decat productia iconitele
 * dispareau complet.
 *
 * Iconitele FontAwesome Free sunt sub licenta CC BY 4.0 (fontawesome.com).
 * Sunt aceleasi active deja livrate cu tema, doar servite altfel.
 *
 * REGENERARE: daca se schimba setul de iconite, se ruleaza din nou scriptul de
 * extragere. NU se editeaza conturile de mai jos de mana.
 *
 * @package sydney-child
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returneaza o iconita SVG inline.
 *
 * Marcajul este ascuns de tehnologiile asistive prin aria-hidden, pentru ca
 * fiecare iconita de pe site sta intr-un <a> care are deja aria-label cu textul
 * real ("Facebook", "Telefon"). Fara aria-hidden, cititorul de ecran ar anunta
 * de doua ori aceeasi legatura.
 *
 * MARCAJUL PASTREAZA INTENTIONAT UN <i> CA INVELIS, desi desenul e un SVG.
 * Motivul: tot CSS-ul existent al site-ului tinteste elementul <i>, iar
 * verificarea pe site-ul de productie a aratat trei reguli care depind de el:
 *   - `.header-links i { color: #000 }`, din CSS-ul aditional, face iconitele
 *     negre desi linkul din jur este rosu;
 *   - `a, i { min-width: 24px; min-height: 24px !important }` le da cutia de
 *     24x24 px din care rezulta spatierea dintre ele;
 *   - `.whatsapp-footer` duce butonul flotant la 80px si la verdele WhatsApp.
 * Daca as fi emis direct <svg>, toate trei ar fi incetat sa se aplice si ar fi
 * trebuit sa copiez valorile aici - adica sa duplic decizii care traiesc in
 * alta parte si care se pot schimba fara ca fisierul asta sa afle. Asa, nu se
 * duplica nimic si aspectul ramane exact cel de dinainte.
 *
 * `fill="currentColor"` face desenul sa mosteneasca culoarea de pe <i>, exact
 * cum o glifa de font mostenea proprietatea `color`.
 *
 * @param string $name  Numele iconitei.
 * @param string $class Clase CSS suplimentare, optional.
 * @return string Marcajul SVG, sau sir gol daca iconita nu exista.
 */
function simonamarin_icon( $name, $class = '' ) {
	$icons = array(
		'envelope' => array(
			'viewBox' => '0 0 512 512',
			'path'    => 'M502 257Q505 260 508.5 258.0Q512 256 512 252V48Q512 28 498.0 14.0Q484 0 464 0H48Q28 0 14.0 14.0Q0 28 0 48V252Q0 256 3.5 257.5Q7 259 10 257Q43 232 164 144Q166 142 177.5 133.0Q189 124 196.0 119.5Q203 115 214.0 108.5Q225 102 235.5 99.0Q246 96 256.0 96.0Q266 96 276.5 99.0Q287 102 298.5 108.5Q310 115 317.0 120.0Q324 125 335.0 133.5Q346 142 348 144Q466 229 502 257ZM256 128Q248 128 236.5 133.5Q225 139 218.0 143.5Q211 148 197.5 158.5Q184 169 183 169Q61 258 9 298Q0 305 0 317V336Q0 356 14.0 370.0Q28 384 48 384H464Q484 384 498.0 370.0Q512 356 512 336V317Q512 305 503 298Q454 260 329 169Q328 169 314.5 158.5Q301 148 294.0 143.5Q287 139 275.5 133.5Q264 128 256 128Z',
		),
		'facebook' => array(
			'viewBox' => '0 0 512 512',
			'path'    => 'M504 192Q504 100 444.0 30.5Q384 -39 295 -53V120H353L364 192H295V239Q295 277 335 277H366V338Q338 343 311 343Q268 343 242.5 318.0Q217 293 217 247V192H154V120H217V-53Q128 -39 68.0 30.5Q8 100 8 192Q8 295 80.5 367.5Q153 440 256.0 440.0Q359 440 431.5 367.5Q504 295 504 192Z',
		),
		'instagram' => array(
			'viewBox' => '0 0 448 512',
			'path'    => 'M143.5 273.5Q177 307 225.0 307.0Q273 307 306.5 273.5Q340 240 340.0 192.0Q340 144 306.5 110.5Q273 77 225.0 77.0Q177 77 143.5 110.5Q110 144 110.0 192.0Q110 240 143.5 273.5ZM172.0 139.0Q194 117 225.0 117.0Q256 117 278.0 139.0Q300 161 300.0 192.0Q300 223 278.0 245.0Q256 267 225.0 267.0Q194 267 172.0 245.0Q150 223 150.0 192.0Q150 161 172.0 139.0ZM363.5 330.5Q371 323 371.0 312.0Q371 301 363.5 293.0Q356 285 345.0 285.0Q334 285 326.0 293.0Q318 301 318.0 312.0Q318 323 326.0 330.5Q334 338 345.0 338.0Q356 338 363.5 330.5ZM448 284Q449 257 449.0 192.0Q449 127 448 100Q445 39 411.5 5.5Q378 -28 317 -30Q290 -32 225.0 -32.0Q160 -32 133 -30Q72 -27 38.5 6.0Q5 39 3 100Q1 127 1.0 192.0Q1 257 3 285Q6 345 39 378Q72 412 133 415Q160 416 225.0 416.0Q290 416 317 415Q378 412 411.5 378.5Q445 345 448 284ZM400 60Q403 68 405.0 80.5Q407 93 408.0 110.5Q409 128 409.0 139.0Q409 150 409.0 170.0Q409 190 409.0 192.5Q409 195 409.0 214.5Q409 234 409.0 245.0Q409 256 408.0 273.5Q407 291 405.0 303.5Q403 316 400 324Q388 355 357 367Q349 370 336.5 372.0Q324 374 306.5 375.0Q289 376 278.5 376.0Q268 376 247.5 376.0Q227 376 225 376Q223 376 203.0 376.0Q183 376 172.0 376.0Q161 376 143.5 375.0Q126 374 113.5 372.0Q101 370 93 367Q62 355 50 324Q47 316 45.0 303.5Q43 291 42.0 273.5Q41 256 41.0 245.5Q41 235 41.0 214.5Q41 194 41 192Q41 190 41.0 170.0Q41 150 41.0 139.0Q41 128 42.0 110.5Q43 93 45.0 80.5Q47 68 50 60Q63 29 93 17Q101 14 113.5 12.0Q126 10 143.5 9.0Q161 8 171.5 8.0Q182 8 202.5 8.0Q223 8 225 8Q228 8 247.5 8.0Q267 8 278.0 8.0Q289 8 306.5 9.0Q324 10 336.5 12.0Q349 14 357 17Q388 30 400 60Z',
		),
		'phone' => array(
			'viewBox' => '0 0 512 512',
			'path'    => 'M494 423Q513 419 513 400Q513 208 377.0 72.0Q241 -64 49 -64Q30 -64 26 -45L2 59Q-3 78 16 86L128 134Q144 141 156 127L205 67Q264 94 309.5 140.0Q355 186 382 244L322 294Q308 305 315 322L363 434Q371 452 390 447Z',
		),
		'whatsapp' => array(
			'viewBox' => '0 0 448 512',
			'path'    => 'M381 351Q448 284 448 194Q448 102 382.0 37.0Q316 -28 224 -28Q167 -28 118 -1L0 -32L32 83Q2 134 2 194Q2 286 67.0 351.0Q132 416 224 416Q316 416 381 351ZM224 9Q300 9 355.0 63.5Q410 118 410 194Q411 268 354 324Q300 378 224.0 378.0Q148 378 93.5 324.0Q39 270 39 194Q39 141 68 96L72 89L53 21L123 39L130 35Q173 9 224 9ZM325 148Q335 143 336 141Q339 135 332 114Q329 106 316.5 97.5Q304 89 295 88Q269 84 228 102Q219 106 210.5 111.0Q202 116 194.5 122.0Q187 128 180.0 134.0Q173 140 167.0 146.0Q161 152 156.0 157.5Q151 163 147.0 168.0Q143 173 140.5 176.5Q138 180 136.0 183.0Q134 186 134 186Q111 217 111 243Q111 268 130 290Q137 296 145 296Q153 296 156 296Q165 296 168 287Q185 246 185 246Q188 240 186 236Q182 227 176.0 220.5Q170 214 169.0 213.0Q168 212 170 206Q184 180 200.5 167.0Q217 154 245 140Q253 136 257 141Q270 156 275 163Q279 169 287 166Q295 163 325 148Z',
		),
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$icon = $icons[ $name ];

	/*
	 * Transformarea exista pentru ca fonturile si SVG-ul numara axa Y invers:
	 * in font Y creste in sus de la linia de baza, in SVG creste in jos de la
	 * coltul din stanga sus. `translate` coboara originea la inaltimea de
	 * ascendenta a fontului, iar `scale(1,-1)` rastoarna axa. Fara ea iconitele
	 * ar aparea cu susul in jos si in afara cadrului.
	 */
	return sprintf(
		'<i class="sm-icon %1$s">'
		. '<svg viewBox="%2$s" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false" role="presentation">'
		. '<g transform="translate(0,%3$d) scale(1,-1)" fill="currentColor"><path d="%4$s"/></g></svg></i>',
		esc_attr( trim( $class ) ),
		esc_attr( $icon['viewBox'] ),
		448,
		$icon['path']
	);
}
