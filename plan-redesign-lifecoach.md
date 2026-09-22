# Plan de redesign — inspirat din Kadence „Life Coach"

Referință: <https://www.kadencewp.com/kadence-theme/starters/life-coach/>
(demo live: `startertemplatecloud.com/g46/`)

Scop: să luăm **sistemul de layout** din Life Coach și să-l aplicăm peste ce
există deja, nu să importăm tema. Textul rămâne neatins, integral.

---

## Pasul 0 — decizia de la care pleacă tot restul

**Recomandare: rămânem pe `sydney-child`. Nu instalăm Kadence.**

Motivele sunt verificate, nu presupuse:

1. **Paleta pe care o ai e deja corectă pentru domeniu, cea din Life Coach nu
   e.** Ai `#3d6f65` (verde-petrol), `#95563a` (teracotă), `#dce7e3` (mentă
   palidă), `#faf8f5` (alb cald), `#20292f` (cerneală) — saturație joasă, exact
   ce cere publicul unui cabinet de psihoterapie. Life Coach e construit pe
   portocaliu saturat pe suprafețe mari. Pe publicul tău ar fi o regresie,
   nu un progres.
2. **Secțiunile din Life Coach există deja la tine, ca marcaj.** Vezi tabelul
   de corespondență mai jos: erou pe două coloane, carduri de servicii, bloc
   „despre mine", card de contact. Nu lipsește structura — lipsește execuția.
3. **Problemele reale sunt de CSS, nu de temă.** Sunt enumerate în Faza 1–3 și
   se repară în câteva ore. Un schimb de temă costă 23–39 h și aruncă 27 KB de
   CSS și 585 de linii de PHP care funcționează.

Dacă totuși vrei Kadence, **fazele 1–6 rămân identice** — inventarul de
secțiuni de mai jos nu depinde de temă. Se schimbă doar unealta cu care le
construim (blocuri Kadence în loc de CSS) și se adaugă costul migrării.

---

## Ce luăm din Life Coach și ce nu

| Luăm | De ce |
|---|---|
| Erou pe lățime completă, foto lipită de marginea dreaptă | acum eroul e un card mentă plutind în alb, mic și timid |
| Scara tipografică mare pe titlul principal | titlul actual e cam cât un `h3` |
| Eticheta mică deasupra titlului | dă context fără să adauge text — o putem lua din titulatura deja existentă pe pagină |
| Perechea de CTA: buton plin + link secundar | ai deja Contact + WhatsApp, dar arată ca două butoane concurente |
| Cardul alb suprapus peste marginea secțiunii | locul natural pentru WhatsApp, decizia deja luată pe `/contact/` |
| Grila de carduri cu înălțimi egale și buton fixat jos | repară exact bug-ul din Faza 2 |
| Imagine cu ramă decalată, colorată | tratament pentru „Despre mine" |
| Aerul dintre secțiuni (spațiere generoasă, ritm vertical) | cel mai mare câștig vizual, cel mai ieftin |

| Nu luăm | De ce |
|---|---|
| Portocaliul saturat | saturație mare pe public vulnerabil |
| Banda de cifre (51% / 392 / 10+) | ar însemna să inventez cifre |
| „People Who Trust Us" (logouri de clienți) | nu există și nu se inventează |
| „Happy Clients About Me" (testimoniale) | nu există; în plus, testimonialele de la clienți într-un cabinet de psihoterapie ridică o problemă deontologică — **de verificat de tine la Colegiul Psihologilor înainte de orice discuție pe tema asta** |
| „Video Courses" / „Course Page" | nu există serviciul |
| CTA final „Get Started Now" | tipar de marketing agresiv, exclus prin regulile proiectului |

---

## Corespondența: secțiune Life Coach → ce ai deja

| Life Coach | La tine acum | Ce e de făcut |
|---|---|---|
| Erou 50/50 + foto | `.homepage-container` + `.profile-picture` (Home, ID 31) | doar CSS; marcajul rămâne |
| Card „How Can I Help You?" + telefon | `.sm-hero-cta-row` + linkul WhatsApp | repoziționare |
| „Explore How Can I Help You" — 3 carduri | `.servicii-cards` / `.servicii-box` (7 carduri) | reparat + regrilat |
| „Transform Your Life" — imagine + text | `.home-page-about-me-container` | ramă decalată + spațiere |
| Antet cu logo-semnătură | `.signature-font` + `inc/contact-links.php` + `inc/icons.php` | font + spațiere |
| Blog în carduri | `inc/articles-list.php` (există deja) | tipografie + grilă |

---

## Faza 0 — fundația (fără nicio schimbare vizuală)

Trei pași care nu se văd, dar fără care restul nu e nici reversibil, nici
revizuibil.

**0.1 — Scoatem CSS-ul din baza de date în tema copil.**
Cele 27 KB de design stau acum în „CSS suplimentar" din Customizer
(`wpez_posts` ID 171). Acolo sunt invizibile pentru git, nu se pot revizui prin
diff, nu intră în backup odată cu codul și **se pierd la orice schimbare de
temă**, pentru că WordPress le stochează per temă. Le mutăm într-un fișier
versionat în `sydney-child/`, încărcat după `style.css`. Golim Customizer-ul
abia după ce confirmăm că fișierul se încarcă, ca să nu avem CSS dublu.

*Backup de bază de date înainte. Duplicator e deja instalat.*

**0.2 — Încărcăm fonturile. Acum nu se încarcă.**
`.signature-font` cere `'Sacramento', cursive`, dar nicăieri în tema copil sau
în CSS-ul din Customizer nu există `@import`, `@font-face` valid sau
`wp_enqueue_style` pentru el. Singurul `@font-face` din fișier e gol:

```css
@font-face {
  font-display: swap;
}
```

Rezultatul se vede în captură: „Simona Marin / psiholog" se randează cu
fallback-ul `cursive`, care pe Windows înseamnă **Comic Sans**. Pe un site de
cabinet.

Le self-găzduim (Taviraj, Poppins, Sacramento) — nu de pe CDN-ul Google, pentru
că pe un site care colectează date de sănătate transferul de IP către Google la
fiecare vizită e o expunere GDPR pe care nu o vrem.

**0.3 — Definim jetoanele de design.**
Culorile de mai sus, plus o scară tipografică și una de spațiere, ca variabile
CSS pe `:root`. Nimic nu se schimbă vizual; devine doar vocabularul fazelor
1–6. Tot aici reparăm `body { line-height: 1em; }` — e mult prea strâns pentru
text citit de cineva în suferință.

*Estimare: 3–4 h. Verificare: capturile înainte/după trebuie să fie identice.*

---

## Faza 1 — eroul de pe Home

Cel mai mare câștig pe unitatea de efort, și punctul unde se vede cel mai
limpede ce am luat din Life Coach.

- Eroul iese din cardul mentă rotunjit și ocupă lățimea completă.
- Fotografia se lipește de marginea dreaptă, fără caseta albă suprapusă care
  acum intră peste secțiunea următoare.
- Titlul urcă pe scara tipografică; sub el, eticheta mică.
- `.textP { text-align: justify; }` devine aliniat la stânga. Textul justificat
  fără despărțire în silabe face „râuri" albe în română, vizibile în captură;
  e o decizie de lizibilitate, nu de gust. **Cuvintele rămân exact aceleași.**
- Contact devine buton plin, WhatsApp devine al doilea CTA, nu un cerc izolat.
- Cardul alb suprapus preia WhatsApp-ul, ca în Life Coach.

*Estimare: 3–5 h.*

---

## Faza 2 — cardurile de servicii (aici e un bug real)

Captura arată butoanele „Contact" și „Detalii" tăiate pe mijlocul literelor, în
toate cele 7 carduri de pe Home. Cauza e verificată în CSS:

```css
.servicii-box { min-height: 271px; }
.servicii-box .servicii-detalii {
    position: absolute;
    bottom: -2px;
    height: 50px;
}
```

Butoanele sunt poziționate absolut, trase 2px sub marginea cutiei, iar cutia nu
rezervă niciun spațiu pentru ele. Reparația la grilă din commit-ul `3c07eb8` a
atins doar dispunerea cardurilor — comentariul din `style.css` spune explicit
că `.servicii-box` a fost lăsat neatins. Deci bug-ul e încă acolo.

- Cardul devine `flex` pe coloană, rândul de butoane fixat jos prin
  `margin-top: auto`.
- Se scoate `min-height` fix; înălțimile se egalizează prin grilă.
- Se adaugă tratamentul de imagine cu ramă decalată din Life Coach.
- Se reia grila la 3 coloane, cu paginile de destinație neschimbate.

*Estimare: 2–3 h.*

---

## Faza 3 — blocul „Despre mine" de pe Home

Corespondentul lui „Transform Your Life & Live Your Dream": imagine cu ramă
decalată în stânga, etichetă + titlu + paragraf + un singur link în dreapta, pe
fundal ușor tentat. Fără banda de cifre.

*Estimare: 2 h.*

---

## Faza 4 — antet și subsol

Life Coach: logo-semnătură stânga, meniu centru, pictograme dreapta. Ai deja
toate cele trei piese; sunt doar prost distanțate, iar semnătura abia în Faza
0.2 începe să se randeze corect. Rămân filtrele existente din
`inc/contact-links.php` — nu copiem `header.php` în tema copil.

*Estimare: 2–3 h.*

---

## Faza 5 — paginile interioare

Despre mine, Servicii, Tarife, Ateliere, cele două grupuri, Terapia online.
Toate folosesc deja aceeași schelă (`.about-me-page`,
`.single-article-container`), deci primesc același sistem dintr-o singură
trecere: erou de pagină consecvent, o singură lățime de coloană pentru text,
același tratament de card.

Politica de confidențialitate și Termenii primesc doar tipografia, atât.

*Estimare: 4–6 h.*

---

## Faza 6 — articolele

Cele 32 de articole sunt proză curată, fără blocuri Gutenberg și fără
constructor de pagini — nu au nevoie de nicio atingere de conținut. Lucrăm doar
la citit: lungimea rândului (65–75 de caractere), spațierea dintre paragrafe,
titlurile intermediare, imaginea reprezentativă. Plus lista din `/articole/` în
carduri, ca blogul din Life Coach.

*Estimare: 3–4 h.*

---

## Ce nu se atinge, în nicio fază

- **Textul.** Niciun cuvânt adăugat, scos, scurtat sau reformulat.
- **Slugurile.** Sunt vechi și încărcate cu cuvinte-cheie
  (`/psiholog-bucuresti-psiholog-clinician-.../`), dar sunt indexate. Nu le
  atingem.
- **`wp-content/themes/simonamarin/`** — temă inactivă.
- **`wp-content/themes/sydney/`** — tema părinte; un update o șterge complet.

---

## Metoda de lucru

O ramură separată. O fază pe sesiune. Pentru fiecare fază: captură înainte,
modificarea, captură după, comparație la nivel de pixel pe desktop și pe mobil
(emulare reală de dispozitiv prin DevTools Protocol), comentariu în cod care
explică *de ce*, commit descriptiv, push. Treci tu prin ea înainte să pornim
următoarea.

**Total: 19–27 h**, în 6–7 sesiuni.
