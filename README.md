<h1 align="center">Atelier Rasterizer</h1>

<p align="center">Rasterize SVG to PNG, JPEG or WebP from PHP, through a rendering binary you choose.</p>

<p align="center">
  <img alt="PHP Version" src="https://img.shields.io/badge/PHP-8.3%2B-a86cf0?labelColor=14141c">
  <img alt="Tests" src="https://img.shields.io/github/actions/workflow/status/ateliersvg/rasterizer/CI.yml?branch=main&label=Tests&labelColor=14141c&color=a86cf0">
  <img alt="PHPUnit" src="https://img.shields.io/badge/PHPUnit-13-a86cf0?labelColor=14141c">
  <img alt="PHPStan" src="https://img.shields.io/badge/PHPStan-max-a86cf0?labelColor=14141c">
  <img alt="Stable" src="https://img.shields.io/github/v/release/ateliersvg/rasterizer?label=Stable&labelColor=14141c&color=a86cf0">
  <img alt="License" src="https://img.shields.io/github/license/ateliersvg/rasterizer?label=License&labelColor=14141c&color=a86cf0">
</p>

A typed PHP abstraction over a rasterizer binary. It does not render SVG itself, and that is the
point: rendering SVG correctly is a browser-sized problem, so this package delegates it to
`resvg` or `rsvg-convert` and gives you a stable API on top.

```php
Rasterizer::create()->rasterize($svg, new BitmapOptions(width: 1200))->save('card.png');
```

Input is markup, any `\Stringable` such as an `atelier/svg` document, or an `SvgInput` built from
a file or a stream. Nothing here depends on `atelier/svg`. Backed by an extensive test suite and
PHPStan at its highest level.

**[Adapters](#adapters) · [Options](#options) · [Input](#input-and-output) ·
[Failure](#when-it-fails) · [Testing](#testing) · [Documentation](#documentation)**

## Installation

```bash
composer require atelier/rasterizer
```

Requires PHP 8.3 or later, plus one rasterizer binary on the host:

```bash
brew install resvg          # macOS
apt install librsvg2-bin    # Debian, Ubuntu
```

Other platforms, package sources, and the binaries' own licences are in
[Installation](docs/installation.md).

## Quick start

```php
use Atelier\Rasterizer\Bitmap\BitmapOptions;
use Atelier\Rasterizer\Rasterizer;

$bitmap = Rasterizer::create()->rasterize($svg, new BitmapOptions(
    width: 1200,
    height: 630,
    keepAspectRatio: true,
));

$bitmap->save('card.png');
```

`Rasterizer::create()` picks the first adapter available on the host. See
[Usage](docs/usage.md).

## Adapters

| Adapter | Binary | Selected by |
|---|---|---|
| [resvg](docs/adapters/resvg.md) | `resvg` | `Rasterizer::resvg()` |
| [rsvg-convert](docs/adapters/rsvg-convert.md) | `rsvg-convert` | `Rasterizer::rsvgConvert()` |

`create()` tries them in that order and returns the first one it finds, which is convenient in
development and unpredictable in production. Naming the adapter explicitly is what makes output
reproducible across machines, because two renderers do not agree on every edge case.

Adapters implement `RasterizerInterface`, so a third one is a class rather than a fork. See
[Adapters](docs/adapters/overview.md).

## Options

`BitmapOptions` is a single immutable value: `format`, `width`, `height`, `keepAspectRatio`,
`scale`, `background`, and `timeout`.

Give one dimension and the other follows the SVG's ratio. Give both, and `keepAspectRatio`
(on by default) fits the drawing inside that box rather than distorting it. See
[Options](docs/options.md).

## Input and output

Input is whatever you already have:

```php
use Atelier\Rasterizer\Svg\SvgInput;

SvgInput::fromString($markup);
SvgInput::fromFile('logo.svg');
SvgInput::fromStream($handle);
```

Output is a `BitmapResult` carrying `contents`, `format`, `width`, `height` and `mimeType`, so it
can go to a file, a response, or object storage without touching the disk. Formats are PNG, JPEG
and WebP, subject to what the selected adapter supports.

## When it fails

Rasterizing shells out, which means it can fail in ways pure PHP cannot: a missing binary, a
non-zero exit, a timeout, an unsupported format. Each is a typed exception rather than a
`false`, and `timeout` is an option because a runaway render is a production incident.

## Testing

Tests that shell out to a binary are slow and depend on the host. The package documents how to
substitute an adapter so a consumer's own suite does not need `resvg` installed. See
[Testing](docs/testing.md).

## Documentation

- [Installation](docs/installation.md): the binaries, per platform, and their licences.
- [Usage](docs/usage.md): input forms, options, and the result.
- [Adapters](docs/adapters/overview.md): what each one supports, and writing a third.
- [Options](docs/options.md): every field, its default, and its bounds.
- [Testing](docs/testing.md): rasterizing in a suite without the binary.

The full documentation is published at
[ateliersvg.com/rasterizer](https://ateliersvg.com/rasterizer/).

## Contributing

Contributions are welcome. Visit the
[project on GitHub](https://github.com/ateliersvg/rasterizer) to
[report a bug](https://github.com/ateliersvg/rasterizer/issues/new),
[suggest a feature](https://github.com/ateliersvg/rasterizer/issues/new), or
[open a pull request](https://github.com/ateliersvg/rasterizer/pulls).

Before submitting code, run:

```bash
composer qa   # PHP-CS-Fixer, PHPStan at level max, and PHPUnit
```

Changes to public behaviour need a test and a documentation update.

## Support

Bug reports, security disclosures, and contribution guidelines are collected at
[ateliersvg.com/support](https://ateliersvg.com/support/).

Atelier is maintained by Simon André. Sharing the package or
[starring it on GitHub](https://github.com/ateliersvg/rasterizer) helps more than you would
think.

## License

Atelier Rasterizer is released under the [MIT License](LICENSE).
