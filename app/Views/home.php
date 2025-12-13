<div class="min-h-screen">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-primary-900 to-primary-700 text-white py-24 md:py-32 -mt-8">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.4\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        </div>
        <div class="absolute inset-0 opacity-20">
            <div class="absolute top-0 left-0 w-96 h-96 bg-primary-400 rounded-full filter blur-3xl animate-float"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-primary-300 rounded-full filter blur-3xl animate-float" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 text-center relative z-10">
            <div class="inline-block mb-6 px-6 py-2 rounded-full glass border border-white/20 animate-fade-scale">
                <span class="text-sm font-medium flex items-center gap-2">
                    <i class="fas fa-star text-yellow-300"></i>
                    منصة إثراء الثقافة السعودية - الأولى في المملكة
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                توثيق <span class="heading-gradient">تراثنا</span><br>
                حفظ <span class="heading-gradient">هويتنا</span>
            </h1>
            <p class="text-lg md:text-xl lg:text-2xl mb-10 max-w-3xl mx-auto opacity-90 leading-relaxed">
                ساهم في بناء أكبر موسوعة رقمية للثقافة السعودية. شارك معرفتك، وثق تراث منطقتك، وكن جزءاً من حفظ تاريخنا العريق للأجيال القادمة.
            </p>
            <?php if (!auth()->check()): ?>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
                    <a href="<?= route('register') ?>" class="btn btn-primary px-10 py-5 text-lg shadow-2xl transform hover:scale-105">
                        <span>ابدأ رحلة التوثيق</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="<?= route('login') ?>" class="btn btn-outline px-10 py-5 text-lg bg-white/10 backdrop-blur-sm border-white/30 text-white hover:bg-white/20">
                        تسجيل الدخول
                    </a>
                </div>
            <?php else: ?>
                <a href="<?= route('projects') ?>" class="btn btn-primary px-10 py-5 text-xl shadow-2xl transform hover:scale-105 inline-flex">
                    <span>تصفح المشاريع المتاحة</span>
                    <i class="fas fa-arrow-left"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="relative -mt-16 z-20 max-w-7xl mx-auto px-4 mb-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
            <div class="card glass p-6 text-center transform hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <div class="text-3xl md:text-4xl font-bold heading-gradient mb-2">
                    <?= $stats['projects'] ?? 0 ?>+
                </div>
                <div class="font-medium text-[var(--text-muted)]">مشروع ثقافي</div>
            </div>
            <div class="card glass p-6 text-center transform hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-users"></i>
                </div>
                <div class="text-3xl md:text-4xl font-bold heading-gradient mb-2">
                    <?= $stats['users'] ?? 0 ?>+
                </div>
                <div class="font-medium text-[var(--text-muted)]">مساهم نشط</div>
            </div>
            <div class="card glass p-6 text-center transform hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="text-3xl md:text-4xl font-bold heading-gradient mb-2">
                    <?= $stats['answers'] ?? 0 ?>+
                </div>
                <div class="font-medium text-[var(--text-muted)]">مساهمة موثقة</div>
            </div>
            <div class="card glass p-6 text-center transform hover:-translate-y-2 transition-all duration-300 group">
                <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <div class="text-3xl md:text-4xl font-bold heading-gradient mb-2">
                    13
                </div>
                <div class="font-medium text-[var(--text-muted)]">منطقة مغطاة</div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="py-20 bg-[var(--bg-body)]">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4 text-[var(--text-main)]">
                    لماذا <span class="heading-gradient">منصتنا</span>؟
                </h2>
                <p class="text-lg text-[var(--text-muted)] max-w-2xl mx-auto">
                    نوفر لك أدوات احترافية وبيئة تفاعلية لتوثيق التراث الثقافي السعودي بطريقة منظمة وفعالة
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">موثوقية عالية</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        نظام مراجعة متقدم يضمن دقة المعلومات الموثقة من خلال فريق متخصص من المراجعين
                    </p>
                </div>

                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">مشاركة مجتمعية</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        انضم إلى مجتمع من الباحثين والمهتمين بالتراث السعودي وشارك خبراتك ومعرفتك
                    </p>
                </div>

                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-purple-500 to-purple-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">تتبع التقدم</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        راقب مساهماتك واحصل على شارات تقدير وإحصائيات تفصيلية عن إنجازاتك
                    </p>
                </div>

                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-green-500 to-green-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-database"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">قاعدة بيانات شاملة</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        الوصول إلى موسوعة رقمية متكاملة تغطي جميع جوانب الثقافة السعودية
                    </p>
                </div>

                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-yellow-500 to-yellow-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">سهولة الاستخدام</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        واجهة مستخدم عصرية وسهلة تعمل على جميع الأجهزة لتجربة سلسة ومريحة
                    </p>
                </div>

                <div class="card p-8 group hover:shadow-2xl transition-all duration-300">
                    <div class="w-16 h-16 mb-6 rounded-2xl bg-gradient-to-br from-red-500 to-red-700 text-white flex items-center justify-center text-3xl group-hover:scale-110 transition-transform shadow-lg">
                        <i class="fas fa-award"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">نظام المكافآت</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        احصل على نقاط وشارات تقدير مقابل مساهماتك القيمة في إثراء المحتوى
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div class="min-h-screen py-20 bg-gradient-to-br from-primary-900 to-primary-700 text-white flex items-center">
        <div class="max-w-7xl mx-auto px-4 w-full">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">
                    مشاريع <span class="text-primary-200">التوثيق</span>
                </h2>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">
                    استكشف المشاريع المتاحة وساهم في توثيق التراث الثقافي السعودي
                </p>
            </div>

            <?php if (!empty($latestProjects)): ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    <?php foreach ($latestProjects as $project): ?>
                        <div class="card glass group hover:shadow-2xl transition-all duration-300 overflow-hidden">
                            <div class="relative h-48 overflow-hidden">
                                <img 
                                    src="<?= $project->image_url ?? asset('images/default-project.jpg') ?>" 
                                    alt="<?= htmlspecialchars($project->name) ?>"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                >
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                                <div class="absolute bottom-4 right-4 left-4">
                                    <div class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-primary-500/90 backdrop-blur-sm mb-2">
                                        <?= htmlspecialchars($project->category ?? 'عام') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3 class="text-xl font-bold mb-3 text-[var(--text-main)] group-hover:text-primary-600 transition-colors">
                                    <?= htmlspecialchars($project->name) ?>
                                </h3>
                                <p class="text-[var(--text-muted)] mb-4 line-clamp-2">
                                    <?= htmlspecialchars($project->summary ?? '') ?>
                                </p>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-[var(--text-muted)] flex items-center gap-2">
                                        <i class="fas fa-question-circle text-primary-600"></i>
                                        <?= $project->total_questions ?? 0 ?> سؤال
                                    </span>
                                    <a href="<?= route('projects.show', ['id' => $project->id]) ?>" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-bold flex items-center gap-2 group/link">
                                        <span>استكشف</span>
                                        <i class="fas fa-arrow-left group-hover/link:-translate-x-1 transition-transform"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="text-center">
                    <a href="<?= route('projects') ?>" class="btn bg-white text-primary-700 hover:bg-primary-50 px-10 py-5 text-lg shadow-2xl transform hover:scale-105 inline-flex">
                        <span>عرض جميع المشاريع</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            <?php else: ?>
                <div class="text-center py-20">
                    <div class="text-6xl mb-6 opacity-50">📁</div>
                    <p class="text-xl opacity-75">لا توجد مشاريع متاحة حالياً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- How It Works Section -->
    <div class="min-h-screen py-20 flex items-center">
        <div class="max-w-7xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-5xl font-bold mb-4 text-[var(--text-main)]">
                    كيف <span class="heading-gradient">تعمل المنصة</span>؟
                </h2>
                <p class="text-lg text-[var(--text-muted)] max-w-2xl mx-auto">
                    ثلاث خطوات بسيطة للبدء في رحلة توثيق التراث الثقافي السعودي
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center text-4xl font-bold shadow-2xl">
                            1
                        </div>
                        <div class="absolute top-1/2 left-full w-full h-1 bg-gradient-to-r from-primary-500 to-transparent hidden md:block"></div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">سجل حسابك</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        أنشئ حساباً مجانياً في دقائق وابدأ رحلتك في التوثيق الثقافي
                    </p>
                </div>

                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-blue-500 to-blue-700 text-white flex items-center justify-center text-4xl font-bold shadow-2xl">
                            2
                        </div>
                        <div class="absolute top-1/2 left-full w-full h-1 bg-gradient-to-r from-blue-500 to-transparent hidden md:block"></div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">اختر مشروعاً</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        تصفح المشاريع المتاحة واختر ما يناسب اهتماماتك ومعرفتك
                    </p>
                </div>

                <div class="text-center">
                    <div class="relative mb-8">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-green-500 to-green-700 text-white flex items-center justify-center text-4xl font-bold shadow-2xl">
                            3
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-4 text-[var(--text-main)]">ابدأ المساهمة</h3>
                    <p class="text-[var(--text-muted)] leading-relaxed">
                        أجب على الأسئلة ووثق المعلومات بدقة لإثراء المحتوى الثقافي
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="min-h-screen py-20 bg-gradient-to-br from-primary-900 to-primary-700 text-white flex items-center">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="max-w-3xl mx-auto">
                <div class="text-6xl mb-6">🇸🇦</div>
                <h2 class="text-3xl md:text-5xl font-bold mb-6">
                    كن جزءاً من الحفاظ على تراثنا
                </h2>
                <p class="text-xl mb-10 opacity-90 leading-relaxed">
                    انضم إلى آلاف المساهمين في توثيق وحفظ التراث الثقافي السعودي للأجيال القادمة
                </p>
                <?php if (!auth()->check()): ?>
                    <a href="<?= route('register') ?>" class="btn bg-white text-primary-700 hover:bg-primary-50 px-10 py-5 text-lg shadow-2xl transform hover:scale-105 inline-flex">
                        <span>ابدأ الآن مجاناً</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                <?php else: ?>
                    <a href="<?= route('dashboard') ?>" class="btn bg-white text-primary-700 hover:bg-primary-50 px-10 py-5 text-lg shadow-2xl transform hover:scale-105 inline-flex">
                        <span>انتقل إلى لوحة التحكم</span>
                        <i class="fas fa-arrow-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
