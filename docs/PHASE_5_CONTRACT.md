# Phase 5 Global Configuration Contract

## Product rule

Configure once globally, inherit everywhere, and use a local override only for a deliberate exception.

`Elementor Site Settings / Kit → EEK Global Settings → Element Global Defaults → Element Template Default → Instance Override → Site`

## Ownership

Elementor remains the source of truth for Global Colors, Global Typography, Theme Style and native Site Settings. EEK must not create a competing color or font store.

EEK owns only stable gaps and extension concerns that Elementor does not already own: semantic spacing, radius, border, surface, elevation, motion, approved element presentation defaults, element template assignments and optional site-region assignments.

## Precedence

### Semantic design

1. Explicit local Elementor control where the element exposes one.
2. Elementor global style / variable.
3. EEK semantic gap token.
4. Component fallback.
5. Browser/theme inheritance.

### Element-global default

`local override → EEK element global default → element manifest default`

Only controls declared in `global_defaults` are eligible. Content, URLs, query criteria and repeater data remain local.

Example:

```json
{
  "global_defaults": {
    "orientation": {
      "label": "Orientation",
      "type": "select",
      "default": "vertical",
      "options": {
        "vertical": "Vertical",
        "horizontal": "Horizontal"
      }
    }
  }
}
```

An instance defaults to `Inherit`. Only when the user selects `Override locally` does the instance value win.

## Template ownership

A visual variant belongs to one functional element. Do not add a new widget class merely because presentation changes.

Example:

```json
{
  "templates": [
    {"id": "default", "name": "Default", "file": "templates/cardDefault.php"},
    {"id": "overlay", "name": "Overlay", "file": "templates/cardOverlay.php"}
  ]
}
```

Template IDs use lowercase kebab-case and are unique within the owning element. Template files must stay inside the element module.

Resolution is:

`instance template override → global template assignment → built-in/native rendering`

If an assigned template disappears, is renamed or becomes incompatible, rendering falls back to the element's built-in/native presentation. A stale global assignment is reported in diagnostics and can be reset to inheritance.

## Header and Footer

Header/Footer settings reference compatible saved Elementor region templates. `Default / Elementor native` is always the first and safest state.

A valid EEK assignment is applied through Elementor's native Theme Location template-ID resolution. EEK does not build a second Theme Builder and does not render an extra header/footer beside the theme.

If the active theme does not support the relevant Elementor Theme Location, EEK does not invent a replacement mechanism.

## Zero-config invariant

Every element should produce a useful baseline immediately after insertion. It should inherit the site design system without requiring manual color, typography, radius, shadow or spacing setup.

Placeholder content is allowed when it prevents a newly inserted element from appearing broken or empty. Placeholder content remains local and is not a global setting.

## Anti-patterns

Do not:

- create `TeamGridWidget`, `TeamCarouselWidget`, `TeamOverlayWidget` for visual variants of the same Team behavior;
- copy resolved global values into every element instance;
- add an EEK Global Colors or Global Fonts store that competes with Elementor;
- hard-code site-level radius, shadow, color or spacing inside a template when a semantic source exists;
- turn page-specific content or query configuration into a global default;
- make blocks/pages own a second styling system;
- require users to edit every page after a global design change.

## New element checklist

Before merging a new element:

- [ ] One functional responsibility, not a visual-variant widget.
- [ ] Meaningful zero-config baseline.
- [ ] Colors and typography inherit Elementor/global semantics.
- [ ] Shared shape/spacing/elevation/motion use EEK semantic tokens.
- [ ] Only stable presentation controls are declared in `global_defaults`.
- [ ] Eligible local presentation controls default to `Inherit`.
- [ ] Content/query controls remain local.
- [ ] Visual variants are module-local templates where appropriate.
- [ ] Missing template/reference cases fall back safely.

## New template checklist

Before merging a new template:

- [ ] It belongs to an existing functional element owner.
- [ ] Its ID is stable lowercase kebab-case.
- [ ] Its file is module-local and registry-valid.
- [ ] It reuses the element behavior/data contract.
- [ ] It consumes semantic design sources instead of creating new global styling.
- [ ] It does not require a new widget PHP class solely for appearance.

## Site maintenance

Theme and active Elementor Kit changes must not rewrite or delete valid EEK semantic settings. Elementor-owned sources are re-resolved live. Template and region references are revalidated and fall back safely when stale.
