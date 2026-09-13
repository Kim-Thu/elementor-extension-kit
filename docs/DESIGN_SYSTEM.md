# Design System Contract

## Architecture

Elementor owns the site-level design capabilities it already exposes. EEK extends only proven semantic gaps.

`Elementor Site Settings / Kit → Global Colors & Fonts / Theme Style / supported variables → EEK semantic-gap tokens → EEK elements → blocks/patterns → pages`

EEK must not create a second Global Colors or Global Fonts settings system.

## Ownership matrix

| Property | Source of truth | EEK rule |
| --- | --- | --- |
| Brand/global colors | Elementor Global Colors | Consume Elementor globals; local widget override may win when explicitly set. |
| Global typography | Elementor Global Fonts / Theme Style | Consume Elementor globals; do not duplicate font catalogs in EEK. |
| Native layout settings | Elementor Site Settings when applicable | Reuse the native setting before adding an EEK token. |
| Spacing semantics | EEK gap tokens | Use only for reusable section/component spacing not already owned by Elementor. |
| Radius semantics | EEK gap tokens | Provide reusable radius scale and component fallbacks. |
| Shadow/elevation | EEK gap tokens | Provide reusable elevation semantics. |
| Border semantics | EEK gap tokens | Provide reusable border width/style/color fallbacks. |
| Component metrics | EEK gap tokens | Add only after repeated real use across elements/compositions. |

## Precedence

1. Explicit Elementor control/local override.
2. Elementor global reference/value when the property is Elementor-owned.
3. EEK component token when the property is an EEK semantic gap.
4. EEK semantic token.
5. EEK primitive/default fallback.

A local element style must not hard-code a global visual constant when the same property belongs to this contract.

## CSS variable contract

EEK variables use the `--eek-*` prefix. Elementor-owned colors/fonts are referenced through Elementor's global variables where available. EEK semantic-gap variables must remain lightweight CSS custom properties; no runtime JavaScript resolver or separate settings engine is allowed solely for token resolution.

Recommended hierarchy:

```css
:root {
    --eek-space-1: 0.25rem;
    --eek-space-2: 0.5rem;
    --eek-space-3: 0.75rem;
    --eek-space-4: 1rem;
    --eek-space-6: 1.5rem;
    --eek-space-8: 2rem;
    --eek-radius-sm: 0.25rem;
    --eek-radius-md: 0.5rem;
    --eek-radius-lg: 1rem;
}

.eek-card {
    gap: var(--eek-card-gap, var(--eek-space-4));
    border-radius: var(--eek-card-radius, var(--eek-radius-md));
}
```

## Elementor global integration

When an EEK widget exposes an Elementor color or typography control, it should use Elementor's native `global` control contract when the value represents a site-level semantic such as primary, secondary, text or accent. Explicit local overrides remain valid and take precedence through Elementor itself.

Widgets that do not expose style controls should inherit site/global values through CSS rather than adding controls only to simulate a design system.

## Composition inheritance

Blocks, page patterns and application patterns are composition contracts. They do not own duplicated theme values. A composition may describe semantic intent, but its rendered elements inherit values from Elementor globals and EEK gap tokens.

## Anti-patterns

- EEK Global Colors/Fonts panel that competes with Elementor Site Settings.
- Copying brand colors or font families into each widget stylesheet.
- Creating a PHP/JS token service when CSS cascade is sufficient.
- Creating separate widgets for visual variants instead of presets/patterns.
- Promoting a one-off spacing/radius value to a shared token before real reuse exists.

## Handoff

- #187 applies Elementor Global Colors/Fonts integration and a shared Elementor-to-EEK CSS bridge.
- #188 adds/migrates semantic gap tokens for spacing, radius, shadow, border and reusable metrics.
- #189 documents migration/governance and future-element rules.
- #190 verifies the final contract end-to-end.
