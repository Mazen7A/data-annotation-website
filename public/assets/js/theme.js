/**
 * Saudi Culture Platform - Theme System
 * Handles dark/light mode and color scheme switching
 */

(function () {
    'use strict';

    const ThemeManager = {
        // Initialize theme system
        init() {
            this.loadTheme();
            this.setupEventListeners();
            this.initScrollAnimations();
        },

        // Load theme from localStorage or system preference
        loadTheme() {
            const savedTheme = localStorage.getItem('theme');
            const savedColorScheme = localStorage.getItem('colorScheme');

            if (savedTheme) {
                this.setTheme(savedTheme);
            } else {
                // Check system preference
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                this.setTheme(prefersDark ? 'dark' : 'light');
            }

            if (savedColorScheme) {
                this.setColorScheme(savedColorScheme);
            }
        },

        // Set theme (light/dark)
        setTheme(theme) {
            // Use Tailwind's dark class
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            localStorage.setItem('theme', theme);

            // Update toggle button if exists
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) {
                toggleBtn.innerHTML = theme === 'dark'
                    ? '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>'
                    : '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>';
            }
        },

        // Toggle between light and dark
        toggleTheme() {
            const isDark = document.documentElement.classList.contains('dark');
            const newTheme = isDark ? 'light' : 'dark';
            this.setTheme(newTheme);

            // Save to server if user is logged in
            this.saveThemePreference(newTheme);
        },

        // Set color scheme
        setColorScheme(scheme) {
            document.documentElement.setAttribute('data-color-scheme', scheme);
            localStorage.setItem('colorScheme', scheme);
        },

        // Save theme preference to server
        async saveThemePreference(theme) {
            try {
                const response = await fetch(window.location.origin + '/Saudi-culture/public/index.php?route=profile.update_theme', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `theme_preference=${theme}`
                });
            } catch (error) {
                console.log('Could not save theme preference:', error);
            }
        },

        // Setup event listeners
        setupEventListeners() {
            // Theme toggle button
            const toggleBtn = document.getElementById('theme-toggle');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => this.toggleTheme());
            }

            // Color scheme buttons
            document.querySelectorAll('[data-color-scheme-btn]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const scheme = e.currentTarget.getAttribute('data-color-scheme-btn');
                    this.setColorScheme(scheme);
                });
            });

            // Listen for system theme changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    this.setTheme(e.matches ? 'dark' : 'light');
                }
            });
        },

        // Initialize scroll animations
        initScrollAnimations() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            // Observe all elements with scroll-fade-in class
            document.querySelectorAll('.scroll-fade-in').forEach(el => {
                observer.observe(el);
            });
        }
    };

    // Utility functions
    window.showToast = function (message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg animate-slide-down ${type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                    'bg-blue-500'
            } text-white`;
        toast.textContent = message;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    };

    // Loading overlay
    window.showLoading = function () {
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
        overlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(overlay);
    };

    window.hideLoading = function () {
        const overlay = document.getElementById('loading-overlay');
        if (overlay) overlay.remove();
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => ThemeManager.init());
    } else {
        ThemeManager.init();
    }

    // Expose ThemeManager globally
    window.ThemeManager = ThemeManager;
})();
