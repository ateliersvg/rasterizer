# atelier/rasterizer

Rasterize SVG documents to bitmaps through external rendering adapters
(`resvg`, `rsvg-convert`). A typed PHP abstraction over a rasterizer binary; it
does not render SVG in PHP itself.

## Install

```bash
composer require atelier/rasterizer
```

Requires PHP 8.3+, `symfony/process`, `psr/log`, and one rasterizer binary on
the host (`resvg` or `rsvg-convert`).

```bash
# macOS
brew install resvg

# Debian / Ubuntu
apt install librsvg2-bin
```

See [Installation](docs/installation.md) for other platforms, package-source
links, and binary licenses.

## Example

```php
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Rasterizer;

$rasterizer = Rasterizer::create();

$bitmap = $rasterizer->rasterize($svg, new BitmapOptions(
    width: 1200,
    height: 630,
    keepAspectRatio: true,
));

$bitmap->save('card.png');
```

`$svg` is a markup string, any `\Stringable` (such as an `Atelier\Svg\Svg`
document), or an `SvgInput` built from a file or stream. `keepAspectRatio` is
enabled by default, so giving both `width` and `height` fits the SVG inside that
box where the selected adapter supports it.

`Rasterizer::create()` picks the first available adapter in package order
(`resvg`, then `rsvg-convert`). For reproducible production output, you can
also select an adapter explicitly:

```php
$rasterizer = Rasterizer::resvg();
$rasterizer = Rasterizer::rsvgConvert();
```

## Documentation

- [Installation](docs/installation.md)
- [Usage](docs/usage.md)
- [Adapters](docs/adapters/overview.md)
- [Options](docs/options.md)
- [Testing](docs/testing.md)

## License

MIT
