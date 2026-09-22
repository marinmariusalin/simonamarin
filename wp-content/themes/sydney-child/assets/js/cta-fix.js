/**
 * Repara butonul principal „Contact" de pe prima pagina.
 * ============================================================================
 *
 * In continutul paginii Home, butonul e scris asa:
 *
 *   <div id="purple-button" class="homepage-button" href="contact-form">
 *
 * Trei probleme intr-o singura linie:
 *   1. e un `<div>`, nu un `<a>` — nu se poate ajunge la el din tastatura si
 *      cititoarele de ecran nu il anunta ca link;
 *   2. `href` pe un `<div>` nu inseamna nimic pentru browser;
 *   3. tinta „contact-form" nu exista nicaieri in pagina (verificat: zero
 *      elemente cu `id="contact-form"`), iar niciun script incarcat nu trateaza
 *      elementul.
 *
 * Rezultat: actiunea principala a site-ului nu face nimic, in niciun fel.
 *
 * Reparatia se face aici, in tema, si NU prin editarea continutului din baza de
 * date — regula proiectului e ca marcajul paginilor apartine utilizatorului.
 * Scriptul inlocuieste `<div>`-ul cu o ancora reala catre pagina de contact,
 * pastrand clasele si continutul exact cum sunt.
 *
 * Tinta vine din PHP (`smCtaFix.contactUrl`), ca sa nu fie scrisa de mana aici.
 */
( function () {
	'use strict';

	function upgrade() {
		var el = document.getElementById( 'purple-button' );

		if ( ! el || el.tagName === 'A' ) {
			return;
		}

		var url = ( window.smCtaFix && window.smCtaFix.contactUrl ) || '/contact/';

		var link = document.createElement( 'a' );
		link.href = url;
		link.className = el.className;
		link.id = el.id;
		link.innerHTML = el.innerHTML;

		// Eticheta accesibila: textul vizibil din buton, daca exista.
		var label = el.querySelector( '.button-text' );
		if ( label && label.textContent.trim() ) {
			link.setAttribute( 'aria-label', label.textContent.trim() );
		}

		el.parentNode.replaceChild( link, el );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', upgrade );
	} else {
		upgrade();
	}
} )();
