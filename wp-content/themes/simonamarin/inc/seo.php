<?php
/**
 * Infrastructura SEO a temei - robots.txt, sitemap, canonical, indexare.
 *
 * @package simonamarin
 */

/*
 * =============================================================================
 * AUDIT SEO - INFRASTRUCTURA (2026-09-21)
 * =============================================================================
 *
 * De ce exista acest fisier
 * -------------------------
 * TODO-urile din celelalte fisiere ale temei acopera SEO-ul "on-page": titluri,
 * excerpt, linkuri interne, schema, imagini. Ceea ce lipsea complet din audit
 * era stratul de INFRASTRUCTURA: cum ajunge un crawler pe site (robots.txt),
 * ce lista de URL-uri primeste (sitemap), pe ce host/protocol/forma de URL
 * aterizeaza (canonical + redirecturi) si daca site-ul are voie sa fie indexat.
 * Acest strat poate anula, singur, tot restul muncii de SEO: un `Disallow: /`
 * sau un `blog_public = 0` face irelevante toate celelalte TODO-uri din tema.
 *
 * Starea la momentul auditului
 * ----------------------------
 *   - NU exista robots.txt fizic in radacina instalarii. WordPress serveste un
 *     robots.txt virtual, iar Rank Math il completeaza (Rank Math > General
 *     Settings > Edit robots.txt). Acesta este comportamentul CORECT si nu
 *     trebuie stricat printr-un fisier fizic - vezi SEO-72.
 *   - NU exista sitemap.xml fizic - corect, este generat dinamic. Ramane de
 *     verificat care generator este activ, ca sa nu fie doua - vezi SEO-74.
 *   - .htaccess contine doar blocul standard WordPress, rescris de Duplicator
 *     la migrarea din 2026-09-21. Nu contine reguli de canonical host si nici
 *     redirecturi 301 pentru URL-urile vechi - vezi SEO-78 si SEO-79.
 *     (.htaccess este in .gitignore si e gestionat de Duplicator/LiteSpeed,
 *     de aceea observatiile despre el sunt documentate aici, nu in fisier.)
 *
 * Cum se foloseste fisierul
 * -------------------------
 * Tot codul de mai jos este COMENTAT intentionat. Nimic din acest fisier nu se
 * executa in acest moment. Fiecare bloc este o propunere gata de activat, DUPA
 * ce s-a verificat pe site-ul live ca Rank Math nu face deja acelasi lucru.
 * Regula generala a proiectului ramane: mai intai verifici ce emite pluginul,
 * apoi completezi in tema doar ce lipseste. O directiva dublata (doua
 * canonical, doua sitemap-uri, doua reguli de robots) face mai mult rau decat
 * absenta ei.
 *
 * =============================================================================
 * 1. ROBOTS.TXT
 * =============================================================================
 *
 * TODO [SEO-72][CRITICAL]: robots.txt nu a fost niciodata verificat sau definit
 * explicit pentru acest site. Este primul fisier pe care il cere orice crawler
 * si singurul care poate bloca, dintr-o linie, intreaga indexare.
 *
 * De verificat, in ordine:
 *   1. Deschide https://<domeniu>/robots.txt pe site-ul LIVE (nu pe Local).
 *   2. Confirma ca NU contine `Disallow: /`. Este cea mai frecventa cauza de
 *      "site-ul a disparut din Google" si apare automat cand instalarea a fost
 *      pornita cu optiunea "Descurajeaza motoarele de cautare" bifata.
 *   3. Confirma ca linia `Sitemap:` exista si arata catre sitemap-ul real
 *      (vezi SEO-76).
 *   4. Confirma ca NU exista un robots.txt FIZIC in radacina. Daca exista, el
 *      are prioritate absoluta si anuleaza tot ce configurezi in Rank Math - o
 *      sursa clasica de "am schimbat setarea si nu se intampla nimic".
 *
 * Continut propus (de pus in Rank Math > Edit robots.txt, NU ca fisier fizic):
 *
 *   User-agent: *
 *   Disallow: /wp-admin/
 *   Allow: /wp-admin/admin-ajax.php
 *   Disallow: /wp-login.php
 *   Disallow: /xmlrpc.php
 *   Disallow: /?s=
 *   Disallow: /search/
 *   Disallow: /*?replytocom
 *   Disallow: /wp-content/plugins/
 *   Allow: /wp-content/uploads/
 *
 *   Sitemap: https://<domeniu>/sitemap_index.xml
 *
 * Doua precizari importante despre lista de mai sus:
 *   - NU se blocheaza /wp-content/themes/ si nici fisierele .css / .js. Google
 *     randeaza pagina ca un browser; daca nu poate citi CSS-ul si JS-ul, vede
 *     un site rupt si evalueaza gresit layout-ul si mobile-friendliness.
 *   - `Disallow` nu inseamna `noindex`. Un URL blocat in robots.txt poate
 *     aparea in continuare in rezultate, fara descriere. Paginile care trebuie
 *     sa dispara efectiv din index au nevoie de meta robots `noindex`, iar
 *     pentru asta crawlerul TREBUIE sa aiba voie sa le citeasca. Cele doua
 *     mecanisme nu se combina pe acelasi URL.
 *
 * TODO [SEO-73][CRITICAL]: Verifica Settings > Reading > "Search engine
 * visibility". Daca optiunea este bifata, WordPress seteaza `blog_public = 0`,
 * emite `<meta name="robots" content="noindex, nofollow">` pe TOT site-ul si
 * modifica robots.txt-ul virtual. Este singura setare din WordPress care poate
 * anula, cu un click, intreg efortul de SEO. Se verifica obligatoriu dupa
 * fiecare migrare, clonare sau restaurare din backup - site-ul a fost migrat cu
 * Duplicator pe 2026-09-21, deci verificarea este necesara acum.
 *
 * TODO [SEO-87][LOW]: Decizie de business, nu tehnica: se permite sau nu
 * accesul crawlerelor de AI (GPTBot, ClaudeBot, CCBot, Google-Extended,
 * PerplexityBot) la continutul site-ului? Pentru un cabinet de psihoterapie,
 * articolele sunt principalul material care construieste autoritate, iar
 * aparitia lor in raspunsuri generate de AI poate aduce vizibilitate fara
 * click. Nu exista un raspuns corect universal - dar decizia trebuie luata
 * explicit si scrisa in robots.txt, nu lasata implicit. Blocarea se face pe
 * User-agent separat si NU afecteaza indexarea in Google Search.
 *
 * TODO [SEO-92][HIGH]: Mediul de dezvoltare (Local) si orice staging viitor
 * trebuie sa fie complet neindexabile. Pe Local nu exista risc real - domeniul
 * nu e public - dar in momentul in care apare un staging pe subdomeniu public,
 * el devine un clon integral al site-ului: continut duplicat 1:1, care poate
 * ajunge sa fie canonicalul ales de Google in locul productiei. Regula: pe
 * staging se bifeaza "Search engine visibility" SI se trimite antetul
 * `X-Robots-Tag: noindex` la nivel de server, plus protectie cu parola.
 *
 * -----------------------------------------------------------------------------
 * Stub pentru completarea robots.txt DIN TEMA.
 * De activat DOAR daca se renunta la editorul de robots.txt din Rank Math -
 * altfel cele doua surse se contrazic si devine imposibil de depanat.
 * -----------------------------------------------------------------------------
 *
 * function simonamarin_robots_txt( $output, $public ) {
 *     if ( '1' !== (string) $public ) {
 *         return $output; // Site marcat privat - nu completam nimic.
 *     }
 *     $output .= "Disallow: /wp-login.php\n";
 *     $output .= "Disallow: /xmlrpc.php\n";
 *     $output .= "Disallow: /?s=\n";
 *     $output .= "Allow: /wp-content/uploads/\n";
 *     return $output;
 * }
 * add_filter( 'robots_txt', 'simonamarin_robots_txt', 10, 2 );
 *
 * =============================================================================
 * 2. SITEMAP XML
 * =============================================================================
 *
 * TODO [SEO-74][HIGH]: Pe site exista DOUA generatoare de sitemap simultan si
 * nu s-a verificat niciodata care este activ:
 *   - WordPress core, din 5.5 incoace, expune /wp-sitemap.xml;
 *   - Rank Math expune /sitemap_index.xml.
 * Rank Math dezactiveaza de regula sitemap-ul de core cand propriul modul este
 * pornit, dar asta trebuie CONFIRMAT deschizand ambele URL-uri in browser.
 * Doua sitemap-uri valide inseamna doua liste de URL-uri, potential
 * divergente, si semnale contradictorii despre ce conteaza pe site.
 *
 * De verificat in sitemap-ul care ramane activ:
 *   - Contine paginile si articolele reale, cu `lastmod` corect.
 *   - NU contine URL-uri marcate `noindex` (contradictie directa: ii spui lui
 *     Google "crawl-uieste asta" si "nu o indexa" in acelasi timp).
 *   - NU contine paginile de atasament, arhivele de autor si arhivele de data
 *     daca acestea sunt dezactivate sau noindex (vezi SEO-30 si SEO-77).
 *   - NU contine URL-uri care raspund 404 sau 301 - un sitemap "murdar" reduce
 *     increderea in intregul fisier.
 *   - Este trimis in Google Search Console si in Bing Webmaster Tools.
 *
 * TODO [SEO-75][MEDIUM]: Sitemap de imagini. Pentru un site de cabinet, cu
 * fotografii proprii, imaginile sunt o sursa reala de trafic prin Google
 * Images. Rank Math include imaginile in sitemap-ul de pagini daca optiunea
 * "Include Images" este pornita - de verificat. Are sens doar impreuna cu
 * alt-text corect (vezi SEO-20, unde alt-ul real este suprascris din tema) si
 * cu nume de fisier descriptive (vezi SEO-91).
 *
 * TODO [SEO-76][HIGH]: Linia `Sitemap:` din robots.txt trebuie sa arate catre
 * sitemap-ul activ, cu URL absolut si https. Este modalitatea prin care
 * crawlerele fara acces la Search Console (Bing, Yandex, motoare alternative)
 * descopera lista de URL-uri. Daca se schimba generatorul de sitemap
 * (SEO-74), aceasta linie trebuie actualizata in aceeasi operatiune - altfel
 * ramane un `Sitemap:` care duce la 404.
 *
 * -----------------------------------------------------------------------------
 * Stub: dezactivarea sitemap-ului de core, DACA se confirma ca ruleaza in
 * paralel cu cel din Rank Math si Rank Math nu il opreste singur.
 * -----------------------------------------------------------------------------
 *
 * add_filter( 'wp_sitemaps_enabled', '__return_false' );
 *
 * Varianta mai fina, daca se pastreaza sitemap-ul de core si se doreste doar
 * scoaterea tipurilor de continut irelevante:
 *
 * function simonamarin_sitemap_exclude( $post_types ) {
 *     unset( $post_types['attachment'] );
 *     return $post_types;
 * }
 * add_filter( 'wp_sitemaps_post_types', 'simonamarin_sitemap_exclude' );
 *
 * =============================================================================
 * 3. CANONICAL, REDIRECTURI, FORMA URL-URILOR
 * =============================================================================
 *
 * TODO [SEO-79][CRITICAL]: Nu exista nicaieri o regula care sa impuna o forma
 * unica a URL-ului. Acelasi continut poate fi servit pe patru variante:
 * http/https x www/non-www, la care se adauga varianta cu si fara slash final.
 * Fiecare combinatie accesibila cu status 200 este continut duplicat.
 * Corect: o singura forma canonica, toate celelalte redirectate 301 catre ea,
 * intr-un SINGUR pas (lanturile http -> https -> www aduna latenta si dilueaza
 * semnalul). Really Simple SSL este instalat si acopera http -> https, dar o
 * face in PHP; la nivel de server regula e mai rapida si mai sigura.
 *
 * TODO [SEO-80][HIGH]: Dupa migrarea cu Duplicator (2026-09-21), `siteurl` si
 * `home` din Settings > General trebuie sa contina domeniul de PRODUCTIE, cu
 * https si in forma canonica aleasa la SEO-79. Daca in baza de date raman
 * URL-uri absolute catre domeniul de Local sau catre vechiul domeniu, ele
 * ajung in continut, in sitemap si in canonical. Se corecteaza cu Search &
 * Replace din Duplicator sau cu WP-CLI, care serializeaza corect - niciodata
 * prin editare manuala a articolelor.
 *
 * TODO [SEO-78][CRITICAL]: Nu exista o harta de redirecturi 301 pentru
 * URL-urile vechi. Site-ul a fost migrat, iar tema anterioara a fost inlocuita
 * cu un schelet Underscores - ceea ce inseamna, aproape sigur, ca structura de
 * pagini s-a schimbat. Fiecare URL vechi care raspunde acum 404 este un link
 * extern pierdut si o pozitie in Google pierduta.
 * Plan: exporta lista de pagini indexate din Search Console (raportul Pages),
 * compara cu lista de URL-uri actuale din sitemap si mapeaza 1:1 fiecare URL
 * disparut catre cel mai apropiat echivalent. Redirect catre homepage doar
 * acolo unde chiar nu exista echivalent - redirectarea in masa catre homepage
 * este tratata de Google ca soft 404.
 * Rank Math are modulele "404 Monitor" si "Redirections"; se pornesc ambele,
 * 404 Monitor arata in cateva zile exact ce URL-uri sunt cerute si lipsesc.
 *
 * TODO [SEO-77][MEDIUM]: Paginile de atasament sunt, implicit, URL-uri
 * indexabile care contin o singura imagine si nimic altceva - continut subtire
 * pe fiecare imagine incarcata vreodata. Corect: redirect catre articolul
 * parinte. Rank Math are optiunea "Redirect Attachments" in General Settings;
 * de verificat ca este pornita. Se trateaza impreuna cu SEO-74, pentru ca
 * aceleasi URL-uri nu trebuie sa apara nici in sitemap.
 *
 * TODO [SEO-85][MEDIUM]: Crawl budget si URL-uri parazit. Chiar daca pe un
 * site mic bugetul de crawl nu este o problema reala, URL-urile cu parametri
 * creeaza duplicat: `?s=` (cautare interna, vezi si SEO-28), `?utm_*` din
 * campanii, `?replytocom` (dispar odata cu inchiderea comentariilor, deja
 * implementata in functions.php), variante de paginare. Regula: parametrii de
 * tracking nu trebuie sa produca niciodata un canonical diferit - canonicalul
 * emis de Rank Math trebuie sa fie mereu varianta curata a URL-ului.
 *
 * TODO [SEO-86][MEDIUM]: Paginarea arhivelor. `rel="next"` / `rel="prev"` nu
 * mai sunt folosite de Google din 2019, deci nu are rost sa fie adaugate.
 * Ce conteaza acum: fiecare pagina /page/N/ trebuie sa aiba canonical catre EA
 * INSASI - nu catre pagina 1, altfel articolele de pe paginile 2+ nu mai sunt
 * descoperite - titlu diferentiat si linkuri numerotate in loc de "Posts
 * navigation" (vezi SEO-39).
 *
 * =============================================================================
 * 4. DESCOPERIRE, MONITORIZARE, SEMNALE EXTERNE
 * =============================================================================
 *
 * TODO [SEO-84][HIGH]: Google Site Kit este instalat, dar nu s-a verificat daca
 * proprietatea din Search Console este cea corecta. Fara Search Console validat
 * nu exista date despre indexare, interogari, pagini in eroare sau Core Web
 * Vitals - adica nu exista feedback pentru niciunul dintre TODO-urile de SEO
 * din acest proiect. De verificat: proprietate de tip Domain (nu prefix URL),
 * sitemap trimis, raportul Pages fara erori majore. In paralel, Bing Webmaster
 * Tools poate importa configuratia direct din Search Console.
 *
 * TODO [SEO-88][MEDIUM]: Indexare rapida a continutului nou. LiteSpeed Cache si
 * Rank Math ofera IndexNow / Instant Indexing, care anunta Bing si Yandex in
 * secunde de la publicare. Efort: o bifa. Google nu foloseste IndexNow, dar
 * pentru Google conteaza ca sitemap-ul are `lastmod` corect (vezi SEO-74).
 *
 * TODO [SEO-90][LOW]: Core Web Vitals se masoara pe date reale de utilizator
 * (CrUX), nu pe scorul Lighthouse din laborator. Sursa de adevar este raportul
 * Core Web Vitals din Search Console. Pe un site cu trafic mic el ramane gol -
 * caz in care se foloseste PageSpeed Insights, dar stiind ca masoara un singur
 * load. TODO-urile PERF din tema se prioritizeaza dupa ce exista date reale.
 *
 * =============================================================================
 * 5. SEMNALE DE INCREDERE SI SEO LOCAL
 * =============================================================================
 *
 * TODO [SEO-89][HIGH]: SEO local. Un cabinet de psihoterapie este un business
 * cu localizare fizica: o parte importanta din cautari sunt de forma
 * "psiholog <oras>" si se rezolva in pachetul local, nu in rezultatele
 * clasice. Pentru asta e nevoie de: profil Google Business Profile revendicat,
 * NAP (nume, adresa, telefon) identic pe site si in profil - lipseste complet
 * din footer, vezi SEO-17 - si schema LocalBusiness sau ProfessionalService cu
 * adresa si program. Fara NAP consistent, site-ul si profilul nu sunt asociate
 * ca fiind aceeasi entitate.
 *
 * TODO [SEO-93][HIGH]: Continutul intra in categoria YMYL (Your Money or Your
 * Life) - sanatate mintala. Google aplica acestor pagini cel mai strict filtru
 * de calitate, iar semnalele de expertiza nu sunt optionale: pagina de autor cu
 * formare, acreditari si numar de inregistrare profesionala, schema Person
 * legata de articole prin `author`, si `sameAs` catre profilurile profesionale
 * publice. Fara ele, continutul unui autor real concureaza in aceleasi conditii
 * cu continut anonim - si pierde.
 *
 * TODO [SEO-82][MEDIUM]: Site icon si imagine sociala implicita. Favicon-ul
 * apare in rezultatele de cautare pe mobil, langa numele site-ului, si este
 * primul semnal vizual de brand in SERP. Se seteaza din Customizer > Site
 * Identity > Site Icon (minim 512x512). Separat, Rank Math trebuie sa aiba o
 * imagine OG implicita pentru paginile fara imagine reprezentativa - altfel
 * link-urile partajate pe WhatsApp si Facebook apar fara imagine.
 *
 * TODO [SEO-83][MEDIUM]: Limba. `language_attributes()` emite valoarea din
 * Settings > General > Site Language. Daca instalarea a ramas pe en_US, fiecare
 * pagina declara `lang="en-US"` peste continut in romana - semnal gresit pentru
 * targetarea geografica (vezi si SEO-08). Site-ul nu are versiune in alta
 * limba, deci hreflang NU este necesar; de adaugat doar daca apare o versiune
 * tradusa, si atunci obligatoriu bidirectional.
 *
 * TODO [SEO-91][LOW]: Imagini - partea care nu tine de cod. Numele fisierului
 * este un semnal de relevanta in Google Images: `cabinet-psihoterapie-cluj.jpg`
 * spune ceva, `IMG_20240312_113245.jpg` nu. Numele se stabileste INAINTE de
 * upload; redenumirea ulterioara schimba URL-ul si strica linkurile existente.
 * Compresia si WebP sunt deja acoperite de pluginurile instalate
 * (webp-uploads, performance-lab) - de verificat ca lucreaza efectiv.
 *
 * =============================================================================
 * Ordinea recomandata
 * =============================================================================
 *   1. SEO-73 si SEO-72 - confirma ca site-ul POATE fi indexat.
 *   2. SEO-79 si SEO-80 - o singura forma canonica de URL.
 *   3. SEO-78           - redirecturile 301 dupa migrare.
 *   4. SEO-74 si SEO-76 - un singur sitemap, corect, anuntat in robots.txt.
 *   5. SEO-84           - Search Console validat, ca sa existe feedback.
 *   6. Restul.
 *
 * Primele cinci puncte nu necesita nicio linie de cod in tema: sunt setari si
 * verificari. Acesta este si motivul pentru care fisierul de fata este,
 * deocamdata, exclusiv documentatie.
 */
