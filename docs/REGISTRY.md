# Element Registry Lifecycle

## Purpose

`ElementRegistry` keeps element discovery and registration small, fail-safe, and local to each element module.

## Runtime flow

1. `Plugin::boot()` hooks `plugins_loaded`.
2. `Plugin::init()` exits early when Elementor is not loaded.
3. `elementor/widgets/register` registers valid widget classes.
4. `wp_enqueue_scripts` registers element CSS/JS handles only.
5. Elementor enqueues a registered handle only when the widget declares it through `get_style_depends()` / `get_script_depends()`.

The registry never globally enqueues an element asset.

## Discovery

Element manifests are discovered from:

```text
src/Elements/*/*/element.json
```

The manifest path list is cached in-process for the request. Decoded manifest data is also cached by manifest path, so widget registration and asset registration do not repeatedly read the same JSON file.

## Fail-safe behavior

An element is skipped instead of causing a frontend fatal when:

- required manifest fields are missing/invalid;
- the configured class does not exist;
- the class is outside `ElementorExtensionKit\Elements\`;
- the class is not an Elementor `Widget_Base` subclass;
- an optional asset is missing or its path is unsafe;
- an asset path is absolute or attempts `../` traversal;
- an asset handle duplicates an earlier manifest in the same request.

Invalid optional assets are ignored. They do not prevent a valid widget class from being registered.

## Performance rules

- No eager loading of element PHP files beyond normal PSR-4/class autoloading.
- No template scan.
- No `Shared/` scan.
- No global CSS/JS bundle for element-specific assets.
- No global enqueue from the registry.
- Do not add a separate validator/service/repository layer unless future complexity proves it necessary.

## Element responsibility

An element owns its own dependency declaration. Example:

```php
public function get_style_depends(): array
{
    return ['eek-card'];
}
```

If the element has no script behavior, omit `script` from `element.json` and do not declare a script dependency.

## Phase 1 verification

Phase 1 uses manual review and PHP syntax checks. Automated CI/validator infrastructure is intentionally deferred.
