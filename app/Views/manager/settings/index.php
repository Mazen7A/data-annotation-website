<div>
    <h1 class="text-3xl font-bold mb-8">إعدادات المنصة</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Theme Settings -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg mb-8" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <h2 class="text-2xl font-bold mb-6">إعدادات المظهر</h2>
                
                <form method="POST" action="<?= route('manager.settings.update_theme') ?>" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            المظهر الافتراضي
                        </label>
                        <select name="default_theme" class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-purple-600 focus:border-transparent" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="purple" <?= ($themeSettings['default_theme'] ?? 'purple') === 'purple' ? 'selected' : '' ?>>بنفسجي (افتراضي)</option>
                            <option value="blue" <?= ($themeSettings['default_theme'] ?? '') === 'blue' ? 'selected' : '' ?>>أزرق</option>
                            <option value="green" <?= ($themeSettings['default_theme'] ?? '') === 'green' ? 'selected' : '' ?>>أخضر</option>
                            <option value="orange" <?= ($themeSettings['default_theme'] ?? '') === 'orange' ? 'selected' : '' ?>>برتقالي</option>
                            <option value="gold" <?= ($themeSettings['default_theme'] ?? '') === 'gold' ? 'selected' : '' ?>>ذهبي</option>
                            <option value="night" <?= ($themeSettings['default_theme'] ?? '') === 'night' ? 'selected' : '' ?>>ليلي</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            الوضع الافتراضي
                        </label>
                        <select name="default_mode" class="w-full px-4 py-3 rounded-lg border focus:ring-2 focus:ring-purple-600 focus:border-transparent" style="background-color: var(--bg-secondary); border-color: var(--border-color); color: var(--text-primary);">
                            <option value="auto" <?= ($themeSettings['default_mode'] ?? 'auto') === 'auto' ? 'selected' : '' ?>>تلقائي (حسب النظام)</option>
                            <option value="light" <?= ($themeSettings['default_mode'] ?? '') === 'light' ? 'selected' : '' ?>>فاتح</option>
                            <option value="dark" <?= ($themeSettings['default_mode'] ?? '') === 'dark' ? 'selected' : '' ?>>داكن</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            اللون الأساسي
                        </label>
                        <input type="color" name="primary_color" value="<?= $themeSettings['primary_color'] ?? '#667eea' ?>" class="w-full h-12 rounded-lg border cursor-pointer" style="border-color: var(--border-color);">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: var(--text-secondary);">
                            اللون الثانوي
                        </label>
                        <input type="color" name="secondary_color" value="<?= $themeSettings['secondary_color'] ?? '#764ba2' ?>" class="w-full h-12 rounded-lg border cursor-pointer" style="border-color: var(--border-color);">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="enable_animations" id="enable_animations" <?= ($themeSettings['enable_animations'] ?? true) ? 'checked' : '' ?> class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-600">
                        <label for="enable_animations" class="mr-3 text-sm font-medium" style="color: var(--text-primary);">
                            تفعيل الرسوم المتحركة
                        </label>
                    </div>

                    <button type="submit" class="w-full gradient-bg text-white px-6 py-3 rounded-lg font-bold hover:opacity-90 transition-all duration-300 transform hover:scale-105">
                        حفظ الإعدادات
                    </button>
                </form>
            </div>
        </div>

        <!-- Theme Preview -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-xl shadow-lg sticky top-24" style="background-color: var(--bg-primary); border: 1px solid var(--border-color);">
                <h3 class="text-xl font-bold mb-6">معاينة المظهر</h3>
                
                <div class="space-y-4">
                    <div class="p-4 rounded-lg gradient-bg text-white">
                        <p class="font-bold">عنصر بالتدرج</p>
                        <p class="text-sm opacity-90">هذا نموذج للعناصر بالتدرج اللوني</p>
                    </div>
                    
                    <div class="p-4 rounded-lg" style="background-color: var(--bg-secondary);">
                        <p class="font-bold" style="color: var(--text-primary);">عنصر عادي</p>
                        <p class="text-sm" style="color: var(--text-secondary);">هذا نموذج للعناصر العادية</p>
                    </div>
                    
                    <button class="w-full gradient-bg text-white px-4 py-2 rounded-lg font-bold">
                        زر رئيسي
                    </button>
                    
                    <button class="w-full border-2 px-4 py-2 rounded-lg font-bold" style="border-color: var(--theme-primary); color: var(--theme-primary);">
                        زر ثانوي
                    </button>
                </div>

                <div class="mt-6 pt-6 border-t" style="border-color: var(--border-color);">
                    <h4 class="font-bold mb-3">المظاهر المتاحة</h4>
                    <div class="grid grid-cols-3 gap-2">
                        <button data-color-scheme-btn="purple" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #667eea, #764ba2);" title="بنفسجي"></button>
                        <button data-color-scheme-btn="blue" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #3b82f6, #1e40af);" title="أزرق"></button>
                        <button data-color-scheme-btn="green" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #10b981, #059669);" title="أخضر"></button>
                        <button data-color-scheme-btn="orange" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #f97316, #ea580c);" title="برتقالي"></button>
                        <button data-color-scheme-btn="gold" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #f59e0b, #d97706);" title="ذهبي"></button>
                        <button data-color-scheme-btn="night" class="h-12 rounded-lg" style="background: linear-gradient(135deg, #6366f1, #4f46e5);" title="ليلي"></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
