(() => {
    'use strict';

    const root = document.querySelector('.eek-settings');
    const content = root?.querySelector('.eek-settings__content');
    const nav = root?.querySelector('.eek-settings__nav');
    const config = window.eekSettingsNavigation;

    if (!root || !content || !nav || !config?.endpoint || !config?.nonce) {
        return;
    }

    // Admin palette is a browser preference, never an Elementor/frontend design setting.
    const themeButtons = root.querySelectorAll('[data-eek-admin-accent]');
    const setTheme = (theme) => {
        const value = theme === 'lotus' ? 'lotus' : 'blue';
        root.setAttribute('data-eek-admin-theme', value);
        themeButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(button.dataset.eekAdminAccent === value));
        });
    };
    let savedTheme = 'blue';
    try {
        savedTheme = window.localStorage.getItem('eek-admin-accent') || 'blue';
    } catch (error) {
        // Private browsing may disable storage; the default blue theme remains usable.
    }
    setTheme(savedTheme);
    themeButtons.forEach((button) => button.addEventListener('click', () => {
        const selected = button.dataset.eekAdminAccent;
        setTheme(selected);
        try {
            window.localStorage.setItem('eek-admin-accent', selected);
        } catch (error) {
            // Choosing a color works even if persistence is unavailable.
        }
    }));

    let pending = null;
    let dirty = false;
    let currentSection = new URL(window.location.href).searchParams.get('section') || 'overview';

    const sectionFromUrl = (url) => {
        const target = new URL(url, window.location.href);
        if (target.origin !== window.location.origin ||
            target.pathname !== window.location.pathname ||
            target.searchParams.get('page') !== 'eek-global-settings') {
            return null;
        }
        return target.searchParams.get('section') || 'overview';
    };

    const templateFilter = (event) => {
        if (!event.target.matches('[data-eek-template-search]')) {
            return;
        }
        const query = event.target.value.trim().toLowerCase();
        content.querySelectorAll('[data-eek-template-row]').forEach((row) => {
            row.hidden = query !== '' && !(row.dataset.search || '').toLowerCase().includes(query);
        });
    };

    root.addEventListener('input', (event) => {
        templateFilter(event);
        if (event.target.closest('.eek-settings__content form')) {
            dirty = true;
        }
    });
    root.addEventListener('change', (event) => {
        if (event.target.closest('.eek-settings__content form')) {
            dirty = true;
        }
    });

    const setActiveSection = (section) => {
        nav.querySelectorAll('a').forEach((link) => {
            if (sectionFromUrl(link.href) === section) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    };

    const loadSection = async (section, url, updateHistory, restoreFocus) => {
        if (dirty && !window.confirm('You have unsaved settings. Leave this section without saving?')) {
            return false;
        }
        if (pending) {
            pending.abort();
        }
        const controller = new AbortController();
        pending = controller;
        // Immediate feedback without clearing content or shifting the page.
        setActiveSection(section);
        content.setAttribute('aria-busy', 'true');

        try {
            const data = new URLSearchParams({
                action: 'eek_settings_section',
                section,
                nonce: config.nonce
            });
            const response = await fetch(config.endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                body: data,
                signal: controller.signal,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' }
            });
            if (!response.ok) {
                throw new Error('Settings request failed');
            }
            const result = await response.json();
            if (!result?.success || typeof result.data?.html !== 'string') {
                throw new Error('Invalid settings response');
            }
            if (controller.signal.aborted) {
                return false;
            }

            content.innerHTML = result.data.html;
            currentSection = section;
            dirty = false;
            setActiveSection(section);
            if (updateHistory) {
                window.history.pushState({ eekSection: section }, '', url);
            }
            if (restoreFocus) {
                const heading = content.querySelector('h2');
                if (heading) {
                    heading.setAttribute('tabindex', '-1');
                    heading.focus({ preventScroll: true });
                }
            }
            return true;
        } catch (error) {
            if (error.name === 'AbortError') {
                return false;
            }
            // Preserve a functional settings page when AJAX fails.
            setActiveSection(currentSection);
            window.location.assign(url);
            return false;
        } finally {
            if (pending === controller) {
                content.removeAttribute('aria-busy');
                pending = null;
            }
        }
    };

    nav.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.button !== 0 || event.metaKey || event.ctrlKey ||
            event.shiftKey || event.altKey || link.target && link.target !== '_self') {
            return;
        }
        const section = sectionFromUrl(link.href);
        if (!section) {
            return;
        }
        event.preventDefault();
        if (section === currentSection) {
            return;
        }
        loadSection(section, link.href, true, true);
    });

    window.addEventListener('popstate', () => {
        const url = window.location.href;
        const section = sectionFromUrl(url);
        if (section && section !== currentSection) {
            loadSection(section, url, false, false);
        }
    });
})();
