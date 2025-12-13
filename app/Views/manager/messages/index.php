<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">الرسائل</h1>
            <p class="text-[var(--text-muted)]">إدارة رسائل التواصل الواردة من المستخدمين</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-300">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($messages)): ?>
        <div class="card p-6 text-center text-[var(--text-muted)]">
            لا توجد رسائل حالياً.
        </div>
    <?php else: ?>
        <div class="card p-0 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--border-light)]">
                    <thead class="bg-[var(--bg-body)]">
                        <tr class="text-right text-sm text-[var(--text-muted)]">
                            <th class="px-4 py-3 font-semibold">الاسم</th>
                            <th class="px-4 py-3 font-semibold">البريد</th>
                            <th class="px-4 py-3 font-semibold">الموضوع</th>
                            <th class="px-4 py-3 font-semibold">الحالة</th>
                            <th class="px-4 py-3 font-semibold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-light)]">
                        <?php foreach ($messages as $msg): ?>
                            <tr class="text-sm">
                                <td class="px-4 py-3 text-[var(--text-main)] font-semibold">
                                    <?= htmlspecialchars($msg->name ?? $msg->user_name ?? 'زائر') ?>
                                </td>
                                <td class="px-4 py-3 text-[var(--text-muted)]">
                                    <?= htmlspecialchars($msg->email ?? '-') ?>
                                </td>
                                <td class="px-4 py-3 text-[var(--text-main)]">
                                    <?= htmlspecialchars($msg->subject) ?>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold
                                        <?= $msg->status === 'resolved' ? 'bg-green-100 text-green-700' : ($msg->status === 'in_progress' ? 'bg-yellow-100 text-yellow-700' : 'bg-orange-100 text-orange-700') ?>">
                                        <?= $msg->status === 'resolved' ? 'مغلقة' : ($msg->status === 'in_progress' ? 'قيد المعالجة' : 'قيد الانتظار') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="<?= route('manager.messages.show', ['id' => $msg->id]) ?>" class="btn btn-ghost text-sm px-3 py-2">
                                            <i class="fas fa-eye ml-1"></i> عرض
                                        </a>
                                        <form method="POST" action="<?= route('manager.messages.delete') ?>" onsubmit="return confirm('تأكيد حذف الرسالة؟');">
                                            <input type="hidden" name="id" value="<?= $msg->id ?>">
                                            <button type="submit" class="btn btn-ghost text-sm px-3 py-2 text-red-600 hover:text-red-700">
                                                <i class="fas fa-trash ml-1"></i> حذف
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
