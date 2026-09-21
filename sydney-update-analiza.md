# Ce s-ar întâmpla dacă actualizăm tema Sydney

**Data analizei:** 21 septembrie 2026
**Metodă:** comparație fișier cu fișier (SHA-256) între tema instalată și pachetul
oficial descărcat de la `downloads.wordpress.org/theme/sydney.latest-stable.zip`.

## Concluzia, în două propoziții

Nu este un update, este înlocuirea temei cu alta. Din 150 de fișiere instalate,
**doar 42 sunt identice** cu versiunea oficială, iar arhitectura temei s-a
schimbat complet între timp — așa că un update nu ar șterge doar
personalizările, ci ar lăsa site-ul fără baza vizuală pe care se sprijină acum.

**Recomandare: nu actualiza Sydney ca operație de rutină.**

---

## Cifrele

| | |
|---|---|
| Versiune oficială actuală | **2.71** |
| Fișiere în tema instalată | 150 |
| Fișiere în Sydney 2.71 | 735 |
| Identice cu originalul | **42** |
| Există în ambele, dar diferă | **66** — se suprascriu la update |
| Există doar local | **42** — se șterg la update |
| Adăugate în 2.71 | **627** |

Raportul 150 vs 735 de fișiere nu este creștere organică: tema a fost
restructurată.

## De ce nu e un update obișnuit

Cea mai clară dovadă este dimensiunea fișierelor cheie:

| Fișier | Instalat | Sydney 2.71 |
|---|---|---|
| `style.css` | **1726 linii** | **18 linii** |
| `header.php` | 245 linii | 57 linii |
| `footer.php` | 77 linii | 25 linii |
| `functions.php` | 570 linii | 816 linii |
| `inc/styles.php` | 229 linii | 1725 linii |
| `inc/woocommerce.php` | 109 linii | 1292 linii |
| `inc/customizer.php` | 64 KB, un singur fișier | **nu mai există** |

Ce arată tabelul: în versiunea instalată, CSS-ul temei stă în `style.css`. În
2.71, `style.css` are 18 linii (doar antetul temei), iar stilurile sunt generate
din `inc/styles.php`, pe baza setărilor din Customizer. **Este alt mecanism, nu
altă versiune a aceluiași mecanism.**

La fel, `inc/customizer.php` — un fișier monolitic de 64 KB — a dispărut și a
fost înlocuit de directorul `inc/customizer/`. Tema a căpătat între timp
module care nu existau deloc: `abilities`, `dashboard`, `modules`,
`integrations`, `performance`, `starter-content`, `display-conditions`.

## Consecința pentru setările site-ului

Site-ul are **43 de setări de Customizer** salvate (mutate deja în
`theme_mods_sydney-child`). Ele au fost scrise de customizer-ul vechi, cel din
`inc/customizer.php`, care în 2.71 nu mai există.

Setările sunt regăsite după nume. Acolo unde numele s-a schimbat în
restructurare, setarea pur și simplu nu se mai aplică — fără eroare, fără
avertisment. Rezultatul probabil este un design care revine parțial la valorile
implicite ale lui Sydney.

**Asta nu se poate preveni printr-o temă copil.** Tema copil protejează
fișierele noastre, nu compatibilitatea setărilor cu părintele.

## Ce personalizări există (determinate prin diff, nu prin presupuneri)

Fișiere modificate față de original care conțin muncă proprie a site-ului:

- **`header.php`** — 90 de linii de JSON-LD (`MedicalBusiness`: denumire,
  telefon, email, interval de preț, logo, profiluri sociale), meta author /
  copyright / theme-color, plus încărcarea FontAwesome.
- **`footer.php`** — subsolul complet: contact, Facebook, Instagram, email,
  lista de servicii.
- **`content-single.php`** — semnătura „Psiholog Simona Marin".
- **`functions.php`** — circa 10 funcții proprii: `defer` pe scripturi, CSS
  non-blocking, `font-display`, Google Analytics, headere de cache, eliminarea
  lui `jquery-migrate`.
- **`style.css`** — regula `a,i { min-width/min-height: 24px }`.
  **Deja mutată** în tema copil, singura protejată în acest moment.

Fișiere care există doar local și ar dispărea, dintre care unele contează:

- `css/fontawesome.min.css`, `css/solid.min.css`, `css/brands.min.css` și tot
  directorul `webfonts/` — setul FontAwesome de care depind iconițele din
  header și din subsol. În 2.71 nu mai este livrat în această formă.
- `js/main.js`, `js/main.min.js`, `js/masonry-init.js`.
- `content-modern.php`, `content-classic-alt.php` — șabloane de listare.
- `inc/wpml/`, `inc/upsell/`, `inc/controls/`, `plugins/` — infrastructură a
  versiunii vechi.

## Corectarea unei recomandări anterioare

Într-un mesaj precedent am spus: mutăm personalizările în tema copil, **apoi**
actualizăm Sydney. Prima parte rămâne validă și utilă. A doua nu.

Un șablon copiat în tema copil este scris pentru markup-ul și clasele CSS ale
lui Sydney 1.x. Pus peste Sydney 2.71, care are altă structură de header și
footer, nu ar produce un site funcțional — ar produce un amestec între două
generații de temă, ceea ce e mai greu de depanat decât oricare dintre ele.

## Opțiunile reale

**1. Rămânem pe versiunea actuală.** Site-ul funcționează. Costul este că tema
nu mai primește corecții, inclusiv de securitate, și că o versiune de PHP
viitoare poate rupe cod scris acum câțiva ani. Este o opțiune legitimă pe
termen scurt, dar este o datorie care se adună.

**2. Tratăm trecerea la 2.71 ca pe un proiect separat**, nu ca pe un buton.
Pe o copie de test: Sydney 2.71 curat, design reconstruit cu uneltele noi ale
temei, apoi comparație vizuală pagină cu pagină cu site-ul actual. Conținutul
nu este în pericol în niciun scenariu — cele 44 de pagini și articole stau în
baza de date, complet independent de temă.

**3. Între timp, indiferent de alegere:** mutăm personalizările în tema copil.
Nu ca pregătire pentru update, ci pentru că astăzi ele sunt într-un director
pe care orice actualizare accidentală — inclusiv un clic greșit în panoul de
administrare — îl șterge fără posibilitate de recuperare.

## Notă despre pachetul oficial

Arhiva descărcată de la wordpress.org conține, pe lângă temă, un director
`CLAUDE-SECURITY-20260723-100751/` și un fișier `CONTEXT.md` — un raport de
scanare de securitate a propriului cod și un glosar intern, lăsate din greșeală
de autorii temei în pachetul publicat. Nu sunt periculoase și nu fac parte din
temă; au fost excluse din comparație.
