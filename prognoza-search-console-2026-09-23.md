# Search Console — ce spun ultimele 12 luni și unde se așteaptă creștere

**23 septembrie 2026.** Sursa: exportul Search Console „Performance on Search”
(Web, ultimele 12 luni, 20.09.2025 – 19.09.2026), descărcat pe 22.09 în
`Downloads\https___simonamarin.ro_-Performance-on-Search-2026-09-22\`.
Este singurul export Search Console disponibil; nu există rapoarte mai vechi
cu care să se compare. Contextul lucrărilor vine din `seo-2026-09-23.md`.

Prognozele sunt estimări de ordin de mărime, nu garanții. Search Console arată
doar căutarea Google, nu harta locală (Google Business Profile) și nici
clienții efectivi.

---

## 1. Ce s-a întâmplat în 12 luni

| Lună | Clicuri | Afișări | CTR | Poziție medie |
|---|---:|---:|---:|---:|
| 2025-10 | 49 | 3075 | 1,59% | 12,0 |
| 2025-11 | 39 | 2361 | 1,65% | 11,0 |
| 2025-12 | 38 | 2010 | 1,89% | 14,0 |
| 2026-01 | 53 | 2652 | 2,00% | 12,1 |
| 2026-02 | 54 | 2547 | 2,12% | 11,5 |
| 2026-03 | 37 | 2571 | 1,44% | 11,0 |
| 2026-04 | 33 | 1959 | 1,68% | 12,1 |
| 2026-05 | 36 | 2117 | 1,70% | 12,3 |
| 2026-06 | 34 | 1480 | 2,30% | 10,1 |
| 2026-07 | 21 | 969 | 2,17% | 14,3 |
| 2026-08 | 20 | 882 | 2,27% | 18,9 |
| 2026-09 (19 zile) | 20 | 715 | 2,80% | 24,8 |

Total: 453 clicuri, 24 322 afișări, CTR 1,86%.

**Citirea corectă a scăderii:** afișările au scăzut la o treime din iunie
încoace, iar poziția medie a coborât de la ~11 la ~25. Dar clicurile s-au oprit
la ~20/lună, iar CTR-ul a *crescut*. Tiparul înseamnă că se pierd afișările
periferice ale articolelor informaționale (stres, gelozie, schimbare — poziții
30–90, CTR 0%), în timp ce nucleul care aduce clicuri — numele și căutarea
locală — rezistă. Scăderea e reală, dar lovește mai ales traficul care oricum nu
aducea clienți.

Toate lucrările din `seo-2026-09-23.md` sunt doar locale: nimic din ele nu a
contribuit la această curbă și nimic nu o poate schimba până la deploy.

### Ce aduce clicurile

| Sursă | Clicuri | Observație |
|---|---:|---|
| Prima pagină | 211 (47%) | poziția 7,3 |
| „psiholog popesti leordeni” | 47 | poziția 5,8, CTR 5,9% — **singura interogare non-brand care aduce clienți** |
| „simona marin” + variante | 32 | poziția 2,3 |
| „comportament si atitudini exemple” | 14 | articol informațional (/7-stalpi…/) |
| Mobil | 321 (71%) | poziția 9,4; desktop 22,7 |
| România | 410 (91%) | diaspora (ES, IT, DE, UK, AT, FR): ~1 900 afișări, 22 de clicuri |

---

## 2. Unde se așteaptă creștere după deploy — în ordinea încrederii

### 2.1 Adrese care acum trimit vizitatorii pe prima pagină — **sigur, imediat**

Verificat azi pe producție:

| Adresă din Google | Clicuri / CTR în 12 luni | Producție acum | Local (după deploy) |
|---|---|---|---|
| /despre-mine/ | 18 / 3,26%, poziția 6,8 | 301 → prima pagină | 301 → /consilier-psihologic/ |
| /tarife/ | 10 / 5,68% (cel mai mare CTR de pe site) | 301 → prima pagină | 301 → /tarife-servicii-psihologice/ |
| /servicii/ | 0 / 71 afișări | 301 → prima pagină | 301 → /servicii-psihologice/ |

Cine caută tarifele sau cine e psihologul ajunge acum pe altă pagină decât a
cerut. După deploy ajunge pe pagina potrivită, iar Google mută în câteva
săptămâni semnalele adreselor vechi pe cele noi. Nu crește numărul de clicuri,
ci **calitatea lor**: exact vizitatorii cei mai aproape de programare.

### 2.2 Căutarea locală și numele — **probabil, creștere de CTR, nu de volum**

„psiholog popesti leordeni” (792 de afișări/an) și numele (~520) sunt deja în
primele 6 poziții. Local s-au adăugat entitatea cabinetului cu `areaServed`,
telefon, program, tarife (`OfferCatalog`) și câte un singur H1 pe pagină.

- Volumul e mic și plafonat: chiar pe poziția 1, interogarea locală principală
  ar aduce de ordinul 150–250 de clicuri/an, față de 47 acum.
- Pârghia mare **nu e în cod, ci în Google Business Profile**: pentru această
  interogare harta locală apare deasupra rezultatelor organice și nu se vede în
  acest export.
- Semnal de extindere: „psiholog berceni” (122 afișări, poziția 55),
  „psiholog bucuresti”, „psihoterapeut bucuresti”, „psiholog copii popesti
  leordeni” — afișări mici, dar arată că Google asociază deja site-ul cu zona.

### 2.3 Paginile de servicii ies din invizibilitate — **probabil, de la aproape zero**

/servicii-psihologice/ și /tarife-servicii-psihologice/ au pe producție 71 și
15 afișări pe an, poziția 18 și 45, fără niciun JSON-LD și fără H1. După
deploy: date structurate, H1, adresele vechi consolidate prin 301, legături din
subsolul fiecărei pagini.

Prognoză: afișările acestor pagini cresc vizibil în 4–8 săptămâni după
reindexare; clicurile rămân puține în cifre absolute, dar sunt cele cu intenție
comercială. Aici e cel mai probabil primul semn că lucrările au efect.

### 2.4 /cuplu/ — **probabil, trafic nou de la zero**

Pe producție articolul e acoperit de arhiva categoriei cu `noindex`, deci nu
apare deloc. Există deja interogări cu afișări pe poziții 40–85: „psiholog
cuplu”, „consiliere cuplu”, „relații de cuplu”, „despre relatii de cuplu”,
„tipuri de relatii de cuplu”. După deploy și cererea de indexare: afișări noi
din prima lună; poziții bune sunt incerte, subiectul e competitiv.

### 2.5 Terapia online — **posibil, efect mediu**

Cele două pagini despre terapia online stau amândouă pe poziția ~33,6, cu 1 060
de afișări și 6 clicuri — tipar de canibalizare. Interogările au volum:
„avantajele psihoterapiei online” (205), „beneficii psihoterapie online” (174),
„sedinte terapie online” (65), „terapie online” (42). Legătura internă dintre
ele (aplicată local) îi arată lui Google care e principala.

Dacă una urcă spre pozițiile 10–15, ordinul de mărime e câteva zeci de clicuri
pe an. Aici e și singura cale spre diaspora (~1 900 de afișări externe/an), iar
`availableChannel` online e deja în datele structurate.

### 2.6 Viteză pe mobil — **indirect**

71% din clicuri sunt pe mobil. Paginile au scăzut de la până la 1 187 KB la
27 KB (imaginile: 124 MB → 17 MB). Viteza e un factor mic de clasare; efectul
așteptat e mai ales mai puține abandonuri și mai multe contactări, nu poziții.
Rămâne Google Tag Manager, 520 KB, de redus din container.

### 2.7 Curățenie tehnică — **mic, de fond**

404 reale în loc de redirecționări spre prima pagină, 47 de pagini de atașament
redirecționate, marker de depanare scos din `robots.txt`. Nu aduce trafic
direct; oprește pierderea de semnale.

---

## 3. Unde **nu** se așteaptă creștere fără o decizie de conținut

| Interogare / pagină | Afișări | De ce nu se mișcă din cod |
|---|---:|---|
| „schimbare” (+ greșeli de scriere) → /schimbare/ | ~2 350 | intenție de dicționar, poziția 10, CTR 0%. Nu aduce clienți. |
| „stresul”, „ce este stresul”, „gestionarea stresului” → /ce-este-stresul/ | ~1 000 | poziții 25–90, concurență cu site-uri medicale mari |
| „gelozia”, „ce este gelozia”, „gelozia la barbati/femei” | ~1 000 | poziții 30–65; două articole pe aceeași temă |
| „părinții” → /parinti/ | 501 | poziția 2, un singur clic: intenție de dicționar |
| „oameni care se tin de mana” și variante | ~130 | poziția 1, dar căutări de imagini, nu de psiholog |

Aici s-ar putea mișca doar prin meta descrieri sau text, care nu se ating fără
aprobarea explicită a utilizatorului (vezi `seo-2026-09-23.md`, secțiunea 3).
Nu recomand prioritizarea lor: chiar recuperate, nu aduc programări.

---

## 4. Prognoză pe scenarii (clicuri Google, pe lună)

| Scenariu | Oct–Nov 2026 | Ian–Mar 2027 |
|---|---|---|
| Fără deploy | 15–20, în scădere | 15–20; afișările continuă să scadă |
| Deploy în octombrie | 15–25 (posibilă fluctuație 2–6 săptămâni după schimbarea de temă și adrese) | 30–45 |
| Deploy + Google Business Profile verificat, cu recenzii | ca mai sus | 30–45 din Search Console, **plus** apeluri și vizite din hartă, care nu apar în acest export |

Baza: ~50 de clicuri/lună în ianuarie–februarie, când site-ul avea aceeași
structură; revenirea la acel nivel e plauzibilă, depășirea lui depinde mai mult
de Google Business Profile decât de site.

---

## 5. Ce trebuie urmărit după deploy

1. **Pages → /servicii-psihologice/, /tarife-servicii-psihologice/, /cuplu/**:
   afișările trebuie să crească în 4–8 săptămâni. Primul indicator.
2. **Queries → „psiholog popesti leordeni”**: poziția și CTR-ul.
3. **Pages → /despre-mine/, /tarife/, /servicii/**: trebuie să dispară treptat,
   înlocuite de adresele noi.
4. **GA4 → evenimentul `contact`** (WhatsApp, telefon, email, formular), pe
   pagina de intrare: singura măsură a clienților, nu a vizitelor.
5. Un export nou peste ~8 săptămâni, cu aceleași filtre, pentru comparație
   directă cu acesta.
