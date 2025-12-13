<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= route('manager.questions', ['project_id' => $project->id]) ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">إضافة سؤال جديد</h1>
            <p class="text-[var(--text-muted)]">مشروع: <?= htmlspecialchars($project->name) ?></p>
        </div>
    </div>

    <div class="card p-6">
        <form action="<?= route('manager.questions.store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="project_id" value="<?= $project->id ?>">

            <!-- Question Text -->
            <div>
                <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">نص السؤال</label>
                <textarea name="question_text" rows="3" class="input-field resize-none" required placeholder="اكتب نص السؤال هنا..."></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Question Type -->
                <div>
                    <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">نوع السؤال</label>
                    <select name="question_type" id="questionType" class="input-field" required onchange="toggleOptions()">
                        <option value="text">نصي (إجابة مفتوحة)</option>
                        <option value="mcq">اختيار من متعدد</option>
                        <option value="true_false">صح / خطأ</option>
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">الفئة (اختياري)</label>
                    <input type="text" name="category" class="input-field" placeholder="مثال: تاريخ، جغرافيا...">
                </div>
            </div>

            <!-- Media Upload -->
            <div>
                <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">صورة أو وسائط (اختياري)</label>
                <input type="file" name="media" class="block w-full text-sm text-[var(--text-muted)]
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-full file:border-0
                    file:text-sm file:font-semibold
                    file:bg-primary-50 file:text-primary-700
                    hover:file:bg-primary-100
                    dark:file:bg-primary-900/20 dark:file:text-primary-400
                ">
            </div>

            <!-- Options Section (Hidden by default) -->
            <div id="optionsSection" class="hidden space-y-4 pt-6 border-t border-[var(--border-light)]">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-[var(--text-main)]">الخيارات</h3>
                    <button type="button" onclick="addOption()" id="addOptionBtn" class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                        <i class="fas fa-plus ml-1"></i> إضافة خيار
                    </button>
                </div>
                
                <div id="optionsList" class="space-y-3">
                    <!-- Options will be added here dynamically -->
                </div>
            </div>

            <div class="pt-6 border-t border-[var(--border-light)]">
                <button type="submit" class="btn btn-primary w-full">
                    <i class="fas fa-save ml-2"></i> حفظ السؤال
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleOptions() {
    const type = document.getElementById('questionType').value;
    const section = document.getElementById('optionsSection');
    const list = document.getElementById('optionsList');
    const addBtn = document.getElementById('addOptionBtn');

    if (type === 'mcq' || type === 'true_false') {
        section.classList.remove('hidden');
        list.innerHTML = ''; // Clear existing

        if (type === 'true_false') {
            addBtn.style.display = 'none';
            addOption('صح', 0);
            addOption('خطأ', 1);
        } else {
            addBtn.style.display = 'block';
            addOption('', 0);
            addOption('', 1);
        }
    } else {
        section.classList.add('hidden');
    }
}

function addOption(value = '', index = null) {
    const list = document.getElementById('optionsList');
    const count = list.children.length;
    const idx = index !== null ? index : count;
    
    const div = document.createElement('div');
    div.className = 'flex items-center gap-3';
    div.innerHTML = `
        <input type="radio" name="correct_options[]" value="${idx}" class="w-4 h-4 text-primary-600 border-gray-300 focus:ring-primary-500">
        <input type="text" name="options[]" value="${value}" class="input-field flex-1" placeholder="نص الخيار" required>
        ${document.getElementById('questionType').value === 'mcq' ? `
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-600 p-2">
            <i class="fas fa-times"></i>
        </button>
        ` : ''}
    `;
    list.appendChild(div);
}
</script>
