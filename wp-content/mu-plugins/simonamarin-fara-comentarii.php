<?php
/**
 * Plugin Name: Simona Marin - site fara comentarii
 * Description: Inchide comentariile pe tot site-ul si opreste afisarea celor existente, fara sa stearga nimic. Independent de tema.
 * Version: 1.0.0
 * Author: Cabinet Individual de Psihologie Simona Marin
 *
 * DE CE EXISTA ACEST FISIER
 *
 * Decizia "site-ul nu are comentarii, deloc" este a utilizatorului si a fost
 * repetata de mai multe ori. Pana acum traia doar in documentatie: in
 * CLAUDE.md si ca punctul D1 din TODO.md, sub titlul "decizii care NU au ajuns
 * pe site". Intre timp, pe fiecare articol se afisa in continuare formularul
 * "Lasa un raspuns", iar sub el comentariile aprobate. O decizie scrisa intr-un
 * fisier de documentatie nu schimba nimic pentru vizitator.
 *
 * DOMENIU: de ce nu e o simpla preferinta de aspect
 *
 * Un vizitator care comenteaza sub un articol despre o problema personala isi
 * asociaza public numele cu acea problema, permanent si indexabil de motoarele
 * de cautare. Pe site-ul unui cabinet de psihoterapie asta este o expunere pe
 * care vizitatorul nu o intelege in momentul in care apasa "Publica", si pe
 * care nu o poate lua inapoi. De aceea comentariile se inchid in cod, nu doar
 * din setari: o setare se poate reactiva dintr-un clic gresit sau la
 * importul unei configuratii, un mu-plugin nu.
 *
 * DE CE MU-PLUGIN SI NU TEMA
 *
 * Acelasi motiv ca la simonamarin-hardening.php: nimic din ce urmeaza nu tine
 * de aspectul site-ului. Regulile trebuie sa ramana in vigoare si daca tema se
 * schimba, si daca Sydney se actualizeaza (un update sterge complet directorul
 * temei parinte - s-a intamplat deja). Un mu-plugin nu poate fi dezactivat
 * accidental din panoul de administrare.
 *
 * DE CE NU STERGE NIMIC
 *
 * In baza de date sunt 119 comentarii: 48 spam, 69 neaprobate si 2 aprobate.
 * Doar cele 2 aprobate erau vizibile public. Codul de mai jos le scoate din
 * afisare pe toate, dar nu sterge niciunul - stergerea atinge continut si este
 * decizia utilizatorului, nu a mea. Din acelasi motiv, ecranul Comentarii din
 * panoul de administrare ramane neatins: daca utilizatorul vrea sa le stearga,
 * are nevoie de el.
 */

/**
 * Inchide comentariile si pingback-urile pe tot site-ul.
 *
 * `comments_open` este verificat in doua locuri diferite si e important ca
 * acopera ambele: la afisare, de comments_template(), care decide daca
 * randeaza formularul; si la trimitere, in wp_handle_comment_submission(),
 * apelata de wp-comments-post.php. Adica un comentariu trimis direct catre
 * acel fisier, cu formularul reconstruit de mana sau de un bot, este respins
 * la server - nu doar ascuns din pagina.
 *
 * @return bool Intotdeauna false.
 */
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

/**
 * Scoate din afisare comentariile existente.
 *
 * `comments_array` este filtrul prin care trece lista pe care o primeste
 * sablonul temei inainte de wp_list_comments(). Returnand o lista goala,
 * comentariile dispar din pagina indiferent ce tema ruleaza si indiferent cum
 * isi scrie ea comments.php - exact genul de independenta pentru care s-a ales
 * un filtru de nucleu si nu un sablon copiat.
 *
 * `get_comments_number` trebuie filtrat separat, altfel raman titlurile de
 * forma "2 comentarii" deasupra unei liste goale.
 *
 * @param array $comments Comentariile gasite pentru articolul curent.
 * @return array Lista goala.
 */
function simonamarin_hide_existing_comments( $comments ) {
	return array();
}
add_filter( 'comments_array', 'simonamarin_hide_existing_comments', 20 );
add_filter( 'get_comments_number', '__return_zero', 20 );

/**
 * Scoate suportul pentru comentarii de la toate tipurile de continut.
 *
 * Fara asta, comentariile raman "sustinute" chiar daca sunt inchise: casuta de
 * discutie ramane in ecranul de editare, coloana cu bula de comentarii ramane
 * in listele de articole, iar un articol nou creat porneste din nou cu
 * comentarii deschise. Se ruleaza pe `init` pentru ca abia atunci sunt
 * inregistrate toate tipurile de continut, inclusiv cele venite din pluginuri.
 */
function simonamarin_remove_comment_support() {
	foreach ( get_post_types() as $post_type ) {
		if ( post_type_supports( $post_type, 'comments' ) ) {
			remove_post_type_support( $post_type, 'comments' );
			remove_post_type_support( $post_type, 'trackbacks' );
		}
	}
}
add_action( 'init', 'simonamarin_remove_comment_support' );

/**
 * Opreste feed-urile de comentarii.
 *
 * Un feed de comentarii este o a doua cale catre acelasi continut, pe care
 * filtrele de afisare de mai sus nu o acopera: /feed/ de pe un articol
 * raspundea cu 200 si servea comentariile in XML. Se scoate atat legatura
 * anuntata in <head>, cat si raspunsul propriu-zis, pentru ca adresa poate fi
 * ceruta direct, fara sa fi fost vreodata anuntata.
 */
add_filter( 'feed_links_show_comments_feed', '__return_false' );

function simonamarin_block_comment_feeds() {
	if ( is_comment_feed() ) {
		wp_die(
			'Site-ul nu are comentarii.',
			'',
			array( 'response' => 404 )
		);
	}
}
add_action( 'template_redirect', 'simonamarin_block_comment_feeds', 1 );

/**
 * Scoate comentariile din API-ul REST pentru vizitatorii nelogati.
 *
 * /wp-json/wp/v2/comments serveste comentariile aprobate oricui le cere, fara
 * sa treaca prin tema si deci fara sa treaca prin filtrele de afisare. Pentru
 * cineva nelogat este o a treia cale catre acelasi continut.
 *
 * Rutele se scot doar pentru vizitatori. Utilizatorii logati le pastreaza,
 * pentru ca editorul de articole si ecranele de administrare se bazeaza pe
 * ele, iar scopul aici este ce vede publicul, nu ce poate face proprietarul
 * site-ului.
 *
 * @param array $endpoints Rutele REST inregistrate.
 * @return array Rutele, fara cele de comentarii pentru vizitatori.
 */
function simonamarin_remove_comment_rest_routes( $endpoints ) {
	if ( is_user_logged_in() ) {
		return $endpoints;
	}

	unset( $endpoints['/wp/v2/comments'] );
	unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'simonamarin_remove_comment_rest_routes' );

/**
 * Nu afisa in subsol widgetul "Comentarii recente".
 *
 * Widgetul isi interogheaza singur comentariile, direct prin get_comments(),
 * deci nu trece prin `comments_array` si ar fi ramas singurul loc din site in
 * care cele doua comentarii aprobate mai apareau. Se opreste doar afisarea:
 * widgetul ramane configurat in zona de widgeturi, ca sa nu se piarda
 * asezarea subsolului daca decizia se schimba vreodata.
 *
 * @param array     $instance Setarile widgetului.
 * @param WP_Widget $widget   Obiectul widget.
 * @return array|false Setarile, sau false pentru a opri afisarea.
 */
function simonamarin_hide_recent_comments_widget( $instance, $widget ) {
	if ( $widget instanceof WP_Widget_Recent_Comments ) {
		return false;
	}

	return $instance;
}
add_filter( 'widget_display_callback', 'simonamarin_hide_recent_comments_widget', 10, 2 );
