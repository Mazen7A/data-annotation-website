<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)] mb-2">أسئلة المشروع: <?= htmlspecialchars($project->name) ?></h1>
            <p class="text-[var(--text-muted)]">إدارة الأسئلة الخاصة بالمشروع</p>
        </div>
        <div class="flex gap-3">
            <a href="<?= route('manager.projects') ?>" class="btn btn-outline">
                <i class="fas fa-arrow-right ml-2"></i> العودة للمشاريع
            </a>
            <a href="<?= route('manager.questions.create', ['project_id' => $project->id]) ?>" class="btn btn-primary">
                <i class="fas fa-plus ml-2"></i> إضافة سؤال جديد
            </a>
            <a href="<?= route('manager.questions.import') ?>" class="btn btn-outline">
                <i class="fas fa-file-upload ml-2"></i> استيراد من ملف
            </a>
        </div>
    </div>

    <?php if (empty($questions)): ?>
        <div class="card p-12 text-center">
            <div class="w-20 h-20 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-6 text-[var(--text-muted)]">
                <i class="fas fa-question text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-[var(--text-main)]">لا توجد أسئلة</h2>
            <p class="text-[var(--text-muted)] mb-6">لم يتم إضافة أي أسئلة لهذا المشروع بعد</p>
            <a href="<?= route('manager.questions.create', ['project_id' => $project->id]) ?>" class="btn btn-primary inline-flex">
                إضافة سؤال
            </a>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--bg-body)] border-b border-[var(--border-light)]">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">السؤال</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الفئة</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-light)]">
                        <?php foreach ($questions as $question): ?>
                            <tr class="hover:bg-[var(--bg-body)] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-[var(--text-main)]"><?= htmlspecialchars(substr($question->question_text, 0, 80)) ?>...</div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeClass = match($question->question_type) {
                                        'mcq' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                        'true_false' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                    $typeName = match($question->question_type) {
                                        'mcq' => 'اختيار من متعدد',
                                        'true_false' => 'صح/خطأ',
                                        'text' => 'نصي',
                                        default => $question->question_type
                                    };
                                    ?>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full <?= $typeClass ?>">
                                        <?= $typeName ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-[var(--text-muted)]">
                                        <?= htmlspecialchars($question->category ?? 'عام') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= route('manager.questions.edit', ['id' => $question->id]) ?>" class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 text-purple-600 hover:bg-purple-100 dark:hover:bg-purple-900/40 flex items-center justify-center transition-colors" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?= route('manager.questions.delete') ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                                            <input type="hidden" name="id" value="<?= $question->id ?>">
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
