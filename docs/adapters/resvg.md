# ResvgRasterizer

Rasterizes through [resvg](https://github.com/linebender/resvg), a Rust SVG
renderer with a narrow, well-defined feature set. It does not implement
scripting, animation, or most filter effects. It emits PNG only.

## Class

`Atelier\Rasterizer\Adapter\ResvgRasterizer`

## Upstream

- Site / project: <https://github.com/linebender/resvg>
- Releases: <https://github.com/linebender/resvg/releases>
- License: dual [Apache-2.0](https://github.com/linebender/resvg/blob/main/LICENSE-APACHE) or [MIT](https://github.com/linebender/resvg/blob/main/LICENSE-MIT)

## Binary

Requires the `resvg` binary. See [installation](../installation.md#resvg).
Check the installed binary with:

```bash
resvg --version
```

## Usage

```php
use Atelier\Rasterizer\Adapter\ResvgRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

$bitmap = (new ResvgRasterizer())->rasterize($svg, new BitmapOptions(width: 1200));
$bitmap->save('out.png');
```

Pass an explicit binary path or temporary directory when needed:

```php
new ResvgRasterizer(
    binaryPath: '/opt/homebrew/bin/resvg',
    temporaryDirectory: '/var/run/atelier',
);
```

## Options

| Option       | Mapped to            | Notes                                       |
| ------------ | -------------------- | ------------------------------------------- |
| `width`      | `--width`            | Pixels.                                     |
| `height`     | `--height`           | Pixels.                                     |
| `keepAspectRatio` | native behavior | resvg preserves ratio when dimensions are set; it has no separate stretch flag. |
| `scale`      | `--zoom`             | Applied only when not `1.0`.                |
| `background` | `--background`       | Any CSS color.                              |
| `format`     | --                   | PNG only; other formats throw `UnsupportedFormatException`. |

Setting both `width` and `height` fits the document within that box while
preserving its aspect ratio. `keepAspectRatio: false` cannot force resvg to
stretch through the current CLI. See [options](../options.md) for the full
reference.
