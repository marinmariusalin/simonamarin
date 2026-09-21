# Sistemul de design — tema simonamarin

Documentul explică **de ce** arată tema așa cum arată. Valorile concrete stau în
[theme.json](theme.json); aici sunt motivele, cifrele și limitele deciziei.

Dacă modifici o culoare sau o dimensiune, citește întâi secțiunea care o
acoperă, apoi rulează din nou verificarea din *Cum se verifică*. Câteva dintre
valori nu sunt preferințe estetice, ci praguri — schimbate „ca să arate mai
bine", pică accesibilitatea.

---

## Contextul care conduce deciziile

Site-ul aparține unui cabinet de psihoterapie. O parte dintre vizitatori ajung
aici căutând ajutor, uneori în perioade proaste. Asta schimbă natura deciziilor
de design: culoarea, ritmul vizual și tonul nu sunt ornament, sunt funcție.

Trei consecințe practice, care se regăsesc în tot ce urmează:

1. **Saturație joasă pe suprafețe mari.** Nu din modă, ci pentru că e efectul
   cel mai bine documentat din literatura de culoare (mai jos).
2. **Fără roșu de alarmă și fără urgență fabricată.** Nici ca accent, nici în
   mesajele de eroare. Pe publicul acesta, tiparele de marketing agresiv
   (countdown, „ultimele locuri", exit-intent) sunt o problemă etică, nu doar
   una de gust.
3. **Accesibilitatea ca prag, nu ca bonus.** Un vizitator obosit, anxios sau cu
   vedere slabă este cazul tipic aici, nu excepția.

---

## Culoare

### Ce spune de fapt cercetarea

Majoritatea a ceea ce circulă sub numele de „psihologia culorilor" —  tabelele
cu „albastru = încredere", „verde = vindecare" — nu rezistă la verificare.
Efectele de nuanță sunt mici, dependente de cultură și de context, și
reproductibilitatea lor este slabă.

Ce **rezistă** este mai puțin spectaculos și mai util:

- **Valdez & Mehrabian (1994)**, pe un eșantion mare, au măsurat reacția
  afectivă (plăcere / activare / dominanță) la culori controlate. Rezultatul
  central: **saturația și luminozitatea explică mult mai mult din reacție decât
  nuanța**. Combinația cea mai plăcută și cel mai puțin activantă este
  *luminozitate mare + saturație joasă*. Nuanța contează, dar mult mai puțin
  decât se presupune.
- Tot acolo, în ordinea plăcerii pe nuanță, zona **albastru-verde** iese în
  partea de sus, iar **galben-verde** în partea de jos.
- **Elliot & Maier** și literatura care a urmat: roșul crește vigilența și
  performanța pe sarcini de evitare, și o scade pe cele de deschidere. Motiv
  suficient să nu fie culoare principală pe un site unde vrei ca omul să se
  simtă în largul lui.

**Concluzia operațională:** decizia importantă nu e „ce nuanță aleg", ci **cât
de saturate și cât de luminoase sunt suprafețele mari**. De aceea toate
suprafețele mari din paletă stau sub 35% saturație și peste 82% luminozitate —
și de aceea nuanța aleasă (verde-albastru) e o alegere secundară, luată din
zona cu cel mai bun scor, dar fără să pretind că ea face diferența.

### De ce verde-albastru și nu albastru

Albastrul e nuanța cea mai larg preferată, dar albastrul **saturat** poartă în
context de sănătate o asociere concretă și nefolositoare aici: spital, clinică,
instituție. Verdele-albastru (sage / teal) stă în aceeași zonă bună de
plăcere, dar evită conotația clinică.

Fundalul nu e alb pur, ci un alb cald (`#FAF8F5`). Albul pur pe un ecran
luminos dă contrast maxim și obosește la lectură lungă; un ton cald foarte
puțin saturat scade strălucirea fără să coboare contrastul sub prag.

### Paleta

| Rol | Slug | Hex | Nuanță | Sat. | Lum. | Unde apare |
|---|---|---|---|---|---|---|
| Fundal pagină | `base` | `#FAF8F5` | 36° | 33% | 97% | fundalul întregului site |
| Card / secțiune | `surface` | `#F0EBE4` | 35° | 29% | 92% | carduri, sidebar |
| Secțiune verde | `primary-soft` | `#DCE7E3` | 158° | 19% | 88% | fundal de citat, secțiuni |
| Linie | `border` | `#DDD5CA` | 35° | 22% | 83% | separatoare decorative |
| Text secundar | `muted` | `#55635F` | 163° | 8% | 36% | legende foto, meta |
| Text principal | `contrast` | `#24302C` | 160° | 14% | 17% | corpul de text, titluri |
| Verde principal | `primary` | `#3D6F65` | 168° | 29% | 34% | linkuri, butoane |
| Verde închis | `primary-dark` | `#2C524A` | 167° | 30% | 25% | hover, focus |
| Accent cărămiziu | `accent` | `#95563A` | 18° | 44% | 41% | **un singur** CTA per pagină |

Plus un token care **nu** e ofertă de culoare pentru autor:
`--wp--custom--border-strong` = `#7C8884`, conturul câmpurilor de formular.

Accentul cărămiziu e singura culoare din paletă cu saturație peste 35%. E
intenționat și e singurul loc cu căldură vizuală puternică — tocmai de aceea se
folosește rar. Folosit peste tot, își pierde funcția și devine zgomot.

### Contraste măsurate (WCAG 2.2)

Toate cele 18 perechi care apar efectiv pe ecran trec. Nu sunt estimări — sunt
calculate din formula de luminanță relativă.

| Pereche | Raport | Prag | Nivel |
|---|---|---|---|
| text principal pe fundal | 12.91:1 | 4.5:1 | AAA |
| text principal pe card | 11.54:1 | 4.5:1 | AAA |
| text pe secțiune verde | 10.81:1 | 4.5:1 | AAA |
| text secundar pe fundal | 5.94:1 | 4.5:1 | AA |
| text secundar pe card | 5.31:1 | 4.5:1 | AA |
| link pe fundal | 5.41:1 | 4.5:1 | AA |
| link pe card | 4.84:1 | 4.5:1 | AA |
| link pe secțiune verde | 4.53:1 | 4.5:1 | AA |
| link hover / focus | 8.21:1 | 4.5:1 | AAA |
| accent pe fundal | 5.40:1 | 4.5:1 | AA |
| accent pe card | 4.83:1 | 4.5:1 | AA |
| text pe buton primar | 5.41:1 | 4.5:1 | AA |
| text pe buton hover | 8.21:1 | 4.5:1 | AAA |
| text pe buton accent | 5.40:1 | 4.5:1 | AA |
| contur câmp formular | 3.47:1 | 3:1 | — |
| inel de focus | 8.21:1 | 3:1 | AAA |

`border` (`#DDD5CA`) are doar 1.37:1 și **este în regulă așa**: WCAG 1.4.11 cere
3:1 pentru elementele necesare ca să identifici un control sau starea lui, nu
pentru separatoare ornamentale. Pentru conturul câmpurilor de formular — care
chiar identifică un control — există `border-strong`, la 3.47:1.

Marjele sunt strânse în câteva locuri (4.53, 4.83, 4.84). **Dacă deschizi
paleta ca să schimbi o culoare, rulează din nou verificarea** — o ajustare de
două-trei procente poate trece o pereche sub prag.

### Vedere cromatică deficitară

Aproximativ **8% dintre bărbați și 0.5% dintre femei** au o formă de deficiență
de vedere cromatică. Am simulat paleta (Viénot et al. 1999, în spațiu LMS)
pentru protanopie, deuteranopie și tritanopie.

Două rezultate contează:

**1. Linkurile trebuie subliniate.** Nu e preferință estetică.

| Vedere | link / fundal | link / text din jur |
|---|---|---|
| normală | 5.41:1 | **2.39:1** |
| protanopie | 5.25:1 | **2.43:1** |
| deuteranopie | 5.75:1 | **2.30:1** |
| tritanopie | 5.41:1 | **2.37:1** |

Linkul se vede bine față de fundal, dar față de **textul din jur** stă la
2.3–2.4:1 la toate tipurile de vedere. Tehnica WCAG G183 cere minim 3:1 dacă
linkul e distins **numai** prin culoare. Nu ajungem acolo — deci sublinierea
este obligatorie, și e setată în `theme.json` la `elements.link`.

Alternativa ar fi fost să închid mult verdele, dar atunci linkul ar fi devenit
aproape la fel de întunecat ca textul și s-ar fi pierdut în alt fel.

**2. Primarul și accentul se apropie la daltoniști.** Distanța dintre ele scade
de la 101 (vedere normală) la 44–52 sub protanopie / deuteranopie — ambele
devin tonuri măslinii asemănătoare. Consecință practică: **accentul nu are voie
să fie singurul lucru care diferențiază două acțiuni.** Fiindcă e folosit doar
pentru un CTA unic, iar butoanele se disting oricum prin poziție și etichetă,
e acceptabil — dar dacă apar vreodată două butoane alăturate de culori diferite,
trebuie să difere și prin formă sau etichetă.

---

## Tipografie și lățime

### Corpul de text: 18px, nu 16px

16px e mărimea implicită a browserului, gândită pentru interfețe. Pe un site
unde oamenii citesc texte lungi, 18px e pragul de la care lizibilitatea nu mai
depinde de cât de bine vede cititorul.

`line-height` 1.65, nu 1.5. WCAG 2.2 criteriul 1.4.12 cere ca pagina să rămână
funcțională dacă utilizatorul **impune** 1.5. Plecând de la 1.65, nimeni nu are
nevoie să forțeze setarea.

### Lățimea coloanei: 40rem (640px)

Lungimea de rând recomandată pentru lectură continuă e 45–75 de caractere.

Calculul: la 18px, cu un sans humanist, lățimea medie a unui caracter e
aproximativ 0.5em ≈ 9px. `640 / 9 ≈ 71 de caractere` — în interval, spre
capătul confortabil.

**Lățimea și mărimea fontului merg împreună.** Dacă fontul crește la 20px și
coloana rămâne 640px, rândul scade la ~64 caractere (încă bine). Dacă fontul
scade la 16px, rândul urcă la ~80 — prea lung. Se ajustează amândouă.

### Lățimea mare: 68rem (1088px)

Coloana de text stă la 640px, dar imaginile și secțiunile marcate „Lățime mare"
ies până la 1088px. Aceeași valoare e și în `$content_width` din
`functions.php` — **cele două trebuie să rămână identice**. `$content_width` e
plafonul pe care WordPress îl folosește când decide ce dimensiune de imagine să
ofere; dacă rămâne mai mic, imaginea principală e servită mică și apare
neclară, iar autorul reacționează punându-i lățime manual în articol.

### Scara de titluri

h1 2.25rem · h2 1.75rem · h3 1.375rem · h4 1.125rem · h5–h6 1rem.

Înainte, h1–h6 primeau doar `clear: both`, deci rămâneau la valorile implicite
ale browserului și un h2 arăta aproape ca un h3. Asta anulează beneficiul
structurii semantice: Google folosește ierarhia de titluri pentru featured
snippets și jump links.

---

## De ce paleta e închisă

În `theme.json`: `color.custom: false`, `color.defaultPalette: false`,
`typography.customFontSize: false`, `spacing.customSpacingSize: false`.

Autorul **nu** mai are color picker liber și nici mărime de font arbitrară.
Asta e intenționat și e miezul problemei pe care o rezolvă theme.json.

Cu controale libere, fiecare alegere se scrie ca **stil inline în articol**.
Consecințele se văd în timp: două articole nu arată la fel, o schimbare de
design cere reeditarea fiecărui articol, iar tema nu mai poate corecta nimic
global fiindcă stilul inline bate orice regulă.

Cu un set fix, aceeași alegere produce o **clasă** (`.has-primary-color`), nu un
stil inline — deci culoarea se poate schimba dintr-un singur loc, pentru toate
articolele deodată.

Costul e real: mai puțină libertate pentru autor. Compensarea corectă nu e
redeschiderea paletei, ci **block patterns** (PUB-09) — structuri gata făcute
din care autorul alege.

---

## Ce am decis să NU fac

- **Dark mode.** Ar cere un al doilea set de contraste verificate și un
  comutator. Făcut pe jumătate e mai rău decât deloc. E pe lista de Prioritate 3.
- **Webfont de brand.** Font stack-ul rămâne system-ui: zero requesturi, zero
  risc de FOIT/FOUT, LCP mai bun. Un font de brand e o decizie de identitate
  vizuală, nu una tehnică — și dacă se ia, e obligatoriu `font-display: swap`
  plus preload pentru `.woff2` de deasupra foldului.
- **Poziționarea sidebar-ului.** Întrebarea deschisă (UX-41) e dacă sidebar-ul
  are vreun rol real sau împrăștie atenția. Până la răspuns, conținutul rămâne
  pe o coloană — oricum varianta bună pe mobil.
- **`add_image_size()` proprii.** Depind de grila cardurilor din listări, care
  nu e încă decisă. Adăugate acum, ar însemna regenerarea mediatecii de două ori.

---

## Cum se verifică

Numerele de mai sus se pot reproduce:

```
python tools/check-contrast.py
```

Scriptul citește paleta, calculează toate perechile care apar pe ecran și
simulează cele trei tipuri de deficiență cromatică. Iese cu cod diferit de zero
dacă vreo pereche pică pragul, deci poate fi pus și într-un hook de commit.

**După orice modificare de culoare, rulează-l.** Paleta are câteva marje sub
5:1, iar o ajustare care „arată mai bine" poate strica accesibilitatea fără
niciun semn vizibil pentru cine are vedere bună.

---

## Ce rămâne de verificat în browser

Nimic din ce e mai sus nu a fost văzut într-un browser: baza de date a
site-ului Local nu rula când a fost scris. Înainte să ajungă în producție:

1. **Meniul mobil** — se deschide la apăsarea butonului, pe un ecran sub 600px.
2. **Un articol existent** — theme.json schimbă aspectul pe tot site-ul odată.
   Articolele vechi cu stil inline vor arăta în continuare altfel (PUB-19).
3. **Editorul de articole** — trebuie să semene acum cu site-ul.
4. **Lățimea mare** — inserează o imagine, apasă „Lățime mare", verifică pe site.
5. **Formularul de contact** — câmpurile au contur vizibil și nu declanșează
   zoom automat pe iOS.

## Surse

- Valdez, P. & Mehrabian, A. (1994). *Effects of color on emotions.* Journal of
  Experimental Psychology: General, 123(4), 394–409.
- Elliot, A. J. & Maier, M. A. (2014). *Color psychology: Effects of perceiving
  color on psychological functioning in humans.* Annual Review of Psychology, 65.
- Viénot, F., Brettel, H. & Mollon, J. D. (1999). *Digital video colourmaps for
  checking the legibility of displays by dichromats.* Color Research &
  Application, 24(4).
- W3C, *Web Content Accessibility Guidelines (WCAG) 2.2* — criteriile 1.4.3,
  1.4.11, 1.4.12 și tehnica G183.
- Bringhurst, R. *The Elements of Typographic Style* — pentru intervalul de
  45–75 de caractere pe rând.
