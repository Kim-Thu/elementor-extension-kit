(() => {
  'use strict';
  const openers = new WeakMap();
  document.addEventListener('click', (event) => {
    const open = event.target.closest('[data-eek-drawer-open]');
    if (open) {
      const dialog = open.closest('[data-eek-drawer]')?.querySelector('dialog');
      if (dialog && typeof dialog.showModal === 'function') {
        openers.set(dialog, open);
        dialog.showModal();
      }
      return;
    }
    const close = event.target.closest('[data-eek-drawer-close]');
    if (close) close.closest('dialog')?.close();
    if (event.target instanceof HTMLDialogElement && event.target.open) event.target.close();
  });
  document.addEventListener('close', (event) => {
    if (!(event.target instanceof HTMLDialogElement) || !event.target.classList.contains('eek-drawer__window')) return;
    openers.get(event.target)?.focus();
  }, true);
})();
