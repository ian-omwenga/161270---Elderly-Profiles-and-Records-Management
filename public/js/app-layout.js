(() => {
    const sidebar = document.getElementById('ecSidebar');
    const toggle = document.getElementById('ecMenuToggle');

    if (sidebar && toggle) {
        toggle.addEventListener('click', () => {
            const open = sidebar.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
        });

        document.addEventListener('click', (event) => {
            if (!sidebar.classList.contains('is-open')) return;
            if (sidebar.contains(event.target) || toggle.contains(event.target)) return;
            sidebar.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        });

        sidebar.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // Use a short fade when navigating between local pages; keep downloads and anchors untouched.
    document.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
            if (link.target === '_blank' || link.hasAttribute('download')) return;
            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            let destination;
            try { destination = new URL(href, window.location.href); } catch { return; }
            if (destination.origin !== window.location.origin || destination.pathname === window.location.pathname && destination.search === window.location.search) return;
            event.preventDefault();
            document.body.classList.add('ec-leaving');
            window.setTimeout(() => { window.location.href = destination.href; }, 130);
        });
    });
})();
