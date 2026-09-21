# -*- coding: utf-8 -*-
"""Extrage conturile SVG ale iconitelor folosite, din woff2-urile FontAwesome ale temei."""
import io
import sys

from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen

sys.stdout.reconfigure(encoding='utf-8')

WEBFONTS = r"c:\Users\Marin Marius Alin\Local Sites\simonamarin\app\public\wp-content\themes\sydney\webfonts"

# nume -> (fisier, codepoint FontAwesome 5)
ICONS = {
    'facebook':  ('fa-brands-400.woff2', 0xF09A),
    'whatsapp':  ('fa-brands-400.woff2', 0xF232),
    'instagram': ('fa-brands-400.woff2', 0xF16D),
    'phone':     ('fa-solid-900.woff2',  0xF095),
    'envelope':  ('fa-solid-900.woff2',  0xF0E0),
}

fonts = {}
results = {}

for name, (fname, cp) in ICONS.items():
    if fname not in fonts:
        fonts[fname] = TTFont(WEBFONTS + '\\' + fname)
    font = fonts[fname]

    upm = font['head'].unitsPerEm
    ascent = font['hhea'].ascent

    cmap = font.getBestCmap()
    if cp not in cmap:
        print('LIPSA: %s (U+%04X) nu exista in %s' % (name, cp, fname))
        continue
    glyph_name = cmap[cp]

    glyph_set = font.getGlyphSet()
    pen = SVGPathPen(glyph_set)
    glyph_set[glyph_name].draw(pen)
    path = pen.getCommands()

    width = font['hmtx'][glyph_name][0]

    results[name] = {
        'path': path,
        'width': width,
        'upm': upm,
        'ascent': ascent,
        'glyph': glyph_name,
    }
    print('%-10s glifa=%-16s latime=%4d  upm=%d ascent=%d  path=%d caractere'
          % (name, glyph_name, width, upm, ascent, len(path)))

print()
total = sum(len(r['path']) for r in results.values())
print('Total contur brut: %d caractere (~%.1f KB)' % (total, total / 1024.0))

# Genereaza fisierul PHP pentru tema copil.
out = []
out.append('<?php')
out.append('/**')
out.append(' * Iconite SVG inline - inlocuiesc FontAwesome.')
out.append(' *')
out.append(' * GENERAT AUTOMAT din webfonturile FontAwesome livrate cu tema parinte')
out.append(' * (sydney/webfonts/fa-brands-400.woff2 si fa-solid-900.woff2), pe 21.09.2026.')
out.append(' * Conturile sunt exact aceleasi pe care le desena fontul, deci iconitele arata')
out.append(' * identic - nu sunt inlocuitori aproximativi dintr-un alt set.')
out.append(' *')
out.append(' * DE CE: site-ul incarca 155 KB de webfonturi (fa-solid-900.woff2 78 KB +')
out.append(' * fa-brands-400.woff2 77 KB) plus trei fisiere CSS, ca sa afiseze CINCI iconite.')
out.append(' * Inline, aceleasi cinci ocupa sub 3 KB si nu mai genereaza nicio cerere.')
out.append(' * In plus, fisierele CSS erau cerute de la https://simonamarin.ro, adresa')
out.append(' * scrisa de mana - deci pe orice alt domeniu decat productia iconitele')
out.append(' * dispareau complet.')
out.append(' *')
out.append(' * Iconitele FontAwesome Free sunt sub licenta CC BY 4.0 (fontawesome.com).')
out.append(' * Sunt aceleasi active deja livrate cu tema, doar servite altfel.')
out.append(' *')
out.append(' * REGENERARE: daca se schimba setul de iconite, se ruleaza din nou scriptul de')
out.append(' * extragere. NU se editeaza conturile de mai jos de mana.')
out.append(' *')
out.append(' * @package sydney-child')
out.append(' */')
out.append('')
out.append('// Exit if accessed directly.')
out.append("if ( ! defined( 'ABSPATH' ) ) {")
out.append('\texit;')
out.append('}')
out.append('')
out.append('/**')
out.append(' * Returneaza o iconita SVG inline.')
out.append(' *')
out.append(' * Marcajul este ascuns de tehnologiile asistive prin aria-hidden, pentru ca')
out.append(' * fiecare iconita de pe site sta intr-un <a> care are deja aria-label cu textul')
out.append(' * real ("Facebook", "Telefon"). Fara aria-hidden, cititorul de ecran ar anunta')
out.append(' * de doua ori aceeasi legatura.')
out.append(' *')
out.append(' * MARCAJUL PASTREAZA INTENTIONAT UN <i> CA INVELIS, desi desenul e un SVG.')
out.append(' * Motivul: tot CSS-ul existent al site-ului tinteste elementul <i>, iar')
out.append(' * verificarea pe site-ul de productie a aratat trei reguli care depind de el:')
out.append(' *   - `.header-links i { color: #000 }`, din CSS-ul aditional, face iconitele')
out.append(' *     negre desi linkul din jur este rosu;')
out.append(' *   - `a, i { min-width: 24px; min-height: 24px !important }` le da cutia de')
out.append(' *     24x24 px din care rezulta spatierea dintre ele;')
out.append(' *   - `.whatsapp-footer` duce butonul flotant la 80px si la verdele WhatsApp.')
out.append(' * Daca as fi emis direct <svg>, toate trei ar fi incetat sa se aplice si ar fi')
out.append(' * trebuit sa copiez valorile aici - adica sa duplic decizii care traiesc in')
out.append(' * alta parte si care se pot schimba fara ca fisierul asta sa afle. Asa, nu se')
out.append(' * duplica nimic si aspectul ramane exact cel de dinainte.')
out.append(' *')
out.append(' * `fill="currentColor"` face desenul sa mosteneasca culoarea de pe <i>, exact')
out.append(' * cum o glifa de font mostenea proprietatea `color`.')
out.append(' *')
out.append(' * @param string $name  Numele iconitei.')
out.append(' * @param string $class Clase CSS suplimentare, optional.')
out.append(' * @return string Marcajul SVG, sau sir gol daca iconita nu exista.')
out.append(' */')
out.append('function simonamarin_icon( $name, $class = \'\' ) {')
out.append('\t$icons = array(')

for name in sorted(results):
    r = results[name]
    vb = '0 0 %d %d' % (r['width'], r['upm'])
    out.append("\t\t'%s' => array(" % name)
    out.append("\t\t\t'viewBox' => '%s'," % vb)
    out.append("\t\t\t'path'    => '%s'," % r['path'].replace("'", "\\'"))
    out.append("\t\t),")

out.append('\t);')
out.append('')
out.append('\tif ( ! isset( $icons[ $name ] ) ) {')
out.append('\t\treturn \'\';')
out.append('\t}')
out.append('')
out.append('\t$icon = $icons[ $name ];')
out.append('')
out.append('\t/*')
out.append('\t * Transformarea exista pentru ca fonturile si SVG-ul numara axa Y invers:')
out.append('\t * in font Y creste in sus de la linia de baza, in SVG creste in jos de la')
out.append('\t * coltul din stanga sus. `translate` coboara originea la inaltimea de')
out.append('\t * ascendenta a fontului, iar `scale(1,-1)` rastoarna axa. Fara ea iconitele')
out.append('\t * ar aparea cu susul in jos si in afara cadrului.')
out.append('\t */')
out.append("\treturn sprintf(")
out.append("\t\t'<i class=\"sm-icon %1$s\">'")
out.append("\t\t. '<svg viewBox=\"%2$s\" xmlns=\"http://www.w3.org/2000/svg\" aria-hidden=\"true\" focusable=\"false\" role=\"presentation\">'")
out.append("\t\t. '<g transform=\"translate(0,%3$d) scale(1,-1)\" fill=\"currentColor\"><path d=\"%4$s\"/></g></svg></i>',")
out.append("\t\tesc_attr( trim( $class ) ),")
out.append("\t\tesc_attr( $icon['viewBox'] ),")
out.append("\t\t%d," % list(results.values())[0]['ascent'])
out.append("\t\t$icon['path']")
out.append("\t);")
out.append('}')
out.append('')

dest = r"c:\Users\Marin Marius Alin\Local Sites\simonamarin\app\public\wp-content\themes\sydney-child\inc\icons.php"
import os
os.makedirs(os.path.dirname(dest), exist_ok=True)
io.open(dest, 'w', encoding='utf-8', newline='\n').write('\n'.join(out))
print('\nScris: sydney-child/inc/icons.php (%d octeti)' % os.path.getsize(dest))
