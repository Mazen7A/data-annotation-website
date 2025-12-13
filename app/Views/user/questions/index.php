<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Progress Bar -->
        <div class="mb-8 animate-fade-scale">
            <div class="flex justify-between text-sm text-[var(--text-muted)] mb-3">
                <span class="font-medium">التقدم في المشروع</span>
                <span class="font-bold text-primary-600 dark:text-primary-400"><?= number_format($progress, 1) ?>%</span>
            </div>
            <div class="w-full bg-[var(--bg-body)] rounded-full h-4 overflow-hidden border border-[var(--border-light)]">
                <div class="bg-gradient-to-r from-primary-600 to-primary-500 h-4 rounded-full transition-all duration-500 shadow-lg shadow-primary-500/30" style="width: <?= $progress ?>%"></div>
            </div>
        </div>

        <!-- Question Card -->
        <div class="card p-8 md:p-10 animate-fade-scale relative overflow-hidden">
            <div class="mb-6 flex flex-wrap items-center gap-3">
                <span class="text-sm text-[var(--text-muted)] font-medium">
                    <?= htmlspecialchars($session->project_name) ?>
                </span>
                <?php if ($question->category): ?>
                    <span class="px-3 py-1.5 text-xs font-bold bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full">
                        <?= htmlspecialchars($question->category) ?>
                    </span>
                <?php endif; ?>
            </div>

            <h2 class="text-3xl md:text-4xl font-bold mb-8 text-[var(--text-main)] leading-tight"><?= htmlspecialchars($question->question_text) ?></h2>

            <!-- Question Media -->
            <?php if ($question->media_url): ?>
                <div class="mb-8 rounded-2xl overflow-hidden border border-[var(--border-light)]">
                    <?php
                    $ext = pathinfo($question->media_url, PATHINFO_EXTENSION);
                    if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                    ?>
                        <img src="<?= asset($question->media_url) ?>" alt="صورة السؤال" class="w-full h-auto">
                    <?php elseif (in_array(strtolower($ext), ['mp4', 'webm'])): ?>
                        <video controls class="w-full h-auto">
                            <source src="<?= asset($question->media_url) ?>" type="video/<?= $ext ?>">
                        </video>
                    <?php elseif (in_array(strtolower($ext), ['mp3', 'wav'])): ?>
                        <audio controls class="w-full p-4 bg-[var(--bg-body)]">
                            <source src="<?= asset($question->media_url) ?>" type="audio/<?= $ext ?>">
                        </audio>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Answer Form -->
            <form method="POST" action="<?= route('questions.submit') ?>" class="space-y-6" id="answer-form">
                <input type="hidden" name="session_id" value="<?= $session->id ?>">
                <input type="hidden" name="question_id" value="<?= $question->id ?>">
                <?php if ($question->question_type === 'mcq'): ?>
                    <!-- Multiple Choice -->
                    <div class="space-y-3">
                        <?php foreach ($question->options as $index => $option): ?>
                            <label class="flex items-center p-5 border-2 border-[var(--border-light)] rounded-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 cursor-pointer transition-all group"
                                data-option-id="<?= $option->id ?>" data-correct="<?= $option->is_correct ? '1' : '0' ?>">
                                <input type="checkbox" name="selected_options[]" value="<?= $option->id ?>" class="ml-4 w-5 h-5 text-primary-600 rounded focus:ring-2 focus:ring-primary-500">
                                <span class="text-lg text-[var(--text-main)] group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors"><?= htmlspecialchars($option->option_text) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($question->question_type === 'true_false'): ?>
                    <!-- True/False -->
                    <div class="space-y-3">
                        <?php foreach ($question->options as $option): ?>
                            <label class="flex items-center p-5 border-2 border-[var(--border-light)] rounded-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 cursor-pointer transition-all group"
                                data-option-id="<?= $option->id ?>" data-correct="<?= $option->is_correct ? '1' : '0' ?>">
                                <input type="radio" name="selected_options[]" value="<?= $option->id ?>" class="ml-4 w-5 h-5 text-primary-600 focus:ring-2 focus:ring-primary-500" required>
                                <span class="text-lg text-[var(--text-main)] group-hover:text-primary-700 dark:group-hover:text-primary-300 transition-colors"><?= htmlspecialchars($option->option_text) ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($question->question_type === 'open'): ?>
                    <!-- Open-ended -->
                    <div>
                        <label class="block text-sm font-bold text-[var(--text-main)] mb-3">
                            <i class="fas fa-pen text-primary-600"></i> إجابتك
                        </label>
                        <textarea 
                            name="answer_text" 
                            rows="6" 
                            required
                            class="input-field resize-none"
                            placeholder="اكتب إجابتك هنا..."
                        ></textarea>
                    </div>

                <?php elseif ($question->question_type === 'list'): ?>
                    <!-- List -->
                    <div>
                        <label class="block text-sm font-bold text-[var(--text-main)] mb-3">
                            <i class="fas fa-list text-primary-600"></i> اذكر العناصر (كل عنصر في سطر منفصل)
                        </label>
                        <textarea 
                            name="answer_text" 
                            rows="8" 
                            required
                            class="input-field resize-none font-mono"
                            placeholder="العنصر الأول&#10;العنصر الثاني&#10;العنصر الثالث"
                        ></textarea>
                        <p class="text-sm text-[var(--text-muted)] mt-3 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            اكتب كل عنصر في سطر منفصل
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 pt-6 border-t border-[var(--border-light)]">
                    <a href="<?= route('dashboard') ?>" class="text-[var(--text-muted)] hover:text-primary-600 transition-colors flex items-center gap-2">
                        <i class="fas fa-arrow-right"></i>
                        <span>العودة للوحة التحكم</span>
                    </a>
                    <button 
                        type="submit"
                        class="btn btn-primary px-8 py-4 text-lg w-full md:w-auto"
                    >
                        <span>إرسال الإجابة</span>
                        <i class="fas fa-arrow-left"></i>
                    </button>
                </div>
            </form>

            <!-- Feedback Overlay -->
            <div id="answer-feedback" class="hidden absolute inset-0 flex items-center justify-center backdrop-blur-sm bg-black/40">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-[var(--border-light)] p-8 text-center space-y-3 w-full max-w-md animate-fade-scale">
                    <div id="feedback-icon" class="text-5xl"></div>
                    <div id="feedback-title" class="text-2xl font-bold text-[var(--text-main)]"></div>
                    <p id="feedback-sub" class="text-[var(--text-muted)]"></p>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="card p-6 mt-6 bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 border-primary-200 dark:border-primary-800">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-primary-600 text-white flex items-center justify-center text-xl">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-primary-900 dark:text-primary-100 mb-2">نصيحة</h3>
                    <p class="text-sm text-primary-800 dark:text-primary-200 leading-relaxed">
                        خذ وقتك في قراءة السؤال بعناية. إجاباتك تساهم في بناء قاعدة معرفية شاملة عن التراث الثقافي السعودي.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('answer-form');
    const feedback = document.getElementById('answer-feedback');
    const icon = document.getElementById('feedback-icon');
    const title = document.getElementById('feedback-title');
    const sub = document.getElementById('feedback-sub');
    const questionType = '<?= $question->question_type ?>';
    const correctIds = Array.from(document.querySelectorAll('[data-correct="1"]')).map(el => el.dataset.optionId);

    const inspireCorrect = ['أحسنت! إجابة دقيقة.', 'رائع! استمرارك يثري المحتوى.', 'ممتاز! تقدم ملحوظ.'];
    const inspireWrong = ['حاول مرة أخرى، المعرفة تأتي بالممارسة.', 'لا بأس، التعلم من الأخطاء.', 'استمر، كل محاولة تضيف خبرة.'];
    const inspirePending = ['تم استلام إجابتك، سيتم المراجعة.', 'شكراً لمساهمتك القيمة.', 'إجابة مسجلة، واصل التقدم!'];

    form?.addEventListener('submit', (e) => {
        if (!feedback) return;
        const formData = new FormData(form);
        const selected = formData.getAll('selected_options[]');

        let status = 'pending';
        if (questionType === 'mcq' || questionType === 'true_false') {
            const sortedSel = [...selected].sort();
            const sortedCorrect = [...correctIds].sort();
            status = JSON.stringify(sortedSel) === JSON.stringify(sortedCorrect) ? 'correct' : 'incorrect';
        }

        // Prevent immediate submission to show animation
        e.preventDefault();
        feedback.classList.remove('hidden');

        if (status === 'correct') {
            icon.innerHTML = '✅';
            title.textContent = 'إجابة صحيحة';
            sub.textContent = inspireCorrect[Math.floor(Math.random() * inspireCorrect.length)];
            feedback.classList.add('animate-pulse');
        } else if (status === 'incorrect') {
            icon.innerHTML = '❌';
            title.textContent = 'إجابة غير صحيحة';
            sub.textContent = inspireWrong[Math.floor(Math.random() * inspireWrong.length)];
            feedback.classList.add('animate-shake');
        } else {
            icon.innerHTML = '✍️';
            title.textContent = 'تم استلام إجابتك';
            sub.textContent = inspirePending[Math.floor(Math.random() * inspirePending.length)];
        }

        setTimeout(() => {
            feedback.classList.add('hidden');
            form.submit();
        }, 1200);
    });
});
</script>
