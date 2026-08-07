# RsvgConvertRasterizer

Rasterizes through `rsvg-convert`, the command-line tool shipped with
[librsvg](https://gitlab.gnome.org/GNOME/librsvg). It renders through cairo and
pango and supports a wider range of filters than resvg. This adapter exposes its
PNG output.

## Class

`Atelier\Rasterizer\Adapter\RsvgConvertRasterizer`

## Upstream

- Site / project: <https://gitlab.gnome.org/GNOME/librsvg>
- Documentation: <https://gnome.pages.gitlab.gnome.org/librsvg/>
- License: [LGPL-2.1-or-later](https://gitlab.gnome.org/GNOME/librsvg/-/blob/main/COPYING.LIB)

## Binary

Requires the `rsvg-convert` binary (package `librsvg2-bin` on Debian/Ubuntu).
See [installation](../installation.md#rsvg-convert).
Check the installed binary with:

```bash
rsvg-convert --version
```

## Usage

```php
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

$bitmap = (new RsvgConvertRasterizer())->rasterize($svg, new BitmapOptions(width: 1200));
$bitmap->save('out.png');
```

Pass an explicit binary path or temporary directory when needed:

```php
new RsvgConvertRasterizer(
    binaryPath: '/opt/homebrew/bin/rsvg-convert',
    temporaryDirectory: '/var/run/atelier',
);
```

## Options

| Option       | Mapped to              | Notes                                       |
| ------------ | ---------------------- | ------------------------------------------- |
| `format`     | `--format`             | PNG only; other formats throw `UnsupportedFormatException`. |
| `width`      | `--width`              | Pixels.                                     |
| `height`     | `--height`             | Pixels.                                     |
| `keepAspectRatio` | `--keep-aspect-ratio` | Applied when both `width` and `height` are set and the option is true. |
| `scale`      | `--zoom`               | Applied only when not `1.0`.                |
| `background` | `--background-color`   | Any CSS color.                              |

Setting both `width` and `height` preserves the document ratio by default. Pass
`keepAspectRatio: false` for rsvg-convert's exact-size behavior. See
[options](../options.md) for the full reference.
