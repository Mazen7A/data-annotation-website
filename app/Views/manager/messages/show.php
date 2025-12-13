<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= route('manager.dashboard') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">تفاصيل الرسالة</h1>
            <p class="text-[var(--text-muted)]">عرض محتوى رسالة التواصل</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Message Content -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-8">
                <div class="flex items-center justify-between mb-8 pb-6 border-b border-[var(--border-light)]">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center text-2xl font-bold shadow-lg shadow-orange-500/30">
                            <?= mb_substr($message->name, 0, 1) ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[var(--text-main)]"><?= htmlspecialchars($message->name) ?></h2>
                            <a href="mailto:<?= htmlspecialchars($message->email) ?>" class="text-[var(--text-muted)] hover:text-primary-600 flex items-center gap-2">
                                <i class="fas fa-envelope"></i>
                                <?= htmlspecialchars($message->email) ?>
                            </a>
                        </div>
                    </div>
                    <div class="text-left">
                        <span class="block text-sm text-[var(--text-muted)] mb-1">تاريخ الإرسال</span>
                        <span class="font-bold text-[var(--text-main)] bg-[var(--bg-body)] px-3 py-1 rounded-lg border border-[var(--border-light)]">
                            <?= date('Y/m/d H:i', strtotime($message->created_at)) ?>
                        </span>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-sm font-bold text-[var(--text-muted)] uppercase mb-3">الموضوع</h3>
                    <p class="text-xl font-bold text-[var(--text-main)]"><?= htmlspecialchars($message->subject) ?></p>
                </div>

                <div>
                    <h3 class="text-sm font-bold text-[var(--text-muted)] uppercase mb-3">نص الرسالة</h3>
                    <div class="bg-[var(--bg-body)] p-6 rounded-xl text-lg leading-relaxed border border-[var(--border-light)] whitespace-pre-wrap">
                        <?= htmlspecialchars($message->message) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-6">
                <h3 class="text-lg font-bold text-[var(--text-main)] mb-6">إجراءات</h3>
                
                <div class="space-y-4">
                    <a href="mailto:<?= htmlspecialchars($message->email) ?>?subject=رد: <?= urlencode($message->subject) ?>" class="btn btn-primary w-full">
                        <i class="fas fa-reply ml-2"></i> رد عبر البريد
                    </a>

                    <form action="<?= route('manager.messages.delete') ?>" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                        <input type="hidden" name="id" value="<?= $message->id ?>">
                        <button type="submit" class="btn w-full bg-red-50 dark:bg-red-900/20 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40 border border-transparent hover:border-red-200 dark:hover:border-red-800">
                            <i class="fas fa-trash-alt ml-2"></i> حذف الرسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
