# TODO — ce a mai rămas de făcut

**Actualizat:** 21 septembrie 2026, după migrarea la Sydney 2.71.
Înlocuiește listele din audituri. Dacă un punct nu apare aici, nu e de făcut.

---

## Ce rulează de fapt

Înainte de orice, contextul fără de care restul e inutil:

| | |
|---|---|
| Temă activă | **`sydney-child`** (copil), părinte **`sydney` 2.71** |
| Cod independent de temă | `wp-content/mu-plugins/simonamarin-hardening.php` |
| Temă **inactivă**, ignorabilă | `wp-content/themes/simonamarin/` |

⚠️ **Tema `simonamarin/` nu rulează și nu a rulat niciodată.** Conține ~263 de
TODO-uri dintr-un audit făcut pe presupunerea greșită că e tema site-ului.
Vezi secțiunea *Obsolet* la final. Nu lucra acolo.

---

## ⛔ Urgent, în afara codului

**Spațiul pe disc.** Discul a fost complet plin în timpul lucrului (0 MB liberi
din 165 GB). Acum sunt ~300 MB. Când discul se umple, MySQL nu mai poate scrie
și **baza de date se poate corupe** — adică poți pierde conținut, nu doar cod.
Nimic din lista de mai jos nu contează dacă asta se întâmplă.

---

## Deschis, în ordinea valorii

### 1. Google Tag Manager — 520 KB, 53% din pagină

Măsurat pe pagina de start: GTM singur e de **8 ori mai greu decât toată tema
Sydney** (66 KB). Site-ul are simultan Site Kit, GTM, UserFeedback și Cookie
Law Info.

Dimensiunea vine din ce e configurat în containerul Google, nu din cod — nu se
poate reduce din temă. De verificat în GTM ce tag-uri sunt active și dacă toate
sunt încă necesare. **Este cea mai mare optimizare disponibilă pe acest site.**

### 2. MIG-03 — subsolul personalizat

Subsolul de dinainte (contact, Facebook, Instagram, email, lista de servicii) a
dispărut la migrare, fiind scris direct în `footer.php`. Acum se afișează
varianta implicită Sydney.

Se reconstruiește **prin widgeturi sau hook-uri**, nu printr-un `footer.php`
copiat în copil — altfel se rupe la următorul update, exact ca prima dată.
Datele de contact există deja structurate în
`sydney-child/inc/contact-links.php`, funcția `simonamarin_contact_links()`.

### 3. MIG-02 — telefon și tarife în datele structurate

Rank Math emite deja schema (Organization, Person, WebSite, WebPage). Lipsesc
față de blocul vechi: `telephone`, `priceRange`, programul de lucru și tipul
`MedicalBusiness`.

Se completează în **Rank Math → Titles & Meta → Local SEO**, nu în cod. Un bloc
JSON-LD scris de mână ar crea a doua entitate concurentă pentru aceeași
afacere, ceea ce e mai rău decât lipsa datelor.

### 4. MIG-05 — tipografia

De reverificat pe toate paginile. Fonturile sunt setate (Poppins), dar pe unele
titluri apare alt font decât pe producție. Cauza probabilă: CSS-ul adițional al
site-ului (opțiunea `custom_css_post_id`, ID 171) țintește clase din Sydney 1.x
care nu mai există.

### 5. MIG-04 — semnătura de sub titlul articolelor

„Psiholog Simona Marin" stătea în `content-single.php`. Se reface prin hook-ul
`sydney_before_single_entry` sau `sydney_inside_top_post`, fără șablon copiat.

### 6. SEC-13 — formularul de contact și GDPR

Formularul transmite date de sănătate, care intră sub articolul 9 GDPR. De
verificat, în afara codului: transportul (există post-smtp — de confirmat TLS),
cât timp rămân mesajele în baza de date, dacă există temei legal și o informare
afișată lângă formular.

Nu se rezolvă din cod, dar cântărește mai mult decât orice header.

### 7. PSY-04 — informații pentru situații de criză

Site-ul nu conține nicăieri ce face un vizitator aflat în criză. Pe un site de
cabinet de psihoterapie asta e o lipsă de fond.

**Nu se inventează și nu se aproximează.** Numerele și formulările se verifică
la sursă înainte de publicare și se decid împreună cu psihoterapeuta.

### 8. SEC-11 — Content-Security-Policy

Nu e emis, deliberat. Pe site rulează pluginuri care injectează scripturi
inline (cache, formulare, SEO, GTM), iar o politică aplicată direct ar rupe
pagini fără avertisment.

Ordinea corectă: întâi `Content-Security-Policy-Report-Only` cu raportare,
câteva zile pe trafic real, abia apoi politica aplicată. Pasul Report-Only nu
blochează nimic.

### 9. SEC-14 — `<meta name="generator">` de la pluginuri

Șapte etichete rămân în `<head>`, puse de Performance Lab, Site Kit și
celelalte. Fiecare își anunță numele **și versiunea exactă**, ceea ce pentru un
scaner e mai util decât versiunea de WordPress.

Nu le-am scos pentru că fiecare folosește alt hook și un `remove_action` pentru
fiecare s-ar rupe tăcut la primul lor update.

### 10. Poziția iconițelor de contact — decizie deschisă

Sunt acum la capătul meniului, în dreapta. În design-ul vechi stăteau lângă
titlu, în stânga. Dacă poziția din stânga contează, singura variantă curată e
pornirea modulului **header builder** din Sydney 2.71 — care cere însă
reconstruirea întregului antet.

---

## Făcut — nu relua

| Ce | Unde | Verificat |
|---|---|---|
| Headere de securitate | mu-plugin | toate 4 prezente în răspuns |
| `Cache-Control` 30 zile → `no-cache` | mu-plugin | header confirmat |
| Marker de depanare în fiecare pagină | mu-plugin | pagina începe cu `<!DOCTYPE html>` |
| `meta generator` WP, shortlink, wlwmanifest, pingback | mu-plugin | 0 apariții |
| Blocare enumerare autori `?author=N` | mu-plugin | redirect 301 |
| Sydney 1.x → **2.71** | `themes/sydney/` | 735 fișiere, site funcțional |
| Temă copil, activă | `themes/sydney-child/` | randare identică la comutare |
| Punct de restaurare pentru Sydney | git `c2369d8` | 150 fișiere |
| FontAwesome → SVG inline | `inc/icons.php` | **−142 KB, −5 cereri** |
| URL-uri hardcodate către producție | eliminate | 4 cereri → 1 |
| Iconițe de contact în antet | `inc/contact-links.php` | desktop + mobil |
| Culorile antetului | Customizer | măsurate pe producție |
| Lupa de căutare din antet | `header_components_l1` | nu exista în original |
| Depășire orizontală pe mobil | rezolvată de 2.71 | 390px = 390px, 3 pagini |

---

## Obsolet — nu lucra acolo

**`wp-content/themes/simonamarin/`** — temă instalată dar **inactivă**.

Conține ~263 de TODO-uri (SEO-01…93, UX-01…78, PSY, PUB, PERF, SEC, CSS, DEAD)
plus `theme.json`, `DESIGN.md`, traduceri `ro_RO` și `inc/security.php`. Toate
au fost scrise în primele sesiuni, pe presupunerea că aceea e tema site-ului.

Nimic de acolo nu rulează. Verificat: `is_textdomain_loaded('simonamarin')`
întoarce `NU`, iar `stylesheet` din baza de date este `sydney-child`.

Ce a fost recuperat din acea muncă este deja mutat în stiva activă — headerele
de securitate sunt acum în mu-plugin. Restul (paleta din `DESIGN.md`, sistemul
de design, traducerile) e inert; Sydney își aduce propriile traduceri în
română.

**Decizie de luat:** tema se șterge sau se păstrează ca arhivă? Atâta timp cât
stă în `wp-content/themes/`, poate fi activată dintr-o greșeală de clic, ceea
ce ar înlocui instantaneu design-ul site-ului cu un schelet gol.

---

## Cum se verifică, ca să nu se repete greșelile

**Verifică întâi ce rulează.** Tema activă se citește din baza de date
(`stylesheet`), nu se deduce din numele directoarelor. Două sesiuni de muncă au
fost pierdute pe o temă inactivă.

**Capturile pe mobil se fac cu emulare reală de dispozitiv**
(`Emulation.setDeviceMetricsOverride` prin DevTools Protocol), nu cu
`--window-size`. Al doilea randează alt viewport decât cel măsurat: la
`--window-size=390,844` pagina se randează la ~500px, deci apare antetul de
desktop. A dus la concluzia falsă că butonul de meniu mobil lipsește.

**Ce e modificat într-o temă sau un plugin se află prin diff cu versiunea
oficială**, descărcată de la sursă. Căutarea după indicii (nume de funcții,
text românesc) ratează exact ce e periculos: o linie schimbată în interiorul
codului original.

**Modificările vizuale se verifică pixel cu pixel**, comparând capturi înainte
și după. Așa a fost prinsă o inversare de ordine CSS care muta layout-ul cu
5,42% din pixeli și pe care ochiul nu o vedea.

---

## Documente conexe

- `sydney-update-analiza.md` — comparația cu Sydney 2.71 oficial, dinaintea migrării
- `audit-2-seo-security-performance.md` — auditul inițial (context istoric; țintea tema inactivă)
- `wp-content/themes/sydney-child/functions.php` — TODO-urile MIG-01…05, lângă cod
- `wp-content/mu-plugins/simonamarin-hardening.php` — SEC-11, SEC-13, SEC-14, lângă cod
