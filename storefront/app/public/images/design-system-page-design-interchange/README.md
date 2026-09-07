# Design Design System Export

Start here. This bundle contains one canonical structural/render source plus focused semantic and accessibility overlays.

## Source Of Truth

Use files in this order:

1. `render/render-contract.json` for canonical component structure, geometry, visual data, modes, and bindings.
2. `llm/components/*.semantic.json` and `a11y/*.a11y.json` for human-authored behavior and accessibility overlays. Files marked `__TODO__` are stubs, not implementation guidance.
3. `render/overrides/component-instance-overrides.json` for nested instance state and property propagation.
4. `design-system.manifest.json` for full Figma source audits and semantic details outside the render contract.
5. `llm/components/*.context.json` and `*.md` as derived, focused views only.

Variant `properties` are canonical identity. Variant `name` is display text and must not be parsed.

## Capabilities

```json
{
  "a11yManifest": true,
  "fonts": false,
  "imageFillBinaries": true,
  "instanceOverrides": true,
  "interactionStates": true,
  "perModeRenders": false,
  "renderedImages": true,
  "semanticManifest": true
}
```

## What Is Inside

- `AGENTS.md`: primary agent instructions for using this export
- `render/render-contract.json`: canonical HTML/SVG structural and visual data
- `design-system.manifest.json`: full Figma semantic and source audit data
- `llm/index.json`: small map of the export and component slice files
- `llm/components/*.md`: readable per-component handoff guides
- `llm/components/*.context.json`: strict render-contract subsets plus implementation metadata
- `llm/components/*.semantic.json`: semantic element and behavior overlays
- `a11y/*.a11y.json`: accessibility overlays
- `render/images/manifest.json`: Figma-rendered visual references and bounds
- `render/overrides/component-instance-overrides.json`: nested instance configuration by parent variant
- `render/preview.html`: simple local preview for the render contract
- `docs/design-system.md`: human-readable documentation

## Compatibility Exports

This ZIP is native DSD only. Use the separate export profiles when you need DTCG tokens, Directed Edges specs, or Google DESIGN.md compatibility files.

## Important Limits

Image-fill binaries and rendered references are embedded when their capability flag is true. Figma does not expose font binaries; `fonts` is false unless licensed WOFF2 files were explicitly supplied to the exporter.
