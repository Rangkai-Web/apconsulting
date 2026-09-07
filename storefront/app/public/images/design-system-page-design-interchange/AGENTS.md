# DSD Agent Instructions

You are working from a Design System Documentation export generated from Figma.

Use the files this way:

- Treat `render/render-contract.json` as the canonical structural and visual source.
- Read `llm/index.json` to choose the smallest useful component files.
- Read one `llm/components/*.md` file for focused component guidance.
- Read its matching `llm/components/*.context.json` file for structured data.
- Apply matching `*.semantic.json` and `a11y/*.a11y.json` overlays only when their status is not `__TODO__`.
- Use `design-system.manifest.json` for full source audits and semantic details outside the render contract.
- Use the separate DTCG, Directed Edges, or Google DESIGN.md export profiles only when a downstream tool specifically asks for those compatibility formats.

## Build Guidance

- Treat the render contract as canonical for structure and pixels.
- Treat semantic and accessibility files as overlays, not alternate trees.
- Prefer component context slices over full-system files when the task is about one component.
- Use `implementation.api` to define React props or HTML data attributes.
- Use `implementation.variants` to map prop combinations to states.
- Prefer recorded auto-layout for CSS flex/grid and use normalized render boxes for details that layout cannot reproduce.
- Use variant property maps as canonical identity; variant names are display-only.
- Prefer tokens and style bindings when they exist.
- Fall back to literal render values when a node has no token or style binding.
- Do not invent accessibility, usage, or content guidance that is not in the export.

## Current Export

- Scope: page (Design)
- Components: 5
- Tokens: 0
- Styles: 75
- Relationships: 293
