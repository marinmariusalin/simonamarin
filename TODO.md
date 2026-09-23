# TODO — ce a mai rămas de făcut

**Actualizat:** 23 septembrie 2026.
Singurul loc din proiect unde se urmăresc sarcinile (active și rezolvate). Toate marcajele `TODO` din codul sursă au fost curățate, iar tema inactivă este izolată. Dacă un punct nu apare aici, nu e de făcut.

---

## Ce rulează de fapt

Înainte de orice, contextul fără de care restul e inutil:

| | |
|---|---|
| Temă activă | **`sydney-child`** (copil), părinte **`sydney` 2.71** |
| Cod independent de temă | `wp-content/mu-plugins/simonamarin-hardening.php`<br>`wp-content/mu-plugins/simonamarin-fara-comentarii.php` |
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

## ⚠️ Decizii luate împreună, care NU au ajuns pe site

Acestea au fost discutate și aprobate, apoi implementate în tema
`simonamarin/` — care nu rulează. Nu sunt idei noi: sunt decizii deja luate,
care așteaptă să fie aplicate acolo unde contează.

### D1. Comentariile — REZOLVAT (22.09.2026)

Ai decis explicit că site-ul nu are comentarii, deloc, și ai repetat-o de mai
multe ori: nici adăugare, nici vizualizare. Decizia stătea aici, în TODO, în
timp ce pe fiecare articol se afișa în continuare „Lasă un răspuns". Acum e
aplicată pe site.

**Ce s-a schimbat**

| | Înainte | Acum |
|---|---|---|
| `default_comment_status` | `open` | `closed` |
| Conținut cu comentarii deschise | **81** (32 articole + 47 atașamente + 2 ciorne) | **0** |
| Formularul de comentarii pe articol | se afișa | nu mai există în HTML |
| Comentarii aprobate, vizibile public | **2** | **0 afișate** |
| Feed de comentarii (`/articol/feed/`, `/comments/feed/`) | 200 | 404 |
| `/wp-json/wp/v2/comments` pentru vizitatori | servea comentariile | 404 |
| Trimitere directă către `wp-comments-post.php` | accepta | 403 |

Regulile sunt în `wp-content/mu-plugins/simonamarin-fara-comentarii.php`, nu în
temă și nu doar în setări: o setare se poate reactiva dintr-un clic sau la
importul unei configurații, un mu-plugin nu poate fi dezactivat din panou.
Fiecare filtru are în fișier motivul pentru care există.

**Cifra de 119 comentarii era înșelătoare.** Defalcarea reală: 48 spam, 69
neaprobate și **doar 2 aprobate** — acelea două erau singurele vizibile
vreodată public, pe articolul „Psihoterapia la distanță".

**Ce rămâne decizia ta:** cele 119 nu sunt șterse, doar scoase din afișare.
Ștergerea atinge conținut. Din același motiv am lăsat neatins ecranul
*Comentarii* din administrare — dacă vrei să le ștergi, ai nevoie de el.

**Verificat**, nu presupus: toate cele 32 de articole publicate au fost cerute
una câte una și niciunul nu mai conține `id="respond"`, `comment-form` sau
`id="comments"`.
### D2. Paleta și sistemul de design — aplicată pe Sydney 2.71 (21.09.2026)

Rezultatul din `wp-content/themes/simonamarin/DESIGN.md` (paletă verde-albastru
desaturată, 18 perechi de contrast verificate WCAG 2.2, simulare de daltonism)
a fost aplicat pe stiva activă, la cererea ta de a rezolva toate TODO-urile de
design dintr-o dată.

**Ce s-a schimbat, și unde:**

- **Paleta globală Sydney** (`global_color_1`…`9`, plus fundalul și culorile de
  antet) — în Customizer, prin `theme_mods_sydney-child` din baza de date, nu
  prin CSS. Fundalul alb devine `#FAF8F5`, textul `#24302C`, linkurile
  `#3D6F65` cu hover `#2C524A`.
- **Gradientul violet `#6726f7→#4c02eb`** (butonul din hero, butonul de submit
  al formularului) — era hardcodat în CSS-ul aditional (Customizer → CSS
  aditional, post ID 171), nu venea din opțiunile de culoare ale Sydney, deci
  nu putea fi „decis" doar din Customizer. Înlocuit cu accentul cărămiziu
  `#95563A` din paletă — singura culoare peste 35% saturație, folosită aici
  exact o dată, ca CTA unic.
- Alte cinci culori hardcodate în același CSS aditional (linkurile „Contact" /
  „Detalii" din carduri, hover-ul lor, bordura mesajului formularului, bordura
  meniului mobil, iconița de meniu mobil) — aduse la verde principal / verde
  închis din paletă.

**De ce nu e în `git diff`:** toate modificările de mai sus stau în baza de
date (theme_mods și postul de CSS aditional), nu în fișiere de temă — exact ce
cerea decizia inițială („prin opțiunile de culori globale, nu prin CSS peste").
Nu există un fișier de versionat pentru ele; verificarea s-a făcut prin captură
de ecran înainte/după pe pagina principală și pe `/servicii-psihologice/`.

**Modernizare vizuală (21.09.2026, a doua trecere):** ai adus o referință
(fundal colorat pe secțiuni, titlu mare cu accent subliniat, poze rotunjite,
umbre domoale) și ai cerut același efect cu pozele și textele existente, fără
să restructurez hero-ul în poză plină cu text suprapus — layout-ul actual
(poză în cadru, text lângă) rămâne.

Adăugat tot în CSS-ul aditional (post 171, bloc nou la final, ca să nu ating
regulile vechi prin potrivire de text pe un fișier cu spațiere neuniformă):
cardul din hero primește fundalul verde-deschis din paletă (`#DCE7E3`) și o
umbră domoală în loc de umbra grea, mov-închisă, de dinainte; titlul principal
crește la 46px și primește un accent subliniat de 90px în culoarea accentului
(`#95563A`); poza Simonei și cardurile de servicii/tarife primesc colțuri
rotunjite; cardurile au acum o umbră ușoară și se ridică vizibil la hover;
butoanele (Contact, submit formular) devin pilulă (`border-radius: 999px`).
Verificat pe pagina principală, pe `/servicii-psihologice/` și pe mobil (emulare
reală de dispozitiv prin DevTools Protocol, 390×844).

**Ce nu s-a atins:** conținutul (texte, titluri) și `DESIGN.md`/`theme.json`
din tema inactivă `simonamarin/`, care rămân document de referință, nu sursă
activă.

**Trei bug-uri reale, găsite și reparate în timpul chestionarului de design
(21.09.2026, a treia trecere):**

1. **Cache-ul CSS al Sydney nu se invalida.** Editarea directă în baza de date
   a `theme_mods` (D2) nu trece prin `set_theme_mod()`, deci nu declanșează
   invalidarea transientului `_transient_sydney_base_css_...` în care Sydney
   ține CSS-ul generat. Rezultatul: paleta era corectă în baza de date, dar
   site-ul continua să servească CSS vechi, cu culorile roșii originale —
   parțial, ceea ce explică impresia de „zone din site-uri diferite”. Rezolvat
   prin ștergerea transientului; se poate reproduce oricând e nevoie de
   regenerare, din **Setări → Permalinkuri → Salvează** (declanșează același
   hook) sau direct din baza de date.
2. **Antetul își schimba culoarea la scroll.** `main_header_background_sticky`
   (fundalul specific stării „sticky”, la scroll) e o setare Sydney separată
   de `main_header_background` (fundalul de sus) și era goală — cădea implicit
   pe una din culorile globale (verde închis, `global_color_2`, după paleta
   nouă). Setată acum explicit, la fel cu restul antetului.
3. **Suprapunere pe pagina Contact.** `.contact-info` și `.homepage-contact`
   folosesc offset-uri (`top: -80px`, înălțime fixă 800px) calculate pentru un
   layout cu poză mare de fundal. Pe Tarife/Ateliere funcționează, pentru că
   secțiunea de contact vine după carduri care îi dau deja spațiu; pe
   `/contact/`, unde vine imediat după titlu, offset-ul o trăgea peste titlu și
   formular. Fix scopat la `body.page-id-186`, ca să nu afecteze celelalte
   pagini cu același marcaj.

**WhatsApp ca prim canal de contact (21.09.2026).** Discutat și aprobat:
formularul de contact probabil nu mai e canalul preferat — un buton WhatsApp
mare, cu iconița deja folosită în antet, a fost adăugat înaintea formularului
pe `/contact/` (pagina 186, în conținutul paginii, nu în temă). Formularul
rămâne funcțional neschimbat, doar stilizat mai discret (font mai mic, buton
de submit cu contur în loc de fundal plin), separat printr-o linie subțire.
Nu s-a adăugat text nou de conținut — eticheta „WhatsApp" de pe buton reia
eticheta deja existentă în antet, nu e text nou scris. Verificat pe desktop și
mobil (emulare reală 390×844).

**Chestionar de design pe 7 pagini (21.09.2026).** ~60 de decizii discutate
și aprobate punctual, în chat, pentru Home, Articole, Servicii, Tarife,
Despre mine, Contact și pagina de articol. Implementare în lucru, pagină cu
pagină; ce s-a făcut până acum:

- **Home:**
  - Coerență vizuală: `.servicii-container` (cutia cu cardurile de servicii)
    avea fundal alb, în contrast dur cu verde pastel al hero-ului de la
    modernizare — acum verde pastel și ea, aceeași suprafață continuă.
  - Cardurile de servicii aveau fundal aproape transparent
    (`rgba(0,0,0,0.03)`), care pe fundalul nou verde se pierdea complet — au
    acum fundal alb-cald solid (`#FAF8F5`), ca să iasă în evidență.
  - Tab-urile „Contact”/„Detalii” din fiecare card (poziționate absolut, cu
    colțuri pătrate) ieșeau vizibil din cardurile cu colțuri rotunjite de la
    modernizare — `overflow: hidden` pe card, ca totul să se taie după
    aceleași colțuri.
  - Telefon/WhatsApp lângă butonul Contact din hero (decizie 1.6): iconiță
    nouă, fără text nou (eticheta „WhatsApp” există deja în antet).
  - Rest neschimbat: structura hero, poza, ordinea secțiunilor — conform
    deciziilor 1.1, 1.5, 1.9.
  - **Trei probleme reale, sub cardurile de servicii, semnalate direct cu
    capturi:** (1) secțiunea „Despre mine” de pe Home avea o imagine de
    fundal peste care cardul verde se suprapunea urât — imaginea a fost
    scoasă; (2) exista un **al doilea bloc „Despre mine”, aproape identic**,
    duplicat la finalul paginii, fără buton — cruft, șters, rămâne un singur
    card; (3) un **formular de contact complet, duplicat**, direct pe Home,
    suferea de același bug de suprapunere reparat pe `/contact/` (aici
    nescopat) — scos complet, acum că `/contact/` are WhatsApp ca CTA
    principal.
  - **Grila cardurilor de servicii** (`.servicii-cards`, folosită doar pe
    Home — verificat) combina `col-md-3` (25% Bootstrap) cu margini fixe în
    px, ceea ce nu împărțea curat pe rânduri: un card ajungea singur, izolat
    mult mai jos, cu spațiu gol lângă el. Înlocuită cu CSS Grid
    (`repeat(auto-fit, minmax(260px, 1fr))`), scopat doar la această grilă.
    Subtitlul de deasupra cardurilor („Psihologie clinică & ...”) a devenit
    accidental o celulă din grilă la prima încercare — corectat cu
    `grid-column: 1 / -1`, să ocupe tot rândul. Rezultat: 3 carduri pe rând,
    3+3+1, fără sărituri.

### D3. Două reguli de accesibilitate pe care Sydney 2.71 nu le are

Verificat în CSS-ul temei:

- **`prefers-reduced-motion`** — 0 apariții. Nu respectă setarea de sistem
  „mișcare redusă". Pe acest public contează mai mult decât pe altul:
  animațiile pot fi inconfortabile pentru persoane cu tulburări vestibulare
  sau cu anxietate.
- **`aspect-ratio`** pe miniaturi — 0 apariții. Fără el, pagina sare la
  încărcarea imaginilor din listări (CLS, una dintre cele trei metrici Core
  Web Vitals).

`focus-visible` există (2 apariții), deci acela e acoperit.

Ambele se adaugă în `sydney-child/style.css`, sunt scurte și nu depind de
versiunea temei.

---

### Ce a rămas fără obiect, ca să nu fie reluat

- **Traducerile `ro_RO`** scrise pentru tema inactivă sunt inerte, dar
  problema nu mai există: Sydney își aduce propriile traduceri și interfața
  este în română (verificat: „Sari la conținut" apare, „Skip to content" nu).
- **Suportul pentru editorul de blocuri** (`editor-styles`, `wp-block-styles`,
  `responsive-embeds`, `align-wide`) — Sydney 2.71 are propriul `theme.json`,
  iar pe site este activ `classic-editor`.
- **Corectura de escaping din `header.php`** — fișierul acela aparținea temei
  inactive; Sydney are propriul antet.

---

## Redesign — unde s-a ajuns

Planul complet, cu fazele și estimările, e în
[plan-redesign-lifecoach.md](plan-redesign-lifecoach.md).

| Faza | Stare |
|---|---|
| 0.1 — CSS-ul scos din baza de date în fișier | **făcut** |
| 0.2 — fonturi găzduite local | **făcut** |
| 0.3 — jetoane de design (culori, scară tipografică, spațiere) | **făcut** |
| 1 — eroul de pe Home | **făcut** |
| 2 — cardurile de servicii | **făcut** |
| 3 — blocul „despre mine" de pe Home | **făcut** |
| 4 — antet și subsol | **făcut** |
| 5 — paginile interioare | **făcut** |
| 6 — articolele | **făcut** |
| 7 — UX polish (cookie banner calm, reCAPTCHA vs WhatsApp, carduri servicii, contact mobil) | **făcut (23.09.2026)** |

Trei lucruri care merită reținute din Fazele 0–3, pentru că niciunul nu se
vedea din citirea fișierelor:

1. **`.row::before` și `.row::after` din Bootstrap devin elemente de grilă.**
   În Bootstrap sunt doar curățare de float-uri, invizibile. În momentul în
   care același `.row` devine `grid` sau `flex`, ele devin copii cu drepturi
   depline și `::before` ocupă prima celulă. Efectul văzut pe Home: textul
   eroului sărea în coloana a doua, fotografia pe rândul următor, iar secțiunea
   de dedesubt intra peste ea. Se anulează explicit în `redesign.css`.
2. **CSS-ul din Customizer se tipărește ultimul**, inline în `<head>` la
   `wp_head` prioritatea 101, deci după orice foaie de stil pusă în coadă. Atâta
   timp cât stătea acolo, orice regulă nouă de aceeași specificitate pierdea, iar
   singura scăpare rămânea `!important` — de asta ajunsese la 27 KB. Mutat în
   fișier, intră în lanțul de dependențe și se așază unde trebuie.
3. **Marcajul paginilor are `</p>` nepereche**, din `wpautop`. Parserul HTML le
   transformă în `<p>`-uri goale care devin ultimul copil, așa că `:last-child`
   nu prinde ce pare că prinde. Se folosește `:last-of-type`.

Încă trei din Fazele 5–6, la fel de puțin evidente:

4. **O declarație `!important` din atributul `style` nu poate fi anulată din
   foaia de stil.** Nu e o chestiune de specificitate — `style="..."` e deja
   cea mai mare, iar `!important` scris acolo bate inclusiv un `!important` din
   fișier. Pe „Despre mine", `margin-top: 90px !important` e scris în conținut,
   deci spațiul se scade de unde se poate: din banda de deasupra și de pe
   containerul părinte.
5. **`a, i { min-height: 24px !important }` din `style.css` tăia fiecare buton
   nou la 24px.** Regula era deja marcată „DE REVIZUIT" chiar în comentariul de
   lângă ea, cu exact acest motiv. Restrânsă acum la țintele reale (iconițe,
   meniu, comenzi) și fără `!important`. Efect colateral bun: butonul
   „Contact" de pe prima pagină se ridică de la 36px la 52px și ajunge, în
   sfârșit, la aceeași înălțime cu butonul WhatsApp de lângă el.
6. **Clasele `.text5`, `.text6`, `.text7` nu sunt clase de text.** În articole
   sunt puse pe `<h2>`. O regulă scrisă pe clasă bate una scrisă pe element,
   așa că prima variantă a foii a transformat titlurile de secțiune din
   articole în paragrafe obișnuite. Elementul decide rolul, nu clasa.

Din Faza 4, încă două:

7. **Un selector doar cu clase pierde în fața unui ID, oricât de lung ar fi.**
   Sydney își scrie regulile de meniu cu `#mainnav ul li a`, atât în foaia
   proprie cât și în CSS-ul generat din Customizer. Prima variantă a foii de
   antet nu folosea ID-ul, așa că meniul își păstra `float`-urile temei și
   rândul de pictograme cădea sub elementele de meniu, pe al doilea rând.
8. **Butonul flotant WhatsApp era vizibil pe desktop, rupt.** În CSS-ul
   istoric, `.whatsapp-footer { display: flex !important }` bătea
   `display: none` de deasupra ei, la orice lățime — dar `position: fixed`
   venea doar dintr-un `@media (max-width: 980px)`. Rezultatul: pe fiecare
   pagină de desktop, o pictogramă de 24px lată cât ecranul, în curs, sub
   subsol. Se vede în toate capturile de dinainte.

Ce **nu** s-a atins, în nicio fază: niciun cuvânt din conținut, niciun slug,
nicio pagină din tema inactivă. Titlurile care apar de două ori pe aceeași
pagină (o dată în antetul Sydney, o dată în conținut, pe Articole, Ateliere și
Despre mine) **nu au fost ascunse** — ar fi însemnat să scot text din pagină.
Al doilea primește rol de etichetă mică deasupra semnăturii: aceleași cuvinte,
alt rol vizual.

---

## Deschis, în ordinea valorii

### 1. Imaginile optimizate trebuie duse în producție

Lucrul pe imagini din 22.09.2026 (vezi [audit-imagini.md](audit-imagini.md)) e
făcut **doar pe local**: `uploads` a scăzut de la 124 MB la 17 MB, cea mai grea
pagină de articol de la 1187 KB la 27 KB, iar livrarea nu mai depinde de
LiteSpeed. Producția are încă starea veche.

Migrarea are două jumătăți care trebuie să plece împreună, altfel paginile
arată către fișiere care nu există:

- **fișierele** din `wp-content/uploads`;
- **baza de date**: conținutul paginilor 192, 215, 989, 794, 1279 și 1512,
  textele alternative (`_wp_attachment_image_alt`), metadata atașamentelor
  (`_wp_attached_file`, `_wp_attachment_metadata`) și setările Rank Math
  `add_img_alt` / `add_img_title`.

Copie de siguranță completă dinainte de modificări:
`E:\simonamarin\_backup-imagini-2026-09-22\`.

### 2. Google Tag — Site Kit scos local (23.09.2026), GA4 păstrat după consimțământ

**Google Analytics continuă**, fără Site Kit: `mu-plugins/simonamarin-analytics.php`
încarcă același GA4 `G-FQ35PBZ9NE` **doar după „Accept” pe analiză** în
bannerul CookieYes (înainte se încărca pentru toți, fără acord). Păstrează
excluderea utilizatorilor autentificați și evenimentul `contact` la trimiterea
formularului, fără niciun câmp din el. Testat în Chrome (fără alegere / Accept /
vizită următoare / Reject / vizită următoare): tag-ul apare doar după Accept.
Cifrele din GA vor fi mai mici — se numără doar cine acceptă.

Tag-ul era GA4 `G-FQ35PBZ9NE` (`gtag.js` de pe googletagmanager.com), injectat
de **Site Kit by Google** — nu exista un container GTM separat. Cântărea
~520 KB, 53% din pagina de start.

**Local, făcut:** Site Kit dezactivat și șters; șterse din baza de date 133 de
opțiuni și 10 rânduri de usermeta — ale Site Kit și resturile a trei pluginuri
Google Analytics dezinstalate demult (GADWP, ExactMetrics, MonsterInsights,
~250 KB de cache vechi). Copie a rândurilor șterse:
`E:\simonamarin\_backup-google-tag-2026-09-23\randuri-sterse.json`. Verificat:
zero referințe la gtag/googletagmanager pe Home, Contact, Despre mine, Tarife,
Blog.

**Pe producție:** pluginurile nu sunt în git, deci nu ajung acolo prin push.
Ajung prin deploy-ul cu Duplicator (care duce baza de date și `wp-content`
local), după care LiteSpeed Cache → Golește tot. Dacă Google Tag trebuie oprit
înainte de deploy: Pluginuri → Site Kit → Dezactivează → Șterge. Opțional, proprietatea GA4 se poate închide și
din contul Google Analytics.

**Tot pe 23.09.2026, local:** șterse Optimization Detective, Image Prioritizer,
Embed Optimizer (beta, adunau date de la vizitatori) și UserFeedback Lite
(0 sondaje; 7 tabele goale și 9 opțiuni șterse, copie în
`E:\simonamarin\_backup-pluginuri-2026-09-23\`). Singurul efect util al OD —
preîncărcarea portretului din hero — e refăcut în `sydney-child/inc/images.php`.
**Păstrate intenționat:** Duplicator (folosit pentru deploy). Really Simple SSL
e inactiv și inutil: SSL-ul e dat de Cloudflare (certificat Google Trust
Services, reînnoit automat, redirect http→https, HSTS, fără conținut mixt).

### 3. MIG-03 — subsolul personalizat

**Rămâne deschis după Faza 4.** Faza 4 a rezolvat doar *prezentarea* subsolului
existent: o singură bandă în cerneala paletei, cu creditele centrate și
discrete. Nu a adăugat conținut — asta e MIG-03 și e o decizie separată.

Subsolul de dinainte (contact, Facebook, Instagram, email, lista de servicii) a
dispărut la migrare, fiind scris direct în `footer.php`. Acum se afișează
varianta implicită Sydney.

De știut înainte de a începe: zona de widgeturi din subsol **nu e goală** —
conține Categorii, Arhive și Comentarii recente, toate trei ascunse prin
decizia „fără sidebar, fără comentarii". Până acum ele lăsau în urmă 190px de
negru gol pe fiecare pagină; acum zona se ascunde automat câtă vreme tot ce e
în ea e ascuns, și reapare singură la primul widget care chiar se vede. Deci
MIG-03 se poate face prin widgeturi, fără nicio modificare de CSS.

Se reconstruiește **prin widgeturi sau hook-uri**, nu printr-un `footer.php`
copiat în copil — altfel se rupe la următorul update, exact ca prima dată.
Datele de contact există deja structurate în
`sydney-child/inc/contact-links.php`, funcția `simonamarin_contact_links()`.

### 4. MIG-02 — telefon și tarife în datele structurate

Rank Math emite deja schema (Organization, Person, WebSite, WebPage). Lipsesc
față de blocul vechi: `telephone`, `priceRange`, programul de lucru și tipul
`MedicalBusiness`.

Se completează în **Rank Math → Titles & Meta → Local SEO**, nu în cod. Un bloc
JSON-LD scris de mână ar crea a doua entitate concurentă pentru aceeași
afacere, ceea ce e mai rău decât lipsa datelor.

### 5. MIG-05 — tipografia pe paginile interioare

**Cauza a fost găsită și eliminată pe Home.** Nu era o setare de font: Taviraj
și Sacramento nu se încărcau deloc. `.signature-font` cerea
`'Sacramento', cursive`, iar singurul `@font-face` din CSS-ul din Customizer
era gol — doar `font-display: swap`, fără niciun fișier. Windows rezolvă
`cursive` ca **Comic Sans**, deci semnătura de pe prima pagină se randa în
Comic Sans. Acum toate trei fonturile sunt găzduite local
(`assets/css/fonts.css`).

Ce a mai rămas: paginile interioare (Despre mine, Servicii, Tarife, Ateliere,
Terapia online, articolele) folosesc încă tipografia veche. Sunt Faza 5 și
Faza 6 din `plan-redesign-lifecoach.md`.

### 6. MIG-04 — semnătura de sub titlul articolelor

„Psiholog Simona Marin" stătea în `content-single.php`. Se reface prin hook-ul
`sydney_before_single_entry` sau `sydney_inside_top_post`, fără șablon copiat.

### 7. SEC-13 — formularul de contact și GDPR

Formularul transmite date de sănătate, care intră sub articolul 9 GDPR. De
verificat, în afara codului: transportul (există post-smtp — de confirmat TLS),
cât timp rămân mesajele în baza de date, dacă există temei legal și o informare
afișată lângă formular.

Nu se rezolvă din cod, dar cântărește mai mult decât orice header.

### 8. PSY-04 — informații pentru situații de criză

Site-ul nu conține nicăieri ce face un vizitator aflat în criză. Pe un site de
cabinet de psihoterapie asta e o lipsă de fond.

**Nu se inventează și nu se aproximează.** Numerele și formulările se verifică
la sursă înainte de publicare și se decid împreună cu psihoterapeuta.

### 9. SEC-11 — Content-Security-Policy

Nu e emis, deliberat. Pe site rulează pluginuri care injectează scripturi
inline (cache, formulare, SEO, GTM), iar o politică aplicată direct ar rupe
pagini fără avertisment.

Ordinea corectă: întâi `Content-Security-Policy-Report-Only` cu raportare,
câteva zile pe trafic real, abia apoi politica aplicată. Pasul Report-Only nu
blochează nimic.

### 10. SEC-14 — `<meta name="generator">` de la pluginuri

Șase etichete rămân în `<head>`, puse de Performance Lab și modulele lui
(a șaptea, a Site Kit, a dispărut odată cu plugin-ul). Fiecare își anunță numele **și versiunea exactă**, ceea ce pentru un
scaner e mai util decât versiunea de WordPress.

Nu le-am scos pentru că fiecare folosește alt hook și un `remove_action` pentru
fiecare s-ar rupe tăcut la primul lor update.

### 11. Poziția iconițelor de contact — decizie deschisă

Sunt acum la capătul meniului, în dreapta. În design-ul vechi stăteau lângă
titlu, în stânga. Dacă poziția din stânga contează, singura variantă curată e
pornirea modulului **header builder** din Sydney 2.71 — care cere însă
reconstruirea întregului antet.

### 12. Redirecturi 301 și URL-uri de bază după migrarea cu Duplicator

Migrarea de pe producție a fost făcută cu Duplicator pe 2026-09-21. De
verificat, în afara codului: `siteurl`/`home` din opțiuni chiar arată spre
`simonamarin.ro` și nu spre mediul local rămas din pachet, și dacă există
URL-uri vechi (structură schimbată, pagini șterse) care ar trebui să
redirecționeze 301 către varianta curentă, ca să nu se piardă linkuri externe
și poziții în Google. Se rezolvă din plugin-ul de redirect deja instalat sau
din `.htaccess`, nu prin cod de temă.

### 13. robots.txt, vizibilitate în motoarele de căutare și sitemap dublu

Trei verificări de configurare, nu de cod: (a) conținutul real al
`robots.txt` (îl generează un plugin, nu a fost verificat ce conține); (b)
Setări → Citire → „Descurajează motoarele de căutare" — trebuie să fie
debifat pe producție, altfel tot site-ul e cu `noindex`; (c) pe site rulează
simultan cel puțin două generatoare de sitemap (Rank Math și încă unul) —
de păstrat unul singur, ca linia `Sitemap:` din robots.txt să nu trimită spre
un fișier concurent sau învechit.

### 14. Search Console — verificare de proprietate

De confirmat că proprietatea din Google Search Console este legată de
domeniul corect (mai ales după migrare). Site Kit a fost scos (vezi punctul 2),
deci verificarea de proprietate se face din Search Console direct (DNS) sau din
Rank Math → Analytics. Se face din admin, nu din cod.

### 15. Conținut YMYL — recitire editorială

Site-ul e „Your Money or Your Life" în termenii Google: sănătate mentală.
Titulatura profesională, disclaimerele și afirmațiile despre metode de lucru
merită o trecere dedicată de verificare la sursă, separat de orice altă
listă tehnică — ține de redactare, nu de cod.

### 16. Disclaimer profesional și mențiune de confidențialitate în subsol

Lipsesc de pe site: o formulare clară că informația de pe site nu înlocuiește
un consult, și o mențiune despre ce se întâmplă cu datele din formular
(vezi și punctul 7, SEC-13). Sunt texte de redactat împreună cu Simona, nu
de aproximat — merg în subsol o dată cu reconstrucția lui (punctul 3).

### 17. Tonul paginilor de eroare și „niciun rezultat"

Pagina 404 și cea de căutare fără rezultate afișează în continuare tonul
implicit al temei. Pe un public care poate ajunge acolo într-un moment
tensionat, formularea contează; e o decizie de redactare (text + eventual un
link mai vizibil către pagina de contact), nu o schimbare de cod.

### 18. CTA-ul din antet și formularea lui

Antetul nu are un buton de acțiune clar către programare/contact. Dacă se
dorește unul, textul și destinația sunt o decizie a Simonei (ton, nu doar
loc); implementarea în sine e simplă odată aleasă formularea.

---

## Făcut — nu relua

| Ce | Unde | Verificat |
|---|---|---|
| Imagini rupte `/ateliere/` + optimizare media (124 MB → 17 MB, 43 alt-texts scrise, sizes optim) | `sydney-child/inc/images.php`, pagini conținut | 44 pagini testate, 0 rupte, 0 cereri eșuate |
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
| `prefers-reduced-motion` (D3) | `sydney-child/style.css` | regula prezentă în fișier |
| `aspect-ratio` pe `.wp-post-image` (D3) | `sydney-child/style.css` | regula prezentă în fișier |
| `<meta name="theme-color">` (PERF-08) | `inc/meta-enhancements.php` | `php -l` fără erori; nu verificat vizual randarea barei de sistem |
| `aria-label` pe navigarea principală (A11Y-02) | `inc/meta-enhancements.php`, filtru `wp_nav_menu_args` | `php -l` fără erori; nu verificat cu cititor de ecran |
| Alt-text implicit pe imaginea reprezentativă (SEO-19/20) | `inc/meta-enhancements.php`, filtru `wp_get_attachment_image_attributes` | `php -l` fără erori; nu verificat pe o pagină reală cu imagine fără alt |
| `noindex` pe căutări fără rezultate (SEO-49) | `inc/meta-enhancements.php`, filtru `wp_robots` | `php -l` fără erori; nu verificat output-ul real de `<meta name="robots">` |
| Mesaj generic la autentificare eșuată (SEC-12) | mu-plugin, filtru `login_errors` | `php -l` fără erori; nu testat cu o încercare reală de login |
| CSS-ul din Customizer mutat în fișier (Faza 0.1) | `sydney-child/assets/css/legacy-customizer.css` | 6 pagini comparate pixel cu pixel înainte/după: 0 diferențe reale |
| Fonturi găzduite local, fără Google Fonts (Faza 0.2) | `sydney-child/assets/css/fonts.css` + `assets/fonts/` | `<link>` către `fonts.googleapis.com` dispărut din `<head>`; semnătura nu mai e Comic Sans |
| Sistem de design pe Home — erou, carduri, „despre mine" (Fazele 1–3) | `sydney-child/assets/css/redesign.css` | capturi desktop 1440 și mobil 390, comparate cu previzualizarea |
| Butonul „Contact" din erou, făcut link real | `sydney-child/assets/js/cta-fix.js` | `<div href>` înlocuit cu `<a>` către `/contact/` |
| Sistem de design pe paginile interioare (Fazele 5–6) | `sydney-child/assets/css/redesign-pages.css` | 10 pagini verificate la 1440px, patru și la 390px; fără derulare orizontală |
| Regula `a, i { min-height: 24px !important }` restrânsă | `sydney-child/style.css` | antetul comparat pixel cu pixel înainte/după: identic |
| Antet, subsol și butonul flotant (Faza 4) | `sydney-child/assets/css/redesign-chrome.css` | antet 86px, lipit de conținut fără gol; fără derulare orizontală la 390 / 768 / 1024 / 1440 |

Toate cinci de mai sus au fost migrate din tema inactivă pe 21 septembrie 2026.
Verificarea făcută efectiv a fost `php -l` pe fiecare fișier modificat — nu o
verificare vizuală în browser și nu o verificare în baza de date. De testat la
prima ocazie: randarea reală a fiecăreia (bara de sistem pe mobil, un
`view-source` pe o pagină de căutare fără rezultate, o încercare de login cu
utilizator inexistent).

---

## Obsolet — nu lucra acolo

**`wp-content/themes/simonamarin/`** — temă instalată dar **inactivă**.

Conținea ~263 de TODO-uri (SEO-01…93, UX-01…78, PSY, PUB, PERF, SEC, CSS, DEAD)
plus `theme.json`, `DESIGN.md`, traduceri `ro_RO` și `inc/security.php`. Toate
au fost scrise în primele sesiuni, pe presupunerea că aceea e tema site-ului.

Nimic de acolo nu rulează. Verificat: `is_textdomain_loaded('simonamarin')`
întoarce `NU`, iar `stylesheet` din baza de date este `sydney-child`.

**Actualizare 21 septembrie 2026: conținutul util a fost mutat integral de
aici, tema poate fi ștearsă în siguranță când se decide.** Inventarul complet
al celor ~263 de TODO-uri a fost extras și triat:

- Ce era cod aplicabil direct, independent de conținut, e acum implementat în
  stiva activă: headerele de securitate și mesajul generic de login în
  mu-plugin, `prefers-reduced-motion`/`aspect-ratio`/theme-color/aria-label pe
  navigare/alt-text implicit/noindex pe căutări fără rezultate în
  `sydney-child` (vezi tabelul „Făcut" de mai sus și
  `sydney-child/inc/meta-enhancements.php`).
- Ce era decizie de conținut, de configurare admin (Rank Math, Search
  Console, robots.txt) sau de redactare a fost mutat ca puncte noi în
  secțiunea „Deschis, în ordinea valorii" (punctele 11–17).
- Ce era cod mort SAU documentație despre fișierele proprii ale temei inactive
  (DEAD-*, CLEAN-*, BUG-* din `js/customizer.js`/`navigation.js`, secțiunile
  CSS-*/DS-* din `style.css`-ul ei) nu a fost migrat: privea exclusiv fișiere
  care nu rulează și nu au echivalent pe tema activă, deci nu au unde să
  „conteze cu adevărat" — sunt pur și simplu invalidate de faptul că tema nu
  rulează, nu recuperabile.
- Paleta din `DESIGN.md` rămâne documentată doar la punctul D2 de mai sus
  (decizie deschisă, aplicare pe Sydney prin Customizer, nu prin CSS peste).

**Decizie rămasă de luat, neschimbată:** tema se șterge sau se păstrează ca
arhivă? Atâta timp cât stă în `wp-content/themes/`, poate fi activată dintr-o
greșeală de clic, ceea ce ar înlocui instantaneu design-ul site-ului cu un
schelet gol. Diferența față de înainte e că acum ștergerea nu ar mai pierde
nimic — totul util a fost deja mutat.

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

- `audit-imagini.md` — documentația completă a auditului și optimizării de imagini (124 MB → 17 MB, pași de deploy în producție)
- `sydney-update-analiza.md` — comparația cu Sydney 2.71 oficial, dinaintea migrării
- `plan-redesign-lifecoach.md` — planul de redesign în faze
- `audit-2-seo-security-performance.md` — auditul inițial (context istoric; țintea tema inactivă)

