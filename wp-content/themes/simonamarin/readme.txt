TODO [DEAD-11][LOW]: Continutul acestui fisier este inca sablonul Underscores
si descrie o alta tema - de rescris sau de sters.
Motiv: readme.txt este citit de WordPress.org la distributia unei teme publice.
Tema aceasta nu se distribuie public, deci fisierul nu are consumator. In plus,
informatiile din el sunt FALSE si induc in eroare pe oricine preia proiectul:
  - "Contributors: automattic" - autorul real este alt proiect;
  - "Description" - descriere nescrisa, placeholder;
  - "Tested up to: 5.4" / "Requires PHP: 5.6" - valori din 2020, contrazise de
    realitate (site-ul ruleaza WordPress 6.x);
  - sectiunea FAQ sustine ca tema include suport pentru WooCommerce si pentru
    Infinite Scroll din Jetpack. Suportul WooCommerce NU exista deloc in cod, iar
    cel pentru Jetpack este cod mort (vezi DEAD-01).
Decizie: ori se sterge fisierul, ori se rescrie cu datele reale ale proiectului
(la fel ca antetul din style.css - vezi CLEAN-06).

----- continut original mai jos -----

=== simonamarin ===

Contributors: automattic
Tags: custom-background, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready

Requires at least: 4.5
Tested up to: 5.4
Requires PHP: 5.6
Stable tag: 1.0.0
License: GNU General Public License v2 or later
License URI: LICENSE

A starter theme called simonamarin.

== Description ==

Description

== Installation ==

1. In your admin panel, go to Appearance > Themes and click the Add New button.
2. Click Upload Theme and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

= Does this theme support any plugins? =

simonamarin includes support for WooCommerce and for Infinite Scroll in Jetpack.

== Changelog ==

= 1.0 - May 12 2015 =
* Initial release

== Credits ==

* Based on Underscores https://underscores.me/, (C) 2012-2020 Automattic, Inc., [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html)
* normalize.css https://necolas.github.io/normalize.css/, (C) 2012-2018 Nicolas Gallagher and Jonathan Neal, [MIT](https://opensource.org/licenses/MIT)
