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
  if (!ctx) return;

  if (categoryChartInstance) categoryChartInstance.destroy();

  categoryChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
      labels,
      datasets: [{
        label: 'عدد الأسئلة',
        data,
        backgroundColor: ['#047857','#065f46','#22c55e','#a3e635','#fde047','#f97316','#ef4444','#7c3aed','#1e40af','#4f46e5'],
        borderColor: '#14532d',
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

    const res = await fetch(dataFile);
    if (!res.ok) throw new Error(`HTTP ${res.status} عند تحميل ${dataFile}`);
    const allQuestions = await res.json();

    if (role === 'manager') renderCategoryChart(allQuestions);

    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      pdMeta.innerHTML = `
        <span class="chip">المهام الكلية: ${allQuestions.length}</span>
        <span class="chip">نوع الأسئلة: ${project.questionTypes.join(', ')}</span>
      `;
    }

    const wrap = document.getElementById('qtype-wrap');
    if (wrap) {
      wrap.innerHTML = '';
      project.categories.forEach(category => {
        const count = allQuestions.filter(q => q.Category === category.id).length;

        wrap.innerHTML += `
          <a href="task.html?id=${projectId}&category=${category.id}" class="qtype-card">
            <h3>${category.label}</h3>
            <p class="small-gray">عدد الأسئلة: ${count}</p>
          </a>
        `;
      });
    }
  } catch (e) {
    document.getElementById('project-name').textContent   = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ أثناء تحميل البيانات: ${e.message}`;
    const wrap = document.getElementById('qtype-wrap'); if (wrap) wrap.innerHTML = "";
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
