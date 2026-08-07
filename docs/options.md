# Options

`BitmapOptions` is an immutable value object describing the requested output. It
is passed to `RasterizerInterface::rasterize()`. Every field has a default, so
`new BitmapOptions()` is valid.

```php
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

$options = new BitmapOptions(
    format: BitmapFormat::Png,
    width: 1200,
    height: 630,
    keepAspectRatio: true,
    scale: 1.0,
    background: '#ffffff',
    timeout: 30.0,
);
```

| Field             | Type            | Default              | Description                                                        |
| ----------------- | --------------- | -------------------- | ------------------------------------------------------------------ |
| `format`          | `BitmapFormat`  | `BitmapFormat::Png`  | Output format. An adapter throws `UnsupportedFormatException` for a format it cannot produce. |
| `width`           | `?int`          | `null`               | Output width in pixels. `null` uses the document's intrinsic width. |
| `height`          | `?int`          | `null`               | Output height in pixels. `null` uses the document's intrinsic height. |
| `keepAspectRatio` | `bool`          | `true`               | When both `width` and `height` are set, request a fit inside that box without distortion. |
| `scale`           | `float`         | `1.0`                | Zoom factor applied to the document. Useful for high-DPI output from the intrinsic or requested size. |
| `background`      | `?string`       | `null`               | Background color (any CSS color, e.g. `#fff`, `white`). `null` keeps transparency. |
| `timeout`         | `float`         | `30.0`               | Maximum seconds the adapter process may run before `RasterizationFailedException` is thrown. |

## BitmapFormat

```php
BitmapFormat::Png;   // 'png',  image/png
BitmapFormat::Jpeg;  // 'jpg',  image/jpeg
BitmapFormat::Webp;  // 'webp', image/webp
```

`mimeType()` and `extension()` return the MIME type and file extension. The
shipped adapters produce PNG; requesting `Jpeg` or `Webp` raises
`UnsupportedFormatException`.

## Sizing

- Set only `width` to scale to that width and derive height from the SVG ratio.
- Set only `height` to scale to that height and derive width from the SVG ratio.
- Set both `width` and `height` with `keepAspectRatio: true` to request a fit
  inside that box without distortion.
- Set both `width` and `height` with `keepAspectRatio: false` when you want the
  selected adapter's exact-size behavior.
- Set `scale` to render at a multiple of the intrinsic or requested size, useful
  for high-DPI output. Prefer dimensions for final output size; use `scale` when
  you want a zoom factor.

`rsvg-convert` maps `keepAspectRatio: true` to `--keep-aspect-ratio` and can
stretch when it is false. `resvg` preserves aspect ratio when dimensions are
provided and does not expose a separate stretch flag in its CLI.
