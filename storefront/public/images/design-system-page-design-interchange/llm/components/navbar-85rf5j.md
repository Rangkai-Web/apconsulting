# Navbar

Use this file with `navbar-85rf5j.context.json`. This Markdown is for quick reading. The JSON slice is the source for exact structured data.

## Files

- Agent instructions: `../../AGENTS.md`
- Structured slice: `navbar-85rf5j.context.json`
- Full render snapshot: `../../render/render-contract.json`
- Full semantic manifest: `../../design-system.manifest.json`

## Summary

- UID: `dsd:component:1:851`
- Type: COMPONENT
- Variants: 1
- Properties: 0
- Bound tokens: 0
- Bound styles: 5

## Default Configuration

```json
{}
```

## Properties

- No component properties recorded.

## Variants

### Default

- UID: `dsd:variant:1:851`
- Size: 1440 x 92
- Properties: Default
- Render nodes: 15


## Token And Style Bindings

- No token bindings recorded.

- Style: Mobile/Text L/Reguler (dsd:style:s:09e01b0603bd54949538767f15b8ffad404dfff9_2c)
- Style: Blue/blue-500 (dsd:style:s:1b9ca90244bdc99575a530c953271c29c900aa95_2c)
- Style: Mobile/Text L/Medium (dsd:style:s:298af3122f7fa217caee135939407cadebd1e1fa_2c)
- Style: Netral/01 (dsd:style:s:a129140a3205b468cc927c263b2f023035db3ecf_2c)
- Style: Netral/03 (dsd:style:s:e741d75fd4d509bf89e143775b407e9e060fda85_2c)

## Implementation Notes

- Preserve the variant names and property names above.
- Use `implementation.api` in the JSON slice as the public React props or HTML attribute contract.
- Use `implementation.variants` to map prop combinations to variant styles.
- Prefer token and style names when bindings exist.
- Translate recorded HORIZONTAL/VERTICAL auto-layout to flexbox and GRID to CSS grid.
- Use literal values from `navbar-85rf5j.context.json` only when no token/style binding exists.
- Render boxes in the component slice are normalized to each variant viewport. Original Figma canvas placement remains available as `canvasBox` when relevant.
- Open `render/render-contract.json` only when the compact slice omits a full-fidelity detail such as a vector network.
