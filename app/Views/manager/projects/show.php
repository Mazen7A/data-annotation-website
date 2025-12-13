<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-4">
            <a href="<?= route('manager.projects') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-[var(--text-main)] mb-1"><?= htmlspecialchars($project->name) ?></h1>
                <div class="flex items-center gap-3 text-sm text-[var(--text-muted)]">
                    <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg">
                        <?= htmlspecialchars($project->category) ?>
                    </span>
                    <span><i class="fas fa-calendar-alt ml-1"></i> تم الإنشاء: <?= date('Y/m/d', strtotime($project->created_at)) ?></span>
                </div>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="<?= route('manager.projects.edit', ['id' => $project->id]) ?>" class="btn btn-outline">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="<?= route('projects.show', ['id' => $project->id]) ?>" target="_blank" class="btn btn-primary">
                <i class="fas fa-external-link-alt"></i> عرض في الموقع
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                    <i class="fas fa-question-circle"></i>
                </div>
                <span class="text-[var(--text-muted)] font-medium">الأسئلة</span>
            </div>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= $stats['total_questions'] ?></p>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-lg bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center">
                    <i class="fas fa-pen"></i>
                </div>
                <span class="text-[var(--text-muted)] font-medium">الإجابات</span>
            </div>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= $stats['total_answers'] ?></p>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-[var(--text-muted)] font-medium">المشاركون</span>
            </div>
            <p class="text-3xl font-bold text-[var(--text-main)]"><?= $stats['total_participants'] ?></p>
        </div>

        <div class="card p-6">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                    <i class="fas fa-percentage"></i>
                </div>
                <span class="text-[var(--text-muted)] font-medium">نسبة الإكمال</span>
            </div>
            <p class="text-3xl font-bold text-[var(--text-main)]">
                <?= $stats['total_questions'] > 0 ? round(($stats['total_answers'] / ($stats['total_questions'] * max(1, $stats['total_participants']))) * 100) : 0 ?>%
            </p>
        </div>
    </div>

    <!-- Charts and Categories Section -->
    <div class="card p-6 bg-gradient-to-br from-primary-50 to-white dark:from-gray-900 dark:to-gray-800 border border-[var(--border-light)]">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
                    <i class="fas fa-layer-group text-primary-500"></i>
                    توزيع الأنواع
                </h3>
                <p class="text-sm text-[var(--text-muted)]">عدد الأسئلة حسب نوعها داخل المشروع</p>
            </div>
            <div class="text-xs px-3 py-1 rounded-full bg-primary-100 text-primary-700 border border-primary-200">
                إجمالي <?= $stats['total_questions'] ?> سؤال
            </div>
        </div>
        <?php
            $typeStats = $stats['type_stats'] ?? [];
            $typeMeta = [
                'mcq' => ['label' => 'اختيار من متعدد', 'icon' => 'list-ul', 'color' => 'text-blue-600', 'bar' => 'bg-blue-500'],
                'true_false' => ['label' => 'صح / خطأ', 'icon' => 'check-double', 'color' => 'text-green-600', 'bar' => 'bg-green-500'],
                'open' => ['label' => 'إجابة مفتوحة', 'icon' => 'pen', 'color' => 'text-purple-600', 'bar' => 'bg-purple-500'],
                'list' => ['label' => 'قائمة', 'icon' => 'stream', 'color' => 'text-amber-600', 'bar' => 'bg-amber-500'],
            ];
            $maxCount = max(1, max($typeStats ?: [0]));
        ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($typeMeta as $key => $meta): 
                $count = $typeStats[$key] ?? 0;
                $percent = $stats['total_questions'] > 0 ? round(($count / $stats['total_questions']) * 100) : 0;
            ?>
            <div class="p-4 rounded-2xl bg-white/80 dark:bg-gray-900/60 border border-[var(--border-light)] shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center <?= $meta['color'] ?>">
                            <i class="fas fa-<?= $meta['icon'] ?>"></i>
                        </div>
                        <div>
                            <p class="text-sm text-[var(--text-muted)]"><?= $meta['label'] ?></p>
                            <p class="text-xl font-bold text-[var(--text-main)]"><?= $count ?> سؤال</p>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-[var(--text-muted)]"><?= $percent ?>%</span>
                </div>
                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                    <div class="h-2 <?= $meta['bar'] ?> rounded-full" style="width: <?= $percent ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Progress Chart -->
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-6 flex items-center gap-2">
                <i class="fas fa-chart-area text-primary-500"></i>
                تقدم المشاركين
            </h3>
            <div class="h-64">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Category Stats -->
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-6 flex items-center gap-2">
                <i class="fas fa-tags text-purple-500"></i>
                توزيع الأسئلة حسب الفئة
            </h3>
            <?php if (empty($stats['category_stats'])): ?>
                <div class="flex flex-col items-center justify-center h-64 text-[var(--text-muted)]">
                    <i class="fas fa-folder-open text-4xl mb-3 opacity-50"></i>
                    <p>لا توجد تصنيفات</p>
                </div>
            <?php else: ?>
                <div class="space-y-4 h-64 overflow-y-auto pr-2 custom-scrollbar">
                    <?php foreach ($stats['category_stats'] as $catStat): ?>
                        <div class="flex items-center justify-between p-3 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)]">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                    <i class="fas fa-tag text-sm"></i>
                                </div>
                                <span class="font-medium text-[var(--text-main)]">
                                    <?= htmlspecialchars($catStat->category ?: 'عام') ?>
                                </span>
                            </div>
                            <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-bold">
                                <?= $catStat->count ?> سؤال
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Activity Chart -->
        <div class="card p-6 lg:col-span-1">
            <h3 class="text-lg font-bold text-[var(--text-main)] mb-6 flex items-center gap-2">
                <i class="fas fa-chart-bar text-blue-500"></i>
                نشاط الإجابات (آخر 7 أيام)
            </h3>
            <div class="h-64">
                <canvas id="activityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b border-[var(--border-light)] flex justify-between items-center">
            <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
                <i class="fas fa-history text-gray-500"></i>
                سجل النشاطات
            </h3>
        </div>
        <?php if (empty($commits)): ?>
            <div class="p-8 text-center text-[var(--text-muted)]">
                <i class="fas fa-clock text-4xl mb-3 opacity-50"></i>
                <p>لا يوجد نشاط مسجل مؤخراً</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-[var(--border-light)]">
                <?php foreach ($commits as $commit): ?>
                    <div class="p-4 hover:bg-[var(--bg-body)] transition-colors flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-[var(--text-muted)]">
                            <i class="fas fa-code-branch"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-medium text-[var(--text-main)]"><?= htmlspecialchars($commit->message) ?></p>
                            <p class="text-sm text-[var(--text-muted)]">
                                <i class="fas fa-user ml-1"></i> <?= htmlspecialchars($commit->user_name) ?>
                                <span class="mx-2">•</span>
                                <i class="fas fa-clock ml-1"></i> <?= date('Y/m/d H:i', strtotime($commit->created_at)) ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    Chart.defaults.color = '#64748b';
    Chart.defaults.font.family = "'IBM Plex Sans Arabic', sans-serif";

    // Progress Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    new Chart(progressCtx, {
        type: 'doughnut',
        data: {
            labels: ['مكتمل', 'قيد التقدم', 'لم يبدأ'],
            datasets: [{
                data: [30, 45, 25],
                backgroundColor: ['#10b981', '#3b82f6', '#e2e8f0'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Activity Chart
    const activityCtx = document.getElementById('activityChart').getContext('2d');
    new Chart(activityCtx, {
        type: 'bar',
        data: {
            labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
            datasets: [{
                label: 'إجابات جديدة',
                data: [12, 19, 3, 5, 2, 3, 15],
                backgroundColor: '#14b8a6',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<!-- Project Location Map -->
<?php if (!empty($project->latitude) && !empty($project->longitude)): ?>
<div class="card overflow-hidden mb-8">
    <div class="p-6 border-b border-[var(--border-light)]">
        <h3 class="text-lg font-bold text-[var(--text-main)] flex items-center gap-2">
            <i class="fas fa-map-marked-alt text-red-500"></i>
            موقع المشروع
        </h3>
    </div>
    <div class="p-6">
        <div id="manager-project-map" class="w-full h-96 rounded-xl overflow-hidden border border-[var(--border-light)]"></div>
        <div class="mt-3 p-3 bg-[var(--bg-body)] rounded-lg border border-[var(--border-light)]">
            <p class="text-sm text-[var(--text-main)] flex items-center gap-2">
                <i class="fas fa-location-dot text-primary-500"></i>
                <span><?= htmlspecialchars($project->location_name) ?></span>
            </p>
            <p class="text-xs text-[var(--text-muted)] mt-1">
                <i class="fas fa-compass ml-1"></i> <?= number_format($project->latitude, 4) ?>°, 
                <?= number_format($project->longitude, 4) ?>°
            </p>
        </div>
    </div>
</div>

<script>
// Initialize manager project location map
const managerProjectLat = <?= $project->latitude ?>;
const managerProjectLng = <?= $project->longitude ?>;

const managerProjectMap = L.map('manager-project-map').setView([managerProjectLat, managerProjectLng], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
}).addTo(managerProjectMap);

const managerMarker = L.marker([managerProjectLat, managerProjectLng]).addTo(managerProjectMap);

managerMarker.bindPopup(`
    <div class="text-center p-2">
        <strong class="text-primary-600"><?= htmlspecialchars($project->name) ?></strong><br>
        <span class="text-sm text-gray-600"><?= htmlspecialchars($project->location_name) ?></span>
    </div>
`).openPopup();
</script>
<?php endif; ?>
