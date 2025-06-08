// Theme toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    const themeToggleText = document.getElementById('theme-toggle-text');

    // Change the icons inside the button based on previous settings
    if (localStorage.getItem('color-theme') === 'dark' || 
        (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        themeToggleLightIcon.classList.remove('hidden');
        document.documentElement.classList.add('dark');
        if (themeToggleText) themeToggleText.textContent = 'Light Mode';
    } else {
        themeToggleDarkIcon.classList.remove('hidden');
        document.documentElement.classList.remove('dark');
        if (themeToggleText) themeToggleText.textContent = 'Dark Mode';
    }

    // Toggle theme on button click
    themeToggleBtn.addEventListener('click', function() {
        // Toggle icons
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // If set via local storage previously
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                if (themeToggleText) themeToggleText.textContent = 'Light Mode';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                if (themeToggleText) themeToggleText.textContent = 'Dark Mode';
            }
        } else {
            // If not set via local storage previously
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
                if (themeToggleText) themeToggleText.textContent = 'Dark Mode';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
                if (themeToggleText) themeToggleText.textContent = 'Light Mode';
            }
        }
    });
});
