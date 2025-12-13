<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'منصة إثراء الثقافة السعودية' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdfa',
                            100: '#ccfbf1',
                            200: '#99f6e4',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#14b8a6', // Main Teal
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a', // Dark Slate
                            950: '#020617',
                        }
                    },
                    fontFamily: {
                        sans: ['IBM Plex Sans Arabic', 'Cairo', 'sans-serif'],
                    },
                    boxShadow: {
                        'glass': '0 4px 30px rgba(0, 0, 0, 0.1)',
                        'glass-dark': '0 4px 30px rgba(0, 0, 0, 0.5)',
                    }
                }
            }
        }
    </script>

    <!-- Custom Styles -->
    <style type="text/tailwindcss">
        @layer base {
            :root {
                /* Semantic Colors */
                --bg-body: #f8fafc;
                --bg-card: #ffffff;
                --text-main: #0f172a;
                --text-muted: #64748b;
                --border-light: #e2e8f0;
                
                /* Glassmorphism */
                --glass-bg: rgba(255, 255, 255, 0.7);
                --glass-border: rgba(255, 255, 255, 0.5);
                --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
            }

            .dark {
                /* Dark Mode Overrides */
                --bg-body: #0f172a;
                --bg-card: #1e293b;
                --text-main: #f1f5f9;
                --text-muted: #94a3b8;
                --border-light: #334155;

                /* Glassmorphism Dark */
                --glass-bg: rgba(30, 41, 59, 0.7);
                --glass-border: rgba(255, 255, 255, 0.1);
                --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
            }

            body {
                @apply bg-[var(--bg-body)] text-[var(--text-main)] font-sans antialiased transition-colors duration-300;
            }
        }

        @layer components {
            /* Modern Card */
            .card {
                @apply bg-[var(--bg-card)] rounded-2xl border border-[var(--border-light)] shadow-sm hover:shadow-lg transition-all duration-300;
            }

            /* Glass Effect */
            .glass {
                background: var(--glass-bg);
                backdrop-filter: blur(12px);
                -webkit-backdrop-filter: blur(12px);
                border: 1px solid var(--glass-border);
                box-shadow: var(--glass-shadow);
            }

            /* Buttons */
            .btn {
                @apply px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center justify-center gap-2 active:scale-95;
            }

            .btn-primary {
                @apply bg-gradient-to-r from-primary-600 to-primary-500 text-white shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 hover:-translate-y-0.5;
            }

            .btn-outline {
                @apply border-2 border-primary-500 text-primary-600 hover:bg-primary-50 dark:text-primary-400 dark:hover:bg-primary-900/30;
            }

            .btn-ghost {
                @apply text-[var(--text-muted)] hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/30;
            }

            /* Inputs */
            .input-field {
                @apply w-full px-4 py-3 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 outline-none transition-all duration-300;
            }

            /* Typography */
            .heading-gradient {
                @apply bg-clip-text text-transparent bg-gradient-to-r from-primary-700 to-primary-500 dark:from-primary-400 dark:to-primary-200;
            }
        }

        @layer utilities {
            .text-shadow {
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .animation-delay-200 {
                animation-delay: 0.2s;
            }
            .animation-delay-400 {
                animation-delay: 0.4s;
            }
        }

        /* Custom Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes fadeInScale {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-fade-scale {
            animation: fadeInScale 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }
        @keyframes shake {
            10%, 90% { transform: translateX(-1px); }
            20%, 80% { transform: translateX(2px); }
            30%, 50%, 70% { transform: translateX(-4px); }
            40%, 60% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        /* Mobile Nav Link */
        .mobile-nav-link {
            @apply flex items-center gap-3 px-4 py-3 rounded-xl text-[var(--text-muted)] font-medium transition-all duration-300 hover:bg-[var(--bg-body)] hover:text-primary-600;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[var(--bg-body)] text-[var(--text-main)] transition-colors duration-300">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 glass border-b border-[var(--border-light)]">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="<?= route('home') ?>" class="flex items-center gap-3 group">
                    <img src="<?= asset('images/logo.svg') ?>" alt="منصة إثراء الثقافة السعودية" class="w-12 h-12 rounded-xl border border-[var(--border-light)] shadow-lg shadow-primary-500/30 bg-white object-contain p-2 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-xl font-bold heading-gradient hidden sm:inline">منصة إثراء الثقافة السعودية</span>
                </a>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center gap-8">
                    <?php if (auth()->check()): ?>
                        <?php if (auth()->isManager()): ?>
                            <a href="<?= route('manager.dashboard') ?>" class="nav-link flex items-center gap-2 text-sm font-medium hover:text-primary-600 transition-colors">
                                <i class="fas fa-chart-pie"></i>
                                لوحة التحكم
                            </a>
                            <!-- <a href="<?= route('manager.settings') ?>" class="nav-link flex items-center gap-2 text-sm font-medium hover:text-primary-600 transition-colors">
                                <i class="fas fa-cog"></i>
                                الإعدادات
                            </a> -->
                        <?php else: ?>
                            <a href="<?= route('dashboard') ?>" class="nav-link flex items-center gap-2 text-sm font-medium hover:text-primary-600 transition-colors">
                                <i class="fas fa-th-large"></i>
                                لوحتي
                            </a>
                        <?php endif; ?>
                        <a href="<?= route('projects') ?>" class="nav-link flex items-center gap-2 text-sm font-medium hover:text-primary-600 transition-colors">
                            <i class="fas fa-layer-group"></i>
                            المشاريع
                        </a>
                        <a href="<?= route('profile') ?>" class="nav-link flex items-center gap-2 text-sm font-medium hover:text-primary-600 transition-colors">
                            <i class="fas fa-user-circle"></i>
                            الملف الشخصي
                        </a>
                    <?php else: ?>
                        <a href="<?= route('about') ?>" class="nav-link text-sm font-medium hover:text-primary-600 transition-colors">عن المنصة</a>
                        <a href="<?= route('contact') ?>" class="nav-link text-sm font-medium hover:text-primary-600 transition-colors">اتصل بنا</a>
                        <a href="<?= route('login') ?>" class="nav-link text-sm font-medium hover:text-primary-600 transition-colors">تسجيل الدخول</a>
                        <a href="<?= route('register') ?>" class="btn btn-primary py-2 px-6 text-sm">إنشاء حساب</a>
                    <?php endif; ?>
                    
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="w-10 h-10 rounded-full bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-all duration-300">
                        <i class="fas fa-moon dark:hidden"></i>
                        <i class="fas fa-sun hidden dark:inline"></i>
                    </button>
                    
                    <?php if (auth()->check()): ?>
                        <a href="<?= route('logout') ?>" class="w-10 h-10 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition-colors" title="تسجيل الخروج">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg text-[var(--text-muted)] hover:bg-[var(--bg-body)]">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4 animate-fade-in border-t border-[var(--border-light)] pt-4">
                <div class="flex flex-col space-y-3">
                    <?php if (auth()->check()): ?>
                        <a href="<?= auth()->isManager() ? route('manager.dashboard') : route('dashboard') ?>" class="mobile-nav-link">
                            <i class="fas fa-chart-pie w-6"></i> لوحة التحكم
                        </a>
                        <a href="<?= route('projects') ?>" class="mobile-nav-link">
                            <i class="fas fa-layer-group w-6"></i> المشاريع
                        </a>
                        <a href="<?= route('profile') ?>" class="mobile-nav-link">
                            <i class="fas fa-user-circle w-6"></i> الملف الشخصي
                        </a>
                        <a href="<?= route('logout') ?>" class="mobile-nav-link text-red-500">
                            <i class="fas fa-sign-out-alt w-6"></i> تسجيل الخروج
                        </a>
                    <?php else: ?>
                        <a href="<?= route('about') ?>" class="mobile-nav-link">عن المنصة</a>
                        <a href="<?= route('contact') ?>" class="mobile-nav-link">اتصل بنا</a>
                        <a href="<?= route('login') ?>" class="mobile-nav-link">تسجيل الدخول</a>
                        <a href="<?= route('register') ?>" class="btn btn-primary w-full text-center">إنشاء حساب</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="container mx-auto px-4 mt-6 animate-slide-down">
            <div class="bg-primary-50 dark:bg-primary-900/30 border border-primary-200 dark:border-primary-800 text-primary-800 dark:text-primary-200 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                <i class="fas fa-check-circle text-xl"></i>
                <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="container mx-auto px-4 mt-6 animate-slide-down">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 px-6 py-4 rounded-2xl shadow-sm flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-xl"></i>
                <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-auto bg-[var(--bg-card)] border-t border-[var(--border-light)]">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="<?= asset('images/logo.svg') ?>" alt="منصة إثراء الثقافة السعودية" class="w-10 h-10 rounded-lg border border-[var(--border-light)] bg-white object-contain p-2 shadow-sm">
                        <h3 class="text-xl font-bold heading-gradient">منصة إثراء الثقافة السعودية</h3>
                    </div>
                    <p class="text-[var(--text-muted)] leading-relaxed max-w-md">
                        منصة وطنية رائدة تهدف إلى جمع وتوثيق وإبراز التراث الثقافي السعودي العريق من خلال مشاركة المجتمع في بناء قاعدة معرفية شاملة.
                    </p>
                </div>
                
                <div>
                    <h3 class="font-bold text-lg mb-6 text-[var(--text-main)]">روابط سريعة</h3>
                    <ul class="space-y-4">
                        <li><a href="<?= route('about') ?>" class="text-[var(--text-muted)] hover:text-primary-600 transition-colors flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> عن المنصة</a></li>
                        <li><a href="<?= route('projects') ?>" class="text-[var(--text-muted)] hover:text-primary-600 transition-colors flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> المشاريع</a></li>
                        <li><a href="<?= route('contact') ?>" class="text-[var(--text-muted)] hover:text-primary-600 transition-colors flex items-center gap-2"><i class="fas fa-chevron-left text-xs"></i> اتصل بنا</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="font-bold text-lg mb-6 text-[var(--text-main)]">تواصل معنا</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-[var(--text-muted)]">
                            <i class="fas fa-envelope text-primary-600"></i>
                            <span>info@saudiculture.sa</span>
                        </li>
                        <li class="flex items-center gap-3 text-[var(--text-muted)]">
                            <i class="fas fa-phone text-primary-600"></i>
                            <span>920000000</span>
                        </li>
                    </ul>
                    <div class="flex gap-4 mt-6">
                        <a href="https://x.com/" class="w-10 h-10 rounded-full bg-[var(--bg-body)] flex items-center justify-center text-[var(--text-muted)] hover:bg-primary-50 hover:text-primary-600 transition-all border border-[var(--border-light)]">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com/" class="w-10 h-10 rounded-full bg-[var(--bg-body)] flex items-center justify-center text-[var(--text-muted)] hover:bg-primary-50 hover:text-primary-600 transition-all border border-[var(--border-light)]">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-[var(--border-light)] mt-12 pt-8 text-center">
                <p class="text-[var(--text-muted)] text-sm">
                    &copy; <?= date('Y') ?> منصة إثراء الثقافة السعودية. جميع الحقوق محفوظة.
                </p>
            </div>
        </div>
    </footer>

    <!-- Theme JavaScript -->
    <script src="<?= asset('js/theme.js') ?>"></script>
    
    <!-- Mobile Menu Toggle -->
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</body>
</html>
