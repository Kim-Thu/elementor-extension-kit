# Composition Patterns

Phase 3 patterns are source-level composition manifests stored under `patterns/`. They describe reusable structure, dependencies, reading order, responsive behavior and accessibility constraints without introducing a PHP widget for every layout.

## Why manifests instead of widget-per-layout classes

Elementor already owns layout through Container/Grid. EEK owns reusable behavior through its element modules. A Hero, FAQ section, Homepage or Dashboard is composition, not a new behavior engine. Pattern manifests keep those layers separate.

These manifests are **not claimed to be version-specific Elementor export JSON**. They are stable project source-of-truth contracts that can be implemented with native Elementor containers plus the declared EEK/native components. A future import/export adapter may consume them when a stable runtime requirement exists.

## Schema

Every pattern uses `schema: "eek.pattern/v1"` and includes:

- `id`: stable dotted identifier.
- `type`: `block`, `page`, or `application`.
- `title` and `purpose`.
- `dependencies.native`: native Elementor/WordPress/provider capabilities.
- `dependencies.eek`: EEK element IDs used by the composition.
- `regions`: ordered structural regions. Source order is the accessibility order.
- `responsive`: reflow constraints; CSS visual order must not contradict source/focus order.
- `accessibility`: composition-level requirements.
- `variants`: presentation presets only; variants do not create new PHP engines.

## Rules

1. Native Elementor Container/Grid owns layout.
2. Existing EEK elements own reusable behavior.
3. Patterns do not implement business logic, persistence, authentication, checkout, billing, form submission or provider APIs.
4. Page patterns compose block patterns where practical.
5. Application patterns compose application/data elements and native containers.
6. Heading hierarchy, link/button semantics and keyboard focus order must survive responsive reflow.
7. Product/blog patterns reuse WooCommerce/WordPress query and business logic rather than replacing it.
8. Runtime Elementor/editor visual smoke must be recorded separately; manifest review alone is not a runtime visual test.
