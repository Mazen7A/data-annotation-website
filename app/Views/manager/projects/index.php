<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)] mb-2">إدارة المشاريع</h1>
            <p class="text-[var(--text-muted)]">إدارة ومتابعة جميع المشاريع الثقافية</p>
        </div>
        <a href="<?= route('manager.projects.create') ?>" class="btn btn-primary">
            <i class="fas fa-plus ml-2"></i> إنشاء مشروع جديد
        </a>
    </div>

    <?php if (empty($projects)): ?>
        <div class="card p-12 text-center">
            <div class="w-20 h-20 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-6 text-[var(--text-muted)]">
                <i class="fas fa-book text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-[var(--text-main)]">لا توجد مشاريع</h2>
            <p class="text-[var(--text-muted)] mb-6">ابدأ بإنشاء مشروع جديد لإثراء المحتوى الثقافي</p>
            <a href="<?= route('manager.projects.create') ?>" class="btn btn-primary inline-flex">
                إنشاء مشروع
            </a>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--bg-body)] border-b border-[var(--border-light)]">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">المشروع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الفئة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الأسئلة</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">المنشئ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-light)]">
                        <?php foreach ($projects as $project): ?>
                            <tr class="hover:bg-[var(--bg-body)] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center text-lg font-bold">
                                            <?= mb_substr($project->name, 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-[var(--text-main)]"><?= htmlspecialchars($project->name) ?></div>
                                            <div class="text-sm text-[var(--text-muted)]"><?= htmlspecialchars(substr($project->summary ?? '', 0, 50)) ?>...</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 text-xs font-bold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full">
                                        <?= htmlspecialchars($project->category ?? 'عام') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold text-[var(--text-main)] bg-[var(--bg-body)] px-3 py-1 rounded-lg border border-[var(--border-light)]">
                                        <?= $project->total_questions ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-[var(--text-muted)]">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-user-circle"></i>
                                        <?= htmlspecialchars($project->creator_name ?? 'غير معروف') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= route('manager.projects.show', ['id' => $project->id]) ?>" class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/40 flex items-center justify-center transition-colors" title="عرض التفاصيل">
                                            <i class="fas fa-chart-pie"></i>
                                        </a>
                                        <a href="<?= route('manager.questions', ['project_id' => $project->id]) ?>" class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/20 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/40 flex items-center justify-center transition-colors" title="الأسئلة">
                                            <i class="fas fa-list-ul"></i>
                                        </a>
                                        <a href="<?= route('manager.projects.edit', ['id' => $project->id]) ?>" class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 hover:bg-purple-100 dark:hover:bg-purple-900/40 flex items-center justify-center transition-colors" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= route('manager.projects.delete') ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                                            <input type="hidden" name="id" value="<?= $project->id ?>">
                                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40 flex items-center justify-center transition-colors" title="حذف">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
