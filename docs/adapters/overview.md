# Adapters

An adapter is a `RasterizerInterface` implementation bound to one external
binary. Both shipped adapters extend `AbstractProcessRasterizer`, which owns the
shared workflow: validate the requested format, resolve the binary, write the
SVG to a temporary file, run the command, read the bitmap, and clean up.

| Adapter                  | Binary          | Formats | Page                                |
| ------------------------ | --------------- | ------- | ----------------------------------- |
| `ResvgRasterizer`        | `resvg`         | PNG     | [resvg](resvg.md)                   |
| `RsvgConvertRasterizer`  | `rsvg-convert`  | PNG     | [rsvg-convert](rsvg-convert.md)     |

All adapters live in the `Atelier\Rasterizer\Adapter` namespace. Each adapter
page documents its binary flags, upstream links, and caveats.

## Choosing an adapter

- Start with `Rasterizer::create()` for local tools and simple applications. It
  selects the first available adapter in package order: `resvg`, then
  `rsvg-convert`.
- Prefer `resvg` for predictable, self-contained rendering.
- Prefer `rsvg-convert` when a document relies on filters resvg does not
  implement.
- Pin the choice explicitly in CI and production rather than auto-detecting, so
  output stays reproducible.

## Adapter factories

`RasterizerFactory` accepts adapter factories and returns the first supported
adapter:

```php
use Atelier\Rasterizer\Adapter\ResvgRasterizerFactory;
use Atelier\Rasterizer\Adapter\RsvgConvertRasterizerFactory;
use Atelier\Rasterizer\RasterizerFactory;

$rasterizer = (new RasterizerFactory([
    new ResvgRasterizerFactory(),
    new RsvgConvertRasterizerFactory(),
]))->create();
```

Adapter factories expose `name()`, `supports()`, and `create()`. Support checks
are cached by `RasterizerFactory`; call `RasterizerFactory::resetSupportCache()`
if a long-running process changes its available binaries.

`supports()` resolves the binary path; it does not execute the binary. Run
`--version` yourself when you need an explicit diagnostic or install check.

## Process execution and logging

Adapters use `ProcessRunnerInterface` as their process boundary. The default
`ProcessRunner` shells out through Symfony Process and accepts an optional
PSR-3 logger. Use a custom runner when you need tracing, instrumentation,
sandboxing, or tests that should not spawn external binaries.

```php
use Atelier\Rasterizer\Adapter\ResvgRasterizer;

$rasterizer = new ResvgRasterizer(logger: $logger);
$rasterizer = new ResvgRasterizer(processRunner: $runner);
```

## Determinism and fonts

Text rendering depends on the fonts installed on the host. The same document can
produce different bitmaps on two machines with different fonts. For reproducible
text, control the fonts available to the adapter (for example, ship the fonts
with the deployment and configure the host's font directory).

Adapters are not interchangeable at the pixel level. resvg and rsvg-convert
differ in filter and font handling, so switching adapters can change output.

## Writing an adapter

Extend `AbstractProcessRasterizer` and implement three methods:

```php
use Atelier\Rasterizer\Adapter\AbstractProcessRasterizer;
use Atelier\Rasterizer\Bitmap\BitmapFormat;
use Atelier\Rasterizer\Bitmap\BitmapOptions;

final class MyRasterizer extends AbstractProcessRasterizer
{
    protected function binaryName(): string
    {
        return 'my-rasterizer';
    }

    /** @return list<BitmapFormat> */
    protected function supportedFormats(): array
    {
        return [BitmapFormat::Png];
    }

    /** @return list<string> */
    protected function buildCommand(string $binary, string $inputPath, string $outputPath, BitmapOptions $options): array
    {
        return [$binary, $inputPath, $outputPath];
    }
}
```

The base class validates the requested format against `supportedFormats()`
before doing any work, so an unsupported request never spawns a process.
