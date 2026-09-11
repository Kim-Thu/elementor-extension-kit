# Phase 1 Security Baseline

## Scope

Phase 1 keeps security controls local and explicit. It does not introduce a security service, validator framework, or middleware layer.

## Direct access

The plugin entry file exits when `ABSPATH` is not defined. Presentation templates must use the same direct-access guard.

## Autoloading

The custom PSR-4 fallback autoloader:

- accepts only classes inside `ElementorExtensionKit\`;
- accepts only valid namespace/class identifier segments;
- resolves the target with `realpath()`;
- requires only real PHP files whose resolved path remains inside `src/`.

Malformed or traversal-like class strings are ignored.

## Manifest and asset paths

`ElementRegistry` rejects optional asset paths that are:

- absolute;
- Windows drive absolute paths;
- null-byte containing;
- containing a `..` path segment;
- missing on disk;
- resolving outside the element module directory.

Invalid optional assets are skipped instead of causing a frontend fatal.

## Input and output

Elementor control values must be treated as untrusted display data.

For the Card reference element:

- title output uses `esc_html()`;
- description output uses `wp_kses_post()`;
- layout selection is restricted to an explicit allowlist before choosing a template.

Escape output as late as possible and according to the final HTML context.

## Authorization rule

Future state-changing admin/AJAX/REST features must use both authorization and request-intent checks where applicable:

- capability/permission checks for authorization;
- nonce checks for request intent in WordPress form/AJAX flows;
- REST endpoints require a `permission_callback`.

A nonce is not authorization.

## Database rule

Future direct SQL must use `$wpdb->prepare()` for dynamic values. Prefer WordPress APIs when they already provide the required operation.

## Fail-safe rule

Invalid manifests, classes, templates, or optional assets should be skipped safely when possible. Do not expose internal filesystem paths or turn a bad optional module into a site-wide fatal error.

## Phase 1 verification

Security verification is manual in Phase 1:

- PHP syntax checks for changed PHP files;
- path tests for valid and traversal-like inputs;
- output-escaping review on the reference element;
- registry/locality review.

CI/CD security automation is deferred.
