<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-main)] mb-2">تعديل الملف الشخصي</h1>
            <p class="text-[var(--text-muted)]">قم بتحديث معلوماتك الشخصية وكلمة المرور</p>
        </div>

        <div class="grid md:grid-cols-1 gap-8">
            <!-- Update Profile Form -->
            <div class="card p-8">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-[var(--border-light)]">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-2xl">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-[var(--text-main)]">المعلومات الشخصية</h2>
                        <p class="text-sm text-[var(--text-muted)]">تحديث الاسم والبريد الإلكتروني</p>
                    </div>
                </div>
                
                <form method="POST" action="<?= route('profile.update') ?>" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-[var(--text-main)] mb-3 flex items-center gap-2">
                            <i class="fas fa-user text-primary-600"></i>
                            الاسم الكامل
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="<?= htmlspecialchars($user->name) ?>"
                            required
                            class="input-field"
                            placeholder="أدخل اسمك الكامل"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-[var(--text-main)] mb-3 flex items-center gap-2">
                            <i class="fas fa-envelope text-primary-600"></i>
                            البريد الإلكتروني
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?= htmlspecialchars($user->email) ?>"
                            required
                            class="input-field"
                            placeholder="example@email.com"
                        >
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-[var(--border-light)]">
                        <button 
                            type="submit"
                            class="btn btn-primary flex-1 justify-center"
                        >
                            <i class="fas fa-save"></i>
                            <span>حفظ التغييرات</span>
                        </button>
                        <a href="<?= route('profile') ?>" class="btn btn-outline flex-1 justify-center">
                            <i class="fas fa-times"></i>
                            <span>إلغاء</span>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Change Password Form -->
            <div class="card p-8">
                <div class="flex items-center gap-4 mb-8 pb-6 border-b border-[var(--border-light)]">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-2xl">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-[var(--text-main)]">تغيير كلمة المرور</h2>
                        <p class="text-sm text-[var(--text-muted)]">تحديث كلمة المرور الخاصة بك</p>
                    </div>
                </div>
                
                <form method="POST" action="<?= route('profile.password') ?>" class="space-y-6">
                    <div>
                        <label for="old_password" class="block text-sm font-bold text-[var(--text-main)] mb-3 flex items-center gap-2">
                            <i class="fas fa-key text-primary-600"></i>
                            كلمة المرور الحالية
                        </label>
                        <input 
                            type="password" 
                            id="old_password" 
                            name="old_password" 
                            required
                            class="input-field"
                            placeholder="أدخل كلمة المرور الحالية"
                        >
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-bold text-[var(--text-main)] mb-3 flex items-center gap-2">
                            <i class="fas fa-lock text-primary-600"></i>
                            كلمة المرور الجديدة
                        </label>
                        <input 
                            type="password" 
                            id="new_password" 
                            name="new_password" 
                            required
                            minlength="6"
                            class="input-field"
                            placeholder="أدخل كلمة المرور الجديدة"
                        >
                        <p class="text-sm text-[var(--text-muted)] mt-2 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            يجب أن تكون 6 أحرف على الأقل
                        </p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-bold text-[var(--text-main)] mb-3 flex items-center gap-2">
                            <i class="fas fa-check-circle text-primary-600"></i>
                            تأكيد كلمة المرور الجديدة
                        </label>
                        <input 
                            type="password" 
                            id="confirm_password" 
                            name="confirm_password" 
                            required
                            class="input-field"
                            placeholder="أعد إدخال كلمة المرور الجديدة"
                        >
                    </div>

                    <div class="pt-6 border-t border-[var(--border-light)]">
                        <button 
                            type="submit"
                            class="btn bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-600 text-white w-full justify-center"
                        >
                            <i class="fas fa-shield-alt"></i>
                            <span>تغيير كلمة المرور</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Notice -->
            <div class="card p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-yellow-200 dark:border-yellow-800">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-xl bg-yellow-600 text-white flex items-center justify-center text-xl">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-[var(--text-main)] mb-2">نصائح الأمان</h3>
                        <ul class="text-sm text-[var(--text-muted)] space-y-2">
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <span>استخدم كلمة مرور قوية تحتوي على أحرف وأرقام ورموز</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <span>لا تشارك كلمة المرور الخاصة بك مع أي شخص</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i class="fas fa-check text-green-600 mt-1"></i>
                                <span>قم بتغيير كلمة المرور بشكل دوري للحفاظ على أمان حسابك</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
