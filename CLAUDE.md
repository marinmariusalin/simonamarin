# Proiect: site-ul cabinetului Simona Marin

Citește acest fișier înainte de orice. Înlocuiește
`wp-content/themes/simonamarin/CLAUDE.md`, care conține informații **greșite**
despre ce rulează pe site.

**Lista de lucru este în [TODO.md](TODO.md).**

---

## Ce rulează — verifică, nu presupune

| | |
|---|---|
| Temă activă | **`sydney-child`**, părinte `sydney` **2.71** |
| Cod independent de temă | `wp-content/mu-plugins/simonamarin-hardening.php`<br>`wp-content/mu-plugins/simonamarin-fara-comentarii.php` |
| **Inactivă, de ignorat** | `wp-content/themes/simonamarin/` |

`wp-content/themes/simonamarin/` este o temă instalată dar **niciodată
activată**. Conține ~263 de TODO-uri, un `theme.json`, un sistem de design și
traduceri — toate scrise în primele sesiuni pe presupunerea greșită că aceea e
tema site-ului. **Nimic de acolo nu rulează.** Nu lucra în ea.

Tema activă se citește din baza de date, nu din structura directoarelor:

```php
get_option('stylesheet')   // sydney-child
get_option('template')     // sydney
```

---

## Reguli de bază

**Conținutul nu se atinge.** Se modifică prezentarea — HTML, CSS, PHP de temă.
Nu se modifică titluri, texte de pagini sau articole, meta descrieri sau
sluguri. Acelea stau în baza de date și aparțin utilizatorului.

**Personalizările nu se scriu în tema părinte.** Tot ce e propriu site-ului
merge în `sydney-child/` sau în mu-plugin. Un update Sydney șterge complet
directorul `sydney/`. S-a întâmplat deja: header-ul, subsolul și datele
structurate scrise acolo au dispărut la migrarea la 2.71.

**Preferă hook-urile și filtrele de nucleu șabloanelor copiate.** Un
`header.php` copiat în tema copil e scris pentru markup-ul unei versiuni și se
rupe tăcut la următoarea. Un filtru ca `wp_nav_menu_items` supraviețuiește
inclusiv unei schimbări complete de temă. Vezi `inc/contact-links.php` pentru
raționamentul complet.

**Rapoartele se salvează local**, în proiect. Nu se publică online.

**Fiecare modificare:** comentariu în cod care explică *de ce*, commit
descriptiv, push.

---

## Domeniu: cabinet de psihoterapie

Publicul include persoane în suferință sau în criză. Tonul, culorile și ritmul
vizual sunt decizii funcționale, nu estetice.

- Saturație joasă pe suprafețe mari, fără roșu de alarmă.
- **Interzise** tiparele de marketing agresiv: countdown, „ultimele locuri",
  exit-intent, confirmshaming. Pe acest public sunt o problemă etică.
- Datele din formularul de contact sunt date de sănătate (GDPR art. 9).
- Informațiile de criză, disclaimerele și titulatura profesională **se verifică
  la sursă înainte de publicare** — niciodată nu se inventează.
- Site-ul **nu are comentarii**, decizie explicită a utilizatorului.

---

## Mediul de lucru

**Site local:** `http://simonamarin.local/` — funcțional, se poate testa direct.
**Producție:** `https://simonamarin.ro/` — utilă ca referință de design.

**Baza de date** din linia de comandă: `DB_HOST` este `localhost` fără port,
deci `wp-load.php` eșuează direct. Portul se citește din `sites.json` din
directorul `Local` din `APPDATA` (pentru acest site: **10005**), iar extensia
`mysqli` nu e activă implicit:

```bash
php -d extension=php_mysqli.dll -d mysqli.default_port=10005 script.php
```

Prefixul tabelelor **nu** este `wp_`, ci `wpez_`.

**Capturi de ecran:** Chrome e instalat. Pentru mobil se folosește **emulare
reală de dispozitiv** prin DevTools Protocol
(`Emulation.setDeviceMetricsOverride` + `Page.captureScreenshot`), **nu**
`--window-size` — acela randează alt viewport decât cel măsurat și a dus deja
la o concluzie falsă.

⚠️ Chrome lăsat cu `--user-data-dir` temporar consumă ~46 MB pe profil.
**Șterge-le după fiecare rulare** — discul acestui calculator a fost deja umplut
complet așa.

---

## Metodă

**Verifică în sistemul care rulează, nu în fișiere.** Două sesiuni de muncă au
fost pierdute pentru că nu am verificat ce temă e activă.

**Ce e modificat în cod terț se află prin diff cu versiunea oficială**
descărcată de la sursă, nu prin căutare după indicii. O euristică ratează exact
ce e periculos: o linie schimbată în interiorul codului original.

**Schimbările vizuale se verifică pixel cu pixel**, comparând capturi înainte și
după. Așa a fost prinsă o inversare de ordine CSS care muta layout-ul cu 5,42%
din pixeli și pe care ochiul nu o vedea.

**Când raportezi că ceva „nu a putut fi verificat"**, asta trebuie să însemne că
ai încercat și ai întâmpinat un obstacol pe care îl numești — nu că ai presupus
că nu se poate.

---

## Documente

- **[TODO.md](TODO.md)** — lista de lucru curentă
- `sydney-update-analiza.md` — comparația cu Sydney 2.71 oficial
- `audit-2-seo-security-performance.md` — auditul inițial (context istoric; țintea tema inactivă)
