# Zero-Config Element Audit

Phase 5 invariant: every EEK element should have a coherent baseline on insertion, inherit the site design system, and need local edits only for content or intentional exceptions.

## Rules
- Elementor owns global color and typography.
- EEK semantic variables own shared spacing, radius, border, surface, elevation and motion gaps.
- Site-wide presentation controls are declared through `global_defaults` in `element.json`.
- Eligible instance presentation controls default to `Inherit`.
- Content, URLs, query criteria and repeaters remain local.
- Elements keep sensible placeholder content when an empty value would make the inserted element visually empty.

## Catalog review
Application, Collection, Content, Foundation and Interactive element families were reviewed against this contract. Existing module CSS consumes the shared semantic token layer rather than requiring per-instance color, typography, radius or shadow setup.

Card (`layout`) and App Navigation (`orientation`) are representative global-default consumers and exercise the manifest allowlist plus instance inheritance pipeline.

## Finding fixed
App Navigation previously started with an empty repeater and rendered no navigation on insertion. It now includes a small neutral sample so the element has a meaningful zero-config baseline. Those entries remain ordinary local content.

## Result
No catalog-wide requirement remains to manually configure color, typography, radius or shadow before an element can follow the site system. Future presentation variants must extend the same element/template contract instead of becoming new widgets solely for appearance.
