# Design System Contract

## Architecture

Elementor owns the site-level design capabilities it already exposes. EEK extends only proven semantic gaps.

`Elementor Site Settings / Kit → Global Colors & Fonts / Theme Style / supported variables → EEK semantic-gap tokens → EEK elements → blocks/patterns → pages`

EEK must not create a second Global Colors or Global Fonts settings system.

## Ownership matrix

| Property | Source of truth | EEK rule |
| --- | --- | --- |
| Brand/global colors | Elementor Global Colors | Consume Elementor globals; explicit local widget override may win. |
| Global typography | Elementor Global Fonts / Theme Style | Consume Elementor globals; do not duplicate font catalogs. |
| Native layout settings | Elementor Site Settings when applicable | Reuse native settings before adding EEK semantics. |
| Spacing semantics | EEK gap tokens | Reusable section/component spacing not already owned by Elementor. |
| Radius semantics | EEK gap tokens | Reusable radius scale and component fallbacks. |
| Shadow/elevation | EEK gap tokens | Reusable elevation semantics. |
| Border/surface/state semantics | EEK gap tokens | Reusable neutral/state presentation with safe defaults. |
| Component metrics | EEK gap tokens | Add only after real reusable semantics exist. |

## Precedence

1. Explicit Elementor control/local override.
2. Elementor global reference/value when the property is Elementor-owned.
3. EEK component token when the property is an EEK semantic gap.
4. EEK semantic token.
5. EEK primitive/default fallback.

A module must not hard-code a global visual constant when the same property belongs to this contract.

## Runtime contract

The shared frontend stylesheet is `assets/frontend/design-system.css`, enqueued as `eek-design-system` before module styles. It bridges Elementor global CSS variables into EEK and defines lightweight `--eek-*` gap tokens. No PHP/JavaScript token resolver or separate design-settings database is required.

Examples:

```css
:root {
    --eek-color-primary: var(--e-global-color-primary, currentColor);
    --eek-font-primary: var(--e-global-typography-primary-font-family, inherit);
    --eek-space-4: 1rem;
    --eek-radius-md: 0.5rem;
    --eek-component-gap: var(--eek-space-4);
    --eek-card-radius: var(--eek-radius-md);
}

.eek-card {
    gap: var(--eek-card-gap, var(--eek-component-gap));
    border-radius: var(--eek-card-radius, var(--eek-radius-md));
}
```

## Elementor controls

When an EEK widget genuinely exposes a color or typography style control, use Elementor's native global-style control contract/reference for site-level semantics such as primary, secondary, text or accent. Do not add a style control merely so a widget can participate in globals; widgets without such controls inherit through the CSS bridge.

Explicit local Elementor overrides remain valid and take precedence through Elementor itself.

## EEK semantic-gap tokens

Use `--eek-*` for reusable gaps such as spacing, radius, shadow/elevation, border, neutral surface/state and component metrics. A component token should fall back to a semantic token, which falls back to a primitive/default. Functional geometry such as aspect ratio, a required touch target, or a behavior-specific dimension does not need to become a global design token unless reuse proves it should.

## Composition inheritance

Blocks, page patterns and application patterns are composition contracts. They do not own duplicated theme values. A composition may describe semantic intent or a preset name, but rendered values inherit from Elementor globals and EEK gap tokens through its native/EKK elements.

## Migration checklist

For an existing or new element:

- [ ] Identify whether each visual property is Elementor-owned, EEK-gap-owned, local/functional, or a legitimate explicit override.
- [ ] Use Elementor globals for color/typography when applicable.
- [ ] Use `--eek-*` semantics for reusable spacing/radius/shadow/border/surface/state values.
- [ ] Keep functional dimensions local unless two or more consumers establish reusable semantics.
- [ ] Preserve element-local CSS/JS ownership; do not create a shared service merely to resolve CSS variables.
- [ ] Keep variants in presets/templates rather than creating widget-per-style classes.
- [ ] Ensure block/page/application manifests do not embed brand colors, font families, radius scales or spacing scales.

## Developer decision formula

`Elementor native capability? → reuse it`

`Visual-only variation? → preset/pattern`

`Real reusable behavior? → element`

`Reusable design gap not owned by Elementor? → --eek-* semantic token`

`One-off functional value? → keep local`

## Anti-patterns

- EEK Global Colors/Fonts panel competing with Elementor Site Settings.
- Copying brand colors or font families into every widget stylesheet.
- Hard-coding reusable radius/spacing/shadow constants across modules.
- Creating a PHP/JS token service when CSS cascade is sufficient.
- Creating separate widgets for visual variants instead of presets/patterns.
- Promoting a one-off value to a shared token before real reuse exists.

## Phase 4 verification

#186 defines ownership. #187 supplies the Elementor bridge. #188 supplies semantic gap tokens and migrates element CSS. #189 locks these governance rules. #190 is the final technical gate.
