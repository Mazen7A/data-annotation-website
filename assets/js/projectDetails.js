/* ==========================================================================
   projectDetails.js  —  MINIMAL-CHANGE, FULLY COMMENTED
   --------------------------------------------------------------------------
   What changed (without altering your structure):
   1) Normalize questions after fetch (type/category/options/answer).
   2) Robust type detection (MCQ / TRUE_FALSE / OPEN_ENDED / LIST).
   3) Correct per-category counting based on project's category ids.
   4) Safer rendering + small UX touches (count badge).
   ========================================================================== */

const DATA_FILES = {
  GENERAL: '../assets/data/GENERAL-questions.json',
  NORTH:   '../assets/data/NORTH-questions.json',
  SOUTH:   '../assets/data/SOUTH-questions.json',
  EAST:    '../assets/data/EAST-questions.json'
};

/* --------------------------- URL helpers (unchanged) --------------------------- */
function getParam(name) {
  const u = new URL(window.location.href);
  return u.searchParams.get(name) || "";
}

const rawProjectId = getParam('id') || 'SC_GENERAL';
const projectId    = rawProjectId.toUpperCase().trim();
const role         = getParam('role') || 'user';

const TYPE_PAGE = {
  MCQ:        '../annotate/mcq.html',
  TRUE_FALSE: '../annotate/true-false.html',
  LIST:       '../annotate/list.html',
  OPEN_ENDED: '../annotate/open-ended.html'
};

const REMOTE_SOURCES = {}; // keep local only here

/* --------------------------- Select the right local file ----------------------- */
let dataFile = DATA_FILES.GENERAL;
if (projectId.includes('NORTH')) dataFile = DATA_FILES.NORTH;
else if (projectId.includes('SOUTH')) dataFile = DATA_FILES.SOUTH;
else if (projectId.includes('EAST'))  dataFile = DATA_FILES.EAST;

let categoryChartInstance = null;
let RAW_ITEMS = []; // keep raw/normalized items to recalc on qType change

/* =============================== NORMALIZATION ===============================
   Make records consistent so counting & filtering are always correct.
   - Unify keys: type, Category, question, options[], answer (index if possible)
   - Support options as array, CSV string, or OptionA..OptionD columns
   ========================================================================== */
function normalizeRow(row = {}) {
  const q = { ...row };

  // --- unify base keys ---
  q.type     = q.type     || q.Type     || '';
  q.Category = q.Category || q.category || q.cat || 'غير مصنف';
  q.question = q.question || q.Question || q.q   || q.text || '';

  // --- normalize options ---
  let options = q.options || q.Options || q.choices || q.Choices;

  if (Array.isArray(options)) {
    options = options.filter(Boolean);
  } else if (typeof options === 'string') {
    options = options.split(',').map(s => String(s).trim()).filter(Boolean);
  } else if (q.OptionA || q.OptionB || q.OptionC || q.OptionD) {
    options = [q.OptionA, q.OptionB, q.OptionC, q.OptionD].filter(Boolean);
  } else {
    options = [];
  }
  q.options = options;

  // --- normalize answer ---
  // allow number index or exact text; map text -> index if we can
  if (q.answer === undefined && q.Answer !== undefined) q.answer = q.Answer;
  if (typeof q.answer === 'string' && q.options.length) {
    const idx = q.options.findIndex(o => String(o).trim() === String(q.answer).trim());
    if (idx >= 0) q.answer = idx;
  }

  return q;
}

/* =============================== TYPE DETECTORS =============================== */
/* Match annotate.js / dataSources.js logic so filters are consistent. */
function isMCQ(x) {
  return (String(x.type||x.Type||'').toUpperCase().includes('MCQ'))
      || (Array.isArray(x.options) && x.options.filter(Boolean).length >= 2);
}
function isTrueFalse(x) {
  const t = String(x.type||x.Type||'').toUpperCase();
  return t.includes('TRUE') || (typeof x.answer === 'boolean');
}
function isOpenEnded(x) {
  const t = String(x.type||x.Type||'').toUpperCase();
  // If it's not MCQ nor TF, treat as open-ended
  return t.includes('OPEN') || (!isMCQ(x) && !isTrueFalse(x));
}
function isList(x) {
  return String(x.type||'').toUpperCase().includes('LIST');
}

/* Filter array by picked type */
function filterByPickedType(items, picked) {
  switch (picked) {
    case 'MCQ':        return items.filter(isMCQ);
    case 'TRUE_FALSE': return items.filter(isTrueFalse);
    case 'OPEN_ENDED': return items.filter(isOpenEnded);
    case 'LIST':       return items.filter(isList);
    default:           return items;
  }
}

/* =============================== MANAGER CHART =============================== */
function renderCategoryChart(allQuestions) {
  const categoryCounts = allQuestions.reduce((acc, q) => {
    const category = q.Category || q.category || 'غير مصنف';
    acc[category] = (acc[category] || 0) + 1;
    return acc;
  }, {});
  const labels = Object.keys(categoryCounts);
  const data   = Object.values(categoryCounts);

  const ctx = document.getElementById('categoryChart');
  if (!ctx) return;

  if (categoryChartInstance) categoryChartInstance.destroy();

  categoryChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{ label: 'عدد الأسئلة', data, borderWidth: 1 }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display:false }, title:{ display:true, text:'توزيع الأسئلة حسب الفئة', font:{ size:16 } } },
      scales: { y: { beginAtZero:true, ticks:{ stepSize:1 } } }
    }
  });
}

/* =============================== PERSIST QTYPE =============================== */
function saveLastQType(val){ try{ localStorage.setItem('lastQType', val); }catch{} }
function loadLastQType(){ try{ return localStorage.getItem('lastQType'); }catch{ return null; } }

/* =============================== RENDER CARDS ================================ */
/**
 * Render per-category cards with accurate counts for the currently picked type.
 * - Uses project.categories[*].id to match against question.Category
 * - Count is shown in a compact badge.
 */
function refreshCategoryCards(project) {
  const sel = document.getElementById('qTypeSelect');
  const pickedType = (sel?.value || 'MCQ').toUpperCase();

  const wrap = document.getElementById('qtype-wrap');
  if (!wrap) return;

  wrap.innerHTML = '';

  // Filter items by selected type
  const filtered = filterByPickedType(RAW_ITEMS, pickedType);

  // Build a lookup of counts per Category value
  const perCatCount = filtered.reduce((acc, q) => {
    const cat = q.Category || 'غير مصنف';
    acc[cat] = (acc[cat] || 0) + 1;
    return acc;
  }, {});

  // Render each configured category as a card
  project.categories.forEach(category => {
    const catId   = category.id;          // this is how your data models the id
    const catName = category.label || catId;
    const count   = perCatCount[catId] || 0;

    const card = document.createElement('div');
    card.className = 'qtype-card';
    card.setAttribute('data-cat', catId);
    card.innerHTML = `
      <h3>${catName}</h3>
      <div class="muted">عدد الأسئلة:</div>
      <div class="count-badge">${count}</div>
      <div class="card-actions">
        <button class="btn btn-primary">فتح البطاقة</button>
      </div>
    `;

    card.addEventListener('click', () => {
      const page = TYPE_PAGE[pickedType] || TYPE_PAGE.MCQ;
      const url = new URL(page, location.href);
      url.searchParams.set('id', projectId);
      url.searchParams.set('role', role);
      url.searchParams.set('type', pickedType);
      url.searchParams.set('cat', catId);

      // OPTIONAL: enforce limits per type
      // if (pickedType === 'MCQ')        url.searchParams.set('limit', '10');
      // if (pickedType === 'OPEN_ENDED') url.searchParams.set('limit', '5');

      location.href = url.toString();
    });

    wrap.appendChild(card);
  });
}

/* =============================== BOOTSTRAP PAGE ============================= */
async function loadProjectDetails() {
  try {
    const allProjects = window.PROJECTS || [];
    if (!allProjects.length) {
      document.getElementById('project-name').textContent    = "خطأ في تهيئة البيانات";
      document.getElementById('project-summary').textContent = "تم تحميل mockData.js ولكن window.PROJECTS فارغة.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    const project = allProjects.find(p => p.id === projectId);
    if (!project) {
      document.getElementById('project-name').textContent    = "المشروع غير موجود";
      document.getElementById('project-summary').textContent = "تأكد من ID المشروع في الرابط وملف mockData.js.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    // Fill header text
    document.getElementById('project-name').textContent = project.name;
    document.getElementById('project-summary').textContent =
      role === 'manager' ? project.managerDescription : project.userDescription;

    // Show manager panel (chart) if role is manager
    if (role === 'manager') {
      const panel = document.getElementById('manager-panel');
      if (panel) panel.style.display = 'block';
    }

    // Fetch local questions JSON
    const res = await fetch(dataFile, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`HTTP ${res.status} عند تحميل ${dataFile}`);

    const raw = await res.json();

    // Normalize to consistent shape
    const items = Array.isArray(raw) ? raw : (raw.items || []);
    RAW_ITEMS = items.map(normalizeRow);

    // Optional chart for manager
    if (role === 'manager') renderCategoryChart(RAW_ITEMS);

    // Meta chips (total + available types)
    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      const availableTypes = Array.from(new Set(
        RAW_ITEMS
          .map(q => (isMCQ(q) ? 'MCQ' : isTrueFalse(q) ? 'TRUE_FALSE' : isOpenEnded(q) ? 'OPEN_ENDED' : isList(q) ? 'LIST' : 'OTHER'))
      ));
      pdMeta.innerHTML = `
        <span class="chip">إجمالي الأسئلة: ${RAW_ITEMS.length}</span>
        <span class="chip">الأنواع المتاحة: ${availableTypes.join(', ')}</span>
      `;
    }

    // Restore last picked qType and listen for changes
    const sel = document.getElementById('qTypeSelect');
    if (sel) {
      const last = loadLastQType();
      if (last && sel.querySelector(`option[value="${last}"]`)) sel.value = last;
      sel.addEventListener('change', () => {
        saveLastQType(sel.value);
        refreshCategoryCards(project);
      });
    }

    // First render
    refreshCategoryCards(project);
  } catch (e) {
    document.getElementById('project-name').textContent    = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ أثناء تحميل البيانات: ${e.message}`;
    const wrap = document.getElementById('qtype-wrap'); if (wrap) wrap.innerHTML = "";
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
