/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	// Return early if the button doesn't exist.
	if ( 'undefined' === typeof button ) {
		return;
	}

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	// TODO [A11Y-04][MEDIUM]: Meniul mobil nu implementeaza nici inchiderea cu
	// tasta Escape, nici focus trap. Un utilizator care navigheaza doar cu tastatura nu
	// poate inchide meniul deschis. Criteriu WCAG 2.1.
	//
	// TODO [PERF-13][LOW]: Listener-ul global de 'click' pe document (mai jos)
	// ruleaza la fiecare click din pagina, chiar si cand meniul e inchis.
	// Fix: adauga listener-ul doar cat timp meniul e deschis si scoate-l la
	// inchidere.

	/*
	 * TODO [UX-08][HIGH]: Meniul mobil se deschide instantaneu, fara tranzitie
	 * si fara nicio schimbare de context vizual. Probleme concrete pe telefon:
	 *   1. Fara blocarea scroll-ului pe <body>: cand meniul e deschis, pagina
	 *      de dedesubt continua sa se deruleze sub deget. Este cel mai frecvent
	 *      motiv de "meniu care se simte stricat" pe mobil.
	 *      Fix: document.body.style.overflow = 'hidden' la deschidere, revenire
	 *      la inchidere (salveaza si restaureaza pozitia de scroll).
	 *   2. Fara overlay/backdrop: nu exista separare vizuala intre meniu si
	 *      continut, iar utilizatorul nu intuieste ca poate inchide atingand
	 *      in afara (desi listener-ul de document exista deja mai jos).
	 *   3. Fara animatie (slide/fade 150-200ms): tranzitia brusca face meniul
	 *      sa para o schimbare de pagina, nu un panou.
	 *      Respecta prefers-reduced-motion - vezi CSS-10.
	 *
	 * TODO [UX-09][MEDIUM]: Meniul nu se inchide dupa ce utilizatorul apasa un
	 * link din el. Pe navigari normale nu se observa (pagina se reincarca), dar
	 * pe link-uri catre ancore din aceeasi pagina (#contact, #servicii - tipic
	 * pe one-page) meniul ramane deschis peste continutul tinta.
	 * Fix: la click pe un <a> din menu, inchide meniul daca href-ul este o
	 * ancora din pagina curenta.
	 */

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	}

	/*
	 * TODO [UX-10][HIGH]: `touchstart` + `event.preventDefault()` (in toggleFocus,
	 * mai jos) anuleaza navigarea la PRIMA atingere pe un link de meniu care are
	 * copii. Intentia originala era "primul tap deschide submeniul, al doilea
	 * navigheaza", dar in implementarea de aici al doilea tap tot nu navigheaza,
	 * pentru ca preventDefault se aplica la fiecare touchstart.
	 * Impact: pe telefon, paginile-parinte din meniu (ex. "Servicii") devin
	 * inaccesibile din navigatie. Este o pierdere directa de trafic si o
	 * frustrare clasica.
	 * Fix: buton separat de expandare langa link (pattern "disclosure"), astfel
	 * incat link-ul sa ramana link, iar sageata sa deschida submeniul.
	 * DE VERIFICAT INTAI daca meniul are submeniuri - altfel codul nu se atinge.
	 */

	// Toggle focus each time a menu link with children receive a touch event.
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 *
	 * TODO [BUG-01][HIGH]: Functia citeste `event` ca variabila GLOBALA implicita
	 * (window.event), nu ca parametru. window.event este o relicva non-standard:
	 *   - nu exista deloc in Firefox pana la versiuni recente si e deprecated;
	 *   - arunca ReferenceError in modul strict / in module ES;
	 *   - este `undefined` daca functia e apelata din alt context.
	 * Impact: navigarea cu TAB prin submeniuri se rupe silentios pe unele
	 * browsere, iar `event.preventDefault()` de la touchstart nu se executa.
	 * Fix: `function toggleFocus( event ) { ... }` - parametrul e deja transmis
	 * automat de addEventListener, e suficient sa fie declarat.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );
