# Testing

The default test command runs the full PHPUnit suite:

```bash
composer test
```

Most tests are pure unit tests and do not require rasterizer binaries:

```bash
composer test:unit
```

System tests exercise the real binaries when they are available in `PATH`.
They are marked with the `system` PHPUnit group and skip themselves when the
required binary is missing:

```bash
composer test:system
```

Current system coverage:

- `resvg` adapter rendering and sizing.
- `rsvg-convert` adapter rendering, sizing, and `keepAspectRatio`.
- binary resolution through the host `PATH`.

Unit coverage also checks command construction through a `TraceableRunner`
decorator and a fake PNG-writing runner, so adapter options can be asserted
without spawning `resvg` or `rsvg-convert`.

`composer qa` runs coding style, PHPStan, and the full PHPUnit suite.
