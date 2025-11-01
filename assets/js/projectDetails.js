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
const role         = (getParam('role') || 'user').toLowerCase();

const TYPE_PAGE = {
  MCQ:        '../annotate/mcq.html',
  TRUE_FALSE: '../annotate/true-false.html',
  LIST:       '../annotate/list.html',
  OPEN_ENDED: '../annotate/open-ended.html'
};

const REMOTE_SOURCES = {
  SC_GENERAL: { source: 'sheet', url: 'https://docs.google.com/spreadsheets/d/1nwVsA24SzxqITv_-jVQ_rQWxQ4eqpGJmIifyxZq2jsY/gviz/tq?tqx=out:csv&gid=993750005' },
  SC_NORTH:   { source: 'sheet', url: 'https://docs.google.com/spreadsheets/d/1nwVsA24SzxqITv_-jVQ_rQWxQ4eqpGJmIifyxZq2jsY/gviz/tq?tqx=out:csv&gid=993750005' },
  SC_SOUTH:   { source: 'sheet', url: 'https://docs.google.com/spreadsheets/d/1nwVsA24SzxqITv_-jVQ_rQWxQ4eqpGJmIifyxZq2jsY/gviz/tq?tqx=out:csv&gid=993750005' },
  SC_EAST:    { source: 'sheet', url: 'https://docs.google.com/spreadsheets/d/1nwVsA24SzxqITv_-jVQ_rQWxQ4eqpGJmIifyxZq2jsY/gviz/tq?tqx=out:csv&gid=993750005' }
};

let dataFile = DATA_FILES.GENERAL;
if (projectId.includes('NORTH')) dataFile = DATA_FILES.NORTH;
else if (projectId.includes('SOUTH')) dataFile = DATA_FILES.SOUTH;
else if (projectId.includes('EAST'))  dataFile = DATA_FILES.EAST;

let categoryChartInstance = null;

function renderCategoryChart(allQuestions) {
  const categoryCounts = allQuestions.reduce((acc, q) => {
    const category = q.Category || 'غير مصنف';
    acc[category] = (acc[category] || 0) + 1;
    return acc;
  }, {});
  const labels = Object.keys(categoryCounts);
  const data   = Object.values(categoryCounts);

  const ctx = document.getElementById('categoryChart');
  if (!ctx || typeof Chart === 'undefined') return;

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
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
  });
}

function saveLastQType(v){ try{ localStorage.setItem('lastQType', v); }catch{} }
function loadLastQType(){ try{ return localStorage.getItem('lastQType'); }catch{ return null; } }

async function loadProjectDetails() {
  try {
    const allProjects = window.PROJECTS || [];
    if (!allProjects.length) {
      document.getElementById('project-name').textContent   = "خطأ في تهيئة البيانات";
      document.getElementById('project-summary').textContent = "تم تحميل mockData.js لكن window.PROJECTS فارغة.";
      document.getElementById('qtype-wrap')?.replaceChildren();
      return;
    }

    const project = allProjects.find(p => (p.id || '').toUpperCase() === projectId);
    if (!project) {
      document.getElementById('project-name').textContent   = "المشروع غير موجود";
      document.getElementById('project-summary').textContent = "تأكد من ID المشروع في الرابط وملف mockData.js.";
      document.getElementById('qtype-wrap')?.replaceChildren();
      return;
    }

    document.getElementById('project-name').textContent = project.name || projectId;
    document.getElementById('project-summary').textContent =
      role === 'manager' ? (project.managerDescription || '') : (project.userDescription || '');

    if (role === 'manager') {
      const panel = document.getElementById('manager-panel');
      if (panel) panel.style.display = 'block';
    }

    const res = await fetch(dataFile, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`HTTP ${res.status} عند تحميل ${dataFile}`);
    const allQuestions = await res.json();

    if (role === 'manager') renderCategoryChart(allQuestions);

    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      const qTypes = Array.isArray(project.questionTypes) ? project.questionTypes.join(', ') : '—';
      pdMeta.innerHTML = `
        <span class="chip">المهام الكلية: ${allQuestions.length}</span>
        <span class="chip">أنواع الأسئلة: ${qTypes}</span>
      `;
    }

    const sel = document.getElementById('qTypeSelect');
    if (sel) {
      const last = loadLastQType();
      if (last && sel.querySelector(`option[value="${last}"]`)) sel.value = last;
      sel.addEventListener('change', () => saveLastQType(sel.value));
    }
    const pickedType = (document.getElementById('qTypeSelect')?.value || 'MCQ').toUpperCase();

    let categoriesArr = Array.isArray(project.categories) ? project.categories.slice() : [];
    if (!categoriesArr.length) {
      const uniq = [...new Set(allQuestions.map(q => String(q.Category || 'غير مصنف')))];
      categoriesArr = uniq.map(v => ({ id: v, label: v }));
    }

    const wrap = document.getElementById('qtype-wrap');
    if (wrap) {
      wrap.innerHTML = '';
      categoriesArr.forEach(category => {
        const count = allQuestions.filter(q => String(q.Category || '') === category.id).length;

        const card = document.createElement('div');
        card.className = 'qtype-card';
        card.setAttribute('data-cat', category.id);
        card.innerHTML = `
          <h3>${category.label || category.id}</h3>
          <p class="small-gray">عدد الأسئلة: ${count}</p>
          <p class="small-gray">النوع الحالي: ${pickedType}</p>
        `;

        card.addEventListener('click', () => {
          const page = TYPE_PAGE[pickedType] || TYPE_PAGE.MCQ;
          const url  = new URL(page, location.href);
          url.searchParams.set('id', projectId);
          url.searchParams.set('role', role);
          url.searchParams.set('type', pickedType);
          url.searchParams.set('cat', category.id);

          const remote = REMOTE_SOURCES[projectId];
          if (remote?.source && remote?.url) {
            url.searchParams.set('source', remote.source);
            url.searchParams.set('url', remote.url);
            url.searchParams.set('limit', '10');
          } else {
            url.searchParams.set('source', 'local');
            url.searchParams.set('limit', '10');
          }

          location.href = url.toString();
        });

        wrap.appendChild(card);
      });

      if (!categoriesArr.length) {
        wrap.innerHTML = `<div class="card"><p>لا توجد فئات لهذا المشروع.</p></div>`;
      }
    }
  } catch (e) {
    document.getElementById('project-name').textContent   = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ أثناء تحميل البيانات: ${e.message}`;
    const wrap = document.getElementById('qtype-wrap');
    if (wrap) wrap.innerHTML = "";
    console.error(e);
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
