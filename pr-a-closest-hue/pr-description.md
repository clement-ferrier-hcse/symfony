Title: [Console] Degrade hex colors to the closest hue in Ansi4 mode

| Q             | A
| ------------- | ---
| Branch?       | 8.2
| Bug fix?      | no
| New feature?  | yes
| Deprecations? | no
| Issues        | -
| License       | MIT

When the terminal only supports 4-bit colors, `AnsiColorMode::Ansi4` degrades hex colors by rounding each RGB channel to 0 or 1 on its own. Many mid-intensity colors end up as black: dark olive `#606702`, dark red `#6d0000` and Material green `#2e7d32` all render as black text.

This PR picks the 4-bit color with the closest hue instead: each color is mapped to the nearest of the six hues (red, yellow, green, cyan, blue, magenta). Greys have no hue, so they're mapped to black or white depending on their brightness, as before.

ECMA-48 (ISO/IEC 6429, SGR 30-37 and 40-47) only names the eight 4-bit colors and doesn't define their RGB values. The hue is the HSV one (A. R. Smith, "Color Gamut Transform Pairs", SIGGRAPH 1978), so each hue targets its pure RGB anchor:

| SGR | Color | Hue anchor | xterm default |
| --- | --- | --- | --- |
| 30 | black | `#000000` (dark greys) | `#000000` |
| 31 | red | `#ff0000` (0°) | `#cd0000` |
| 32 | green | `#00ff00` (120°) | `#00cd00` |
| 33 | yellow | `#ffff00` (60°) | `#cdcd00` |
| 34 | blue | `#0000ff` (240°) | `#0000ee` |
| 35 | magenta | `#ff00ff` (300°) | `#cd00cd` |
| 36 | cyan | `#00ffff` (180°) | `#00cdcd` |
| 37 | white | `#ffffff` (light greys) | `#e5e5e5` |

There's no brightness or saturation cutoff. The original implementation (#46944) had a low-saturation check, but it never ran: `0 === round(...)` is always false, because `round()` returns a float and `===` compares types. `getSaturation()` also had a precedence bug (`(int) $diff * 100 / $v` casts only `$diff`, so it gave 0 or 100), and it was removed in #50317. Had the check run, it would have turned every color under 25% saturation black, including white, light greys and pastels such as `#ffeeee`. I tried such cutoffs again: any value is arbitrary, and moving it changes a few percent of the colors. So a color keeps its hue as long as it has one, even when it's very dark or very pale.

Before / after, with xterm's default palette (the last column above: `color0` to `color7` of its `XTerm-col.ad`, i.e. the X11 `rgb.txt` colors black, red3, green3, yellow3, blue2, magenta3, cyan3 and gray90). The real colors depend on the terminal:

| Color | Before | After |
| --- | --- | --- |
| `#606702` dark olive | black | yellow |
| `#6d0000` dark red | black | red |
| `#2e7d32` Material green 800 | black | green |
| `#00796b` Material teal 700 | black | cyan |
| `#795548` brown | black | red |
| `#607d8b` blue grey | blue | cyan |
| `#eec7fa` pastel lilac | white | magenta |
| `#f5f5dc` beige | white | yellow |
| `#1565c0` Material blue 800 | blue | blue |
| `#e65100` dark orange | red | red |
| `#c0c0c0` silver | white | white |

Over 2,048 colors covering the RGB cube (16 levels of red and green, 8 of blue), 742 change. In both images, each cell shows the requested color in its top-left triangle and the Ansi4 result in its bottom-right one.

| Before | After |
| --- | --- |
| <!-- drag and drop ansi4-before.png here --> | <!-- drag and drop ansi4-after.png here --> |

The Ansi8 and Ansi24 modes are not affected. `ColorTest::testDegradedTrueColorsToAnsi4` still passes unchanged. In `AnsiColorModeTest`, two existing expectations change on purpose: `#606702` now gives yellow instead of black, and `#eec7fa` magenta instead of white. New cases cover each hue and the grey boundary (`#7f7f7f` black, `#808080` white).
