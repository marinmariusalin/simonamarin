# Audit imagini — ce se vede, ce nu se vede, ce e prost optimizat

**Data:** 22 septembrie 2026
**Metodă:** inventar pe disc + interogare directă în baza de date (`wpez_`, port
10005) + randare reală a tuturor celor 44 de pagini și articole publicate în
Chrome prin DevTools Protocol, cu emulare de dispozitiv (390×844 @DPR3 și
1440×900 @DPR1) și cache dezactivat.
**Verificat și în producție** (`simonamarin.ro`), nu doar local.

---

## Rezumat

| | |
|---|---|
| Fișiere în `wp-content/uploads/` | 124 MB |
| Atașamente în biblioteca media | 96 (95 imagini + 1 `.docx`) |
| Atașamente efectiv folosite | **47** |
| Atașamente orfane (nefolosite nicăieri) | **41** |
| Atașamente referite doar de plugin-uri dezinstalate | 8 |
| Spațiu recuperabil fără nicio pierdere vizibilă | **≈ 86 MB din 124 MB** |
| Imagini rupte pe site | **2** (pagina *Ateliere*, și în producție) |
| Cea mai grea imagine livrată pe o pagină | **1187 KB** (articolul #1760, pe mobil) |

---

## 1. Imagini rupte — de reparat primele

Pagina **Ateliere** (`page_id=1279`) conține în conținut două adrese de imagine
care nu există. Serverul le răspunde cu `301` către pagina principală, deci
browserul afișează două casete goale de 326×200 px:

| URL din conținut | Fișierul care există de fapt |
|---|---|
| `2018/10/grup-de-suport-pentru-femei-insarcinate.webp` | `…-insarcinate.jpg.webp` |
| `2018/11/22211111-prezentare-1024x576.webp` | `…-1024x576.jpg.webp` |

Sunt scrise direct în `post_content`-ul paginii (verificat în baza de date),
probabil de plugin-ul *Converter for Media* (`uploads/cwv-webp-images/`), care
între timp a fost dezinstalat. Restul celor 213 de adrese de imagine randate pe
site răspund `200` — doar acestea două sunt rupte.

Sunt adrese de fișier, nu text de conținut, dar stau în câmpul de conținut al
paginii, deci aștept confirmarea ta înainte să le ating.

---

## 2. Ce nu apare nicăieri — de scos

### 2.1. Cele 41 de atașamente orfane (17,8 MB, 751 fișiere)

Neregăsite în: conținutul niciunui articol, pagini sau tip de postare
(inclusiv popup-uri, carusele, formulare), nicio imagine reprezentativă, nicio
opțiune de temă sau de plugin, nicio referință prin ID (galerii, blocuri
Gutenberg, `wp-image-NNN`, widget-uri serializate). Verificate separat prin
nume de fișier **și** prin ID numeric.

| ID | Greutate | Fișier |
|---|---|---|
| 863 | 1938 KB | `2018/09/12-prezentare.png` |
| 866 | 1938 KB | `2018/09/12-prezentare-1.png` |
| 2322 | 1413 KB | `2021/10/Munte3.jpg` |
| 2300 | 1286 KB | `2021/10/homepage-1.jpeg` |
| 1762 | 1168 KB | `2020/02/IMG_20161128_162048-scaled.jpg` |
| 2323 | 1105 KB | `2021/10/munte4-e1634905597874.jpeg` |
| 2078 | 1042 KB | `2020/08/IMG_20200829_145249.jpg` |
| 2321 | 985 KB | `2021/10/Munte2.jpg` |
| 2077 | 630 KB | `2020/08/IMG_20200829_145420.jpg` |
| 867 | 595 KB | `2018/09/12-prezentare.jpg` |
| 2299 | 581 KB | `2021/10/IMG_20200718_194713-1.jpg` |
| 769 | 528 KB | `2018/08/compressed_1633173882IMG_20180820_202914_224.jpg` |
| 1461 | 497 KB | `2019/05/56852676_2292391814312155_1439580751656910848_n.jpg` |
| 1462 | 465 KB | `2019/05/57485361_2297918727092797_1126883415698702336_n.jpg` |
| 2080 | 394 KB | `2020/08/IMG_20200829_150054.jpg` |
| 767 | 390 KB | `2018/08/compressed_985889618IMG_20180820_202535_847.jpg` |
| 771 | 387 KB | `2018/08/compressed_44881082620180721_195305.jpg` |
| 402 | 348 KB | `2018/04/1107e4e4-cd60-47c6-a2fb-3df5aedc64ec-1.jpg` |
| 2237 | 310 KB | `2021/06/terapieonline-scaled-e1635928129541.jpg` |
| 27 | 292 KB | `2018/02/cropped-cropped-sofia-alexandrina-122-e1552995576397.jpg` |
| 319 | 237 KB | `2018/03/cropped-564251_2325176260224833_1720545604_n.jpg` |
| 1072 | 196 KB | `2018/11/45621157_2198418443709493_5367808920624562176_n.jpg` |
| 1073 | 196 KB | `2018/11/45621157_…_n-1.jpg` |
| 2224 | 155 KB | `2021/06/terapia-online-1.jpg` |
| 19 | 149 KB | `2018/02/cropped-cropped-sofia-alexandrina-122.jpg` |
| 14 | 116 KB | `2018/02/cropped-sofia-alexandrina-122-e1552995576397.jpg` |
| 2289 | 111 KB | `2021/10/WhatsApp-Image-2021-10-21-at-9.36.44-PM.jpeg` |
| 1017 | 106 KB | `2018/10/e6f5d201c7f1e458f6efd23323215962.jpg` |
| 1858 | 105 KB | `2020/05/facebook-messenger.png` |
| 719 | 105 KB | `2018/07/cropped-unnamed-copy-e1532118966735.jpeg` |
| 720 | 105 KB | `2018/07/cropped-unnamed-copy-e1532118966735-1.jpeg` |
| 1361 | 93 KB | `2019/01/Povesti-care-Regleaza-Comportamentul-…-gokid-300x288.jpg` |
| 2298 | 92 KB | `2021/10/IMG_20200718_194713-e1634890798992.jpg` |
| 395 | 88 KB | `2018/03/cropped-564251_232517660224833_1720545604_n-1-1.jpg` |
| 3367 | 82 KB | `2022/05/cropped-logo3.png` |
| 1732 | 17 KB | `2020/02/portretul-omului-gelos.docx` |
| 3363 | 14 KB | `2022/05/logo1.png` |
| 1109 | 3 KB | `2019/01/comodo_secure_seal_76x26_transp.png` |
| 714, 716, 717 | 2 KB fiecare | `2018/07/cropped-unnamed*.png` |

Observație: ID 1732 nu e imagine, ci documentul Word `portretul-omului-gelos.docx`,
încărcat pe articolul 1731 și niciodată legat. Îl las în listă pentru că e tot
un fișier orfan, dar e decizia ta dacă vrei să rămână arhivat.

### 2.2. Cele 8 atașamente referite doar rezidual (de plugin-uri dezinstalate)

Nu apar pe nicio pagină. Singurele referințe sunt în opțiuni rămase de la
plugin-uri care nu mai există (`wonderm00n_open_graph_settings`,
`schema_wp_settings`, `aioseo_options`, `redux_builder_amp`) sau în meta de la
Yoast/AIOSEO — adică metadate sociale moarte. Le-aș scoate, dar merită o
verificare a ta: dacă vreuna era imaginea de Open Graph a paginii principale,
înlocuirea ei e o decizie separată.

`713` `2018/07/unnamed.png` · `718` `2018/07/unnamed-copy-e1532118966735.jpeg` ·
`768` `2018/08/compressed_1946404118IMG_20180820_202911_501.jpg` ·
`770` `2018/08/compressed_94957555520180721_195217.jpg` ·
`2180` `2021/04/2aaaaaa.jpg` (doar într-o pagină în ciornă) ·
`3364` `2022/05/logo2.png` · `3365` `2022/05/logo2-1.jpg` · `3366` `2022/05/logo3.png`

### 2.3. 126 de variante de dimensiune care nu mai sunt înregistrate (14,5 MB)

Miniaturi `-1200x900`, `-1200x675`, `-1200x1028`, `-1536x1152` etc., generate de
o temă sau o configurație anterioară. Nu apar în `_wp_attachment_metadata` al
niciunui atașament și **niciuna nu e livrată** de site (verificat prin
intersecția cu lista completă a fișierelor cerute de browser). Cele mai grele:

```
948 KB  2020/05/unnamed-1200x1028.png
855 KB  2020/05/unnamed-1200x900.png
675 KB  2020/05/unnamed-1200x675.png
592 KB  2020/02/family-e1583085239496-1200x720.png
588 KB  2020/02/family-e1583085239496-1200x900.png
577 KB  2020/02/family-e1583085239496-1200x675.png
402 KB  2020/02/IMG_20200226_145300-1200x1200.png
…
```

### 2.4. 20 de fișiere nerevendicate de niciun atașament (3,1 MB)

⚠️ **Atenție la două dintre ele.** `2020/02/family.png` (1141 KB) și
`2020/02/family-e1583085142480.png` (631 KB) sunt versiunile de dinainte de
editare ale imaginii reprezentative a articolului #1760. WordPress le păstrează
ca să poți face „Restaurează imaginea originală". Dacă le ștergi, pierzi
posibilitatea aceea — nu se rupe nimic pe site, dar e ireversibil. La fel
`2021/10/munte4.jpeg` și `munte4-e1634905480428.jpeg`, originalele unui atașament
oricum orfan.

Restul sunt resturi clare: `*-updraft-pre-smush-original.png`,
`2018/07/unnamed-copy.jpeg`, duplicate `terapieonline-*`.

### 2.5. Directoare de la plugin-uri dezinstalate (≈ 50 MB)

Niciunul dintre plugin-urile care le-au creat nu mai e instalat (lista activă:
classic-editor, contact-form-7, cookie-law-info, embed-optimizer,
dominant-color-images, image-prioritizer, litespeed-cache, loginizer,
webp-uploads, optimization-detective, performance-lab, post-smtp, rank-math,
site-kit, speculation-rules, userfeedback-lite, duplicator).

| Director | Mărime | Plugin care l-a creat |
|---|---|---|
| `uploads/ShortpixelBackups/` | 36 MB | ShortPixel — dezinstalat |
| `uploads/photo-gallery/` | 9,3 MB | Photo Gallery by WD — dezinstalat |
| `uploads/cwv-webp-images/` | 4,5 MB | Converter for Media — dezinstalat |
| `uploads/disabler-logs/` | 218 KB | — |
| `uploads/hummingbird-assets/` | 188 KB | Hummingbird — dezinstalat |
| `uploads/pum/` | 72 KB | Popup Maker — dezinstalat |
| `uploads/sass/`, `redux/`, `siteorigin-widgets/` | 11 KB | dezinstalate |

`ShortpixelBackups/` conține originalele de dinainte de comprimare. Sunt o
plasă de siguranță pentru o operație făcută acum ani, cu un plugin care nu mai
există. Le-aș arhiva o dată în afara site-ului și apoi le-aș șterge de pe
server.

---

## 3. Ce apare — cum e optimizat

### 3.1. Ce merge bine

**WebP se livrează corect.** LiteSpeed Cache rescrie `img.src` **și** `srcset`
către fișierele `.jpg.webp` / `.png.webp` de lângă originale, pentru browsere
reale. Am confirmat în producție: `Simona-Marin.jpg` → `Simona-Marin.jpg.webp`,
`Content-Type: image/webp`.

> Atenție la metodă: cu `curl` fără User-Agent de browser primești HTML-ul
> neconvertit și pari să tragi concluzia că WebP nu funcționează. Răspunsul din
> producție are `Vary: User-Agent` exact din acest motiv.

Din 183 de variante JPEG/PNG randate pe site, **179 au un frate `.webp`**.
Lipsesc doar patru:

```
60 KB  2018/02/rsz_shutterstock_124858933-min_1-1024x576.jpg   (articolul #89)
22 KB  2018/05/Cartoon-Teen-Depression-458x475.jpg
12 KB  2018/05/8cafcfc532bd982471b54ff541eb923d.jpg
 9 KB  2018/05/8cafcfc532bd982471b54ff541eb923d-480x480.jpg
```

**Miniaturile din arhivă se aleg corect** pentru imaginile încărcate leneș:
`sizes="auto, …"` face browserul să ceară varianta `-350x…` (3–19 KB) pentru un
card de 331×200 px. Exact cum trebuie.

### 3.2. Problema principală: PNG-urile convertite aproape fără pierderi

Măsurat în octeți pe pixel — reperul pentru WebP cu pierderi, calitate bună, pe
fotografii, e **0,05–0,15 B/px**:

| Fișier livrat | B/px | Mărime |
|---|---|---|
| `2020/02/family-1536x768.png.webp` | **1,03** | 1187 KB |
| `2020/05/unnamed-350x300.png.webp` | **1,08** | 110 KB |
| `2020/05/unnamed-230x197.png` | **1,53** | 68 KB |
| `2020/02/family-830x415.png.webp` | **0,88** | 295 KB |
| `2020/02/Cu-relatia-de-cuplu-la-psiholog-768x516.png.webp` | 0,53 | 204 KB |
| `2020/02/IMG_20200226_145300-1536x1536.png.webp` | 0,37 | 859 KB |
| — pentru comparație — | | |
| `2021/10/Simona-Marin.jpg.webp` | **0,04** | 110 KB la 1367×2048 |
| `2020/08/IMG_20200829_150315-830x902.jpg` | **0,09** | 66 KB |
| `2019/08/61424915_…-830x849.jpg.webp` | **0,14** | 95 KB |

Cauza: cinci fotografii au fost încărcate ca **PNG**, nu ca JPEG. LiteSpeed le
convertește în WebP fără pierderi, ceea ce pe o fotografie produce fișiere de
10–25 de ori mai mari decât ar trebui. Vinovatele:

- `2020/02/family.png` → articolul #1760 *Cand părinții devin … părinți*
- `2020/02/IMG_20200226_145300.png` → #1731 *Portretul omului gelos*
- `2020/05/unnamed.png` → #1870 *Ce este stresul?*
- `2020/02/Cu-relatia-de-cuplu-la-psiholog.png` → #1716
- `2018/10/cartoon4829.png` → #1022

Plus trei imagini încărcate direct ca `.webp` în decembrie 2024, comprimate la
0,25–0,53 B/px:

- `2024/12/80c6e043-…​.webp` (542 KB) → #3575 *EMDR*
- `2024/12/artc-dieta.webp` (525 KB) → #3590 *Dietă și sănătatea mintală*
- `2024/12/artc-relaxare.webp` (259 KB) → #3601 *Relaxarea fizică și psihologică*

### 3.3. Cât costă asta, măsurat

Trafic de imagini pe mobil (390×844, DPR 3, fără cache):

| Pagină | Imagini | Trafic | Caseta reală |
|---|---|---|---|
| **#1760** *Cand părinții devin … părinți* | 1 | **1187 KB** | 328×185 |
| **#1731** *Portretul omului gelos* | 1 | **859 KB** | 328×185 |
| **#3575** *EMDR* | 1 | **542 KB** | 328×185 |
| **#3590** *Dietă și sănătatea mintală* | 1 | **525 KB** | 328×185 |
| **#1993** *7 stâlpi…* | 1 | **444 KB** | 328×185 |
| **#3601** *Relaxarea fizică…* | 1 | **259 KB** | 328×185 |
| **Articole** (arhiva, `page_id=1229`) | 33 | **2868 KB** | carduri 331×200 |
| Despre mine | 1 | 113 KB | 320×450 |
| Home | 2 | 114 KB | 358×358 fundal |
| Servicii / Tarife | 1 | 105 KB | **0×0** |
| Contact | 0 | 0 | — |

Pe o conexiune mobilă de 5 Mbps, 1187 KB înseamnă aproape două secunde doar
pentru imaginea de antet a articolului, iar ea are `fetchpriority="high"` —
deci e chiar elementul care definește LCP-ul.

### 3.4. Alte trei lucruri de reparat

**Atributul `sizes` greșit pe primele trei carduri din arhivă.** Cele trei
articole din 2024 sunt deasupra pliului, deci nu primesc `loading="lazy"`, deci
nu primesc nici `sizes="auto"`. Rămân cu valoarea implicită WordPress
`(max-width: 1024px) 100vw, 1024px`, iar browserul descarcă varianta de
**1024×1024 px** (258–542 KB fiecare) pentru un card de 331×200. Doar aici sunt
**1,3 MB** irosiți pe pagina *Articole*. Restul cardurilor, fiind leneșe,
descarcă 3–19 KB fiecare.

**Imagini descărcate și neafișate.** Pe *Servicii* și *Tarife*,
`2021/10/Simona-Marin.webp` se descarcă integral — 102 KB — într-un element cu
caseta 0×0. Pe mobil e tot traficul de imagini al paginii, cheltuit pe ceva ce
nu se vede.

**Decupaj risipitor.** Imaginile reprezentative pătrate (830×830) sunt afișate
în casete de 328×185, adică 16:9. Peste jumătate din pixelii descărcați sunt
tăiați de CSS. Un format înregistrat cu raportul corect ar rezolva-o.

### 3.5. Textele alternative — de semnalat, nu de atins

Un plugin dezinstalat (`auto_image_alt`) a generat automat texte alternative
din numele fișierului. Rezultatul, pe pagina *Articole*:

```
alt="8cafcfc532bd982471b54ff541eb923d – Cabinet Individual de Psihologie"
alt="c561e196a004b45fe74f884a62da1332 images about clipart baby on clip art
     clipartandscrap clipartix 736 836 – Cabinet Individual de Psihologie"
alt="20100621 wang qifeng cartoon 50 – Cabinet Individual de Psihologie"
alt="rsz shutterstock 124858933 min 1 – Cabinet Individual de Psihologie"
alt="uplu mare – Cabinet Individual de Psihologie"
```

Pentru cineva care navighează cu cititor de ecran, asta e zgomot. Aproximativ
zece imagini sunt în situația asta, restul au text alternativ scris de om
(`alt="Ce povești spunem copiilor"`, `alt="Punct de cotitură…"`).

Textul alternativ e text, deci **nu îl modific fără cererea ta explicită**. Îl
semnalez ca atare.

---

## 4. Ordinea în care aș lucra

1. **Cele două imagini rupte din *Ateliere*.** Sunt vizibile, sunt în producție,
   se repară în două corecții de adresă.
2. **Recomprimarea celor opt imagini grele** (cinci PNG-uri + trei WebP-uri din
   2024). Doar asta scoate ~3,5 MB din traficul celor șase articole și mută
   LCP-ul lor de la secunde la sub o secundă. Fișierele se înlocuiesc, conținutul
   nu se atinge.
3. **`sizes` pentru cardurile nelazy din arhivă** — un filtru
   `wp_calculate_image_sizes` în tema copil. Minus 1,3 MB pe pagina *Articole*.
4. **Curățenia pe disc**, în ordinea siguranței: directoarele plugin-urilor
   dezinstalate (50 MB) → variantele de dimensiune neînregistrate (14,5 MB) →
   cele 41 de atașamente orfane (17,8 MB). Ultima cere confirmarea ta, fiindcă
   atinge biblioteca media.
5. Imaginea descărcată degeaba pe *Servicii* / *Tarife*.
6. Cele patru fișiere fără frate WebP.

Punctul 4 contează dincolo de performanță: TODO.md notează că discul acestui
calculator a fost deja umplut complet, cu riscul de a corupe baza de date. 86 MB
nu rezolvă problema aceea, dar nu strică.

---

## Note de metodă

- Măsurătorile de randare sunt făcute cu `Emulation.setDeviceMetricsOverride`,
  nu cu `--window-size`, și cu `Network.setCacheDisabled`. Profilul temporar
  Chrome a fost șters după rulare.
- „Folosit" înseamnă: regăsit în `post_content` (orice tip de postare, exclusiv
  reviziile), în `_thumbnail_id`, în `wpez_postmeta` al unei postări, în
  `wpez_options`, în `wpez_termmeta`, sau referit prin ID numeric în galerii,
  blocuri Gutenberg, widget-uri serializate ori `wp-image-NNN`.
- Lista de fișiere „în uz" a fost încrucișată cu lista completă a cererilor de
  rețea făcute de browser pe toate cele 44 de pagini publicate. Intersecția
  dintre candidații de ștergere și fișierele efectiv livrate este **zero**.
- Baza de date locală e o copie a producției; HTML-ul randat coincide cu cel din
  producție pe paginile verificate. Dacă între timp s-a încărcat conținut nou
  direct în producție, inventarul trebuie reluat acolo.
