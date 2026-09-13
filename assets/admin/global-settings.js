(() => {
    const input = document.querySelector('[data-eek-template-search]');
    const rows = Array.from(document.querySelectorAll('[data-eek-template-row]'));

    if (!input || rows.length === 0) {
        return;
    }

    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        rows.forEach((row) => {
            const haystack = (row.getAttribute('data-search') || '').toLowerCase();
            row.hidden = query !== '' && !haystack.includes(query);
        });
    });
})();
