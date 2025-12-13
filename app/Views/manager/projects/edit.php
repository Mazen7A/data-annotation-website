<div class="max-w-7xl mx-auto">
    <div class="flex items-center gap-4 mb-8">
        <a href="<?= route('manager.projects') ?>" class="w-10 h-10 rounded-xl bg-[var(--bg-body)] border border-[var(--border-light)] flex items-center justify-center text-[var(--text-muted)] hover:text-primary-600 hover:border-primary-500 transition-colors">
            <i class="fas fa-arrow-right"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-main)]">تعديل المشروع</h1>
            <p class="text-[var(--text-muted)]">تحديث معلومات المشروع</p>
        </div>
    </div>

    <div class="card p-6">
        <form id="project-form" method="POST" action="<?= route('manager.projects.update') ?>" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="id" value="<?= $project->id ?>">

            <div>
                <label for="name" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    اسم المشروع *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?= htmlspecialchars($project->name) ?>"
                    required
                    class="input-field"
                    placeholder="مثال: الأزياء التقليدية السعودية"
                >
            </div>

            <div>
                <label for="category" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    الفئة
                </label>
                <input 
                    type="text" 
                    id="category" 
                    name="category" 
                    value="<?= htmlspecialchars($project->category ?? '') ?>"
                    class="input-field"
                    placeholder="مثال: تراث، طعام، معالم"
                >
            </div>

            <div>
                <label for="summary" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    نبذة مختصرة
                </label>
                <textarea 
                    id="summary" 
                    name="summary" 
                    rows="3"
                    class="input-field resize-none"
                    placeholder="نبذة مختصرة عن المشروع"
                ><?= htmlspecialchars($project->summary ?? '') ?></textarea>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    الوصف التفصيلي
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="6"
                    class="input-field resize-none"
                    placeholder="وصف تفصيلي للمشروع وأهدافه"
                ><?= htmlspecialchars($project->description ?? '') ?></textarea>
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    صورة المشروع
                </label>
                <?php if ($project->image_url): ?>
                    <div class="mb-3">
                        <img src="<?= asset($project->image_url) ?>" alt="Current Image" class="h-32 rounded-lg border border-[var(--border-light)]">
                    </div>
                <?php endif; ?>
                <input 
                    type="file" 
                    id="image" 
                    name="image" 
                    accept="image/*"
                    class="block w-full text-sm text-[var(--text-muted)]
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-primary-50 file:text-primary-700
                        hover:file:bg-primary-100
                        dark:file:bg-primary-900/20 dark:file:text-primary-400
                    "
                >
                <p class="text-sm text-[var(--text-muted)] mt-2">يفضل استخدام صور بحجم 800x600 بكسل</p>
            </div>

            <!-- Map Location Picker -->
            <div>
                <label class="block text-sm font-medium text-[var(--text-muted)] mb-2">
                    <i class="fas fa-map-marker-alt ml-1"></i> موقع المشروع (اختياري)
                </label>
                <p class="text-xs text-[var(--text-muted)] mb-3">انقر على الخريطة لتحديد أو تحديث موقع المشروع</p>
                
                <div id="map" class="w-full h-96 rounded-xl border-2 border-[var(--border-light)] overflow-hidden"></div>
                
                <input type="hidden" name="latitude" id="latitude" value="<?= $project->latitude ?? '' ?>">
                <input type="hidden" name="longitude" id="longitude" value="<?= $project->longitude ?? '' ?>">
                <input type="hidden" name="location_name" id="location_name" value="<?= htmlspecialchars($project->location_name ?? '') ?>">
                
                <div id="selected-location" class="mt-3 p-3 bg-[var(--bg-body)] rounded-lg border border-[var(--border-light)] <?= empty($project->latitude) ? 'hidden' : '' ?>">
                    <p class="text-sm text-[var(--text-main)]">
                        <i class="fas fa-check-circle text-green-500 ml-1"></i>
                        <span id="location-display"><?= htmlspecialchars($project->location_name ?? '') ?></span>
                    </p>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center gap-3 pt-6 border-t border-[var(--border-light)]">
                <a href="<?= route('manager.projects') ?>" class="btn btn-ghost">
                    إلغاء
                </a>
                <div class="flex gap-3 items-center">
                    <span id="selected-bank-count" class="text-xs px-3 py-1 rounded-full bg-primary-50 text-primary-700 border border-primary-100">
                        0 سؤال مختار
                    </span>
                    <button type="button" id="open-bank-modal" class="btn btn-outline">
                        <i class="fas fa-database ml-2"></i> اختيار أسئلة من البنك
                    </button>
                    <button 
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fas fa-save ml-2"></i> حفظ التغييرات
                    </button>
                </div>
            </div>
            <div id="bank-hidden-inputs"></div>
        </form>
    </div>

    

    <!-- Bank Modal -->
    <div id="bank-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center" style="z-index: 9999;">
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-[var(--border-light)] w-11/12 max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
            <div class="p-4 border-b border-[var(--border-light)] flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-[var(--text-main)]">بنك الأسئلة</h3>
                    <p class="text-sm text-[var(--text-muted)]">تصفية واختيار الأسئلة لإضافتها للمشروع</p>
                </div>
                <button id="close-bank-modal" class="w-10 h-10 rounded-full bg-[var(--bg-body)] text-[var(--text-muted)] hover:text-primary-600 flex items-center justify-center">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3 border-b border-[var(--border-light)]">
                <input type="text" id="bank-search" value="<?= htmlspecialchars($bankSearch ?? '') ?>" placeholder="بحث في نص السؤال" class="input-field md:col-span-2">
                <select id="bank-type" class="input-field">
                    <option value="">النوع (الكل)</option>
                    <option value="mcq">اختيار من متعدد</option>
                    <option value="true_false">صح / خطأ</option>
                    <option value="open">مفتوح</option>
                    <option value="list">قائمة</option>
                </select>
                <input type="text" id="bank-category" value="<?= htmlspecialchars($bankCategory ?? '') ?>" placeholder="فئة" class="input-field">
            </div>

            <div class="flex-1 overflow-hidden flex flex-col">
                <div class="flex-1 overflow-y-auto p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if (empty($bankQuestions)): ?>
                        <div class="text-center text-[var(--text-muted)] py-8 col-span-2">لا توجد أسئلة مطابقة.</div>
                    <?php else: ?>
                        <?php foreach ($bankQuestions as $bq): ?>
                            <?php
                            $typeBadge = match($bq->question_type) {
                                'mcq' => 'bg-blue-100 text-blue-700',
                                'true_false' => 'bg-green-100 text-green-700',
                                'open' => 'bg-purple-100 text-purple-700',
                                'list' => 'bg-yellow-100 text-yellow-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            $typeLabel = match($bq->question_type) {
                                'mcq' => 'اختيار من متعدد',
                                'true_false' => 'صح/خطأ',
                                'open' => 'مفتوح',
                                'list' => 'قائمة',
                                default => $bq->question_type
                            };
                            ?>
                            <label class="bank-card p-4 border border-[var(--border-light)] rounded-xl hover:border-primary-500 transition-colors cursor-pointer flex gap-3 bg-[var(--bg-body)] shadow-sm"
                                data-type="<?= $bq->question_type ?>"
                                data-category="<?= htmlspecialchars($bq->category ?? '') ?>"
                                data-text="<?= htmlspecialchars($bq->question_text) ?>">
                                <input type="checkbox" class="bank-checkbox mt-1 w-5 h-5 text-primary-600 rounded focus:ring-primary-500" value="<?= $bq->id ?>">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $typeBadge ?>"><?= $typeLabel ?></span>
                                        <?php if (!empty($bq->category)): ?>
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700"><?= htmlspecialchars($bq->category) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-[var(--text-main)] font-semibold leading-relaxed line-clamp-2"><?= htmlspecialchars($bq->question_text) ?></p>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="p-4 border-t border-[var(--border-light)] flex justify-end gap-2">
                    <button type="button" id="close-bank-modal-bottom" class="btn btn-ghost">إغلاق</button>
                    <button type="button" id="apply-bank-selection" class="btn btn-primary">
                        <i class="fas fa-plus ml-2"></i> إضافة الأسئلة المحددة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('bank-modal');
    const openBtn = document.getElementById('open-bank-modal');
    const closeBtn = document.getElementById('close-bank-modal');
    const closeBottom = document.getElementById('close-bank-modal-bottom');
    const searchInput = document.getElementById('bank-search');
    const typeSelect = document.getElementById('bank-type');
    const categoryInput = document.getElementById('bank-category');
    const cards = Array.from(document.querySelectorAll('.bank-card'));
    const applyBtn = document.getElementById('apply-bank-selection');
    const projectForm = document.getElementById('project-form');
    const hiddenHolderId = 'bank-hidden-inputs';
    let hiddenHolder = document.getElementById(hiddenHolderId);
    if (!hiddenHolder) {
        hiddenHolder = document.createElement('div');
        hiddenHolder.id = hiddenHolderId;
        projectForm?.appendChild(hiddenHolder);
    }

    const toggleModal = (show) => {
        if (show) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };
    const applyFilter = () => {
        const q = (searchInput?.value || '').toLowerCase();
        const t = typeSelect?.value || '';
        const c = (categoryInput?.value || '').toLowerCase();
        cards.forEach(card => {
            const text = card.dataset.text.toLowerCase();
            const type = card.dataset.type;
            const cat = (card.dataset.category || '').toLowerCase();
            const matchText = !q || text.includes(q);
            const matchType = !t || type === t;
            const matchCat = !c || cat.includes(c);
            card.style.display = (matchText && matchType && matchCat) ? 'flex' : 'none';
        });
    };

    openBtn?.addEventListener('click', () => toggleModal(true));
    closeBtn?.addEventListener('click', () => toggleModal(false));
    closeBottom?.addEventListener('click', () => toggleModal(false));
    modal?.addEventListener('click', (e) => {
        if (e.target === modal) toggleModal(false);
    });
    searchInput?.addEventListener('input', applyFilter);
    typeSelect?.addEventListener('change', applyFilter);
    categoryInput?.addEventListener('input', applyFilter);
    applyFilter();

    const syncSelection = () => {
        if (!projectForm) return;
        hiddenHolder.innerHTML = '';
        const selected = document.querySelectorAll('.bank-checkbox:checked');
        selected.forEach(chk => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'bank_question_ids[]';
            input.value = chk.value;
            hiddenHolder.appendChild(input);
        });
        const badge = document.getElementById('selected-bank-count');
        if (badge) {
            badge.textContent = `${selected.length} سؤال مختار`;
        }
    };

    document.querySelectorAll('.bank-checkbox').forEach(chk => {
        chk.addEventListener('change', syncSelection);
    });

    applyBtn?.addEventListener('click', () => {
        syncSelection();
        toggleModal(false);
    });

    projectForm?.addEventListener('submit', () => {
        syncSelection();
    });
});
</script>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.error('Leaflet library not loaded');
        return;
    }

    // Initialize map
    const existingLat = <?= $project->latitude ?? 24.7136 ?>;
    const existingLng = <?= $project->longitude ?? 46.6753 ?>;
    const hasLocation = <?= !empty($project->latitude) ? 'true' : 'false' ?>;

    const map = L.map('map').setView([existingLat, existingLng], hasLocation ? 12 : 6);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    let marker = null;

    // Add existing marker if location exists
    if (hasLocation) {
        marker = L.marker([existingLat, existingLng]).addTo(map);
    }

    // Handle map click
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        
        // Remove existing marker
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Add new marker
        marker = L.marker([lat, lng]).addTo(map);
        
        // Update form fields
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        document.getElementById('location_name').value = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        
        // Show selected location
        document.getElementById('selected-location').classList.remove('hidden');
        document.getElementById('location-display').textContent = `الإحداثيات: ${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        
        // Reverse geocoding
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=ar`)
            .then(response => response.json())
            .then(data => {
                if (data.display_name) {
                    document.getElementById('location_name').value = data.display_name;
                    document.getElementById('location-display').textContent = data.display_name;
                }
            })
            .catch(err => console.log('Geocoding error:', err));
    });
});
</script>
