# Elementor Extension Kit

Reusable Elementor extension architecture focused on small modules, clear ownership, conditional assets, and easy cross-project reuse.

## Phase 1 status

Phase 1 defines the frozen core architecture. New architectural ideas do not get inserted into Phase 1; they become a later-phase change.

Integration flow:

```text
issue branch -> develop -> master after explicit approval
```

Completed issue branches are intentionally retained after merge for audit/debug history.

## Core principles

- PSR-4 namespace: `ElementorExtensionKit\`
- Public prefix/asset handles: `eek-`
- An element starts with one main class: `{Element}.php`.
- Do not add `Widget`, `Logic`, `Service`, `Renderer`, or helper layers unless real complexity/reuse proves they are needed.
- `templates/` exists only when multiple markup layouts or readability justify it.
- CSS/JS are optional and stay inside the element module when they are element-specific.
- Do not ship JavaScript when an element has no JavaScript behavior.
- `Shared/` is not a default folder. Extract shared runtime code only after at least two real consumers prove the same semantics and the reuse benefit is greater than the indirection/coupling cost.
- Registry registers assets; it does not globally enqueue element assets.
- Invalid optional modules/assets fail safely where possible.

## Current structure

```text
src/
├── Core/
│   ├── Autoloader.php
│   └── Plugin.php
├── Elementor/
│   └── ElementRegistry.php
└── Elements/
    └── Content/
        └── Card/
            ├── Card.php
            ├── element.json
            ├── templates/
            │   ├── cardDefault.php
            │   └── cardOverlay.php
            └── css/
                └── card.css
```

`templates/`, `css/`, and `js/` are optional per element. The current Card has no JavaScript because it has no frontend JavaScript behavior.

## Minimal element contract

Default module shape:

```text
src/Elements/{Domain}/{Element}/
├── {Element}.php
├── element.json
├── templates/    # optional
├── css/          # optional
└── js/           # optional
```

Minimal `element.json` fields:

- `id`
- `name`
- `class`
- `handle`

`style` and `script` are optional and must resolve inside the element module.

## Phase 1 documentation

- `docs/ARCHITECTURE.md` — module boundaries and architecture rules.
- `docs/ELEMENT_CONTRACT.md` — minimal manifest/element contract.
- `docs/DEVELOPMENT_WORKFLOW.md` — issue, branch, commit, verification and phase-freeze rules.
- `docs/REGISTRY.md` — discovery/registration lifecycle and asset behavior.
- `docs/SECURITY.md` — Phase 1 security baseline.
- `docs/SHARED.md` — evidence-based Shared extraction policy.
- `docs/NAMING.md` — namespace, class, handle and CSS naming conventions.

## Development rule

Every implementation change belongs to an issue with Goal, Actions, Acceptance Criteria, Verification, Expected Result and Done Checklist. Each issue uses its own branch and is merged into `develop` after verification. `master` is outside the automatic Phase 1 integration flow and is only updated after explicit approval.
