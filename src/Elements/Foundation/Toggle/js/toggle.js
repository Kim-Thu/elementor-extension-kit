(() => {
  'use strict';

  const selector = '.eek-toggle__control[role="switch"]';

  document.addEventListener('click', (event) => {
    const control = event.target.closest(selector);
    if (!control) return;

    const next = control.getAttribute('aria-checked') !== 'true';
    control.setAttribute('aria-checked', next ? 'true' : 'false');
    control.dispatchEvent(new CustomEvent('eek:toggle-change', { bubbles: true, detail: { checked: next } }));
  });
})();
