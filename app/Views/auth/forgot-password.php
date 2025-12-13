<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-primary-50 to-purple-50 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo/Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-primary-600 to-purple-600 flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-key text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-[var(--text-main)]">نسيت كلمة المرور؟</h2>
            <p class="mt-2 text-sm text-[var(--text-muted)]">
                أدخل بريدك الإلكتروني وسنرسل لك رمز التحقق
            </p>
        </div>

        <!-- Form Card -->
        <div class="card p-8">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                    <p class="text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $_SESSION['error'] ?>
                    </p>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form method="POST" action="<?= route('password.send-code') ?>" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                        البريد الإلكتروني
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-[var(--text-muted)]"></i>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            required
                            class="input-field pr-10"
                            placeholder="example@email.com"
                            autocomplete="email"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full btn btn-primary py-3 text-lg font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                >
                    <i class="fas fa-paper-plane ml-2"></i>
                    إرسال رمز التحقق
                </button>
            </form>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="<?= route('login') ?>" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium flex items-center justify-center gap-2">
                <i class="fas fa-arrow-right"></i>
                العودة لتسجيل الدخول
            </a>
        </div>
    </div>
</div>
