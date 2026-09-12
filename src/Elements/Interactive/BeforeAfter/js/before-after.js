(() => {
  'use strict';
  document.addEventListener('input', (event) => {
    const range = event.target.closest('[data-eek-before-after] .eek-before-after__range');
    if (!range) return;
    const root = range.closest('[data-eek-before-after]');
    root?.style.setProperty('--eek-position', `${range.value}%`);
  });
})();
