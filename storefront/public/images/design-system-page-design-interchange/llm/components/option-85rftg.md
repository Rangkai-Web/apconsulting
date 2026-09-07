# Option

Use this file with `option-85rftg.context.json`. This Markdown is for quick reading. The JSON slice is the source for exact structured data.

## Files

- Agent instructions: `../../AGENTS.md`
- Structured slice: `option-85rftg.context.json`
- Full render snapshot: `../../render/render-contract.json`
- Full semantic manifest: `../../design-system.manifest.json`

## Summary

- UID: `dsd:component:1:788`
- Type: COMPONENT_SET
- Variants: 3
- Properties: 2
- Bound tokens: 0
- Bound styles: 4

## Default Configuration

```json
{
  "Property 1": "Default",
  "Text": "Option"
}
```

## Properties

- Text (TEXT), default: Option
- Property 1 (VARIANT), default: Default

## Variants

### Property 1=Default

- UID: `dsd:variant:1:787`
- Size: 86 x 40
- Properties: Property 1=Default
- Render nodes: 2

### Property 1=Hover

- UID: `dsd:variant:1:786`
- Size: 87 x 40
- Properties: Property 1=Hover
- Render nodes: 2

### Property 1=Pressed

- UID: `dsd:variant:1:785`
- Size: 87 x 40
- Properties: Property 1=Pressed
- Render nodes: 2


## Token And Style Bindings

- No token bindings recorded.

- Style: Mobile/Text L/Reguler (dsd:style:s:09e01b0603bd54949538767f15b8ffad404dfff9_2c)
- Style: Mobile/Text L/Medium (dsd:style:s:298af3122f7fa217caee135939407cadebd1e1fa_2c)
- Style: Blue/blue-600 (dsd:style:s:824f7782f4c1e781bb23a9849d220530271bac1a_2c)
- Style: Netral/03 (dsd:style:s:e741d75fd4d509bf89e143775b407e9e060fda85_2c)

## Implementation Notes

- Preserve the variant names and property names above.
- Use `implementation.api` in the JSON slice as the public React props or HTML attribute contract.
- Use `implementation.variants` to map prop combinations to variant styles.
- Prefer token and style names when bindings exist.
- Translate recorded HORIZONTAL/VERTICAL auto-layout to flexbox and GRID to CSS grid.
- Use literal values from `option-85rftg.context.json` only when no token/style binding exists.
- Render boxes in the component slice are normalized to each variant viewport. Original Figma canvas placement remains available as `canvasBox` when relevant.
- Open `render/render-contract.json` only when the compact slice omits a full-fidelity detail such as a vector network.
