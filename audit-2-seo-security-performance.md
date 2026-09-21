# Audit 2 — SEO / Securitate / Performanță / Cod inutil

**Tema auditată:** `wp-content/themes/simonamarin/` (temă activă — confirmat din `app/sql/local.sql`: `template` și `stylesheet` = `simonamarin`)
**Data:** 21 septembrie 2026
**Metodă:** citire integrală a codului temei. Zero modificări de conținut — doar comentarii `TODO` în cod.
**Verificare:** toate cele 20 de fișiere PHP trec `php -l` (PHP 8.2). `composer.json` și `package.json` sunt JSON valid.

---

## 0. Ce s-a schimbat efectiv în această sesiune

| Fișier | Modificare |
|---|---|
| `composer.json` | **Bug reparat.** Conținea un bloc de comentariu `/* … */` în interiorul obiectului `require`, adăugat la auditul anterior. JSON nu acceptă comentarii → `composer install` și `composer lint:php` eșuau cu Parse error. Comentariul a fost mutat într-o cheie string validă. |
| restul fișierelor | Doar comentarii `TODO` adăugate. Niciun comportament schimbat. |

**63 de marcaje TODO** adăugate, cu ID-uri urmăribile (`SEO-01` … `SEO-40`, `SEC-01` … `SEC-10`, `PERF-01` … `PERF-13`, `CSS-01` … `CSS-10`, `CLEAN-01` … `CLEAN-07`, `A11Y-01` … `A11Y-04`, `BUG-01` … `BUG-02`).

Distribuția pe fișiere: `functions.php` 41 · `header.php` 12 · `style.css` 9 · `inc/template-tags.php` 6 · `comments.php` 4 · `footer.php` 4 · restul 1–3.

---

## 1. SEO — secțiunea cea mai importantă

### Constatarea de fond

Tema este **Underscores (`_s`) practic neatins**. Underscores este un starter theme: prin definiție nu conține niciun fel de SEO. Nu are schema, nu are Open Graph, nu are breadcrumbs, nu are paginare numerotată, nu are related posts, nu are meniu de footer. Toate acestea lipsesc pentru că nimeni nu le-a adăugat, nu pentru că ar fi fost șterse.

Rank Math (`seo-by-rank-math`) este instalat și acoperă o parte din aceste lipsuri (title, meta description, canonical, sitemap, o schemă de bază). **Regula pentru sesiunile următoare: verifică întâi ce emite Rank Math în HTML-ul live, apoi completează în temă doar ce lipsește.** Un canonical dublu sau o schemă Article duplicată fac mai mult rău decât absența lor.

### Câștiguri mari, efort mic — de făcut primele

| ID | Problemă | Fișier |
|---|---|---|
| **SEO-16** | Două link-uri externe **dofollow pe fiecare pagină din site** (`wordpress.org`, `underscores.me`). Scurgere de autoritate către site-uri fără legătură cu subiectul. Se rezolvă prin ștergere — 2 minute. | [footer.php](wp-content/themes/simonamarin/footer.php) |
| **SEO-24 / SEO-38** | Listările (homepage, arhive) afișează `the_content()` **integral**, nu excerpt. Același text apare pe homepage + arhivă categorie + arhivă tag + pagina articolului. Conținut duplicat masiv; Google alege singur canonicalul și de multe ori greșit. | [template-parts/content.php](wp-content/themes/simonamarin/template-parts/content.php) |
| **SEO-03** | `$content_width = 640` — valoarea default din Underscores, nu lățimea reală a coloanei. WordPress o folosește ca plafon pentru imaginile din conținut → imaginea LCP e servită mică și arată blurat pe desktop. | [functions.php](wp-content/themes/simonamarin/functions.php) |
| **SEO-15** | `wp_nav_menu()` fără `'fallback_cb' => false`. Dacă meniul nu e asignat, WordPress listează **toate** paginile publicate, inclusiv thank-you/landing/test. Link-uri interne nedorite pe fiecare pagină. | [header.php](wp-content/themes/simonamarin/header.php) |
| **SEO-20** | Argumentul `alt` din `the_post_thumbnail()` **suprascrie** alt-textul real al imaginii cu titlul articolului. Se pierde textul descriptiv din Media Library → relevanță pierdută în Google Images. | [inc/template-tags.php](wp-content/themes/simonamarin/inc/template-tags.php) |

### Lipsuri structurale

- **SEO-01 / SEO-37** — zero JSON-LD propriu. Fără BreadcrumbList și Person/Organization complete, nu există rich results.
- **SEO-02** — breadcrumbs inexistente în template-uri. Rank Math oferă `rank_math_the_breadcrumbs()`; trebuie activat și apelat (loc marcat în `header.php`, după `</nav>`).
- **SEO-35** — fără related posts. Fiecare articol e o „frunză" în graful intern: primește link, nu trimite mai departe.
- **SEO-39** — `the_posts_navigation()` în loc de `the_posts_pagination()`. Fără paginare numerotată, articolele de la pagina 5+ sunt la 5 click-uri de homepage → crawl depth mare.
- **SEO-10 / SEO-18** — o singură locație de meniu. Fără meniu de footer nu există linking secundar către servicii / contact / pagini legale.
- **SEO-17** — footerul nu conține NAP (nume, adresă, telefon), program, link-uri legale sau sociale. Fără NAP consistent nu există semnal de Local SEO / Google Business Profile.
- **SEO-14 / SEO-27** — risc de `<h1>` dublu: `content-page.php` emite automat titlul ca `<h1>`, iar paginile construite cu blocuri au de regulă propriul `<h1>` în conținut. De verificat pagină cu pagină în browser.

### De verificat în Rank Math (nu în cod)

- **SEO-28** — rezultatele căutării interne trebuie `noindex, follow`. Altfel devin soft 404 și vector de SEO poisoning.
- **SEO-30** — arhivele de autor și de dată sunt active. Pe site cu un singur autor, arhiva de autor e duplicat aproape perfect al homepage-ului.
- **SEO-29 / SEO-34** — paginare arhive și `/comment-page-2/`.
- **SEO-08** — verifică Settings → General → Site Language = `ro_RO`, altfel `language_attributes()` scoate `lang="en-US"` pe conținut românesc.

---

## 2. Securitate

Nu am găsit nicio vulnerabilitate exploatabilă direct în codul temei. Tema **nu procesează input de utilizator** (`$_GET`/`$_POST`/`$_REQUEST` sunt complet absente) și **nu face query-uri SQL directe** — deci fără SQLi (SEC-05, verificat). `DISALLOW_FILE_EDIT` este deja activ în `wp-config.php`. Problemele de mai jos sunt reale, dar de tip expunere și igienă.

| ID | Severitate | Problemă |
|---|---|---|
| **SEC-06** | Critică | `header.php`: `echo $simonamarin_description;` fără escapare, mascat cu `phpcs:ignore`. Valoarea vine din `blogdescription`, editabilă de orice cont cu `manage_options`. XSS stocat dacă un cont e compromis sau dacă un plugin scrie în opțiune. Fix: `wp_kses_post()`. |
| **SEC-01** | Critică | Zero security headers: fără CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy. HSTS poate fi acoperit de `really-simple-ssl` — de verificat. Atenție: CSP trebuie testat, poate rupe Site Kit și Cookie Law Info. |
| **SEC-03** | Mare | `inc/template-functions.php` publică URL-ul XML-RPC în `<head>` pe fiecare pagină. Vector de spam pingback și de amplificare DDoS. Loginizer acoperă doar login-ul clasic, nu și xmlrpc. |
| **SEC-02** | Mare | Versiunea WordPress expusă prin meta generator și `?ver=` pe fiecare asset core → fingerprinting pentru exploituri automate. |
| **SEC-09** | Medie | Formularul de comentarii nu are protecție anti-spam. **Akismet nu este instalat.** Pentru un site de prezentare, cea mai sigură opțiune e dezactivarea comentariilor din Settings → Discussion. |
| **SEC-04** | Medie | Enumerarea autorilor: `/?author=1` redirecționează către `/author/<username>/` și dezvăluie username-ul de admin. |
| **SEC-07** | Medie | `footer.php`: link pe `http://` către underscores.me, pe site cu SSL forțat → mixed-content warning. |
| **SEC-10** | Mică | Cookie-uri `comment_author_*` pentru vizitatori nelogați — trebuie declarate în politica de cookies (GDPR, cu cookie-law-info instalat). |

---

## 3. Performanță

| ID | Severitate | Problemă |
|---|---|---|
| **PERF-01** | Critică | `_S_VERSION` hardcodat `'1.0.0'`, niciodată incrementat → **cache busting inexistent**. După orice editare de CSS/JS, vizitatorii recurenți și LiteSpeed servesc fișierul vechi. Fix: `filemtime()`. |
| **PERF-10** | Critică | `the_post_thumbnail()` fără argumente pe single/page. WordPress adaugă automat `loading="lazy"` — exact pe imaginea care e de regulă elementul LCP. Fără `fetchpriority="high"`. Impact direct pe Core Web Vitals. |
| **PERF-02** | Mare | `navigation.js` se încarcă pe fiecare pagină chiar și fără meniu, fără `defer`. |
| **PERF-03** | Mare | Nu se dezactivează balastul core: emoji script + styles (~15KB JS), `wp-embed.min.js`, RSD, wlwmanifest, shortlink, generator. |
| **PERF-04 / PERF-07** | Mare | Fără resource hints. `style.css` e render-blocking, fără preload și fără critical CSS. LiteSpeed are opțiune CCSS — de verificat dacă e activată. |
| **PERF-09** | Mare | `the_custom_logo()` fără width/height explicite → CLS. Logo-ul e above-the-fold și nu trebuie lazy-loaded. |
| **PERF-11** | — | `image-prioritizer`, `optimization-detective`, `webp-uploads`, `performance-lab`, `speculation-rules`, `embed-optimizer` sunt **deja instalate**. Verifică HTML-ul live înainte de a implementa manual orice din cele de mai sus — riscul principal este dublarea optimizărilor, nu absența lor. |

---

## 4. CSS și JS inutil

### `style.css` — 977 linii, practic tot default Underscores

- **CSS-01** — nu conține niciun stil propriu al site-ului: fără culori de brand, fără tipografie proprie, fără layout. Ori designul real vine din altă parte (și atunci aproape tot fișierul e CSS mort livrat la fiecare pagină), ori site-ul chiar arată ca tema default. **Nu ștergem nimic fără măsurătoare** — rulează Coverage în Chrome DevTools pe 5 pagini reprezentative.
- **CSS-02** — ~320 de linii (68–385) sunt normalize.css v8.0.1 integral, cu reguli pentru IE 8/9/10/11 și Edge legacy (`-ms-text-size-adjust`, `button::-moz-focus-inner`, fix-uri `<legend>` și `progress` în IE). Cod mort în 2026.
- **CSS-06** — secțiunea `# Layouts` este **complet goală**. Fără container cu lățime maximă, textul se întinde pe toată fereastra pe ecrane mari.
- **CSS-07** — **un singur `@media`** în tot fișierul (linia ~771, doar pentru afișarea meniului pe desktop). Fără niciun breakpoint de tipografie sau spațiere. Contează direct la mobile-first indexing.
- **CSS-03** — secțiunea „Jetpack infinite scroll" e CSS mort (Jetpack nu e instalat).
- **CSS-04** — `.gallery-columns-2` … `-9` generate toate, deși se folosesc 2–4.
- **CSS-05** — zero CSS custom properties; blochează schimbarea de brand și dark mode.
- **Verificat OK:** `img { height: auto; max-width: 100% }` **există** (linia ~584), la fel și pentru embed/iframe/object. Nu apare scroll orizontal din cauza imaginilor.

### JavaScript

- **BUG-01 (real, `navigation.js`)** — `toggleFocus()` citește `event` ca variabilă **globală implicită** (`window.event`), nu ca parametru. `window.event` e non-standard și deprecated, aruncă `ReferenceError` în mod strict / module ES. Navigarea cu TAB prin submeniuri se rupe silențios pe unele browsere. Fix: declară parametrul — `function toggleFocus( event )`.
- **`customizer.js` — reprioritizat de la CRITICAL la LOW.** Auditul anterior îl marcase drept critic pentru dependența de jQuery. Fișierul se încarcă **doar** în previzualizarea din Customizer (`customize_preview_init`), niciodată pe frontend-ul public — iar jQuery e oricum încărcat de WordPress în ecranul de Customizer. Impact real asupra performanței și SEO: **zero**. Rescrierea rămâne igienă de cod, dar nu merită timp înaintea SEO.

### Fișiere și cod mort

- **CLEAN-02** — scripturile npm `watch` și `compile:css` citesc din `sass/`, **director care nu există**. Build-ul e mort. ⚠️ Nu rula `npm run compile:css` — ar putea suprascrie `style.css`.
- **CLEAN-04** — tema **`sydney` (6,2 MB) este inactivă** în `wp-content/themes/`, iar `style.css`-ul ei a fost suprascris cu reguli ad-hoc și **nu mai conține antetul `Theme Name`** — e o temă coruptă. Temele inactive nu primesc update-uri și sunt vector clasic de atac. De șters după backup. (E în afara scope-ului git al temei — nu am atins-o.)
- **CLEAN-03** — fișiere de dezvoltare livrate în producție: `.eslintrc`, `.stylelintrc.json`, `phpcs.xml.dist`, `composer.json`, `package.json`, `README.md`, `readme.txt`, `LICENSE`.
- **CLEAN-05** — `inc/jetpack.php` e cod mort (include condiționat, deci impact runtime zero).
- **CLEAN-06** — metadate neconfigurate: `style.css` declară încă `Author: Underscores.me`, `Description: Description`, Theme URI către underscores.me.
- **Versiuni declarate greșit** — `Requires PHP: 5.6` (EOL feb. 2017) și `Tested up to: 5.4` (2020). Cele două locuri, `style.css` și `composer.json`, trebuie ținute sincronizate.

---

## 5. Accesibilitate

- **A11Y-02** — `<nav>` fără `aria-label`; va deveni problemă când se adaugă breadcrumbs și meniu de footer.
- **A11Y-04** — meniul mobil nu se închide cu Escape și nu are focus trap. Utilizatorul care navighează doar cu tastatura nu poate închide meniul. Criteriu WCAG 2.1.
- **A11Y-01** — lipsește `'navigation-widgets'` din `html5` support → widget-urile de navigație ies fără `<nav>` semantic.
- **A11Y-03** — butonul de meniu nu își schimbă textul/starea la deschidere.

---

## 6. Ordinea recomandată pentru sesiunile următoare

**Sesiunea 1 — câștig maxim, risc minim (~1–2 ore)**
`SEO-16` ștergere link-uri footer · `SEO-15` `fallback_cb => false` · `SEO-20` scoate `alt` suprascris · `SEC-06` `wp_kses_post()` · `BUG-01` parametrul `event` · `PERF-01` versionare prin `filemtime`. Toate sunt modificări mici, izolate, ușor de testat.

**Sesiunea 2 — audit live înainte de cod**
Deschide site-ul live și inspectează HTML-ul: ce emite Rank Math (canonical, OG, schema), ce face LiteSpeed, ce fac pluginurile de imagine. Abia apoi decide ce se implementează în temă. Rulează Coverage în DevTools pentru CSS-01.

**Sesiunea 3 — SEO structural**
`SEO-02` breadcrumbs · `SEO-24/38` excerpt în listări · `SEO-39` paginare numerotată · `SEO-35` related posts · `SEO-17/18` footer util + meniu de footer.

**Sesiunea 4 — performanță și curățenie**
`PERF-10` imaginea LCP · `PERF-02/03` dequeue și defer · `SEO-03` content width · `CLEAN-04` ștergerea temei sydney · `CLEAN-02` decizie asupra build-ului SCSS.

**Sesiunea 5 — securitate și accesibilitate**
`SEC-01` headers (cu testare atentă) · `SEC-03` xmlrpc · `SEC-02` ascunderea versiunii · `SEC-04` enumerare autori · `A11Y-02/04`.

---

## 7. Note de metodă

- **Nu s-a modificat niciun text de pagină sau articol.** Singura schimbare funcțională e repararea JSON-ului din `composer.json`.
- Marcajele `TODO` sunt căutabile după ID. Pentru a le lista pe toate:
  `grep -rn "TODO \[" wp-content/themes/simonamarin/`
- Tema sydney nu a fost atinsă — e în afara scope-ului definit în `CLAUDE.md`.
- Multe dintre constatări depind de ce fac cele 19 pluginuri instalate. Raportul marchează explicit unde e nevoie de verificare live înainte de a scrie cod, tocmai ca să nu dublăm optimizări existente.

---

## 8. SEO — stratul de infrastructură (adăugat 2026-09-21, sesiunea 3)

Auditul inițial acoperea SEO-ul *on-page* (titluri, excerpt, schema, linking intern). Lipsea complet stratul care decide dacă site-ul este **descoperit și indexat** — robots.txt, sitemap, forma canonică a URL-urilor, redirecturile după migrare. Acest strat poate anula singur tot restul: un `Disallow: /` sau un `blog_public = 0` face irelevante toate celelalte TODO-uri din temă.

Toate TODO-urile noi (**SEO-72 … SEO-93**) sunt documentate în fișierul nou [inc/seo.php](wp-content/themes/simonamarin/inc/seo.php), inclus din `functions.php`. Fișierul conține **doar documentație și stub-uri comentate** — nicio linie nu se execută la runtime.

### Constatări la momentul auditului

- **Nu există `robots.txt` fizic** în rădăcină → WordPress servește unul virtual, completat de Rank Math. Corect; nu trebuie stricat printr-un fișier fizic.
- **Nu există `sitemap.xml` fizic** → generat dinamic. Rămâne de confirmat care generator e activ: `/wp-sitemap.xml` (core) sau `/sitemap_index.xml` (Rank Math). Două sitemap-uri valide = două liste de URL-uri divergente.
- **`.htaccess`** conține doar blocul standard WordPress, rescris de Duplicator la migrarea din 21.09.2026. Fără reguli de canonical host, fără redirecturi 301 pentru URL-urile vechi. (Fișierul e în `.gitignore` și gestionat de Duplicator/LiteSpeed — de aceea observațiile stau în `inc/seo.php`, nu în el.)

### Priorități — blocante de indexare

| ID | Problemă | Unde se rezolvă |
|---|---|---|
| **SEO-73** | `Search engine visibility` (Settings → Reading). Bifat = `noindex` pe tot site-ul. Se verifică obligatoriu după orice migrare — iar site-ul tocmai a fost migrat cu Duplicator. | WP Admin |
| **SEO-72** | `robots.txt` niciodată verificat. Conținut propus complet în `inc/seo.php`. Regula cheie: **nu** se blochează CSS/JS, altfel Google randează un site rupt. | Rank Math → Edit robots.txt |
| **SEO-79** | Nicio regulă de formă canonică a URL-ului: http/https × www/non-www × cu/fără slash final = până la 8 variante ale aceleiași pagini, toate cu status 200. | server / .htaccess |
| **SEO-80** | `siteurl` / `home` trebuie să conțină domeniul de producție cu https, nu domeniul de Local rămas după migrare. | WP Admin + Search & Replace |
| **SEO-78** | Zero redirecturi 301 pentru URL-urile vechi, deși tema anterioară a fost înlocuită cu un schelet Underscores. Fiecare 404 = link extern și poziție pierdute. Se pornesc modulele *404 Monitor* + *Redirections* din Rank Math. | Rank Math |
| **SEO-74 / SEO-76** | Un singur sitemap, curat (fără `noindex`, fără 404/301, cu `lastmod` corect), anunțat prin linia `Sitemap:` din robots.txt. | Rank Math + GSC |
| **SEO-84** | Site Kit e instalat, dar proprietatea Search Console nu e verificată. Fără GSC nu există feedback pentru niciun TODO de SEO din proiect. | Site Kit / GSC |

### Restul TODO-urilor noi

`SEO-75` sitemap de imagini · `SEO-77` redirect pagini de atașament · `SEO-82` site icon + imagine OG implicită · `SEO-83` `Site Language = ro_RO` (hreflang **nu** e necesar — site monolingv) · `SEO-85` parametri de query și crawl budget · `SEO-86` canonical self pe `/page/N/` · `SEO-87` decizie privind crawlerele de AI · `SEO-88` IndexNow · `SEO-89` SEO local (NAP + GBP + schema LocalBusiness) · `SEO-90` Core Web Vitals din date reale (CrUX), nu Lighthouse · `SEO-91` nume descriptive de fișier pentru imagini · `SEO-92` staging neindexabil · `SEO-93` YMYL/E-E-A-T — semnale de expertiză pentru conținut de sănătate mintală.

**De reținut:** primele cinci priorități nu necesită nicio linie de cod în temă. Sunt setări și verificări — cel mai bun raport efort/impact din tot auditul.
