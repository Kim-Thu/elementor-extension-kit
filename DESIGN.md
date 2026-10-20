# EEK Admin Design System

Status: implemented for the **EEK Global Settings admin screen** (Issue #272). This is an admin visual system, **not** the frontend widget design contract.

## Design direction

Compact, task-oriented WordPress admin UI inspired by shadcn's quiet surfaces and typography. No React/shadcn dependency is installed. The brand accent is configurable **within this admin screen only**, Blue by default or Lotus (rose), without changing saved EEK settings or Elementor Site Settings.

## Token ownership

Canonical source: `assets/admin/global-settings.css`, inside `.eek-settings`. Do not duplicate token declarations or add one-off override layers at the bottom of that file.

| Semantic role | CSS variable | Blue | Lotus |
|---|---|---|---|
| Primary/action | `--eek-admin-accent` | `#1d4ed8` | `#ad1457` |
| Primary hover | `--eek-admin-accent-hover` | `#1e40af` | `#880e4f` |
| Selected surface | `--eek-admin-accent-soft` | `#eff6ff` | `#fdf2f8` |
| Focus | `--eek-admin-focus` | `#1d4ed8` | `#ad1457` |
| Canvas | `--eek-admin-bg` | `#f8fafc` | unchanged |
| Surface | `--eek-admin-surface` | `#ffffff` | unchanged |
| Muted surface | `--eek-admin-surface-muted` | `#f1f5f9` | unchanged |
| Foreground | `--eek-admin-text` | `#0f172a` | unchanged |
| Secondary text | `--eek-admin-text-muted` | `#475569` | unchanged |
| Border | `--eek-admin-border` | `#e2e8f0` | unchanged |
| Strong border | `--eek-admin-border-strong` | `#cbd5e1` | unchanged |
| Success | `--eek-admin-success` | `#166534` | unchanged |
| Warning | `--eek-admin-warning` | `#92400e` | unchanged |
| Danger | `--eek-admin-danger` | `#b91c1c` | unchanged |

Radius: `--eek-admin-radius-sm/md/lg` = 6/9/12px. Spacing: `--eek-admin-space-1..7` = 4/8/12/16/24/32/40px. Use system fonts (no added font download). Animations are not used for tab transitions. No decorative border-left accents.

### Interaction and states

- Sidebar active item: soft selected surface and accent foreground. Focus-visible: 2px outline + 3px offset, not border-left.
- Controls: full labels, min 39px height, focused accent ring. Primary actions use tokenized solid accent.
- Semantic badge variants (inherited/native, override, warning), feedback, unsaved-changes and AJAX busy states are maintained.
- Responsive: stacked navigation below 960px; single-column forms and lists below 720px.
- Theme buttons are `type=button` and have `aria-pressed`; JS uses the `data-eek-admin-theme` attribute on `.eek-settings`.
- LocalStorage key `eek-admin-accent` persists a **browser preference only**, with safe fallback to blue when storage is unavailable.

## Non-goals and compatibility

- Do not apply these admin colors to frontend EEK elements or override the site's design system.
- Elementor Site Settings/Kit still own global colors and typography; EEK Global Settings own complementary semantic spacing, radius, border, shadow, surface and motion. Widget instances may inherit or locally override per Issue #197 and #187.
- Keep existing WordPress routes, forms, nonces, persistence and AJAX section switching.
- Design-only changes do not constitute browser/runtime performance verification; site-owner visual review is still required.

## Component responsibilities

- `src/Core/Admin/GlobalSettingsPage.php`: page header, accent preference controls, section output.
- `assets/admin/global-settings.css`: sole owner of admin semantic tokens and responsive styles.
- `assets/admin/global-settings.js`: light browser preference switch alongside existing section navigation.
- Existing PHP panels retain the save/reset/data contracts and remain independent from theme choice.
