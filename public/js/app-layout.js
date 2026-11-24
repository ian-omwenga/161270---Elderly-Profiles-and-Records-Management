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
})();
