<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-[var(--text-main)] mb-2">لوحة تحكم المشرف</h1>
            <p class="text-[var(--text-muted)]">نظرة عامة على أداء المنصة والإحصائيات</p>
        </div>
        <div class="flex gap-3">
            <span class="px-4 py-2 rounded-xl bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 font-medium border border-primary-100 dark:border-primary-800">
                <i class="fas fa-calendar-alt ml-2"></i>
                <?= date('Y/m/d') ?>
            </span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Projects Card -->
        <div class="card p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xl">
                        <i class="fas fa-book"></i>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> 12%
                    </span>
                </div>
                <h3 class="text-[var(--text-muted)] text-sm font-medium mb-1">إجمالي المشاريع</h3>
                <p class="text-3xl font-bold text-[var(--text-main)]"><?= $totalProjects ?></p>
            </div>
        </div>

        <!-- Users Card -->
        <div class="card p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl">
                        <i class="fas fa-users"></i>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> 8%
                    </span>
                </div>
                <h3 class="text-[var(--text-muted)] text-sm font-medium mb-1">المستخدمون النشطون</h3>
                <p class="text-3xl font-bold text-[var(--text-main)]"><?= $totalUsers ?></p>
            </div>
        </div>

        <!-- Answers Card -->
        <div class="card p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center text-xl">
                        <i class="fas fa-pen-fancy"></i>
                    </div>
                    <span class="text-xs font-bold text-green-500 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded-lg flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> 24%
                    </span>
                </div>
                <h3 class="text-[var(--text-muted)] text-sm font-medium mb-1">إجمالي الإجابات</h3>
                <p class="text-3xl font-bold text-[var(--text-main)]"><?= $totalAnswers ?></p>
            </div>
        </div>

        <!-- Messages Card -->
        <div class="card p-6 relative overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-500/10 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center text-xl">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <?php if ($pendingMessages > 0): ?>
                        <span class="text-xs font-bold text-orange-500 bg-orange-100 dark:bg-orange-900/30 px-2 py-1 rounded-lg flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i> جديد
                        </span>
                    <?php endif; ?>
                </div>
                <h3 class="text-[var(--text-muted)] text-sm font-medium mb-1">رسائل معلقة</h3>
                <p class="text-3xl font-bold text-[var(--text-main)]"><?= $pendingMessages ?></p>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
                    <i class="fas fa-chart-line text-primary-500"></i>
                    نشاط المساهمات
                </h3>
                <select class="text-sm bg-[var(--bg-body)] border border-[var(--border-light)] rounded-lg px-3 py-1 outline-none">
                    <option>آخر 7 أيام</option>
                    <option>آخر 30 يوم</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="commitsChart"></canvas>
            </div>
        </div>

        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-500"></i>
                    توزيع المشاريع
                </h3>
                <button class="text-sm text-[var(--text-muted)] hover:text-primary-600">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
            </div>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoriesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Unreviewed Answers -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-[var(--text-main)] flex items-center gap-2">
                    <i class="fas fa-tasks text-purple-500"></i>
                    إجابات تحتاج مراجعة
                </h2>
                <a href="<?= route('manager.reviews') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">عرض الكل</a>
            </div>
            
            <?php if (empty($unreviewedAnswers)): ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-4 text-[var(--text-muted)]">
                        <i class="fas fa-check text-2xl"></i>
                    </div>
                    <p class="text-[var(--text-muted)]">لا توجد إجابات تحتاج مراجعة</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($unreviewedAnswers, 0, 5) as $answer): ?>
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-[var(--bg-body)] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border border-[var(--border-light)]">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                                <?= mb_substr($answer->user_name ?? 'U', 0, 1) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-[var(--text-main)] truncate"><?= htmlspecialchars($answer->user_name ?? 'مستخدم') ?></h4>
                                <p class="text-sm text-[var(--text-muted)] truncate"><?= htmlspecialchars($answer->question_text ?? '') ?></p>
                            </div>
                            <a href="<?= route('manager.reviews.show', ['id' => $answer->id]) ?>" class="btn btn-sm bg-white dark:bg-gray-800 border border-[var(--border-light)] hover:border-primary-500 text-[var(--text-muted)] hover:text-primary-600 shadow-sm">
                                مراجعة
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Messages -->
        <div class="card p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-[var(--text-main)] flex items-center gap-2">
                    <i class="fas fa-inbox text-orange-500"></i>
                    الرسائل المعلقة
                </h2>
                <a href="<?= route('manager.messages') ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium">عرض الكل</a>
            </div>
            
            <?php if (empty($pendingMessagesData)): ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-[var(--bg-body)] rounded-full flex items-center justify-center mx-auto mb-4 text-[var(--text-muted)]">
                        <i class="fas fa-envelope-open text-2xl"></i>
                    </div>
                    <p class="text-[var(--text-muted)]">لا توجد رسائل معلقة</p>
                </div>
            <?php else: ?>
                <div class="space-y-4">
                    <?php foreach (array_slice($pendingMessagesData, 0, 5) as $message): ?>
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-[var(--bg-body)] hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors border border-[var(--border-light)]">
                            <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-[var(--text-main)] truncate"><?= htmlspecialchars($message->name ?? $message->user_name ?? 'زائر') ?></h4>
                                <p class="text-sm text-[var(--text-muted)] truncate"><?= htmlspecialchars($message->subject) ?></p>
                            </div>
                            <a href="<?= route('manager.messages.show', ['id' => $message->id]) ?>" class="btn btn-sm bg-white dark:bg-gray-800 border border-[var(--border-light)] hover:border-orange-500 text-[var(--text-muted)] hover:text-orange-600 shadow-sm">
                                عرض
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <a href="<?= route('manager.projects.create') ?>" class="card p-6 hover:border-primary-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-xl shadow-lg shadow-primary-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">إنشاء مشروع</h3>
                <p class="text-sm text-[var(--text-muted)]">إضافة مشروع جديد للمنصة</p>
            </div>
        </a>

        <a href="<?= route('manager.projects') ?>" class="card p-6 hover:border-blue-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-layer-group"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">إدارة المشاريع</h3>
                <p class="text-sm text-[var(--text-muted)]">تعديل وحذف المشاريع</p>
            </div>
        </a>

        <a href="<?= route('manager.users') ?>" class="card p-6 hover:border-green-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-700 text-white flex items-center justify-center text-xl shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-users-cog"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">إدارة المستخدمين</h3>
                <p class="text-sm text-[var(--text-muted)]">التحكم في الصلاحيات</p>
            </div>
        </a>

        <a href="<?= route('manager.questions.import') ?>" class="card p-6 hover:border-purple-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-purple-700 text-white flex items-center justify-center text-xl shadow-lg shadow-purple-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">استيراد بنك الأسئلة</h3>
                <p class="text-sm text-[var(--text-muted)]">رفع ملف JSON وحفظه في بنك الأسئلة أو ربطه بمشروع</p>
            </div>
        </a>

        <a href="<?= route('manager.bank.questions') ?>" class="card p-6 hover:border-green-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-green-700 text-white flex items-center justify-center text-xl shadow-lg shadow-green-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">إدارة بنك الأسئلة</h3>
                <p class="text-sm text-[var(--text-muted)]">عرض، إضافة، حذف الأسئلة المخزنة في البنك</p>
            </div>
        </a>

        <?php if ($totalProjects == 0): ?>
        <a href="<?= route('manager.projects.import-data') ?>" class="card p-6 hover:border-blue-500 group transition-all duration-300 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-xl shadow-lg shadow-blue-500/30 group-hover:scale-110 transition-transform">
                <i class="fas fa-cloud-download-alt"></i>
            </div>
            <div>
                <h3 class="font-bold text-[var(--text-main)]">استيراد المشاريع الجاهزة</h3>
                <p class="text-sm text-[var(--text-muted)]">إضافة المشاريع والأسئلة من ملف البيانات مرة واحدة</p>
            </div>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Integration -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Common Chart Options
    Chart.defaults.color = '#64748b';
    Chart.defaults.font.family = "'IBM Plex Sans Arabic', sans-serif";
    
    // Commits Chart
    const commitsCtx = document.getElementById('commitsChart').getContext('2d');
    const commitsData = <?= json_encode($commitStats) ?>;
    
    // Create gradient
    const gradient = commitsCtx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(20, 184, 166, 0.2)');
    gradient.addColorStop(1, 'rgba(20, 184, 166, 0)');

    new Chart(commitsCtx, {
        type: 'line',
        data: {
            labels: commitsData.map(d => d.date),
            datasets: [{
                label: 'المساهمات',
                data: commitsData.map(d => d.count),
                borderColor: '#14b8a6',
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#14b8a6',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(148, 163, 184, 0.1)' },
                    ticks: { stepSize: 1 }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // Categories Chart
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    const categoriesData = <?= json_encode($projectCategories) ?>;
    
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: {
            labels: categoriesData.map(d => d.category),
            datasets: [{
                data: categoriesData.map(d => d.count),
                backgroundColor: [
                    '#14b8a6', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6'
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                }
            }
        }
    });
</script>
