(() => {
  'use strict';
  const openers = new WeakMap();
  document.addEventListener('click', (event) => {
    const open = event.target.closest('[data-eek-dialog-open]');
    if (open) {
      const root = open.closest('[data-eek-dialog]');
      const dialog = root?.querySelector('dialog');
      if (dialog && typeof dialog.showModal === 'function') {
        openers.set(dialog, open);
        dialog.showModal();
      }
      return;
    }
    const close = event.target.closest('[data-eek-dialog-close]');
    if (close) close.closest('dialog')?.close();
    if (event.target instanceof HTMLDialogElement && event.target.open) event.target.close();
  });
  document.addEventListener('close', (event) => {
    if (!(event.target instanceof HTMLDialogElement)) return;
    const opener = openers.get(event.target);
    if (opener) opener.focus();
  }, true);
})();
