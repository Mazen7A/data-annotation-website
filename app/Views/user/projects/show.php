<div class="min-h-screen py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-7xl mx-auto">
            <!-- Project Header -->
            <div class="card overflow-hidden mb-8 animate-fade-scale">
                <div class="relative h-64 md:h-96 overflow-hidden">
                    <img 
                        src="<?= $project->image_url ?? asset('images/default-project.jpg') ?>" 
                        alt="<?= htmlspecialchars($project->name) ?>" 
                        class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-700"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
                    <div class="absolute bottom-0 right-0 p-6 md:p-10 text-white w-full">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <div class="px-4 py-1.5 rounded-full text-sm font-bold bg-primary-600/90 backdrop-blur-md shadow-lg border border-primary-500/30">
                                <?= htmlspecialchars($project->category ?? 'عام') ?>
                            </div>
                            <?php if ($session): ?>
                                <div class="px-4 py-1.5 rounded-full text-sm font-bold backdrop-blur-md shadow-lg border <?= $session->status == 'completed' ? 'bg-green-500/20 border-green-400 text-green-300' : 'bg-yellow-500/20 border-yellow-400 text-yellow-300' ?>">
                                    <?= $session->status == 'completed' ? '✅ مكتمل' : '⏳ قيد التقدم' ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <h1 class="text-4xl md:text-5xl font-bold mb-3 leading-tight"><?= htmlspecialchars($project->name) ?></h1>
                        <p class="text-lg text-gray-200 max-w-2xl line-clamp-2">
                            <?= htmlspecialchars($project->summary ?? '') ?>
                        </p>
                    </div>
                </div>
                
                <div class="p-6 md:p-10">
                    <!-- Action Bar -->
                    <?php if ($session && $session->status != 'completed'): ?>
                        <div class="flex flex-col md:flex-row gap-4 mb-10">
                            <a href="<?= route('questions', ['session_id' => $session->id]) ?>" class="btn btn-primary flex-1 py-4 text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                                <i class="fas fa-play-circle text-2xl"></i>
                                <span>متابعة العمل</span>
                            </a>
                            

                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        <!-- Main Content -->
                        <div class="lg:col-span-2 space-y-10">
                            <!-- About Section -->
                            <div>
                                <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 text-[var(--text-main)]">
                                    <span class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center text-lg">
                                        <i class="fas fa-info"></i>
                                    </span>
                                    نبذة عن المشروع
                                </h3>
                                <div class="card p-6 leading-relaxed text-lg text-[var(--text-muted)]">
                                    <?= nl2br(htmlspecialchars($project->description ?? $project->summary)) ?>
                                </div>
                            </div>

                            <!-- Comments Section -->
                            <div>
                                <h3 class="text-2xl font-bold mb-6 flex items-center gap-3 text-[var(--text-main)]">
                                    <span class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-lg">
                                        <i class="fas fa-comments"></i>
                                    </span>
                                    التعليقات (<?= count($comments) ?>)
                                </h3>
                                
                                <!-- Add Comment Form -->
                                <?php if (auth()->check()): ?>
                                    <form method="POST" action="<?= route('projects.comment') ?>" class="card p-6 mb-6">
                                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                        <textarea 
                                            name="comment" 
                                            rows="3" 
                                            class="input-field mb-4 resize-none" 
                                            placeholder="شارك رأيك أو تعليقك حول المشروع..."
                                            required
                                        ></textarea>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i>
                                            <span>إضافة تعليق</span>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Comments List -->
                                <div class="space-y-4">
                                    <?php if (empty($comments)): ?>
                                        <div class="card p-8 text-center text-[var(--text-muted)]">
                                            <i class="fas fa-comment-slash text-4xl mb-3 opacity-50"></i>
                                            <p>لا توجد تعليقات حتى الآن. كن أول من يعلق!</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($comments as $comment): ?>
                                            <div class="card p-6 hover:shadow-lg transition-all">
                                                <div class="flex items-start gap-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                                            <?= mb_substr($comment->user_name, 0, 1) ?>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center justify-between mb-2">
                                                            <h4 class="font-bold text-[var(--text-main)]"><?= htmlspecialchars($comment->user_name) ?></h4>
                                                            <div class="flex items-center gap-2">
                                                                <span class="text-xs text-[var(--text-muted)] bg-[var(--bg-body)] px-3 py-1 rounded-full">
                                                                    <?= date('Y/m/d H:i', strtotime($comment->created_at)) ?>
                                                                </span>
                                                                <?php if (auth()->check() && auth()->id() == $comment->user_id): ?>
                                                                    <form method="POST" action="<?= route('projects.comment.delete') ?>" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعليق؟')">
                                                                        <input type="hidden" name="comment_id" value="<?= $comment->id ?>">
                                                                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                                                        <button type="submit" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 transition-colors p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" title="حذف التعليق">
                                                                            <i class="fas fa-trash-alt"></i>
                                                                        </button>
                                                                    </form>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <p class="text-[var(--text-muted)] leading-relaxed">
                                                            <?= nl2br(htmlspecialchars($comment->comment)) ?>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                        <!-- Sidebar Stats -->
                        <div class="space-y-6">
                            <div class="card p-6 sticky top-24">
                                <h3 class="font-bold text-[var(--text-main)] mb-6 text-lg">إحصائيات المشروع</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-[var(--bg-body)] rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                                <i class="fas fa-question-circle"></i>
                                            </div>
                                            <span class="text-[var(--text-muted)]">عدد الأسئلة</span>
                                        </div>
                                        <span class="font-bold text-xl text-[var(--text-main)]"><?= $project->total_questions ?? 0 ?></span>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-[var(--bg-body)] rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                            <span class="text-[var(--text-muted)]">الوقت المقدر</span>
                                        </div>
                                        <span class="font-bold text-xl text-[var(--text-main)]">15-20 د</span>
                                    </div>

                                    <div class="flex items-center justify-between p-4 bg-[var(--bg-body)] rounded-xl">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <span class="text-[var(--text-muted)]">المساهمين</span>
                                        </div>
                                        <span class="font-bold text-xl text-[var(--text-main)]">+100</span>
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($answerStats['total'])): ?>
                                <div class="card p-6">
                                    <h3 class="font-bold text-[var(--text-main)] mb-4 text-lg flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500"></i>
                                        نتائج إجاباتك
                                    </h3>
                                    <div class="grid grid-cols-3 gap-3 text-center">
                                        <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800">
                                            <div class="text-2xl font-bold text-green-600"><?= $answerStats['correct'] ?></div>
                                            <div class="text-xs text-[var(--text-muted)]">صحيحة</div>
                                        </div>
                                        <div class="p-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                                            <div class="text-2xl font-bold text-red-600"><?= $answerStats['incorrect'] ?></div>
                                            <div class="text-xs text-[var(--text-muted)]">خاطئة</div>
                                        </div>
                                        <div class="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800">
                                            <div class="text-2xl font-bold text-yellow-600"><?= $answerStats['pending'] ?></div>
                                            <div class="text-xs text-[var(--text-muted)]">قيد المراجعة</div>
                                        </div>
                                    </div>
                                    <?php
                                        $gradePercent = $answerStats['total'] > 0 ? round(($answerStats['correct'] / $answerStats['total']) * 100) : 0;
                                    ?>
                                    <div class="mt-4 p-4 bg-gradient-to-r from-primary-50 to-white dark:from-primary-900/20 dark:to-gray-900 rounded-xl border border-primary-100 dark:border-primary-800 text-center">
                                        <div class="text-sm text-[var(--text-muted)] mb-1">تقديرك الحالي</div>
                                        <div class="text-3xl font-bold text-primary-600"><?= $gradePercent ?>%</div>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-3 text-center">المفتوحة/القوائم تعتبر قيد المراجعة.</p>
                                </div>

                                <div class="card p-6">
                                    <h3 class="font-bold text-[var(--text-main)] mb-4 text-lg flex items-center gap-2">
                                        <i class="fas fa-list-check text-primary-500"></i>
                                        آخر إجاباتك
                                    </h3>
                                    <div class="space-y-4">
                                        <?php foreach (array_slice($answersWithStatus, 0, 5) as $ans): ?>
                                            <div class="p-4 rounded-xl border border-[var(--border-light)] bg-[var(--bg-body)] hover:border-primary-500 transition-colors">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-sm font-bold text-[var(--text-main)] line-clamp-1"><?= htmlspecialchars($ans->question_text) ?></span>
                                                    <?php
                                                        $badgeClass = $ans->status['type'] === 'correct' ? 'bg-green-100 text-green-700' : ($ans->status['type'] === 'incorrect' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                                    ?>
                                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $badgeClass ?>">
                                                        <?= $ans->status['label'] ?>
                                                    </span>
                                                </div>
                                                <?php if (!empty($ans->selected_options) && in_array($ans->question_type, ['mcq','true_false'])): ?>
                                                    <p class="text-xs text-[var(--text-muted)]">إجابتك: <?= implode(', ', $ans->selected_options) ?></p>
                                                <?php elseif (!empty($ans->answer_text)): ?>
                                                    <p class="text-xs text-[var(--text-muted)] line-clamp-2">إجابتك: <?= htmlspecialchars($ans->answer_text) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Category Distribution -->
                            <?php if (!empty($typeStats)): ?>
                                <div class="card p-6">
                                    <h3 class="font-bold text-[var(--text-main)] mb-6 text-lg flex items-center gap-2">
                                        <i class="fas fa-layer-group text-primary-500"></i>
                                        أنواع الأسئلة
                                    </h3>
                                    <?php
                                        $typeMeta = [
                                            'mcq' => ['label' => 'اختيار من متعدد', 'icon' => 'list-ul', 'color' => 'bg-blue-100 text-blue-700'],
                                            'true_false' => ['label' => 'صح / خطأ', 'icon' => 'check-double', 'color' => 'bg-green-100 text-green-700'],
                                            'open' => ['label' => 'مفتوحة', 'icon' => 'pen', 'color' => 'bg-purple-100 text-purple-700'],
                                            'list' => ['label' => 'قوائم', 'icon' => 'stream', 'color' => 'bg-amber-100 text-amber-700'],
                                        ];
                                        $totalTypes = array_sum($typeStats);
                                    ?>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <?php foreach ($typeMeta as $key => $meta): 
                                            $count = $typeStats[$key] ?? 0;
                                            if ($count === 0) continue;
                                            $percent = $totalTypes > 0 ? round(($count / $totalTypes) * 100) : 0;
                                        ?>
                                            <div class="p-4 rounded-xl border border-[var(--border-light)] bg-[var(--bg-body)] hover:border-primary-500 transition-colors">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center gap-3">
                                                        <span class="w-10 h-10 rounded-xl flex items-center justify-center <?= $meta['color'] ?>">
                                                            <i class="fas fa-<?= $meta['icon'] ?>"></i>
                                                        </span>
                                                        <div>
                                                            <p class="font-bold text-[var(--text-main)] text-sm"><?= $meta['label'] ?></p>
                                                            <p class="text-xs text-[var(--text-muted)]"><?= $count ?> سؤال</p>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-bold text-[var(--text-muted)]"><?= $percent ?>%</span>
                                                </div>
                                                <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 overflow-hidden">
                                                    <div class="h-2 bg-primary-500 rounded-full" style="width: <?= $percent ?>%"></div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($categoryStats)): ?>
                                <div class="card p-6">
                                    <h3 class="font-bold text-[var(--text-main)] mb-6 text-lg flex items-center gap-2">
                                        <i class="fas fa-tags text-purple-500"></i>
                                        توزيع الأسئلة
                                    </h3>
                                    <div class="space-y-3">
                                        <?php foreach ($categoryStats as $catStat): ?>
                                            <div class="flex items-center justify-between p-3 bg-[var(--bg-body)] rounded-xl border border-[var(--border-light)] hover:border-primary-500 transition-colors">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white flex items-center justify-center text-xs shadow-md">
                                                        <i class="fas fa-tag"></i>
                                                    </div>
                                                    <span class="font-medium text-[var(--text-main)] text-sm">
                                                        <?= htmlspecialchars($catStat->category ?: 'عام') ?>
                                                    </span>
                                                </div>
                                                <span class="px-2.5 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-lg text-xs font-bold">
                                                    <?= $catStat->count ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Project Location Map -->
                            <?php if (!empty($project->latitude) && !empty($project->longitude)): ?>
                                <div class="card p-6">
                                    <h3 class="font-bold text-[var(--text-main)] mb-6 text-lg flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                        موقع المشروع
                                    </h3>
                                    <div id="project-map" class="w-full h-80 rounded-xl overflow-hidden border border-[var(--border-light)]"></div>
                                    <div class="mt-3 p-3 bg-[var(--bg-body)] rounded-lg border border-[var(--border-light)]">
                                        <p class="text-sm text-[var(--text-main)] flex items-center gap-2">
                                            <i class="fas fa-location-dot text-primary-500"></i>
                                            <span><?= htmlspecialchars($project->location_name) ?></span>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!$session): ?>
                                <div class="card p-6 bg-gradient-to-br from-primary-600 to-primary-700 text-white text-center">
                                    <div class="text-5xl mb-4">🚀</div>
                                    <h3 class="text-xl font-bold mb-2">جاهز للبدء؟</h3>
                                    <p class="text-primary-100 mb-6 text-sm">ساهم في توثيق هذا المعلم التراثي واحصل على نقاط تميز</p>
                                    
                                    <form method="POST" action="<?= route('projects.start') ?>">
                                        <input type="hidden" name="project_id" value="<?= $project->id ?>">
                                        <button type="submit" class="w-full btn bg-white text-primary-700 hover:bg-primary-50 py-3 rounded-xl font-bold shadow-md transition-all transform hover:-translate-y-1">
                                            ابدأ الآن
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Location Map - Full Width Professional Section -->
            <?php if (!empty($project->latitude) && !empty($project->longitude)): ?>
                <div class="card overflow-hidden mb-8 animate-fade-scale">
                    <div class="p-6 md:p-10 border-b border-[var(--border-light)]">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-red-500 to-red-600 text-white flex items-center justify-center shadow-lg">
                                <i class="fas fa-map-marked-alt text-xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-[var(--text-main)]">موقع المشروع</h2>
                                <p class="text-[var(--text-muted)]">اكتشف الموقع الجغرافي لهذا المعلم الثقافي</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <!-- Map Container -->
                        <div id="project-map" class="w-full h-[500px] md:h-[600px]"></div>
                        
                        <!-- Location Info Overlay -->
                        <div class="absolute bottom-6 right-6 left-6 md:left-auto md:w-96 z-[1000]">
                            <div class="card p-6 shadow-2xl backdrop-blur-md bg-white/95 dark:bg-gray-900/95">
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 text-white flex items-center justify-center shadow-lg">
                                            <i class="fas fa-location-dot text-xl"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-[var(--text-main)] mb-2 text-lg"><?= htmlspecialchars($project->name) ?></h3>
                                        <p class="text-sm text-[var(--text-muted)] flex items-start gap-2">
                                            <i class="fas fa-map-pin mt-1 text-primary-500"></i>
                                            <span class="flex-1"><?= htmlspecialchars($project->location_name) ?></span>
                                        </p>
                                        <div class="mt-3 pt-3 border-t border-[var(--border-light)] flex items-center gap-4 text-xs text-[var(--text-muted)]">
                                            <span><i class="fas fa-compass ml-1"></i> <?= number_format($project->latitude, 4) ?>°</span>
                                            <span><i class="fas fa-globe ml-1"></i> <?= number_format($project->longitude, 4) ?>°</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($project->latitude) && !empty($project->longitude)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.error('Leaflet library not loaded. Please check if Leaflet CSS and JS are included in the layout.');
        return;
    }

    // Check if map container exists
    const mapContainer = document.getElementById('project-map');
    if (!mapContainer) {
        console.error('Map container not found');
        return;
    }

    try {
        // Initialize project location map
        const projectLat = <?= $project->latitude ?>;
        const projectLng = <?= $project->longitude ?>;

        const projectMap = L.map('project-map', {
            zoomControl: true,
            scrollWheelZoom: true
        }).setView([projectLat, projectLng], 14);

        // Add OpenStreetMap tiles with better styling
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(projectMap);

        // Custom marker icon
        const customIcon = L.divIcon({
            className: 'custom-marker',
            html: `
                <div class="relative">
                    <div class="absolute -top-12 -left-6 w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-2xl border-4 border-white animate-bounce">
                        <i class="fas fa-landmark text-white text-xl"></i>
                    </div>
                    <div class="absolute -top-3 -left-1 w-2 h-2 bg-red-600 rounded-full"></div>
                </div>
            `,
            iconSize: [24, 24],
            iconAnchor: [12, 24]
        });

        // Add marker with custom icon
        const marker = L.marker([projectLat, projectLng], { icon: customIcon }).addTo(projectMap);

        // Add circle to highlight the area
        L.circle([projectLat, projectLng], {
            color: '#ef4444',
            fillColor: '#ef4444',
            fillOpacity: 0.1,
            radius: 200
        }).addTo(projectMap);

        // Add popup
        marker.bindPopup(`
            <div class="text-center p-3">
                <div class="text-2xl mb-2">🏛️</div>
                <strong class="text-primary-600 text-lg block mb-1"><?= htmlspecialchars($project->name) ?></strong>
                <span class="text-sm text-gray-600"><?= htmlspecialchars($project->category ?? 'معلم ثقافي') ?></span>
            </div>
        `, {
            maxWidth: 300,
            className: 'custom-popup'
        });

        console.log('Map initialized successfully');
    } catch (error) {
        console.error('Error initializing map:', error);
    }
});
</script>

<style>
.custom-marker {
    background: transparent;
    border: none;
}

.custom-popup .leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.custom-popup .leaflet-popup-tip {
    display: none;
}

@keyframes bounce {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

.animate-bounce {
    animation: bounce 2s infinite;
}
</style>
<?php endif; ?>
