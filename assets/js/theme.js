/**
 * TypeMaster Theme Manager
 * Handles immediate theme application to prevent flash of incorrect theme
 */
(function () {
    function getTheme() {
        return localStorage.getItem('typemaster_theme') || 'dark';
    }

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);

        // Update icon
        const icon = document.getElementById('themeIcon');
        if (icon) {
            icon.src = theme === 'dark' ? 'assets/icons/sun.png' : 'assets/icons/moon.png';
        }

        // Force repaint to ensure styles apply immediately
        document.documentElement.style.display = 'none';
        document.documentElement.offsetHeight; // triggers reflow
        document.documentElement.style.display = '';
    }

    // Apply immediately
    applyTheme(getTheme());

    // Expose toggle function globally
    window.toggleTheme = function () {
        const current = getTheme();
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem('typemaster_theme', next);
        applyTheme(next);

        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: next } }));

        return next;
    };
})();
