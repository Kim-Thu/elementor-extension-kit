(() => {
  'use strict';
  const activate = (tab) => {
    const root = tab.closest('[data-eek-tabs]');
    if (!root) return;
    root.querySelectorAll('[role="tab"]').forEach((item) => {
      const selected = item === tab;
      item.setAttribute('aria-selected', selected ? 'true' : 'false');
      item.tabIndex = selected ? 0 : -1;
      const panel = document.getElementById(item.getAttribute('aria-controls'));
      if (panel) panel.hidden = !selected;
    });
  };
  document.addEventListener('click', (event) => {
    const tab = event.target.closest('[data-eek-tabs] [role="tab"]');
    if (tab) activate(tab);
  });
  document.addEventListener('keydown', (event) => {
    const tab = event.target.closest('[data-eek-tabs] [role="tab"]');
    if (!tab) return;
    const tabs = [...tab.closest('[role="tablist"]').querySelectorAll('[role="tab"]')];
    const index = tabs.indexOf(tab);
    let next = null;
    if (event.key === 'ArrowRight') next = tabs[(index + 1) % tabs.length];
    if (event.key === 'ArrowLeft') next = tabs[(index - 1 + tabs.length) % tabs.length];
    if (event.key === 'Home') next = tabs[0];
    if (event.key === 'End') next = tabs[tabs.length - 1];
    if (!next) return;
    event.preventDefault();
    activate(next);
    next.focus();
  });
})();
