<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)] mb-2">مراجعة الإجابات</h1>
            <p class="text-[var(--text-muted)]">مراجعة وتقييم إجابات المستخدمين</p>
        </div>
    </div>

    <?php if (empty($answers)): ?>
        <div class="card p-12 text-center">
            <div class="w-20 h-20 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-6 text-[var(--text-muted)]">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-[var(--text-main)]">لا توجد إجابات للمراجعة</h2>
            <p class="text-[var(--text-muted)]">جميع الإجابات تمت مراجعتها</p>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--bg-body)] border-b border-[var(--border-light)]">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">السؤال</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">المشروع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-light)]">
                        <?php foreach ($answers as $answer): ?>
                            <tr class="hover:bg-[var(--bg-body)] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-[var(--text-main)] max-w-md">
                                        <?= htmlspecialchars(substr($answer->question_text, 0, 80)) ?>...
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-sm">
                                            <?= mb_substr($answer->user_name, 0, 1) ?>
                                        </div>
                                        <span class="text-sm text-[var(--text-main)]"><?= htmlspecialchars($answer->user_name) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-[var(--text-muted)]">
                                        <?= htmlspecialchars($answer->project_name ?? 'غير محدد') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $typeClass = match($answer->question_type) {
                                        'mcq' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                        'true_false' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300'
                                    };
                                    $typeName = match($answer->question_type) {
                                        'mcq' => 'اختيار متعدد',
                                        'true_false' => 'صح/خطأ',
                                        'text' => 'نصي',
                                        default => $answer->question_type
                                    };
                                    ?>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full <?= $typeClass ?>">
                                        <?= $typeName ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-[var(--text-muted)]">
                                    <?= date('Y/m/d', strtotime($answer->created_at)) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= route('manager.reviews.show', ['id' => $answer->id]) ?>" class="w-8 h-8 rounded-lg bg-primary-50 dark:bg-primary-900/20 text-primary-600 hover:bg-primary-100 dark:hover:bg-primary-900/40 flex items-center justify-center transition-colors" title="مراجعة">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
