<div class="min-h-screen py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="animate-slide-right">
                <h1 class="text-4xl font-bold mb-6 gradient-text">تواصل معنا</h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-12 leading-relaxed">
                    نسعد باستقبال استفساراتكم واقتراحاتكم. فريقنا جاهز للرد عليكم ومساعدتكم في أي وقت.
                </p>

                <div class="space-y-8">
                    <div class="flex items-start gap-6 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center text-white text-xl flex-shrink-0">📧</div>
                        <div>
                            <h3 class="text-lg font-bold mb-1">البريد الإلكتروني</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">للاستفسارات العامة والدعم الفني</p>
                            <a href="mailto:support@saudiculture.sa" class="text-green-600 font-semibold hover:underline">support@saudiculture.sa</a>
                        </div>
                    </div>

                    <div class="flex items-start gap-6 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center text-white text-xl flex-shrink-0">📱</div>
                        <div>
                            <h3 class="text-lg font-bold mb-1">وسائل التواصل</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-2">تابعنا على منصات التواصل الاجتماعي</p>
                            <div class="flex gap-4">
                                <a href="#" class="text-gray-400 hover:text-green-600 transition-colors"><span class="sr-only">Twitter</span>🐦</a>
                                <a href="#" class="text-gray-400 hover:text-green-600 transition-colors"><span class="sr-only">Instagram</span>📸</a>
                                <a href="#" class="text-gray-400 hover:text-green-600 transition-colors"><span class="sr-only">LinkedIn</span>💼</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="animate-slide-left delay-100">
                <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 border border-gray-100 dark:border-gray-700">
                    <h2 class="text-2xl font-bold mb-6">أرسل لنا رسالة</h2>
                    <form method="POST" action="<?= route('contact.submit') ?>" class="space-y-6">
                        <?php if (!auth()->check()): ?>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الاسم</label>
                                    <input type="text" id="name" name="name" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">البريد الإلكتروني</label>
                                    <input type="email" id="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                </div>
                            </div>
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رقم الجوال</label>
                                <input type="tel" id="phone" name="phone" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                            </div>
                        <?php endif; ?>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">نوع الرسالة</label>
                            <select id="type" name="type" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                                <option value="technical_issue">مشكلة تقنية</option>
                                <option value="feedback">ملاحظات واقتراحات</option>
                                <option value="project_question">سؤال عن المشاريع</option>
                                <option value="feature_request">طلب ميزة جديدة</option>
                                <option value="bug_report">بلاغ عن خطأ</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الموضوع</label>
                            <input type="text" id="subject" name="subject" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الرسالة</label>
                            <textarea id="message" name="message" rows="5" required class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all"></textarea>
                        </div>

                        <button type="submit" class="w-full btn btn-primary py-4 text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all">
                            إرسال الرسالة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
