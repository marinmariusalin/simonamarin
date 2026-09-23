# Imaginile site-ului — ce era greșit și ce s-a reparat

**Auditat și reparat:** 22 septembrie 2026
**Stare:** executat pe mediul local. **Nu este încă în producție** — vezi
secțiunea *Ce mai trebuie făcut* la final.

**Copie de siguranță completă** înainte de orice modificare, în
`E:\simonamarin\_backup-imagini-2026-09-22\` — baza de date (`local-db.sql`,
8,5 MB) și toate cele 124 MB din `wp-content/uploads`. Fiecare pas de mai jos e
reversibil din ea.

---

## Rezultatul, în cifre

| | Înainte | După |
|---|---|---|
| `wp-content/uploads` | 124 MB | **17 MB** |
| Atașamente în biblioteca media | 96 | **48** |
| Imagini rupte pe site | 2 | **0** |
| Texte alternative generate din numele fișierului | ~10 | **0** |
| Cea mai grea pagină de articol (mobil) | 1187 KB | **27 KB** |
| Pagina Articole, derulată integral (mobil) | 2868 KB | **1417 KB** |
| Pagina Articole, încărcare inițială (mobil) | — | 415 KB |
| Livrare optimizată fără niciun plugin | nu | **da** |

Măsurat în Chrome prin DevTools Protocol, cu emulare reală de dispozitiv
(390×844 la DPR 3 și 1440×900 la DPR 1) și cache dezactivat.

---

## 1. Cele două imagini rupte din pagina Ateliere

Adresele din conținut arătau către `…insarcinate.webp` și
`…prezentare-1024x576.webp` — fișiere care nu există. Le scrisese plugin-ul
*Converter for Media*, dezinstalat între timp; fișierul real, generat de
LiteSpeed, se numea `…jpg.webp`. Serverul răspundea `301`, deci pe pagină
apăreau două casete goale de 326×200 — și în producție, nu doar local.

Reparația nu pune la loc o adresă produsă de plugin, ci markup generat de
WordPress din biblioteca media. Sursa există indiferent ce plugin e activ, iar
`srcset` lasă browserul să aleagă varianta potrivită pentru slotul real
(513×200 pe desktop, 686×200 pe tabletă, 326×200 pe mobil, cu `object-fit:
cover`). Ambele imagini se afișează acum, verificat în browser.

---

## 2. Textele alternative

Câmpul „Alt Text" era gol pe 17 dintre imaginile folosite. Rank Math (Image
SEO, `img_alt_format = '%filename% %sep% %sitename%'`) completa golul cu numele
fișierului plus numele site-ului, iar în pagină ajungea:

```
alt="8cafcfc532bd982471b54ff541eb923d – Cabinet Individual de Psihologie"
alt="c561e196a004b45fe74f884a62da1332 images about clipart baby on clip art …"
```

Pentru cineva care navighează cu cititor de ecran asta e zgomot, nu informație.
Pe un site de cabinet, unde o parte din public ajunge în condiții de oboseală
sau suferință, accesibilitatea nu e un detaliu tehnic.

**S-a făcut:**

- **43 de texte alternative scrise pe baza a ce se vede efectiv în fiecare
  imagine.** Le-am parcurs vizual, în planșe de contact, nu le-am dedus din
  numele fișierului. Unde alt-ul existent era greșit, l-am corectat: `1579`
  spunea „Anxietate" pentru o fotografie cu un copil în leagăn, `2081` spunea
  „Relaxare" pentru un pumn strâns, `1727` avea „Destramara" scris greșit.
- **Alt-urile care repetau titlul articolului au fost înlocuite cu descrieri.**
  Un cititor de ecran anunță deja titlul din legătura cardului; repetat în alt
  nu adaugă nimic, iar cine nu vede imaginea tot nu află ce e în ea.
- **Generarea automată din Rank Math a fost oprită** (`add_img_alt` și
  `add_img_title` pe `off`), ca golul să nu mai fie umplut cu numele
  fișierului. Rămâne plasa de siguranță din tema copil
  (`simonamarin_fallback_thumbnail_alt`), care folosește titlul articolului —
  o aproximare onestă.
- Au rămas trei alt-uri goale, pe atașamentele `316`, `320`, `321`: sunt
  fundaluri decorative din Customizer, nu se afișează ca `<img>`, iar pentru o
  imagine pur decorativă alt-ul gol este corect.

O notă care ține de tine, nu de mine: pe paginile *Despre mine*, *Servicii* și
*Tarife*, alt-ul portretului era `consilier psihologic` / `servicii
psihologice` / `tarife servicii psihologice` — descria pagina, nu imaginea.
L-am pus `Simona Marin, psiholog`. Dacă preferi să rămână formularea cu
cuvinte-cheie, se schimbă într-un minut.

---

## 3. Imaginile nefolosite, scoase

**Din 124 MB au rămas 17 MB.** Nimic din ce s-a șters nu apărea pe site:
fiecare candidat a trecut simultan prin trei verificări — numele nu apare în
conținutul niciunei postări, atașamentul nu e imagine reprezentativă nicăieri,
și niciun fișier al lui nu a fost cerut de browser la parcurgerea tuturor celor
44 de pagini publicate.

| Ce | Cât |
|---|---|
| 48 de atașamente nefolosite (41 orfane + 7 referite doar de plugin-uri dezinstalate) | 21 MB |
| Variante de dimensiune neînregistrate (`-1200x900`, `-1200x675`…) de la o temă anterioară | 14,7 MB |
| `uploads/ShortpixelBackups/` — ShortPixel, dezinstalat | 36 MB |
| `uploads/photo-gallery/` — Photo Gallery by WD, dezinstalat | 9,3 MB |
| `uploads/cwv-webp-images/` — Converter for Media, dezinstalat | 4,5 MB |
| `uploads/wpforms/`, `hummingbird-assets/`, `pum/`, `sass/`, `disabler-logs/`, `redux/`, `siteorigin-widgets/` | 2,2 MB |
| Variante învechite, după recodare | 7,6 MB |

Atașamentele s-au șters prin `wp_delete_attachment()`, nu cu `rm`, ca să dispară
odată cu ele și rândurile din baza de date — altfel biblioteca media ar fi
rămas plină de intrări care arată către nimic.

**Ce am păstrat deliberat:** `2020/02/family.png` (1141 KB) și
`family-e1583085142480.png` (631 KB) sunt versiunile de dinainte de editare ale
unei imagini reprezentative. WordPress le ține pentru „Restaurează imaginea
originală". Nu se afișează nicăieri, dar ștergerea lor pierde ireversibil acea
posibilitate, așa că am lăsat decizia la tine.

**Un atașament am refuzat să-l șterg:** `2180` (`2aaaaaa.jpg`) e folosit
într-o pagină aflată în ciornă (*Terapie online*). Dacă ciorna nu-ți mai
trebuie, se poate scoate și el.

**Două adrese moarte care nu sunt de la mine:**
`2018/03/564251_232517660224833_1720545604_n.jpg` (în `theme_mods_sydney-child`)
și `2022/05/cropped-Logo-Simona-Marin.png` (în `sd_data` și în opțiunile Rank
Math) lipseau deja din copia de siguranță — erau rupte dinainte. Nu se văd pe
nicio pagină.

---

## 4. Optimizarea, fără plugin

Până acum WebP-ul ajungea în pagină pentru că **LiteSpeed Cache rescria
adresele la fiecare cerere**. Dacă plugin-ul era oprit, în pagină ajungeau
fișierele originale — dintre care unul de 1363 KB. Asta era dependența de
plugin.

Acum fișierele sunt comprimate ca atare, iar patru filtre de nucleu din tema
copil (`inc/images.php`) fac ca și cele încărcate de acum înainte să fie la fel.

### Ce s-a schimbat în fișiere

**Trei fotografii erau salvate ca PNG.** PNG nu comprimă fotografii; conversia
la JPEG le-a dus de la 1363 KB la 65 KB. Toate trei sunt doar imagini
reprezentative, deci WordPress reconstruiește singur markup-ul și nicio adresă
scrisă de mână nu s-a rupt.

**Toate sub-dimensiunile au fost regenerate.** Aici era greutatea reală: o
imagine avea un original de 165 KB, dar variantele lui însumau 5976 KB, pentru
că fuseseră generate cândva dintr-un fișier mai mare și rămăseseră așa la
fiecare schimbare de temă.

**Desenele au rămas PNG, cu paletă redusă la 256 de culori.** Redimensionarea
unui desen plat introduce degradeuri care umflă PNG-ul de patru ori (132 KB
pentru 350×300); cuantizarea îl readuce la 44 KB fără pierdere vizibilă.

**Nu s-a recodat ce era deja bun.** Raportul octeți/pixel spune asta: o
fotografie JPEG bine comprimată stă la 0,04–0,15 B/px. `Simona-Marin.jpg` e la
0,04 — recodată la calitate 82 ar fi crescut de la 112 KB la 142 KB, deci a
rămas neatinsă.

### De ce nu am trecut tot pe WebP

Ar mai fi scos vreo 30%, dar schimbă adresa fiecărei imagini. Site-ul are opt
ani, imaginile sunt indexate în căutarea Google și citate în cardurile
rețelelor sociale; o redenumire generală le aruncă pe toate. Nu merită pentru
câteva zeci de kiloocteți, cât timp greutatea reală venea din sub-dimensiuni.
Dacă vrei totuși migrarea completă, se poate face separat, ca decizie asumată.

### Atributul `sizes`, care mințea browserul

`sizes` e promisiunea făcută browserului despre cât de lată va fi imaginea în
pagină; pe baza ei alege din `srcset`. Valoarea implicită WordPress este
`(max-width: Npx) 100vw, Npx` — „imaginea ocupă toată lățimea ecranului". Pe
cardurile din arhivă, care au 331 px, asta e fals de trei ori.

Primele trei carduri, fiind deasupra pliului, nu primesc `loading="lazy"`, deci
nu primesc nici `sizes="auto"` (care există din WordPress 6.7, dar funcționează
doar pe imaginile leneșe) și rămâneau cu `100vw`: **1326 KB doar pentru ele.**

Filtrul din temă pune lățimile măsurate în browser, nu estimate:
- card de arhivă: 331 px la 1440, 326 px la 820 și la 390;
- antet de articol: 1092 px la 1440, 688 px la 820, 328 px la 390.

O subtilitate care m-a costat o încercare: filtrul evident,
`wp_calculate_image_sizes`, **nu funcționează** aici. La `wp-includes/media.php`
linia 1163 nucleul îl cheamă cu un `$size_array` de forma `array(1000, 1000)`,
unde numele dimensiunii nu mai există. Numele ajunge nealterat abia la
`wp_get_attachment_image_attributes`, linia 1201.

### O imagine descărcată și niciodată arătată

Paginile *Servicii* și *Tarife* aveau în conținut
`<img src=".../Simona-Marin.webp" style="display:none">`. `display:none` ascunde
elementul, dar nu oprește descărcarea: 131 KB, adică tot traficul de imagini al
paginii, cheltuit pe ceva ce nu se vede. Nu am scos elementul — poartă un `alt`
și poate fi acolo intenționat — ci i-am pus `loading="lazy"`. O imagine leneșă
care nu intră niciodată în ecran nu se descarcă deloc. Verificat: ambele pagini
trimit acum **0 KB** de imagini.

### Filtrele adăugate în temă (`inc/images.php`)

| Filtru | Ce face | De ce |
|---|---|---|
| `jpeg_quality`, `wp_editor_set_quality` | 82 pentru JPEG, 80 pentru WebP | Plugin-urile instalate de-a lungul timpului au schimbat valoarea și unele au lăsat-o schimbată după dezinstalare |
| `big_image_size_threshold` | 1600 px | Cel mai mare loc unde o imagine se vede e antetul articolului: 1092 px CSS, adică 2184 la densitate dublă; peste 1600 diferența nu se mai vede |
| `intermediate_image_sizes_advanced` | scoate `2048x2048` | Nu e cerută niciodată; rămânea pe disc ca fișier mort |
| `wp_get_attachment_image_attributes` | `sizes` corect | Vezi mai sus |

---

## 5. Verificare

Toate cele 44 de pagini publicate, parcurse în Chrome cu emulare de dispozitiv,
cache dezactivat, derulate până la capăt:

| Pagină | Înainte | După |
|---|---|---|
| *Cand părinții devin … părinți* | 1187 KB | **27 KB** |
| *Portretul omului gelos* | 859 KB | **36 KB** |
| *EMDR* | 542 KB | **170 KB** |
| *Dietă și sănătatea mintală* | 525 KB | **163 KB** |
| *7 stâlpi…* | 444 KB | **89 KB** |
| *Relaxarea fizică…* | 259 KB | **82 KB** |
| **Articole** (arhiva, 32 de imagini) | 2868 KB | **1417 KB** |
| Servicii / Tarife | 105 KB | **0 KB** |
| Ateliere | 0 KB (imagini rupte) | 106 KB (se afișează) |

Controale trecute:
- **0 imagini rupte** și **0 cereri eșuate** pe cele 44 de pagini;
- **48 de atașamente, 0 originale lipsă, 0 variante lipsă;**
- **0 căi absolute** în `_wp_attached_file` (vezi mai jos de ce contează);
- capturi de ecran pe desktop și mobil, verificate vizual pe *Home*,
  *Articole*, *Ateliere*, *Despre mine* și două articole.

### Trei greșeli făcute pe parcurs, prinse și reparate

Le notez pentru că toate trei sunt capcane care se pot repeta.

**webp-uploads a deturnat regenerarea.** Plugin-ul are
`perflab_modern_image_format = webp` și se leagă de
`wp_generate_attachment_metadata()`. Rulat din linia de comandă, a convertit
originalele la WebP și a scris în `_wp_attached_file` o **cale absolută**
(`E:/simonamarin/...`). Ar fi funcționat pe acest calculator și s-ar fi rupt la
prima migrare în producție, unde rădăcina e alta. Restaurat din copia de
siguranță, reluat cu plugin-ul neutralizat prin filtru.

**O eroare proprie a șters 38 de originale.** Pe Windows,
`get_attached_file()` întoarce uneori contrabare. Fără normalizare, calea veche
și cea nouă nu se mai potriveau la comparație, iar bucla de curățenie ștergea
chiar originalul tocmai păstrat. Restaurat din copia de siguranță; scriptul are
acum normalizare și o plasă de siguranță care refuză să șteargă ceva pe baza
unei metadate goale.

**O a treia, prinsă abia a doua zi: `wp_update_post()` din linia de comandă
filtrează HTML-ul.** Fără utilizator autentificat nu există `unfiltered_html`,
deci kses a curățat pagina *Despre mine* (ID 28) la salvarea textului
alternativ: a scos eticheta `<script type="application/ld+json">`, iar datele
structurate Person au rămas afișate ca text pe pagină; a scos și
`itemprop="jobTitle"`. Nicio altă pagină salvată atunci nu a pierdut etichete
(verificat față de revizia anterioară a fiecăreia). Refăcute pe 2026-09-23
direct în baza de date, doar cele două elemente, cu textul paginii verificat
identic; copia dinainte: `E:/simonamarin/_backup-despre-mine-2026-09-23.html`.
**Regula:** conținutul se scrie din CLI cu `$wpdb->update()`, nu cu
`wp_update_post()` — sau cu `kses_remove_filters()` înainte.

---

## Ce mai trebuie făcut

**1. Ducerea în producție.** Tot ce e mai sus s-a făcut pe `simonamarin.local`.
Producția are încă imaginile vechi și textele alternative vechi. Migrarea are
două jumătăți care trebuie să plece împreună:
- **fișierele** din `wp-content/uploads` (17 MB, structură nouă);
- **baza de date**: conținutul a patru pagini, textele alternative, metadata
  atașamentelor (`_wp_attached_file`, `_wp_attachment_metadata`) și setarea
  Rank Math.

Dacă pleacă doar una, paginile arată către fișiere care nu există. Spune-mi cum
migrezi de obicei și pregătesc pașii.

**2. Arhiva pe mobil la densitate triplă.** Pagina *Articole* încarcă 415 KB
inițial și 1417 KB derulată integral. Cardul are 326 px, dar pe un ecran la DPR 3
asta înseamnă 978 px reali, deci browserul cere pe drept o variantă de ~1000 px.
Se poate coborî printr-o dimensiune înregistrată cu raportul 16:9 al cardului —
acum imaginile pătrate sunt tăiate de CSS și jumătate din pixelii descărcați se
aruncă. Ar schimba însă încadrarea tuturor imaginilor reprezentative, deci e o
decizie de design, nu una tehnică.

**3. Cele două fișiere de restaurare** (`family.png`, `family-e1583085142480.png`,
1,7 MB împreună) — de păstrat sau de șters, decizia ta.

**4. Migrarea completă la WebP**, dacă vrei ultimii ~30%, cu costul schimbării
adreselor tuturor imaginilor.
