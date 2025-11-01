 
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

const rawProjectId = getParam('id');
const projectId    = (rawProjectId || 'SC_GENERAL').toUpperCase().trim();
const role         = getParam('role') || 'user';

// ✅ صفحات التوسيم حسب النوع
const TYPE_PAGE = {
  MCQ:        '../annotate/mcq.html',
  TRUE_FALSE: '../annotate/true-false.html',
  LIST:       '../annotate/list.html',
  OPEN_ENDED: '../annotate/open-ended.html'
};

// ✅ مصادر خارجية (اختياري) — عدّلها حسب احتياجك
// ضع raw URL من GitHub أو CSV من Google Sheets (صيغة gviz:out=csv&sheet=...)
const REMOTE_SOURCES = {
  'SC_GENERAL': {
    // مثال من Google Sheet
    // source: 'sheet',
    // url: 'https://docs.google.com/spreadsheets/d/<ID>/gviz/tq?tqx=out:csv&sheet=General'
  },
  'SC_SOUTH': {
    // مثال من GitHub RAW
    // source: 'github',
    // url: 'https://raw.githubusercontent.com/LamaAy/SaudiCulture-Dataset/main/regions/SOUTH.json'
  }
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

  const chartData = {
    labels,
    datasets: [{
      label: 'عدد الأسئلة',
      data,
      backgroundColor: ['#047857','#065f46','#22c55e','#a3e635','#fde047','#f97316','#ef4444','#7c3aed','#1e40af','#4f46e5'],
      borderColor: '#14532d',
      borderWidth: 1
    }]
  };

  const ctx = document.getElementById('categoryChart');
  if (!ctx) return;

  if (categoryChartInstance) categoryChartInstance.destroy();

  categoryChartInstance = new Chart(ctx, {
    type: 'bar',
    data: chartData,
    options: {
      responsive: true,
      plugins: { legend: { display: false }, title: { display: true, text: 'توزيع الأسئلة حسب الفئة', font: { size: 16 } } },
      scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
  });
}

function saveLastQType(val){ try{ localStorage.setItem('lastQType', val); }catch{} }
function loadLastQType(){ try{ return localStorage.getItem('lastQType'); }catch{ return null; } }

async function loadProjectDetails() {
  try {
    const allProjects = window.PROJECTS || [];
    if (!allProjects.length) {
      document.getElementById('project-name').textContent   = "خطأ في تهيئة البيانات";
      document.getElementById('project-summary').textContent= "تم تحميل mockData.js ولكن مصفوفة المشاريع (window.PROJECTS) فارغة.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    const project = allProjects.find(p => p.id === projectId);
    if (!project) {
      document.getElementById('project-name').textContent   = "المشروع غير موجود";
      document.getElementById('project-summary').textContent= "تأكد من ID المشروع في الرابط وملف mockData.js.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    document.getElementById('project-name').textContent = project.name;
    document.getElementById('project-summary').textContent =
      role === 'manager' ? project.managerDescription : project.userDescription;

    if (role === 'manager') {
      const managerPanel = document.getElementById('manager-panel');
      if (managerPanel) managerPanel.style.display = 'block';
    }

    // تحميل أسئلة الملف المحلي لحساب عدد الأسئلة في كل فئة فقط
    const response = await fetch(dataFile);
    if (!response.ok) throw new Error(`HTTP ${response.status} عند تحميل ${dataFile}`);
    const allQuestions = await response.json();

    if (role === 'manager') renderCategoryChart(allQuestions);

    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      pdMeta.innerHTML = `
        <span class="chip">المهام الكلية: ${allQuestions.length}</span>
        <span class="chip">نوع الأسئلة: ${project.questionTypes.join(', ')}</span>
      `;
    }

    // ► إعداد قيمة نوع الأسئلة الافتراضية
    const sel = document.getElementById('qTypeSelect');
    if (sel) {
      const last = loadLastQType();
      if (last && sel.querySelector(`option[value="${last}"]`)) sel.value = last;
      sel.addEventListener('change', () => saveLastQType(sel.value));
    }

    // ► رسم بطاقات الأقسام + التنقّل لصفحات التوسيم الصحيحة
    const qtypeWrap = document.getElementById('qtype-wrap');
    if (qtypeWrap) {
      qtypeWrap.innerHTML = '';
      project.categories.forEach(category => {
        const count = allQuestions.filter(q => q.Category === category.id).length;

        const card = document.createElement('div');
        card.className = 'qtype-card';
        card.setAttribute('data-cat', category.id);
        card.innerHTML = `
          <h3>${category.label}</h3>
          <div class="muted">عدد الأسئلة: ${count}</div>
        `;

        card.addEventListener('click', () => {
          const pickedType = (document.getElementById('qTypeSelect')?.value || 'MCQ').toUpperCase();
          const page = TYPE_PAGE[pickedType] || TYPE_PAGE.MCQ;

          const url = new URL(page, location.href);
          url.searchParams.set('id', projectId);
          url.searchParams.set('role', role);
          url.searchParams.set('type', pickedType);
          url.searchParams.set('cat', category.id);

          // تمرير مصدر خارجي لهذا المشروع (إن وُجد)
          const remote = REMOTE_SOURCES[projectId];
          if (remote?.source && remote?.url) {
            url.searchParams.set('source', remote.source); // 'sheet' | 'github'
            url.searchParams.set('url', remote.url);
            url.searchParams.set('limit', '10');           // الدكتور يطلب 10 أسئلة
          }

          location.href = url.toString();
        });

        qtypeWrap.appendChild(card);
      });
    }

  } catch (error) {
    document.getElementById('project-name').textContent = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ أثناء تحميل أو معالجة البيانات: ${error.message}`;
    const qwrap = document.getElementById('qtype-wrap'); if (qwrap) qwrap.innerHTML = "";
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
