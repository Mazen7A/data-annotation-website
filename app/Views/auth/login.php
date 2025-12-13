<div class="min-h-[80vh] flex items-center justify-center py-12">
    <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 border border-gray-100 dark:border-gray-700 animate-fade-in">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold gradient-text mb-2">تسجيل الدخول</h2>
                <p class="text-gray-600 dark:text-gray-400">مرحباً بك مجدداً في منصة توثيق الثقافة السعودية</p>
            </div>
            
            <form method="POST" action="<?= route('login') ?>" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        البريد الإلكتروني
                    </label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all pl-10"
                            placeholder="example@email.com"
                        >
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        كلمة المرور
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all pl-10"
                            placeholder="••••••••"
                        >
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </div>
                    </div>
                    <div class="text-left mt-2">
                        <a href="<?= route('password.forgot') ?>" class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                            نسيت كلمة المرور؟
                        </a>
                    </div>
                </div>

                <button 
                    type="submit"
                    class="w-full btn btn-primary py-4 text-lg shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all"
                >
                    تسجيل الدخول
                </button>
            </form>

            <div class="mt-8 text-center">
                <p class="text-gray-600 dark:text-gray-400">
                    ليس لديك حساب؟
                    <a href="<?= route('register') ?>" class="text-green-600 font-bold hover:text-green-700 hover:underline transition-colors">
                        سجل الآن
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>
