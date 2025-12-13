<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex items-center gap-4">
        <a href="<?= route('manager.projects') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)]">استيراد بنك الأسئلة</h1>
            <p class="text-[var(--text-muted)]">ارفع ملف JSON يحتوي على أسئلة بمختلف الأنواع لإضافتها إلى مشروع أو بنك الأسئلة العام.</p>
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
        <div class="lg:col-span-2 card p-6">
            <form method="POST" action="<?= route('manager.questions.import.store') ?>" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-main)] mb-2">اختيار المشروع</label>
                    <select name="project_id" class="input-field">
                        <option value="bank">بنك الأسئلة العام (ينشأ تلقائياً)</option>
                        <?php foreach ($projects as $p): ?>
                            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[var(--text-main)] mb-2">ملف الأسئلة (JSON)</label>
                    <input
                        type="file"
                        name="file"
                        accept=".json,application/json"
                        required
                        class="block w-full text-sm text-[var(--text-muted)]
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-full file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary-50 file:text-primary-700
                            hover:file:bg-primary-100
                            dark:file:bg-primary-900/20 dark:file:text-primary-400"
                    >
                    <p class="text-xs text-[var(--text-muted)] mt-2">الحد الأقصى الموصى به: 5MB.</p>
                </div>

                <div class="bg-[var(--bg-body)] border border-[var(--border-light)] rounded-xl p-4">
                    <h3 class="font-semibold text-[var(--text-main)] mb-2">صيغة JSON المتوقعة</h3>
                    <pre class="text-xs bg-gray-900 text-gray-100 p-3 rounded-lg overflow-auto">
[
  {
    "question": "نص السؤال",
    "type": "mcq | true_false | open | list",
    "category": "الفئة",
    "options": ["خيار 1", "خيار 2"], // لـ MCQ/TrueFalse
    "answer": 0 // رقم الخيار الصحيح أو نص الإجابة
  }
]</pre>
                    <p class="text-xs text-[var(--text-muted)] mt-2">إذا كان النوع true_false ولم توجد خيارات، سيتم إنشاء (صحيح/خطأ) تلقائياً.</p>
                </div>

                <button type="submit" class="btn btn-primary w-full justify-center">
                    <i class="fas fa-cloud-upload-alt ml-2"></i>
                    رفع واستيراد الأسئلة
                </button>
            </form>
        </div>

        <div class="card p-6 space-y-4 bg-gradient-to-br from-primary-50 to-white dark:from-gray-900 dark:to-gray-800">
            <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
                <i class="fas fa-info-circle text-primary-600"></i>
                نصائح سريعة
            </h3>
            <ul class="list-disc pr-5 text-sm text-[var(--text-muted)] space-y-2">
                <li>تأكد من نوع السؤال: mcq، true_false، open، list.</li>
                <li>للـ MCQ أرفق مصفوفة خيارات، وضع رقم الإجابة في الحقل answer.</li>
                <li>للأسئلة المفتوحة أو القوائم، يمكنك وضع إجابة نموذجية في answer (اختياري).</li>
                <li>يمكنك استيراد إلى مشروع محدد أو إلى بنك الأسئلة العام لاستخدامه لاحقاً.</li>
            </ul>
        </div>
    </div>
</div>
