
<div class="min-h-screen py-12">
    <div class="container mx-auto px-4">
        <!-- Welcome Header -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-12 animate-fade-in">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold heading-gradient mb-2">
                    مرحباً، <?= htmlspecialchars(auth()->user()->name) ?> <i class="fas fa-hand-wave"></i>
                </h1>
                <p class="text-[var(--text-muted)]">
                    لوحة التحكم الخاصة بمساهماتك وإنجازاتك
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= route('projects') ?>" class="btn btn-primary px-6 py-3 rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center gap-2">
                    <span>تصفح المشاريع</span>
                    <i class="fas fa-rocket"></i>
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 animate-slide-up">
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-900/30 text-teal-600 dark:text-teal-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <span class="text-sm text-[var(--text-muted)]">إجمالي المساهمات</span>
                </div>
                <div class="text-3xl font-bold text-[var(--text-main)]"><?= $totalAnswers ?></div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="text-sm text-[var(--text-muted)]">مشاريع مكتملة</span>
                </div>
                <div class="text-3xl font-bold text-[var(--text-main)]"><?= $totalCompleted ?></div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <span class="text-sm text-[var(--text-muted)]">قيد التقدم</span>
                </div>
                <div class="text-3xl font-bold text-[var(--text-main)]"><?= count($activeSessions) ?></div>
            </div>

            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-2xl">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <span class="text-sm text-[var(--text-muted)]">نقاط التميز</span>
                </div>
                <div class="text-3xl font-bold text-[var(--text-main)]"><?= $totalAnswers * 10 ?></div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="mb-12 animate-slide-up delay-100">
            <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-[var(--text-main)]">
                <span>مشاريع قيد العمل</span>
                <span class="text-sm font-normal text-[var(--text-muted)]">(<?= count($activeSessions) ?>)</span>
            </h2>
            
            <?php if (empty($activeSessions)): ?>
                <div class="bg-[var(--bg-card)] rounded-3xl p-12 text-center border border-[var(--border-light)]">
                    <div class="text-6xl mb-4 opacity-50 text-[var(--text-muted)]">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2 text-[var(--text-main)]">لا توجد مشاريع نشطة</h3>
                    <p class="text-[var(--text-muted)] mb-6">ابدأ رحلة التوثيق الآن وساهم في حفظ تراثنا</p>
                    <a href="<?= route('projects') ?>" class="btn btn-outline px-8 py-2 rounded-xl">
                        استعراض المشاريع
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($activeSessions as $session): ?>
                        <?php 
                            // Find project details from allProjects array
                            $project = null;
                            foreach ($allProjects as $p) {
                                if ($p->id == $session->project_id) {
                                    $project = $p;
                                    break;
                                }
                            }
                            if (!$project) continue;
                        ?>
                        <div class="card overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <h3 class="text-lg font-bold line-clamp-1 text-[var(--text-main)]"><?= htmlspecialchars($project->name) ?></h3>
                                    <span class="px-2 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">
                                        قيد التقدم
                                    </span>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm mb-1">
                                        <span class="text-[var(--text-muted)]">التقدم</span>
                                        <span class="font-bold text-teal-600"><?= number_format($session->progress, 0) ?>%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-teal-600 h-2 rounded-full transition-all duration-500" style="width: <?= $session->progress ?>%"></div>
                                    </div>
                                </div>
                                
                                <a href="<?= route('questions', ['session_id' => $session->id]) ?>" class="btn btn-primary w-full py-2 rounded-xl text-sm shadow-md">
                                    متابعة العمل
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Projects -->
        <?php if (!empty($completedSessions)): ?>
            <div class="animate-slide-up delay-200">
                <h2 class="text-2xl font-bold mb-6 flex items-center gap-2 text-[var(--text-main)]">
                    <span>مشاريع مكتملة</span>
                    <span class="text-sm font-normal text-[var(--text-muted)]">(<?= count($completedSessions) ?>)</span>
                </h2>
                
                <div class="bg-[var(--bg-card)] rounded-3xl shadow-lg border border-[var(--border-light)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">المشروع</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">تاريخ البدء</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">تاريخ الإكمال</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">الحالة</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">صحيح</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">خاطئ</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">الدرجة</th>
                                    <th class="px-6 py-4 text-sm font-bold text-[var(--text-muted)]">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                <?php foreach ($completedSessions as $session): ?>
                                    <?php 
                                        $project = null;
                                        foreach ($allProjects as $p) {
                                            if ($p->id == $session->project_id) {
                                                $project = $p;
                                                break;
                                            }
                                        }
                                        if (!$project) continue;
                                        $stats = $completedStats[$project->id] ?? ['correct' => 0, 'incorrect' => 0, 'percent' => 0];
                                    ?>
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 font-medium text-[var(--text-main)]"><?= htmlspecialchars($project->name) ?></td>
                                        <td class="px-6 py-4 text-[var(--text-muted)]"><?= date('Y/m/d', strtotime($session->started_at)) ?></td>
                                        <td class="px-6 py-4 text-[var(--text-muted)]"><?= date('Y/m/d', strtotime($session->completed_at)) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                مكتمل
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-semibold text-green-600"><?= $stats['correct'] ?></td>
                                        <td class="px-6 py-4 font-semibold text-rose-600"><?= $stats['incorrect'] ?></td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                                    <div class="h-2 bg-primary-500 rounded-full" style="width: <?= $stats['percent'] ?>%"></div>
                                                </div>
                                                <span class="text-sm font-bold text-[var(--text-main)]"><?= $stats['percent'] ?>%</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="<?= route('projects.show', ['id' => $project->id]) ?>" class="text-teal-600 hover:text-teal-700 font-medium text-sm">
                                                عرض التفاصيل
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
```
