<div class="space-y-8">
    <div class="flex items-center gap-4">
        <a href="<?= route('manager.dashboard') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)]">بنك الأسئلة</h1>
            <p class="text-[var(--text-muted)]">إدارة وإنشاء الأسئلة العامة بجميع الأنواع</p>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-4 flex items-center gap-2">
                <i class="fas fa-plus-circle text-primary-500"></i>
                إنشاء سؤال جديد
            </h3>
            <form method="POST" action="<?= route('manager.bank.questions.store') ?>" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold mb-1 text-[var(--text-main)]">نص السؤال</label>
                    <textarea name="question_text" rows="4" class="input-field resize-none" required></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-[var(--text-main)]">النوع</label>
                    <select name="question_type" class="input-field" required>
                        <option value="mcq">اختيار من متعدد</option>
                        <option value="true_false">صح / خطأ</option>
                        <option value="open">مفتوح</option>
                        <option value="list">قائمة (عناصر)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-[var(--text-main)]">الفئة</label>
                    <input type="text" name="category" class="input-field" placeholder="مثال: الطعام، التراث">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-[var(--text-main)]">الخيارات (للاختيارات فقط)</label>
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" name="options[]" class="input-field" placeholder="الخيار <?= $i+1 ?>">
                            <label class="flex items-center gap-1 text-sm text-[var(--text-muted)]">
                                <input type="checkbox" name="correct_options[]" value="<?= $i ?>"> صحيح
                            </label>
                        </div>
                    <?php endfor; ?>
                    <p class="text-xs text-[var(--text-muted)]">إذا كان النوع صح/خطأ وبدون خيارات، سيتم إنشاء (صحيح/خطأ) تلقائياً.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-[var(--text-main)]">إجابة نموذجية (للمفتوح/القائمة)</label>
                    <textarea name="answer_text" rows="2" class="input-field resize-none"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-full justify-center">
                    <i class="fas fa-save ml-2"></i> حفظ السؤال
                </button>
            </form>
        </div>

        <div class="card p-6 lg:col-span-2 space-y-4">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div>
                    <h3 class="text-lg font-bold text-[var(--text-main)]">جميع الأسئلة</h3>
                    <p class="text-sm text-[var(--text-muted)]">بحث وتصفية حسب النوع أو الفئة</p>
                </div>
                <a href="<?= route('manager.questions.import') ?>" class="text-sm text-primary-600 hover:text-primary-700 flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> استيراد من ملف
                </a>
            </div>

            <form method="GET" action="<?= strtok(route('manager.bank.questions'), '?') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="route" value="manager.bank.questions">
                <input type="text" name="q" value="<?= htmlspecialchars($searchTerm ?? '') ?>" placeholder="بحث في نص السؤال" class="input-field md:col-span-2">
                <select name="type" class="input-field">
                    <option value="">النوع (الكل)</option>
                    <option value="mcq" <?= ($filterType ?? '') === 'mcq' ? 'selected' : '' ?>>اختيار من متعدد</option>
                    <option value="true_false" <?= ($filterType ?? '') === 'true_false' ? 'selected' : '' ?>>صح / خطأ</option>
                    <option value="open" <?= ($filterType ?? '') === 'open' ? 'selected' : '' ?>>مفتوح</option>
                    <option value="list" <?= ($filterType ?? '') === 'list' ? 'selected' : '' ?>>قائمة</option>
                </select>
                <div class="flex gap-2">
                    <input type="text" name="category" value="<?= htmlspecialchars($filterCategory ?? '') ?>" placeholder="فئة" class="input-field">
                    <button class="btn btn-primary flex items-center justify-center gap-2 w-28"><i class="fas fa-search"></i> تصفية</button>
                </div>
            </form>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="p-3 rounded-xl bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-800 text-center">
                    <div class="text-xs text-[var(--text-muted)]">المجموع</div>
                    <div class="text-xl font-bold text-[var(--text-main)]"><?= count($questions) ?></div>
                </div>
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 text-center">
                    <div class="text-xs text-[var(--text-muted)]">اختيار</div>
                    <div class="text-xl font-bold text-blue-600"><?= $counts['mcq'] ?? 0 ?></div>
                </div>
                <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800 text-center">
                    <div class="text-xs text-[var(--text-muted)]">صح / خطأ</div>
                    <div class="text-xl font-bold text-green-600"><?= $counts['true_false'] ?? 0 ?></div>
                </div>
                <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 text-center">
                    <div class="text-xs text-[var(--text-muted)]">مفتوح/قائمة</div>
                    <div class="text-xl font-bold text-purple-600"><?= ($counts['open'] ?? 0) + ($counts['list'] ?? 0) ?></div>
                </div>
            </div>

            <?php if (empty($questions)): ?>
                <div class="text-center text-[var(--text-muted)] py-12">لا توجد أسئلة مطابقة.</div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($questions as $q): ?>
                        <div class="p-4 border border-[var(--border-light)] rounded-xl hover:border-primary-500 transition-colors bg-[var(--bg-body)] shadow-sm">
                            <div class="flex items-start justify-between gap-3">
                                <div class="space-y-2">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <?php
                                        $typeBadge = match($q->question_type) {
                                            'mcq' => 'bg-blue-100 text-blue-700',
                                            'true_false' => 'bg-green-100 text-green-700',
                                            'open' => 'bg-purple-100 text-purple-700',
                                            'list' => 'bg-yellow-100 text-yellow-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        $typeLabel = match($q->question_type) {
                                            'mcq' => 'اختيار من متعدد',
                                            'true_false' => 'صح/خطأ',
                                            'open' => 'مفتوح',
                                            'list' => 'قائمة',
                                            default => $q->question_type
                                        };
                                        ?>
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $typeBadge ?>"><?= $typeLabel ?></span>
                                        <?php if (!empty($q->category)): ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700"><?= htmlspecialchars($q->category) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[var(--text-main)] font-semibold leading-relaxed"><?= htmlspecialchars($q->question_text) ?></p>
                                </div>
                                <form method="POST" action="<?= route('manager.bank.questions.delete') ?>" onsubmit="return confirm('تأكيد حذف السؤال من البنك؟');">
                                    <input type="hidden" name="id" value="<?= $q->id ?>">
                                    <button type="submit" class="text-red-500 hover:text-red-700 p-2 rounded-lg hover:bg-red-50">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
