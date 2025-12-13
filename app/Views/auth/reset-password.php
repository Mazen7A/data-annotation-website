<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-primary-50 to-purple-50 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-md w-full space-y-8">
        <!-- Logo/Header -->
        <div class="text-center">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-green-600 to-emerald-600 flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-[var(--text-main)]">إعادة تعيين كلمة المرور</h2>
            <p class="mt-2 text-sm text-[var(--text-muted)]">
                أدخل رمز التحقق وكلمة المرور الجديدة
            </p>
        </div>

        <!-- Display Code (Simulating Email) -->
        <?php if (!empty($display_code)): ?>
            <div class="card p-6 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-500">
                <div class="text-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-3xl mb-3"></i>
                    <h3 class="font-bold text-[var(--text-main)] mb-2">رمز التحقق الخاص بك</h3>
                    <p class="text-sm text-[var(--text-muted)] mb-4">استخدم هذا الرمز لإعادة تعيين كلمة المرور</p>
                    <div class="inline-block px-6 py-3 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                        <span class="text-3xl font-mono font-bold text-green-600 dark:text-green-400 tracking-widest">
                            <?= $display_code ?>
                        </span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-3">
                        <i class="fas fa-clock ml-1"></i>
                        صالح لمدة 15 دقيقة
                    </p>
                </div>
            </div>
        <?php endif; ?>

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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                    <p class="text-sm text-green-600 dark:text-green-400 flex items-center gap-2">
                        <i class="fas fa-check-circle"></i>
                        <?= $_SESSION['success'] ?>
                    </p>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="POST" action="<?= route('password.update') ?>" class="space-y-6">
                <!-- Email (hidden or readonly) -->
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                
                <?php if (!empty($email)): ?>
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                            البريد الإلكتروني
                        </label>
                        <div class="input-field bg-gray-50 dark:bg-gray-800 cursor-not-allowed">
                            <?= htmlspecialchars($email) ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Verification Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                        رمز التحقق (6 أرقام)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-key text-[var(--text-muted)]"></i>
                        </div>
                        <input
                            type="text"
                            id="code"
                            name="code"
                            required
                            maxlength="6"
                            pattern="[0-9]{6}"
                            class="input-field pr-10 text-center text-2xl font-mono tracking-widest"
                            placeholder="000000"
                            autocomplete="off"
                        >
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                        كلمة المرور الجديدة
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-[var(--text-muted)]"></i>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            minlength="6"
                            class="input-field pr-10"
                            placeholder="••••••••"
                        >
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-1">6 أحرف على الأقل</p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirm" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                        تأكيد كلمة المرور
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-[var(--text-muted)]"></i>
                        </div>
                        <input
                            type="password"
                            id="password_confirm"
                            name="password_confirm"
                            required
                            minlength="6"
                            class="input-field pr-10"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full btn btn-primary py-3 text-lg font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all"
                >
                    <i class="fas fa-check ml-2"></i>
                    تغيير كلمة المرور
                </button>
            </form>
        </div>

        <?php if (!empty($email)): ?>
            <form method="POST" action="<?= route('password.send-code') ?>" class="card p-4 flex items-center justify-between gap-3 text-sm text-[var(--text-muted)]">
                <span>لم يصلك الرمز؟</span>
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <button type="submit" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 font-semibold">
                    إعادة إرسال الرمز
                </button>
            </form>
        <?php endif; ?>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="<?= route('login') ?>" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium flex items-center justify-center gap-2">
                <i class="fas fa-arrow-right"></i>
                العودة لتسجيل الدخول
            </a>
        </div>
    </div>
</div>
