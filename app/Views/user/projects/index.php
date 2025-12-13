<div class="min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 animate-fade-in">
            <h1 class="text-4xl font-bold gradient-text mb-4">المشاريع المتاحة</h1>
            <p class="text-xl text-gray-600 dark:text-gray-300">استكشف مشاريع التوثيق وساهم في حفظ تراثنا</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($projects as $project): ?>
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-lg overflow-hidden card-hover border border-gray-100 dark:border-gray-700 animate-slide-up">
                    <div class="relative h-48">
                        <img 
                            src="<?= $project->image_url ?? asset('images/default-project.jpg') ?>" 
                            alt="<?= htmlspecialchars($project->name) ?>" 
                            class="w-full h-full object-cover"
                        >
                        <div class="absolute top-4 right-4 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-green-700 dark:text-green-400 shadow-sm">
                            <?= htmlspecialchars($project->category ?? 'عام') ?>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-3 text-gray-900 dark:text-white">
                            <?= htmlspecialchars($project->name) ?>
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 leading-relaxed">
                            <?= htmlspecialchars($project->summary ?? '') ?>
                        </p>
                        
                        <div class="flex items-center justify-between mt-6">
                            <?php if (isset($userSessions[$project->id])): ?>
                                <?php $session = $userSessions[$project->id]; ?>
                                <div class="flex items-center gap-2">
                                    <?php if ($session->status == 'completed'): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            ✅ مكتمل
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                            ⏳ قيد التقدم
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= route('projects.show', ['id' => $project->id]) ?>" class="btn btn-outline px-6 py-2 rounded-xl text-sm">
                                    متابعة
                                </a>
                            <?php else: ?>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    <?= $project->total_questions ?? 0 ?> سؤال
                                </span>
                                <a href="<?= route('projects.show', ['id' => $project->id]) ?>" class="btn btn-primary px-6 py-2 rounded-xl text-sm shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                                    بدء المشروع
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($projects)): ?>
            <div class="text-center py-12">
                <div class="text-6xl mb-4 text-gray-300 dark:text-gray-600">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-600 dark:text-gray-400">لا توجد مشاريع متاحة حالياً</h3>
                <p class="text-gray-500 dark:text-gray-500 mt-2">يرجى العودة لاحقاً للاطلاع على المشاريع الجديدة</p>
            </div>
        <?php endif; ?>
    </div>
</div>
