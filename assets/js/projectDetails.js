
const DATA_FILES = {
  GENERAL: '../assets/data/GENERAL-questions.json',
  NORTH:   '../assets/data/NORTH-questions.json',
  SOUTH:   '../assets/data/SOUTH-questions.json',
  EAST:    '../assets/data/EAST-questions.json'
};

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

const REMOTE_SOURCES = {}; // نتركها فارغة هنا؛ التصفح داخل تفاصيل المشروع يعتمد على المحلي فقط لعرض العدّ

let dataFile = DATA_FILES.GENERAL;
if (projectId.includes('NORTH')) dataFile = DATA_FILES.NORTH;
else if (projectId.includes('SOUTH')) dataFile = DATA_FILES.SOUTH;
else if (projectId.includes('EAST'))  dataFile = DATA_FILES.EAST;

let categoryChartInstance = null;
let RAW_ITEMS = []; // نحتفظ بنسخة خام لنعيد الحساب عند تغيير نوع الأسئلة

// Helpers to detect types (مطابقة لطريقة annotate.js/dataSources.js)
function isMCQ(x) {
  return (String(x.type||x.Type||'').toUpperCase().includes('MCQ')) || (Array.isArray(x.options) && x.options.filter(Boolean).length >= 2);
}
function isTrueFalse(x) {
  const t = String(x.type||x.Type||'').toUpperCase();
  return t.includes('TRUE') || (typeof x.answer === 'boolean');
}
function isOpenEnded(x) {
  const t = String(x.type||x.Type||'').toUpperCase();
  // لا توجد خيارات (أو أقل من 2) وليس true/false
  return t.includes('OPEN') || (!isMCQ(x) && !isTrueFalse(x));
}
function filterByPickedType(items, picked) {
  switch (picked) {
    case 'MCQ': return items.filter(isMCQ);
    case 'TRUE_FALSE': return items.filter(isTrueFalse);
    case 'OPEN_ENDED': return items.filter(isOpenEnded);
    case 'LIST': return items.filter(x => String(x.type||'').toUpperCase().includes('LIST'));
    default: return items;
  }
}

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
      datasets: [{
        label: 'عدد الأسئلة',
        data,
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display:false }, title:{ display:true, text:'توزيع الأسئلة حسب الفئة', font:{ size:16 } } },
      scales: { y: { beginAtZero:true, ticks:{ stepSize:1 } } }
    }
  });
}

function saveLastQType(val){ try{ localStorage.setItem('lastQType', val); }catch{} }
function loadLastQType(){ try{ return localStorage.getItem('lastQType'); }catch{ return null; } }

function refreshCategoryCards(project) {
  const sel = document.getElementById('qTypeSelect');
  const pickedType = (sel?.value || 'MCQ').toUpperCase();

  const wrap = document.getElementById('qtype-wrap');
  if (!wrap) return;

  wrap.innerHTML = '';

  // فلتر العناصر وفق النوع المختار
  const filtered = filterByPickedType(RAW_ITEMS, pickedType);

  project.categories.forEach(category => {
    const count = filtered.filter(q => (q.Category || q.category) === category.id).length;

    const card = document.createElement('div');
    card.className = 'qtype-card';
    card.setAttribute('data-cat', category.id);
    card.innerHTML = `
      <h3>${category.label}</h3>
      <p class="small-gray">عدد الأسئلة: ${count}</p>
    `;

    card.addEventListener('click', () => {
      const page = TYPE_PAGE[pickedType] || TYPE_PAGE.MCQ;
      const url = new URL(page, location.href);
      url.searchParams.set('id', projectId);
      url.searchParams.set('role', role);
      url.searchParams.set('type', pickedType);
      url.searchParams.set('cat', category.id);
      location.href = url.toString();
    });

    wrap.appendChild(card);
  });
}

async function loadProjectDetails() {
  try {
    const allProjects = window.PROJECTS || [];
    if (!allProjects.length) {
      document.getElementById('project-name').textContent = "خطأ في تهيئة البيانات";
      document.getElementById('project-summary').textContent = "تم تحميل mockData.js ولكن window.PROJECTS فارغة.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    const project = allProjects.find(p => p.id === projectId);
    if (!project) {
      document.getElementById('project-name').textContent = "المشروع غير موجود";
      document.getElementById('project-summary').textContent = "تأكد من ID المشروع في الرابط وملف mockData.js.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    document.getElementById('project-name').textContent = project.name;
    document.getElementById('project-summary').textContent =
      role === 'manager' ? project.managerDescription : project.userDescription;

    if (role === 'manager') {
      const panel = document.getElementById('manager-panel');
      if (panel) panel.style.display = 'block';
    }

    const res = await fetch(dataFile, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`HTTP ${res.status} عند تحميل ${dataFile}`);
    const raw = await res.json();
    RAW_ITEMS = Array.isArray(raw) ? raw : (raw.items || []);

    if (role === 'manager') renderCategoryChart(RAW_ITEMS);

    // ميتاداتا أعلى البطاقة
    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      pdMeta.innerHTML = `
        <span class="chip">المهام الكلية: ${RAW_ITEMS.length}</span>
        <span class="chip">نوع الأسئلة: ${project.questionTypes.join(', ')}</span>
      `;
    }

    // حدث تغيير نوع الأسئلة → أعِد عدّ العناصر لكل بطاقة
    const sel = document.getElementById('qTypeSelect');
    if (sel) {
      const last = loadLastQType();
      if (last && sel.querySelector(`option[value="${last}"]`)) sel.value = last;
      sel.addEventListener('change', () => {
        saveLastQType(sel.value);
        refreshCategoryCards(project);
      });
    }

    refreshCategoryCards(project);
  } catch (e) {
    document.getElementById('project-name').textContent   = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ أثناء تحميل البيانات: ${e.message}`;
    const wrap = document.getElementById('qtype-wrap'); if (wrap) wrap.innerHTML = "";
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
