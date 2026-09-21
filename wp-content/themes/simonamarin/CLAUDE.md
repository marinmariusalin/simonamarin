# Tema WordPress simonamarin - CLAUDE.md

## 📍 Project Setup

**Path:** `c:\Users\Marin Marius Alin\Local Sites\simonamarin\app\public`  
**Tema:** `wp-content/themes/simonamarin/`  
**Repository:** https://github.com/marinmariusalin/simonamarin  
**Branch:** main  
**Type:** Underscores-based WordPress theme

## 🎯 Scope - Ce Modificam

### ✅ PERMIS - Modifica DOAR:
- Template files: `header.php`, `footer.php`, `sidebar.php`, `index.php`, `page.php`, etc.
- Structura HTML din template-parts
- CSS/SCSS: `style.css`, layout files
- JavaScript: `js/navigation.js`, `js/customizer.js`
- PHP din tema: `functions.php`, `inc/*.php`

### ❌ INTERZIS - NU modifica:
- **Conținut pagini/articole** - textul ramâne asa cum e
- Plugin-uri și plugin code
- WordPress core files
- Alte teme
- Baza de date direct (doar via WordPress API)

**REGULA DE AUR:** Modifici doar PREZENTAREA (HTML/CSS), NU CONȚINUTUL paginilor!

## 📊 Audit Status

### Raport Complet
- **Fișier:** `audit-report.html` (LOCAL - nu online!)
- **Data:** 21 septembrie 2026
- **Score:** 54/100 (MEDIU-SLAB)

### Probleme Identificate
- **8 Critice** - Necesită urgent
- **15 Avertismente** - Important
- **12 Recomandări** - Future improvements

### Comentarii Adăugate În:
```
functions.php      - Critical TODOs
header.php         - Security escaping marker (linia 44)
style.css          - Version warnings
customizer.js      - jQuery deprecation
footer.php         - Hardcoded links issue
composer.json      - PHP version requirement
```

## 🚀 Prioritate de Lucru

### Prioritate 1 - URGENT
1. ✅ **REZOLVAT** Update PHP requirement: 5.6 → 7.4 (composer.json + style.css)
2. ✅ **REZOLVAT** Update WordPress tested: 5.4 → 7.0
   Nota: WP-ul instalat este **7.0.5**, nu 6.4. Verificat în `wp-includes/version.php`.
3. ✅ **REZOLVAT PARȚIAL** Security headers → `inc/security.php`
   X-Content-Type-Options, Referrer-Policy, X-Frame-Options, Permissions-Policy.
   HSTS scris dar **off implicit** (se pornește cu o constantă în wp-config.php).
   **CSP rămâne deschis ca SEC-11** - se face întâi în mod Report-Only.
4. ✅ **REZOLVAT** Escaping în header.php → `wp_kses_post()`. Era la linia 189,
   nu 44 (linia 44 era în blocul de comentarii).
5. ⏸️ **DEPRIORITIZAT** Rescrie customizer.js fără jQuery.
   Auditul din 2026-09-21 a reevaluat asta de la CRITICAL la LOW, cu motiv bun:
   fișierul se încarcă **doar** în previzualizarea din Customizer, niciodată pe
   frontend, iar jQuery e oricum încărcat de WordPress în acel ecran. Impact pe
   performanță și SEO: zero. Nu merită efort înaintea SEO/PERF.

### Prioritate 2 - IMPORTANT
1. ⬜ JSON-LD structured data (SEO-01)
2. ⬜ Cache control headers
3. ⬜ Lazy loading imagini
4. ⬜ Migrare node-sass → sass
5. ✅ **REZOLVAT PARȚIAL** Block Editor support
   Adăugate: `editor-styles` + `add_editor_style`, `wp-block-styles`,
   `responsive-embeds`.
   **NU** s-a adăugat `align-wide` - fără `theme.json` nu face nimic vizibil.
   **theme.json (PUB-01) rămâne deschis și are nevoie de decizia ta** - vezi mai jos.

### ⚠️ Blocat pe decizia utilizatorului

**theme.json (PUB-01, CRITICAL)** este piesa centrală care oprește obiceiul de
a pune CSS inline în fiecare articol. Nu poate fi scris fără două lucruri pe
care nu le pot inventa:
- **lățimile reale** ale coloanei site-ului (`contentSize` / `wideSize`),
  măsurate în browser - auditul cere explicit să nu fie valori copiate;
- **paleta de brand** (culorile reale ale cabinetului).

Odată ce le am, în aceeași sesiune intră și `align-wide` (PUB-04) și
`add_image_size()` (SEO-04 / UX-76), care depind de aceeași grilă.

### Prioritate 3 - NICE-TO-HAVE (2-3 luni)
1. CSS custom properties
2. Dark mode support
3. Image optimization WebP
4. Breadcrumb navigation

## 📁 Structura Tema

```
simonamarin/
├── functions.php          - Main theme functions
├── header.php             - Header template
├── footer.php             - Footer template
├── style.css              - Main styles
├── style-rtl.css          - RTL styles
├── index.php              - Main template
├── page.php               - Page template
├── single.php             - Single post template
├── archive.php            - Archive template
├── search.php             - Search template
├── 404.php                - 404 template
├── sidebar.php            - Sidebar
├── comments.php           - Comments template
├── inc/                   - Includes folder
│   ├── functions.php      - Enhanced functions
│   ├── template-tags.php  - Template tags
│   ├── customizer.php     - Customizer
│   ├── custom-header.php  - Header customization
│   └── jetpack.php        - Jetpack compatibility
├── js/                    - JavaScript
│   ├── navigation.js      - Menu script
│   └── customizer.js      - Customizer preview (TODO: jQuery rewrite)
├── template-parts/        - Reusable templates
│   ├── content.php
│   ├── content-page.php
│   ├── content-search.php
│   └── content-none.php
└── languages/             - Translation files
```

## 🔧 Development Tools

### NPM Scripts
```bash
npm run watch          # Watch SASS files
npm run compile:css    # Compile CSS
npm run compile:rtl    # Generate RTL
npm run lint:scss      # Lint SCSS
npm run lint:js        # Lint JavaScript
```

### Composer Scripts
```bash
composer lint:wpcs     # PHP WordPress standards
composer lint:php      # PHP syntax check
```

## ⚠️ IMPORTANT Notes

### 1. NU modifica continut din pagini!
```
❌ WRONG: Șterge text din articol
✅ RIGHT: Schimbă CSS pentru afișare text
```

### 2. Rapoarte locale, nu online
- Audit report rămâne LOCAL în scratchpad
- Nu se publică pe artifact/web

### 3. Doar tema se commitează
```
✅ Tracked: wp-content/themes/simonamarin/
❌ Ignored: plugins, uploads, core, cache
```

### 4. Fiecare modificare = comentariu + commit
- Adăuga comentariu în cod ÎNAINTE de modificare
- Fă commit descriptiv
- Push la GitHub imediat

## 📝 Template pentru Modificări

Când faci o modificare:

```php
// TODO [PRIORITY]: Descriere problema
// File: functions.php
// Impact: Care se schimbă pentru user
// Fix: Ce exact se modifica

// Change here...
```

Commit message:
```
[TEMA] Brief description of change

- What was changed
- Why it was changed
- Line numbers or functions affected

See: audit-report.html for context
```

## 🔒 Git Workflow

```bash
# 1. Update din remote
git pull origin main

# 2. Modifica fișierele temei
# 3. Adauga comentarii în cod

# 4. Commit
git add wp-content/themes/simonamarin/
git commit -m "[TEMA] descriptie"

# 5. Push
git push origin main
```

## 📞 Getting Help

1. **Check:** `audit-report.html` pentru context
2. **Look:** Comentariile în fișierele modificate
3. **Read:** Memory files din `.claude/projects/.../memory/`
4. **Ask:** Claude cu context-ul temei

---

**Last Updated:** 21 septembrie 2026 (sesiune de implementare)  
**Status:** Implementare - Prioritatea 1 terminată, theme.json blocat pe decizie
