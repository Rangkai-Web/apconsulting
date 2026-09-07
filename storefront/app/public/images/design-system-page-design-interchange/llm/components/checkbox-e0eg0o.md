# Checkbox

Use this file with `checkbox-e0eg0o.context.json`. This Markdown is for quick reading. The JSON slice is the source for exact structured data.

## Files

- Agent instructions: `../../AGENTS.md`
- Structured slice: `checkbox-e0eg0o.context.json`
- Full render snapshot: `../../render/render-contract.json`
- Full semantic manifest: `../../design-system.manifest.json`

## Summary

- UID: `dsd:component:1:63`
- Type: COMPONENT_SET
- Variants: 5
- Properties: 1
- Bound tokens: 0
- Bound styles: 3

## Default Configuration

```json
{
  "Property 1": "Uncheck"
}
```

## Properties

- Property 1 (VARIANT), default: Uncheck

## Variants

### Property 1=Uncheck

- UID: `dsd:variant:1:64`
- Size: 24 x 24
- Properties: Property 1=Uncheck
- Render nodes: 2

### Property 1=Unchecklist

- UID: `dsd:variant:1:66`
- Size: 24 x 24
- Properties: Property 1=Unchecklist
- Render nodes: 2

### Property 1=Check

- UID: `dsd:variant:1:68`
- Size: 24 x 24
- Properties: Property 1=Check
- Render nodes: 2

### Property 1=Checklist

- UID: `dsd:variant:1:70`
- Size: 24 x 24
- Properties: Property 1=Checklist
- Render nodes: 2

### Property 1=Checklist2

- UID: `dsd:variant:1:72`
- Size: 24 x 24
- Properties: Property 1=Checklist2
- Render nodes: 3


## Token And Style Bindings

- No token bindings recorded.

- Style: Blue/blue-500 (dsd:style:s:1b9ca90244bdc99575a530c953271c29c900aa95_2c)
- Style: Blue/blue-100 (dsd:style:s:7a3afc951828fe83ebc277f2845c4e082c61f6b4_2c)
- Style: Netral/02 (dsd:style:s:984e34ce070ff28ecbcc1d6af075791340946a10_2c32:166)

## Implementation Notes

- Preserve the variant names and property names above.
- Use `implementation.api` in the JSON slice as the public React props or HTML attribute contract.
- Use `implementation.variants` to map prop combinations to variant styles.
- Prefer token and style names when bindings exist.
- Translate recorded HORIZONTAL/VERTICAL auto-layout to flexbox and GRID to CSS grid.
- Use literal values from `checkbox-e0eg0o.context.json` only when no token/style binding exists.
- Render boxes in the component slice are normalized to each variant viewport. Original Figma canvas placement remains available as `canvasBox` when relevant.
- Open `render/render-contract.json` only when the compact slice omits a full-fidelity detail such as a vector network.
