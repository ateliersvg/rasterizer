# Installation

## Package

```bash
composer require atelier/rasterizer
```

Requires PHP 8.3+. Runtime dependencies are `symfony/process` and `psr/log`.
`psr/log` is only an interface dependency; pass a concrete logger if you want
process execution logs. `atelier/svg` is optional: build documents with it if
you like, but any SVG markup string works.

## Rasterizer binaries

The package shells out to a rasterizer binary. Install at least one.
`Rasterizer::create()` checks adapters in this order:

| Class                    | Binary          |
| ------------------------ | --------------- |
| `ResvgRasterizer`        | `resvg`         |
| `RsvgConvertRasterizer`  | `rsvg-convert`  |

## Quick binary install

These commands are package-manager examples. Follow the linked package pages
for supported versions, platforms, and distro-specific naming.

macOS with Homebrew:

```bash
brew install resvg
# or
brew install librsvg
```

Sources: [Homebrew resvg formula](https://formulae.brew.sh/formula/resvg),
[Homebrew librsvg formula](https://formulae.brew.sh/formula/librsvg).

Debian / Ubuntu-family systems:

```bash
apt install librsvg2-bin
```

Sources: [Debian librsvg2-bin package](https://packages.debian.org/librsvg2-bin),
[Ubuntu librsvg2-bin package search](https://packages.ubuntu.com/search?keywords=librsvg2-bin).

Fedora:

```bash
dnf install librsvg2-tools
```

Source: [Fedora librsvg2-tools package](https://packages.fedoraproject.org/pkgs/librsvg2/librsvg2-tools/).

For CI and production, pin the chosen binary and version through your system
package manager, base image, or release artifact.

## Binary licenses

The package shells out to these tools; it does not redistribute them.

| Binary | Project | License |
| ------ | ------- | ------- |
| `resvg` | [linebender/resvg](https://github.com/linebender/resvg) | Dual licensed [Apache-2.0](https://github.com/linebender/resvg/blob/main/LICENSE-APACHE) or [MIT](https://github.com/linebender/resvg/blob/main/LICENSE-MIT). |
| `rsvg-convert` | [GNOME librsvg](https://gitlab.gnome.org/GNOME/librsvg) | [LGPL-2.1-or-later](https://gitlab.gnome.org/GNOME/librsvg/-/blob/main/COPYING.LIB). |

Distribution packages can carry extra metadata or patches; check your distro's
package page when license compliance matters.

### resvg

```bash
# macOS
brew install resvg

# cargo
cargo install resvg

# or download a release binary
# https://github.com/linebender/resvg/releases
```

### rsvg-convert

```bash
# macOS
brew install librsvg

# Debian / Ubuntu
apt install librsvg2-bin

# Fedora
dnf install librsvg2-tools
```

## Other operating systems and install methods

- `resvg` publishes binaries and source releases on
  [GitHub releases](https://github.com/linebender/resvg/releases).
- `resvg` can also be installed from Rust with `cargo install resvg`; see the
  [resvg project](https://github.com/linebender/resvg).
- `rsvg-convert` is distributed with GNOME librsvg; see the
  [librsvg documentation](https://gnome.pages.gitlab.gnome.org/librsvg/) and
  [project repository](https://gitlab.gnome.org/GNOME/librsvg).
- On Windows, `resvg` is the simpler option: download a Windows release or
  install it with Cargo, then make `resvg.exe` available in `PATH` or pass
  `binaryPath` explicitly.
- `rsvg-convert` on Windows usually comes from a Unix-like packaging
  environment such as MSYS2; see the
  [MSYS2 librsvg package](https://packages.msys2.org/base/mingw-w64-librsvg).

The PHP package has no OS-specific process code: it runs wherever the target
executable is reachable through `PATH` or given as `binaryPath`.

## Verifying a binary

A rasterizer resolves its binary through `PATH`. Confirm it is reachable and
print the installed version with:

```bash
resvg --version
rsvg-convert --version
```

`Rasterizer::create()` does not run `--version` itself: it only resolves the
path. No binary is executed until rasterization.

When a binary lives outside `PATH`, use the lower-level adapter directly and
pass an explicit path:

```php
use Atelier\Rasterizer\Adapter\ResvgRasterizer;

new ResvgRasterizer(binaryPath: '/opt/homebrew/bin/resvg');
```

If neither the configured path nor `PATH` yields the binary, the rasterizer
throws `BinaryNotFoundException`.
