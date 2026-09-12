(() => {
  'use strict';
  document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-eek-accordion] .eek-accordion__trigger');
    if (!trigger) return;
    const root = trigger.closest('[data-eek-accordion]');
    const panel = document.getElementById(trigger.getAttribute('aria-controls'));
    if (!root || !panel) return;
    const next = trigger.getAttribute('aria-expanded') !== 'true';
    if (next && root.dataset.single === 'true') {
      root.querySelectorAll('.eek-accordion__trigger[aria-expanded="true"]').forEach((other) => {
        if (other === trigger) return;
        other.setAttribute('aria-expanded', 'false');
        const otherPanel = document.getElementById(other.getAttribute('aria-controls'));
        if (otherPanel) otherPanel.hidden = true;
      });
    }
    trigger.setAttribute('aria-expanded', next ? 'true' : 'false');
    panel.hidden = !next;
  });
})();
