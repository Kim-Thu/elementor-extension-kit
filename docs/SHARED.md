# Shared Extraction Policy

## Default rule

Do not create or use `src/Shared/` by default.

An element should keep its own code inside its module until reuse is proven by real consumers.

## Extraction gate

Move code to `Shared/` only when all conditions are true:

1. At least two real consumers already use the same behavior/data contract.
2. The consumers share the same semantics, not only similar names or markup.
3. Extraction removes meaningful duplication.
4. The shared dependency does not create measurable runtime lookup/load work that outweighs the reuse benefit.
5. The shared dependency does not make elements depend on each other.
6. Copying/removing an element still makes required shared dependencies obvious.
7. The indirection cost is lower than keeping the duplicated code local.

If any condition is unclear, keep the code local.

## Good Shared candidates

Examples only after the gate is met:

- design tokens used by multiple modules;
- a focus-visible/accessibility helper used by multiple interactive elements;
- a common link-attribute formatter used by multiple elements with the same link semantics;
- a motion utility used consistently by multiple modules.

## Bad Shared candidates

Do not extract merely because code looks similar:

- Card and Pricing CSS combined because both use a box/card shape;
- a helper used only by Card;
- a template partial used by one element;
- an attribute helper created before a second consumer exists;
- one generic service that hides unrelated element-specific decisions.

## Dependency rule

`Shared/` may support elements. An element must not become a shared dependency of another element.

Allowed:

```text
Element A ─┐
           ├─> Shared helper
Element B ─┘
```

Not allowed:

```text
Element B -> Element A
```

## Asset rule

Shared CSS/JS is allowed only when the asset remains meaningful if any one consumer is removed. Element-specific styling/behavior stays in the element module.

## Phase 1 state

Phase 1 intentionally does not create `src/Shared/` because the current reference implementation does not yet have two proven consumers for a shared runtime abstraction.

This is a deliberate result, not missing architecture.
