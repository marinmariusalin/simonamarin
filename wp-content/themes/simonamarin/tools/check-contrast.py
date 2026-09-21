# -*- coding: utf-8 -*-
"""
Verifica paleta temei simonamarin: contrast WCAG 2.2 si vedere cromatica.

Ruleaza din radacina temei:      python tools/check-contrast.py
Iese cu cod 1 daca vreo pereche pica pragul, deci poate fi pus intr-un hook.

Paleta NU este scrisa aici - este citita din theme.json, ca sa nu existe doua
surse de adevar. Daca schimbi o culoare in theme.json, scriptul o vede.

Motivatia fiecarei valori este in DESIGN.md.
"""
import colorsys
import json
import io
import os
import sys

if hasattr(sys.stdout, 'reconfigure'):
    sys.stdout.reconfigure(encoding='utf-8')

THEME_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def load_palette():
    """Citeste paleta si tokenurile custom din theme.json."""
    path = os.path.join(THEME_DIR, 'theme.json')
    with io.open(path, encoding='utf-8') as fh:
        data = json.load(fh)

    settings = data['settings']
    palette = {c['slug']: c['color'] for c in settings['color']['palette']}

    # borderStrong nu este intrare de paleta (nu trebuie sa apara ca optiune de
    # culoare in editor), dar este folosit in CSS si are prag de contrast.
    custom = settings.get('custom', {})
    if 'borderStrong' in custom:
        palette['border-strong'] = custom['borderStrong']

    return palette


# (prim-plan, fundal, prag, descriere). Doar perechi care apar efectiv pe ecran.
PAIRS = [
    ('contrast', 'base', 4.5, 'text principal pe fundalul paginii'),
    ('contrast', 'surface', 4.5, 'text principal pe card'),
    ('contrast', 'primary-soft', 4.5, 'text pe sectiune verde'),
    ('muted', 'base', 4.5, 'text secundar / legenda foto'),
    ('muted', 'surface', 4.5, 'text secundar pe card'),
    ('primary', 'base', 4.5, 'LINK in text pe fundal'),
    ('primary', 'surface', 4.5, 'link pe card'),
    ('primary', 'primary-soft', 4.5, 'link pe sectiune verde'),
    ('primary-dark', 'base', 4.5, 'link hover / focus'),
    ('accent', 'base', 4.5, 'accent pe fundal'),
    ('accent', 'surface', 4.5, 'accent pe card'),
    ('base', 'primary', 4.5, 'text pe buton primar'),
    ('base', 'primary-dark', 4.5, 'text pe buton hover'),
    ('base', 'accent', 4.5, 'text pe buton accent'),
    ('primary', 'base', 3.0, 'contur buton / icon (componenta UI)'),
    ('border-strong', 'base', 3.0, 'contur camp formular (componenta UI)'),
    ('border-strong', 'surface', 3.0, 'contur camp formular pe card'),
    ('primary-dark', 'base', 3.0, 'inel de focus pe fundal'),
]

# Suprafetele mari: saturatie joasa + luminozitate mare (vezi DESIGN.md).
LARGE_SURFACES = ['base', 'surface', 'primary-soft', 'border']
MAX_SURFACE_SATURATION = 35.0
MIN_SURFACE_LIGHTNESS = 82.0


def to_linear(channel):
    c = channel / 255.0
    return c / 12.92 if c <= 0.04045 else ((c + 0.055) / 1.055) ** 2.4


def channels(hexstr):
    h = hexstr.lstrip('#')
    return [int(h[i:i + 2], 16) for i in (0, 2, 4)]


def luminance(hexstr):
    r, g, b = (to_linear(c) for c in channels(hexstr))
    return 0.2126 * r + 0.7152 * g + 0.0722 * b


def contrast(a, b):
    la, lb = luminance(a), luminance(b)
    hi, lo = max(la, lb), min(la, lb)
    return (hi + 0.05) / (lo + 0.05)


def hsl(hexstr):
    r, g, b = (c / 255.0 for c in channels(hexstr))
    h, l, s = colorsys.rgb_to_hls(r, g, b)
    return h * 360, s * 100, l * 100


def to_srgb(value):
    value = max(0.0, min(1.0, value))
    v = 12.92 * value if value <= 0.0031308 else 1.055 * (value ** (1 / 2.4)) - 0.055
    return int(round(max(0.0, min(1.0, v)) * 255))


RGB2LMS = [[0.31399022, 0.63951294, 0.04649755],
           [0.15537241, 0.75789446, 0.08670142],
           [0.01775239, 0.10944209, 0.87256922]]
LMS2RGB = [[5.47221206, -4.6419601, 0.16963708],
           [-1.1252419, 2.29317094, -0.1678952],
           [0.02980165, -0.19318073, 1.16364789]]

# Vienot, Brettel & Mollon (1999).
SIM = {
    'protanopie':   [[0, 1.05118294, -0.05116099], [0, 1, 0], [0, 0, 1]],
    'deuteranopie': [[1, 0, 0], [0.9513092, 0, 0.04866992], [0, 0, 1]],
    'tritanopie':   [[1, 0, 0], [0, 1, 0], [-0.86744736, 1.86727089, 0]],
}


def matmul(matrix, vector):
    return [sum(matrix[i][j] * vector[j] for j in range(3)) for i in range(3)]


def simulate(hexstr, kind):
    lin = [to_linear(c) for c in channels(hexstr)]
    lms = matmul(SIM[kind], matmul(RGB2LMS, lin))
    return '#%02X%02X%02X' % tuple(to_srgb(c) for c in matmul(LMS2RGB, lms))


def rgb_distance(a, b):
    ca, cb = channels(a), channels(b)
    return sum((ca[i] - cb[i]) ** 2 for i in range(3)) ** 0.5


def main():
    palette = load_palette()
    problems = []

    print('PALETA (din theme.json)')
    print('%-15s %-9s %6s %7s %7s' % ('slug', 'hex', 'nuanta', 'sat%', 'lum%'))
    for slug in sorted(palette):
        hue, sat, lig = hsl(palette[slug])
        print('%-15s %-9s %6.0f %7.1f %7.1f' % (slug, palette[slug], hue, sat, lig))

    print('')
    print('SUPRAFETE MARI: saturatie < %.0f%% si luminozitate > %.0f%%'
          % (MAX_SURFACE_SATURATION, MIN_SURFACE_LIGHTNESS))
    print('(Valdez & Mehrabian 1994 - saturatia si luminozitatea conteaza mai')
    print(' mult decat nuanta; saturatie joasa + luminozitate mare = cel mai')
    print(' placut si cel mai putin activant. Vezi DESIGN.md.)')
    for slug in LARGE_SURFACES:
        if slug not in palette:
            continue
        _, sat, lig = hsl(palette[slug])
        ok = sat <= MAX_SURFACE_SATURATION and lig >= MIN_SURFACE_LIGHTNESS
        if not ok:
            problems.append('suprafata "%s": saturatie %.1f%%, luminozitate %.1f%%'
                            % (slug, sat, lig))
        print('  %-15s sat %5.1f%%  lum %5.1f%%   %s'
              % (slug, sat, lig, 'OK' if ok else 'PROBLEMA'))

    print('')
    print('CONTRAST WCAG 2.2 (4.5:1 text normal, 3:1 componente UI)')
    print('%-42s %8s %8s  %s' % ('pereche', 'raport', 'prag', 'verdict'))
    for fg, bg, need, label in PAIRS:
        if fg not in palette or bg not in palette:
            problems.append('lipseste culoarea "%s" sau "%s" din theme.json' % (fg, bg))
            continue
        ratio = contrast(palette[fg], palette[bg])
        ok = ratio >= need
        if not ok:
            problems.append('%s: %.2f:1, are nevoie de %.1f:1' % (label, ratio, need))
        level = 'AAA' if ratio >= 7 else ('AA' if ratio >= 4.5 else
                                          ('AA-mare' if ratio >= 3 else '-'))
        print('%-42s %6.2f:1 %6.1f:1  %s %s'
              % (label, ratio, need, 'OK  ' if ok else 'PICA', level))

    print('')
    print('VEDERE CROMATICA DEFICITARA (~8% barbati, ~0.5% femei)')
    print('Linkul fata de textul din jur - tehnica WCAG G183 cere 3:1 daca')
    print('linkul e distins NUMAI prin culoare:')
    underline_needed = False
    for kind in ('normal', 'protanopie', 'deuteranopie', 'tritanopie'):
        if kind == 'normal':
            link, text, bg = palette['primary'], palette['contrast'], palette['base']
        else:
            link = simulate(palette['primary'], kind)
            text = simulate(palette['contrast'], kind)
            bg = simulate(palette['base'], kind)
        vs_text = contrast(link, text)
        if vs_text < 3.0:
            underline_needed = True
        print('  %-14s link/fundal %5.2f:1   link/text %4.2f:1'
              % (kind, contrast(link, bg), vs_text))

    if underline_needed:
        print('')
        print('  => Linkul NU atinge 3:1 fata de textul din jur.')
        print('     Sublinierea este OBLIGATORIE, nu optionala.')
        print('     Setata in theme.json la styles.elements.link.textDecoration.')

    print('')
    print('Primarul si accentul, sub fiecare tip de vedere:')
    for kind in ('normal', 'protanopie', 'deuteranopie', 'tritanopie'):
        if kind == 'normal':
            a, b = palette['primary'], palette['accent']
        else:
            a, b = simulate(palette['primary'], kind), simulate(palette['accent'], kind)
        print('  %-14s primary=%s accent=%s  distanta %5.1f'
              % (kind, a, b, rgb_distance(a, b)))
    print('  => Distanta scade mult la protanopie/deuteranopie. Accentul nu are')
    print('     voie sa fie singurul lucru care diferentiaza doua actiuni.')

    print('')
    if problems:
        print('PROBLEME (%d):' % len(problems))
        for item in problems:
            print('  - ' + item)
        return 1

    print('Toate verificarile trec.')
    return 0


if __name__ == '__main__':
    sys.exit(main())
