<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)] mb-2">إدارة المستخدمين</h1>
            <p class="text-[var(--text-muted)]">إدارة حسابات المستخدمين والصلاحيات</p>
        </div>
    </div>

    <?php if (empty($users)): ?>
        <div class="card p-12 text-center">
            <div class="w-20 h-20 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-6 text-[var(--text-muted)]">
                <i class="fas fa-users text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-[var(--text-main)]">لا يوجد مستخدمين</h2>
            <p class="text-[var(--text-muted)]">لم يتم تسجيل أي مستخدمين في المنصة بعد</p>
        </div>
    <?php else: ?>
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[var(--bg-body)] border-b border-[var(--border-light)]">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الدور</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border-light)]">
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-[var(--bg-body)] transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold">
                                            <?= mb_substr($user->name, 0, 1) ?>
                                        </div>
                                        <div class="font-bold text-[var(--text-main)]"><?= htmlspecialchars($user->name) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-[var(--text-muted)]">
                                    <?= htmlspecialchars($user->email) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($user->role == 'manager'): ?>
                                        <span class="px-3 py-1 text-xs font-bold bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full">
                                            مشرف
                                        </span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full">
                                            مستخدم
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-[var(--text-muted)]">
                                    <?= date('Y/m/d', strtotime($user->created_at)) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?= route('manager.users.show', ['id' => $user->id]) ?>" class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/40 flex items-center justify-center transition-colors" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($user->id != auth()->id()): ?>
                                            <form method="POST" action="<?= route('manager.users.update-role') ?>" class="inline">
                                                <input type="hidden" name="id" value="<?= $user->id ?>">
                                                <input type="hidden" name="role" value="<?= $user->role == 'manager' ? 'user' : 'manager' ?>">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-orange-50 dark:bg-orange-900/20 text-orange-600 hover:bg-orange-100 dark:hover:bg-orange-900/40 flex items-center justify-center transition-colors" title="تغيير الدور" onclick="return confirm('هل أنت متأكد من تغيير دور المستخدم؟')">
                                                    <i class="fas fa-user-shield"></i>
                                                </button>
                                            </form>

                                            <form method="POST" action="<?= route('manager.users.delete') ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                <input type="hidden" name="id" value="<?= $user->id ?>">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/40 flex items-center justify-center transition-colors" title="حذف">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
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
