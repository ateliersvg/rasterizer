# atelier/rasterizer

`atelier/rasterizer` turns SVG documents into bitmaps (PNG) by driving
external rendering binaries. It is a typed PHP abstraction over a rasterizer
binary, not a PHP renderer.

```
string | \Stringable | SvgInput (from string, file, or stream)
   -> SvgInput                   validated SVG markup
   -> Rasterizer::create()       selects resvg / rsvg-convert
   -> BitmapResult               contents, format, mimeType, save()
```

Runtime dependencies are `symfony/process` and `psr/log`. The package does not
depend on `atelier/svg`: a bare string or any `\Stringable` is accepted as
markup, so an `Atelier\Svg\Svg` document passes directly.

## Documentation

- [Installation](installation.md) -- Composer and the rasterizer binaries.
- [Usage](usage.md) -- rasterize a document, save a file, pass options.
- [Adapters](adapters/overview.md) -- resvg and rsvg-convert, and how to choose.
  - [ResvgRasterizer](adapters/resvg.md)
  - [RsvgConvertRasterizer](adapters/rsvg-convert.md)
- [Options](options.md) -- `BitmapOptions` reference.
- [Testing](testing.md) -- unit and system test groups.

## Scope

The package rasterizes SVG to PNG through `resvg` and `rsvg-convert`. It does
not encode JPEG or WebP, produce PDF, or drive ImageMagick or a browser.
