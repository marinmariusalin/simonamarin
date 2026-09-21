TRADUCERILE TEMEI simonamarin
=============================

DEAD-04 [LOW] - REZOLVAT 2026-09-21. Textul generic Underscores care era aici a
fost inlocuit cu instructiunile reale ale acestui proiect.


CE ESTE IN ACEST DIRECTOR
-------------------------

simonamarin.pot   Sablonul: toate sirurile temei, cu traduceri goale.
                  Se regenereaza din cod cand se adauga siruri noi.
ro_RO.po          Traducerea in romana, editabila de om.
ro_RO.mo          Aceeasi traducere, compilata binar. ACESTA este fisierul pe
                  care il citeste WordPress la runtime, nu .po.


REGULA CARE SE UITA CEL MAI DES
-------------------------------

Editarea lui ro_RO.po NU schimba nimic pe site. WordPress citeste ro_RO.mo.
Dupa orice modificare in .po trebuie recompilat .mo:

    wp i18n make-mo languages/ languages/

(wp-cli/i18n-command este deja in require-dev din composer.json, deci comanda
este disponibila dupa `composer install`. Alternativ, msgfmt din gettext, sau
Poedit, care salveaza automat si .mo la fiecare salvare a .po.)

Daca traducerea "nu se aplica" dupa o editare, prima verificare este exact asta:
timestamp-ul lui .mo fata de .po.


CAND SE ADAUGA SIRURI NOI IN TEMA
---------------------------------

1. Scrie sirul in cod cu domeniul 'simonamarin', de exemplu
   esc_html__( 'Text nou', 'simonamarin' ).
2. Regenereaza sablonul:      composer make-pot
3. Adu sirurile noi in .po:   wp i18n update-po languages/simonamarin.pot languages/
4. Tradu intrarile goale din ro_RO.po.
5. Recompileaza:              wp i18n make-mo languages/ languages/


CE SE TRADUCE SI CE NU
----------------------

Aici se traduc EXCLUSIV sirurile interfetei temei: butoane, etichete, mesaje de
navigare, titluri de arhiva, texte de eroare. Acestea sunt scrise in fisierele
PHP ale temei.

Textele articolelor si ale paginilor NU ajung niciodata aici. Ele stau in baza
de date si nu se modifica din tema - este regula de baza a proiectului.


DESPRE TONUL TRADUCERILOR
-------------------------

Site-ul este al unui cabinet de psihoterapie. O parte dintre vizitatori ajung
aici in cautarea unui ajutor, iar unele dintre sirurile de mai sus apar fix in
momentele proaste: pagina negasita, cautare fara rezultate.

De aceea traducerea lui "Oops! That page can't be found." este
"Pagina aceasta nu a fost gasita." - fara "Oops!", fara exclamatii si fara
glume. Nu este o scapare de traducere, este o decizie. Aceeasi logica se aplica
oricarui sir nou: neutru, direct, si spune-i omului ce poate face mai departe.

Nota: acest fisier acopera doar FORMULAREA mesajelor. Ce informatii ar trebui
sa contina pagina de 404 sau subsolul site-ului (de exemplu date de contact in
caz de criza) este o discutie separata - vezi PSY-04 in footer.php. Acele
informatii se verifica inainte de publicare, nu se aproximeaza.


PLURALUL IN ROMANA
------------------

Romana are trei forme, nu doua:
    1 comentariu  /  5 comentarii  /  25 de comentarii

Regula folosita in .po:
    nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);

Cine adauga un sir cu _n() sau _nx() trebuie sa completeze toate cele trei
msgstr[0..2]. O traducere cu doua forme va afisa gresit "25 comentarii".
