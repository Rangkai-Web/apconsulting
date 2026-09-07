# Frame 37

Use this file with `frame-37-e0efyx.context.json`. This Markdown is for quick reading. The JSON slice is the source for exact structured data.

## Files

- Agent instructions: `../../AGENTS.md`
- Structured slice: `frame-37-e0efyx.context.json`
- Full render snapshot: `../../render/render-contract.json`
- Full semantic manifest: `../../design-system.manifest.json`

## Summary

- UID: `dsd:component:1:84`
- Type: COMPONENT
- Variants: 1
- Properties: 3
- Bound tokens: 0
- Bound styles: 5

## Default Configuration

```json
{
  "Show Frame 36": true,
  "Text": "Parents Text",
  "Text 2": "Describe"
}
```

## Properties

- Text (TEXT), default: Parents Text
- Text 2 (TEXT), default: Describe
- Show Frame 36 (BOOLEAN), default: true

## Variants

### Default

- UID: `dsd:variant:1:84`
- Size: 275 x 42
- Properties: Default
- Render nodes: 8


## Token And Style Bindings

- No token bindings recorded.

- Style: Blue/blue-500 (dsd:style:s:1b9ca90244bdc99575a530c953271c29c900aa95_2c)
- Style: Netral/03 (dsd:style:s:1f64b2d379f2cce5b1f8f1a240063edc04a5750f_2c32:165)
- Style: Netral/01 (dsd:style:s:73d0742d46de65fc67da6394de3639a84bd73ffd_2c32:167)
- Style: Mobile/Text M/Medium (dsd:style:s:ba1c3cf51a7ef0dd625bd853bb95559ccdd13bf6_2c)
- Style: Mobile/Text S/Reguler (dsd:style:s:d8bfe1a2a6f352783cb999230fbfa6d2cf4ff09c_2c)

## Implementation Notes

- Preserve the variant names and property names above.
- Use `implementation.api` in the JSON slice as the public React props or HTML attribute contract.
- Use `implementation.variants` to map prop combinations to variant styles.
- Prefer token and style names when bindings exist.
- Translate recorded HORIZONTAL/VERTICAL auto-layout to flexbox and GRID to CSS grid.
- Use literal values from `frame-37-e0efyx.context.json` only when no token/style binding exists.
- Render boxes in the component slice are normalized to each variant viewport. Original Figma canvas placement remains available as `canvasBox` when relevant.
- Open `render/render-contract.json` only when the compact slice omits a full-fidelity detail such as a vector network.
