// Global admin dark mode helper
(function(){
    try {
    const body = document.body;
    // Prefer toggle in topbar; fallback to sidebar for compatibility
    let toggle = document.getElementById('darkModeToggle');
    if (!toggle) toggle = document.querySelector('.sidebar-bottom #darkModeToggle');

        function applyTheme() {
            const theme = localStorage.getItem('admin_theme');
            if (theme === 'dark') {
                body.classList.add('dark');
                body.classList.remove('light');
            } else {
                body.classList.add('light');
                body.classList.remove('dark');
            }
        }

        // apply immediately
        applyTheme();

        if (!toggle) return;

        // Update toggle label to reflect current theme
        function updateToggle() {
            const isDark = body.classList.contains('dark');
            toggle.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        }

        // Prevent double-binding if script is loaded multiple times
        if (toggle.dataset._adminDarkBound) { updateToggle(); return; }
        toggle.dataset._adminDarkBound = '1';

        toggle.addEventListener('click', function(){
            const isDark = body.classList.contains('dark');
            localStorage.setItem('admin_theme', isDark ? 'light' : 'dark');
            applyTheme();
            updateToggle();
        });
        updateToggle();
    } catch (e) {
        // silent fail in older browsers
        console && console.error && console.error('dark-mode init error', e);
    }
})();
