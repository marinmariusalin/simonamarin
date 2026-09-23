<?php
/**
 * Plugin Name: Simona Marin - Google Analytics dupa consimtamant
 * Description: GA4 (G-FQ35PBZ9NE) incarcat doar dupa ce vizitatorul accepta cookie-urile de analiza in bannerul CookieYes. Inlocuieste Site Kit.
 *
 * De ce exista, in loc de Site Kit (scos pe 23.09.2026):
 *
 * 1. Greutatea. Site Kit incarca `gtag.js` (522 KB, 170 KB transferati) pentru
 *    fiecare vizitator, la prima afisare. Aici scriptul nu se descarca deloc
 *    pana la acord, deci prima incarcare e mai usoara pentru toata lumea.
 *
 * 2. Consimtamantul. Site Kit punea tag-ul in pagina inainte de orice alegere
 *    din banner. Pe un site de psihoterapie, unde formularul transporta date de
 *    sanatate (GDPR art. 9), masurarea fara acord nu e acceptabila.
 *
 * 3. Independenta de tema. Stand in mu-plugin, masurarea supravietuieste unei
 *    schimbari de tema si nu poate fi dezactivata din greseala din lista de
 *    pluginuri.
 *
 * Pastrat identic cu Site Kit, ca rapoartele din GA sa continue fara ruptura:
 * acelasi ID de masurare, excluderea utilizatorilor autentificati (setarea
 * `trackingDisabled: loggedinUsers` din Site Kit) si evenimentul `contact` la
 * trimiterea cu succes a formularului Contact Form 7.
 *
 * Diferit fata de Site Kit, deliberat: evenimentul `contact` NU trimite nimic
 * din formular. Site Kit putea atasa email, telefon si adresa (`gtagUserData`);
 * aici se transmite doar faptul ca un mesaj a fost trimis. Semnalele Google
 * pentru publicitate (Google Signals, personalizare reclame) sunt oprite.
 *
 * Consecinta de stiut: GA numara doar vizitatorii care accepta analiza, deci
 * cifrele vor fi mai mici decat inainte. Nu e o scadere de trafic.
 *
 * @package simonamarin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scriptul de incarcare condiționata.
 *
 * Acordul se citeste din doua locuri, pentru cele doua momente posibile:
 * - la incarcarea paginii, din cookie-ul `cookieyes-consent` (vizitator care a
 *   ales deja pe o pagina anterioara); formatul este
 *   `consentid:...,consent:yes,action:yes,necessary:yes,analytics:yes,...`;
 * - pe loc, din evenimentul `cookieyes_consent_update`, pe care CookieYes il
 *   emite cu `{ accepted: [...], rejected: [...] }` la orice alegere din banner.
 *
 * Retragerea acordului pe aceeasi pagina seteaza `ga-disable-<ID>`, mecanismul
 * oficial Google care opreste orice trimitere ulterioara.
 */
function simonamarin_analytics_script() {
	// Ca Site Kit: adminii si editorii nu intra in statistici.
	if ( is_user_logged_in() ) {
		return;
	}
	?>
<script id="simonamarin-analytics">
(function () {
	var ID = 'G-FQ35PBZ9NE';
	var loaded = false;

	window.dataLayer = window.dataLayer || [];
	function gtag() { window.dataLayer.push(arguments); }
	window.gtag = gtag;

	function hasConsent() {
		var m = document.cookie.match(/(?:^|;\s*)cookieyes-consent=([^;]*)/);
		if (!m) { return false; }
		try { m[1] = decodeURIComponent(m[1]); } catch (e) {}
		return /(?:^|,)analytics:yes(?:,|$)/.test(m[1]);
	}

	function load() {
		window['ga-disable-' + ID] = false;
		if (loaded) { return; }
		loaded = true;
		gtag('js', new Date());
		gtag('config', ID, {
			allow_google_signals: false,
			allow_ad_personalization_signals: false
		});
		var s = document.createElement('script');
		s.async = true;
		s.src = 'https://www.googletagmanager.com/gtag/js?id=' + ID;
		document.head.appendChild(s);
	}

	if (hasConsent()) { load(); }

	document.addEventListener('cookieyes_consent_update', function (e) {
		var accepted = (e.detail && e.detail.accepted) || [];
		if (accepted.indexOf('analytics') !== -1) {
			load();
		} else if (loaded) {
			window['ga-disable-' + ID] = true;
		}
	});

	// Acelasi nume de eveniment ca Site Kit, fara niciun camp din formular.
	document.addEventListener('wpcf7mailsent', function () {
		if (loaded && !window['ga-disable-' + ID]) {
			gtag('event', 'contact');
		}
	});

	// Clicurile pe WhatsApp, telefon si email. WhatsApp e canalul principal de
	// contact de la redesign, iar formularul - singurul masurat pana acum - e
	// secundar, deci fara asta GA nu vede aproape niciun client care scrie.
	// Se trimite doar canalul si pagina de pe care s-a facut clicul: niciun
	// numar, nicio adresa (parametrul link_url e scos explicit, pentru ca
	// gtag l-ar completa singur cu numarul de telefon).
	document.addEventListener('click', function (e) {
		if (!loaded || window['ga-disable-' + ID] || !e.target.closest) { return; }
		var a = e.target.closest('a[href]');
		if (!a) { return; }
		var href = a.getAttribute('href');
		var method = /^(https?:)?\/\/(wa\.me|api\.whatsapp\.com)\//i.test(href) ? 'whatsapp'
			: /^tel:/i.test(href) ? 'telefon'
			: /^mailto:/i.test(href) ? 'email' : '';
		if (method) {
			gtag('event', 'contact', { method: method, link_url: undefined });
		}
	}, true);
})();
</script>
	<?php
}
add_action( 'wp_head', 'simonamarin_analytics_script', 20 );
