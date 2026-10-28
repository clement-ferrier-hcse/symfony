Title: [Console] Restore the saturation check of the Ansi4 color degradation

| Q             | A
| ------------- | ---
| Branch?       | 8.2
| Bug fix?      | no
| New feature?  | yes
| Deprecations? | no
| Issues        | -
| License       | MIT

#46944 added a saturation check to `AnsiColorMode::degradeHexColorToAnsi4()`: colors whose saturation rounded to 0 (under 25%) were meant to get a special result (black), the others were rounded per channel. #50317 removed it as redundant, because it never took effect. Three defects made it dead code:

* `(int) $diff * 100 / $v` casts `$diff` (between 0 and 1) before multiplying, so the saturation was only ever 0 or 100.
* `0 === round(...)` is always false: `round()` returns a float, and `===` does not match it with the int `0`.
* `0 === $diff = ...` has the same problem, as `$diff` is a float for most colors (greys included).

The precedence problem was pointed out in a comment on #50317, but the method was removed anyway.

This PR brings the check back with those defects fixed:

* `(int) ($diff * 100 / $v)` instead of `(int) $diff * 100 / $v`.
* `0.0 === round(...)` instead of `0 === round(...)`.
* `0.0 === $diff = ...` instead of `0 === $diff = ...`. The channels are divided by `255.0` because `255 / 255` and `0 / 255` are ints in PHP: without it `$diff` would sometimes be an int, so the `0.0` comparison would be skipped for black (`#000000`) and `getSaturation()` would throw a `DivisionByZeroError`.

One thing differs from #46944: it returned black for every color under 25% saturation, which would turn white and light greys into black text on a dark terminal. Here those colors become black or white depending on their brightness: black if the highest RGB channel is under 128, white otherwise. The other colors are still rounded per channel. `getSaturation()` is a private helper again.

This is a small change, and not a pure improvement. Greys, white, black and most pastels already give black or white today, because rounding the three channels together gives the same result for them (`#808080` and above is white, `#7f7f7f` and below is black), so they do not change. What changes are the muted colors that are mid-tone: a channel is at 128 or more and another one under 128, so per-channel rounding gives a saturated color (red, green, blue...) even though the color is almost grey. They now become white. Black never changes, and no color goes from white to anything else:

| Color | Saturation | Before | After |
| --- | --- | --- | --- |
| `#8a7a7a` | 11% | red | white |
| `#907878` | 16% | red | white |
| `#a1887f` Material brown 300 | 21% | yellow | white |
| `#997792` | 22% | magenta | white |
| `#777792` | 18% | blue | white |
| `#66886d` | 24% | green | white |
| `#ffffff` white | 0% | white | white |
| `#808080` grey | 0% | white | white |
| `#eec7fa` pastel lilac | 20% | white | white |
| `#606702` dark olive | 98% | black | black |
| `#1565c0` Material blue 800 | 89% | blue | blue |
| `#ffd700` gold | 100% | yellow | yellow |

Over 2,048 colors covering the RGB cube (16 levels of red and green, 8 of blue), 10 change, all of them to white: 2 were red, 2 green, 1 yellow, 1 blue, 2 magenta and 2 cyan. Black stays at 256 colors and white goes from 256 to 266. Over the whole 24-bit cube, 100,272 of 16,777,216 colors change (0.6%), all of them from red, green, yellow, blue, magenta or cyan to white (14,303 each of red, green and blue, 19,121 each of yellow, magenta and cyan). Black stays at 2,097,152 colors and white goes from 2,097,152 to 2,197,424. In both images, each cell shows the requested color in its top-left triangle and the Ansi4 result in its bottom-right one.

| Before | After |
| --- | --- |
| <!-- drag and drop ansi4-saturation-before.png here --> | <!-- drag and drop ansi4-saturation-after.png here --> |

Other limitations:

* The saturation is truncated to an int from floats, so a color exactly on the 25% limit can land on either side of it: `#666688` (exactly 25%) gives 24 and becomes white, where exact arithmetic would keep it blue. About 0.02% of the 24-bit colors (3,636) are on the other side of the limit than with exact arithmetic, and the result differs for 684 of them.
* The 25% limit comes from #46944 and is arbitrary, as is rounding the saturation to the nearest 50. So is the brightness limit of 128 between black and white, which only looks at the highest channel.

The Ansi8 and Ansi24 modes are not affected. `ColorTest::testDegradedTrueColorsToAnsi4` and the existing Ansi4 expectations of `AnsiColorModeTest` still pass unchanged. A new `testColorsConversionToAnsi4DependsOnSaturation()` covers the six pure hues, pastel colors, both sides of the 25% limit and of the brightness limit, black, greys and white.
