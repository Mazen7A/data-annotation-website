<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-[var(--text-main)] mb-2">الملف الشخصي</h1>
            <p class="text-[var(--text-muted)]">إدارة معلوماتك الشخصية وإحصائياتك</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Info Card -->
            <div class="lg:col-span-2">
                <div class="card p-8">
                    <!-- Profile Header -->
                    <div class="flex items-center gap-6 mb-8 pb-8 border-b border-[var(--border-light)]">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                            <?= mb_substr($user->name, 0, 1) ?>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-[var(--text-main)] mb-2"><?= htmlspecialchars($user->name) ?></h2>
                            <p class="text-[var(--text-muted)] flex items-center gap-2 mb-2">
                                <i class="fas fa-envelope text-primary-600"></i>
                                <?= htmlspecialchars($user->email) ?>
                            </p>
                            <div class="flex items-center gap-2">
                                <span class="px-4 py-1.5 rounded-full text-sm font-bold <?= $user->role === 'manager' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300' : 'bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300' ?>">
                                    <i class="fas <?= $user->role === 'manager' ? 'fa-user-shield' : 'fa-user' ?>"></i>
                                    <?= $user->role === 'manager' ? 'مشرف' : 'مستخدم' ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <h3 class="text-xl font-bold mb-6 text-[var(--text-main)] flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        المعلومات الشخصية
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="p-5 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <label class="text-sm text-[var(--text-muted)] mb-2 block flex items-center gap-2">
                                <i class="fas fa-user text-primary-600"></i>
                                الاسم الكامل
                            </label>
                            <p class="text-lg font-semibold text-[var(--text-main)]"><?= htmlspecialchars($user->name) ?></p>
                        </div>
                        
                        <div class="p-5 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <label class="text-sm text-[var(--text-muted)] mb-2 block flex items-center gap-2">
                                <i class="fas fa-envelope text-primary-600"></i>
                                البريد الإلكتروني
                            </label>
                            <p class="text-lg font-semibold text-[var(--text-main)]"><?= htmlspecialchars($user->email) ?></p>
                        </div>
                        
                        <div class="p-5 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <label class="text-sm text-[var(--text-muted)] mb-2 block flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-primary-600"></i>
                                تاريخ التسجيل
                            </label>
                            <p class="text-lg font-semibold text-[var(--text-main)]"><?= date('Y/m/d', strtotime($user->created_at)) ?></p>
                        </div>

                        <div class="p-5 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <label class="text-sm text-[var(--text-muted)] mb-2 block flex items-center gap-2">
                                <i class="fas fa-shield-alt text-primary-600"></i>
                                نوع الحساب
                            </label>
                            <p class="text-lg font-semibold text-[var(--text-main)]">
                                <?= $user->role === 'manager' ? 'حساب إداري' : 'حساب مستخدم' ?>
                            </p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-[var(--border-light)]">
                        <a href="<?= route('profile.edit') ?>" class="btn btn-primary flex-1 justify-center">
                            <i class="fas fa-edit"></i>
                            <span>تعديل الملف الشخصي</span>
                        </a>
                        <a href="<?= route('dashboard') ?>" class="btn btn-outline flex-1 justify-center">
                            <i class="fas fa-arrow-right"></i>
                            <span>العودة للوحة التحكم</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Stats Card -->
                <div class="card p-6">
                    <h3 class="text-xl font-bold mb-6 text-[var(--text-main)] flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        إحصائياتي
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="p-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl text-center group hover:shadow-lg transition-all">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-primary-600 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="text-4xl font-bold heading-gradient mb-2">
                                <?= \App\Models\Answer::countByUser($user->id) ?>
                            </div>
                            <div class="text-sm text-[var(--text-muted)] font-medium">إجمالي الإجابات</div>
                        </div>
                        
                        <div class="p-6 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-xl text-center group hover:shadow-lg transition-all">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-600 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">
                                <?= \App\Models\Session::countCompleted($user->id) ?>
                            </div>
                            <div class="text-sm text-[var(--text-muted)] font-medium">مشاريع مكتملة</div>
                        </div>

                        <div class="p-6 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl text-center group hover:shadow-lg transition-all">
                            <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-purple-600 text-white flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="text-4xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                                <?= \App\Models\Session::countActive($user->id) ?>
                            </div>
                            <div class="text-sm text-[var(--text-muted)] font-medium">مشاريع قيد العمل</div>
                        </div>
                    </div>
                </div>

                <!-- Achievement Badge -->
                <div class="card p-6 bg-gradient-to-br from-yellow-50 to-yellow-100 dark:from-yellow-900/20 dark:to-yellow-800/20 border-yellow-200 dark:border-yellow-800">
                    <div class="text-center">
                        <div class="text-5xl mb-4">🏆</div>
                        <h4 class="font-bold text-lg text-[var(--text-main)] mb-2">مساهم نشط</h4>
                        <p class="text-sm text-[var(--text-muted)]">
                            شكراً لمساهماتك القيمة في إثراء المحتوى الثقافي
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
