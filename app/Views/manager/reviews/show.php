<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= route('manager.dashboard') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">مراجعة الإجابة</h1>
            <p class="text-[var(--text-muted)]">مراجعة وتقييم إجابة المستخدم</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Answer Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Question Card -->
            <div class="card p-6">
                <h3 class="text-sm font-bold text-[var(--text-muted)] uppercase mb-4">السؤال</h3>
                <p class="text-xl font-medium text-[var(--text-main)] leading-relaxed">
                    <?= htmlspecialchars($answer->question_text) ?>
                </p>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg text-sm font-bold">
                        <?= htmlspecialchars($answer->project_name ?? 'مشروع عام') ?>
                    </span>
                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-bold">
                        <?= $answer->question_type == 'text' ? 'نصي' : 'اختيار من متعدد' ?>
                    </span>
                </div>
            </div>

            <!-- Answer Card -->
            <div class="card p-6 border-primary-500/30 shadow-lg shadow-primary-500/10">
                <h3 class="text-sm font-bold text-[var(--text-muted)] uppercase mb-4">إجابة المستخدم</h3>
                
                <?php if ($answer->question_type == 'text'): ?>
                    <div class="bg-[var(--bg-body)] p-6 rounded-xl text-lg leading-relaxed border border-[var(--border-light)]">
                        <?= nl2br(htmlspecialchars($answer->answer_text)) ?>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php 
                        $selectedOptions = $answer->selected_options ?? [];
                        if (is_string($selectedOptions)) {
                            $selectedOptions = json_decode($selectedOptions);
                        }
                        foreach ($selectedOptions as $option): 
                        ?>
                            <div class="flex items-center gap-3 p-4 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                                <i class="fas fa-check-circle text-green-500 text-xl"></i>
                                <span class="text-lg"><?= htmlspecialchars($option) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-6 flex items-center gap-4 pt-6 border-t border-[var(--border-light)]">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-xl font-bold text-[var(--text-muted)]">
                        <?= mb_substr($answer->user_name, 0, 1) ?>
                    </div>
                    <div>
                        <h4 class="font-bold text-[var(--text-main)]"><?= htmlspecialchars($answer->user_name) ?></h4>
                        <p class="text-sm text-[var(--text-muted)]">
                            <i class="fas fa-clock ml-1"></i>
                            <?= date('Y/m/d H:i', strtotime($answer->created_at)) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Form -->
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-6">
                <h3 class="text-lg font-bold text-[var(--text-main)] mb-6">قرار المراجعة</h3>
                
                <form action="<?= route('manager.reviews.review') ?>" method="POST" class="space-y-6">
                    <input type="hidden" name="answer_id" value="<?= $answer->id ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">الحالة</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="approved" class="peer sr-only" checked>
                                <div class="p-4 rounded-xl border-2 border-[var(--border-light)] peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 text-center transition-all">
                                    <i class="fas fa-check-circle text-2xl text-green-500 mb-2 block"></i>
                                    <span class="font-bold text-green-600">قبول</span>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="status" value="rejected" class="peer sr-only">
                                <div class="p-4 rounded-xl border-2 border-[var(--border-light)] peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 text-center transition-all">
                                    <i class="fas fa-times-circle text-2xl text-red-500 mb-2 block"></i>
                                    <span class="font-bold text-red-600">رفض</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="4" class="input-field resize-none" placeholder="أضف ملاحظات للمستخدم..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-save ml-2"></i> حفظ المراجعة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
