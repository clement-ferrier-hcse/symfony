import json, sys
from PIL import Image, ImageDraw, ImageFont

data = json.load(open(sys.argv[1]))
out_dir = sys.argv[2]
PALETTE = ["#000000", "#cd0000", "#00cd00", "#cdcd00", "#0000ee", "#cd00cd", "#00cdcd", "#e5e5e5"]
NAMES = ["black", "red", "green", "yellow", "blue", "magenta", "cyan", "white"]
BG, FG, MUTED, LINE = "#f6f7f5", "#1d211f", "#5d655f", "#d5d9d3"
SS = 4          # supersampling factor; final PNG is 2x the logical size
OUT = 2
SANS = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"
SANS_B = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
MONO = "/usr/share/fonts/truetype/dejavu/DejaVuSansMono.ttf"

CELL, GAP, AXIS, PAD, COLS, COL_GAP, ROW_GAP = 22, 2, 18, 24, 2, 36, 34
n = len(data["rg"])
GRID = n * CELL + (n - 1) * GAP

def font(path, size):
    return ImageFont.truetype(path, size * SS)

def hexrgb(h):
    return tuple(int(h[i:i + 2], 16) for i in (1, 3, 5))

def render(variant, title, subtitle, note, path):
    rows = (len(data["grids"]) + COLS - 1) // COLS
    header = 112
    width = PAD * 2 + COLS * (AXIS + GRID) + (COLS - 1) * COL_GAP
    block = 22 + GRID + 22
    height = header + rows * block + (rows - 1) * ROW_GAP + 56
    img = Image.new("RGB", (width * SS, height * SS), BG)
    d = ImageDraw.Draw(img)
    s = lambda v: int(round(v * SS))

    d.text((s(PAD), s(22)), title, font=font(SANS_B, 20), fill=FG)
    d.text((s(PAD), s(52)), subtitle, font=font(SANS, 12), fill=MUTED)
    # palette legend
    x = PAD
    for i, (c, name) in enumerate(zip(PALETTE, NAMES)):
        d.rectangle([s(x), s(78), s(x + 12), s(90)], fill=c, outline=LINE, width=SS)
        label = f"{i} {name}"
        d.text((s(x + 17), s(77)), label, font=font(MONO, 11), fill=FG)
        x += 17 + d.textlength(label, font=font(MONO, 11)) / SS + 14

    for gi, grid in enumerate(data["grids"]):
        col, row = gi % COLS, gi // COLS
        gx = PAD + col * (AXIS + GRID + COL_GAP) + AXIS
        gy = header + row * (block + ROW_GAP)
        d.text((s(gx), s(gy)), f"blue #{data['b'][gi]:02x}", font=font(MONO, 12), fill=FG)
        top = gy + 22
        for ri, cells in enumerate(grid):
            for ci, pair in enumerate(cells):
                x0, y0 = gx + ci * (CELL + GAP), top + ri * (CELL + GAP)
                x1, y1 = x0 + CELL, y0 + CELL
                target = (data["rg"][ri], data["rg"][ci], data["b"][gi])
                result = hexrgb(PALETTE[pair[variant]])
                d.polygon([(s(x0), s(y0)), (s(x1), s(y0)), (s(x0), s(y1))], fill=target)
                d.polygon([(s(x1), s(y0)), (s(x1), s(y1)), (s(x0), s(y1))], fill=result)
        # axes
        d.text((s(gx + GRID / 2), s(top + GRID + 4)), "green 00 → ff", font=font(MONO, 10), fill=MUTED, anchor="mt")
        lab = Image.new("RGBA", (s(GRID), s(14)), (0, 0, 0, 0))
        ImageDraw.Draw(lab).text((s(GRID / 2), 0), "red 00 → ff", font=font(MONO, 10), fill=MUTED, anchor="mt")
        lab = lab.rotate(90, expand=True)
        img.paste(lab, (s(gx - AXIS), s(top)), lab)

    d.text((s(PAD), s(height - 40)), note, font=font(SANS, 12), fill=MUTED)
    img = img.resize((width * OUT, height * OUT), Image.LANCZOS)
    img.save(path, optimize=True)
    print(path, img.size)

changed = sum(p[0] != p[1] for g in data["grids"] for r in g for p in r)
total = sum(len(r) for g in data["grids"] for r in g)
sub = "Each cell: requested color in the top-left triangle, Ansi4 result in the bottom-right one (xterm palette)."
render(0, "Before: each RGB channel rounded to 0 or 1", sub,
       f"{total:,} colors: 16 levels of red and green, 8 levels of blue.", f"{out_dir}/ansi4-before.png")
render(1, "After: closest hue, greys to black or white", sub,
       f"{total:,} colors: 16 levels of red and green, 8 levels of blue. {changed:,} of them change.", f"{out_dir}/ansi4-after.png")
