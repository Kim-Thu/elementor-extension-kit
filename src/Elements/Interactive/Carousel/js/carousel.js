(() => {
  'use strict';
  const state = new WeakMap();

  const getParts = (root) => ({
    viewport: root.querySelector('[data-eek-carousel-viewport]'),
    slides: [...root.querySelectorAll('[data-eek-slide]')],
    status: root.querySelector('[data-eek-carousel-status]')
  });

  const go = (root, delta) => {
    const parts = getParts(root);
    if (!parts.viewport || !parts.slides.length) return;
    const current = state.get(root)?.index ?? 0;
    const index = (current + delta + parts.slides.length) % parts.slides.length;
    state.set(root, { ...(state.get(root) || {}), index });
    parts.slides[index].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
    if (parts.status) parts.status.textContent = `Slide ${index + 1} of ${parts.slides.length}`;
  };

  const start = (root) => {
    if (root.dataset.eekReady === 'true') return;
    root.dataset.eekReady = 'true';
    state.set(root, { index: 0, timer: null });
    const delay = Number(root.dataset.autoplay || 0);
    if (delay > 0) {
      const resume = () => {
        const current = state.get(root) || { index: 0 };
        if (current.timer) clearInterval(current.timer);
        current.timer = setInterval(() => go(root, 1), delay);
        state.set(root, current);
      };
      const pause = () => {
        const current = state.get(root);
        if (current?.timer) clearInterval(current.timer);
        if (current) current.timer = null;
      };
      root.addEventListener('pointerenter', pause);
      root.addEventListener('pointerleave', resume);
      root.addEventListener('focusin', pause);
      root.addEventListener('focusout', (event) => { if (!root.contains(event.relatedTarget)) resume(); });
      resume();
    }
  };

  document.addEventListener('click', (event) => {
    const next = event.target.closest('[data-eek-carousel-next]');
    if (next) { go(next.closest('[data-eek-carousel]'), 1); return; }
    const prev = event.target.closest('[data-eek-carousel-prev]');
    if (prev) go(prev.closest('[data-eek-carousel]'), -1);
  });

  const init = () => document.querySelectorAll('[data-eek-carousel]').forEach(start);
  document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', init) : init();
  new MutationObserver(init).observe(document.documentElement, { childList: true, subtree: true });
})();
