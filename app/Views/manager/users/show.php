<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="<?= route('manager.users') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">ملف المستخدم</h1>
            <p class="text-[var(--text-muted)]">تفاصيل ونشاط المستخدم</p>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="card p-8">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-4xl font-bold shadow-lg shadow-primary-500/30">
                <?= mb_substr($user->name, 0, 1) ?>
            </div>
            <div class="flex-1 text-center md:text-right space-y-2">
                <h2 class="text-3xl font-bold text-[var(--text-main)]"><?= htmlspecialchars($user->name) ?></h2>
                <div class="flex flex-wrap justify-center md:justify-start gap-4 text-[var(--text-muted)]">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-envelope"></i>
                        <?= htmlspecialchars($user->email) ?>
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="fas fa-calendar-alt"></i>
                        انضم في <?= date('Y/m/d', strtotime($user->created_at)) ?>
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="fas fa-user-tag"></i>
                        <?= $user->role == 'manager' ? 'مشرف' : 'مستخدم' ?>
                    </span>
                </div>
            </div>
            
            <?php if ($user->id != auth()->id()): ?>
                <div class="flex gap-3">
                    <form method="POST" action="<?= route('manager.users.update-role') ?>">
                        <input type="hidden" name="id" value="<?= $user->id ?>">
                        <input type="hidden" name="role" value="<?= $user->role == 'manager' ? 'user' : 'manager' ?>">
                        <button type="submit" class="btn btn-outline" onclick="return confirm('هل أنت متأكد من تغيير دور المستخدم؟')">
                            <i class="fas fa-user-shield ml-2"></i> تغيير الدور
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card p-6 text-center">
            <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fas fa-book-reader"></i>
            </div>
            <h3 class="text-[var(--text-muted)] font-medium mb-1">المشاريع المشارك بها</h3>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= count($sessions) ?></p>
        </div>

        <div class="card p-6 text-center">
            <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fas fa-pen-fancy"></i>
            </div>
            <h3 class="text-[var(--text-muted)] font-medium mb-1">الإجابات المقدمة</h3>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= count($answers) ?></p>
        </div>

        <div class="card p-6 text-center">
            <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl mx-auto mb-3">
                <i class="fas fa-star"></i>
            </div>
            <h3 class="text-[var(--text-muted)] font-medium mb-1">متوسط التقييم</h3>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= number_format($avgScore, 1) ?></p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Sessions -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-6 flex items-center gap-2">
                <i class="fas fa-history text-primary-500"></i>
                آخر الجلسات
            </h3>
            <?php if (empty($sessions)): ?>
                <p class="text-center text-[var(--text-muted)] py-8">لا توجد جلسات مسجلة</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($sessions, 0, 5) as $session): ?>
                        <div class="flex items-center justify-between p-4 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <div>
                                <h4 class="font-bold text-[var(--text-main)]"><?= htmlspecialchars($session->project_name) ?></h4>
                                <p class="text-sm text-[var(--text-muted)]"><?= date('Y/m/d', strtotime($session->started_at)) ?></p>
                            </div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full <?= $session->status == 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' ?>">
                                <?= $session->status == 'completed' ? 'مكتمل' : 'قيد التقدم' ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Answers -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-6 flex items-center gap-2">
                <i class="fas fa-comment-alt text-blue-500"></i>
                آخر الإجابات
            </h3>
            <?php if (empty($answers)): ?>
                <p class="text-center text-[var(--text-muted)] py-8">لا توجد إجابات مسجلة</p>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($answers, 0, 5) as $answer): ?>
                        <div class="p-4 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <p class="text-sm text-[var(--text-muted)] mb-2"><?= htmlspecialchars($answer->question_text) ?></p>
                            <p class="font-medium text-[var(--text-main)]">
                                <?php if ($answer->question_type == 'text'): ?>
                                    <?= htmlspecialchars(substr($answer->answer_text, 0, 50)) ?>...
                                <?php else: ?>
                                    <span class="text-primary-600"><i class="fas fa-check-circle ml-1"></i> إجابة اختيارية</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
