(() => {
  'use strict';
  const set = (root, visible) => {
    const tip = root?.querySelector('[role="tooltip"]');
    if (tip) tip.hidden = !visible;
  };
  document.addEventListener('pointerover', (event) => set(event.target.closest('[data-eek-tooltip]'), true));
  document.addEventListener('pointerout', (event) => {
    const root = event.target.closest('[data-eek-tooltip]');
    if (root && !root.contains(event.relatedTarget)) set(root, false);
  });
  document.addEventListener('focusin', (event) => set(event.target.closest('[data-eek-tooltip]'), true));
  document.addEventListener('focusout', (event) => set(event.target.closest('[data-eek-tooltip]'), false));
  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    document.querySelectorAll('[data-eek-tooltip] [role="tooltip"]').forEach((tip) => { tip.hidden = true; });
  });
})();
