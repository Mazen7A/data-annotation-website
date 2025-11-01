const DATA_FILES = {
  GENERAL: '../assets/data/GENERAL-questions.json',
  NORTH: '../assets/data/NORTH-questions.json',
  SOUTH: '../assets/data/SOUTH-questions.json',
  EAST: '../assets/data/EAST-questions.json'
};

function getParam(name) {
  const u = new URL(window.location.href);
  return u.searchParams.get(name) || "";
}

const rawProjectId = getParam('id');
const projectId = rawProjectId.toUpperCase().trim();
const role = getParam('role');

let dataFile = DATA_FILES.GENERAL;
if (projectId.includes('NORTH')) dataFile = DATA_FILES.NORTH;
else if (projectId.includes('SOUTH')) dataFile = DATA_FILES.SOUTH;
else if (projectId.includes('EAST')) dataFile = DATA_FILES.EAST;

let categoryChartInstance = null;

function renderCategoryChart(allQuestions) {
  const categoryCounts = allQuestions.reduce((acc, q) => {
    const category = q.Category || 'غير مصنف';
    acc[category] = (acc[category] || 0) + 1;
    return acc;
  }, {});

  const labels = Object.keys(categoryCounts);
  const data = Object.values(categoryCounts);

  const backgroundColors = [
    '#047857', '#065f46', '#22c55e', '#a3e635', '#fde047',
    '#f97316', '#ef4444', '#7c3aed', '#1e40af', '#4f46e5'
  ];

  const chartData = {
    labels: labels,
    datasets: [{
      label: 'عدد الأسئلة',
      data: data,
      backgroundColor: backgroundColors.slice(0, labels.length),
      borderColor: '#14532d',
      borderWidth: 1
    }]
  };

  const ctx = document.getElementById('categoryChart');

  if (ctx) {
    if (categoryChartInstance) {
      categoryChartInstance.destroy();
    }

    categoryChartInstance = new Chart(ctx, {
      type: 'bar',
      data: chartData,
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          title: {
            display: true,
            text: 'توزيع الأسئلة حسب الفئة',
            font: { size: 16 }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });
  }
}

async function loadProjectDetails() {
  try {
    const allProjects = window.PROJECTS || [];

    if (allProjects.length === 0) {
      document.getElementById('project-name').textContent = "خطأ في تهيئة البيانات";
      document.getElementById('project-summary').textContent = "تم تحميل mockData.js ولكن مصفوفة المشاريع (window.PROJECTS) فارغة.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    const project = allProjects.find(p => p.id === projectId);

    if (!project) {
      document.getElementById('project-name').textContent = "المشروع غير موجود";
      document.getElementById('project-summary').textContent = "الرجاء التأكد من تطابق ID المشروع في الرابط وملف mockData.js.";
      document.getElementById('qtype-wrap').innerHTML = "";
      return;
    }

    document.getElementById('project-name').textContent = project.name;
    document.getElementById('project-summary').textContent =
      role === 'manager' ? project.managerDescription : project.userDescription;

    const managerPanel = document.getElementById('manager-panel');
    if (role === 'manager' && managerPanel) {
      managerPanel.style.display = 'block';
    }

    const response = await fetch(dataFile);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status} for file: ${dataFile}`);
    }

    const allQuestions = await response.json();

    if (role === 'manager') {
      renderCategoryChart(allQuestions);
    }

    const numberOfAnnotators = project.annotatorsCount || 0;
    const completedTasks = project.stats?.completedTasks || 0;
    const totalTasks = project.stats?.totalTasks || allQuestions.length;
    const progressPercent = totalTasks > 0 ? ((completedTasks / totalTasks) * 100).toFixed(1) : 0;

    const pdMeta = document.getElementById('pd-meta');
    if (pdMeta) {
      pdMeta.innerHTML = `
        <span class="chip">المهام الكلية: ${totalTasks}</span>
        <span class="chip">نوع الأسئلة: ${project.questionTypes.join(', ')}</span>
      `;
    }

    const managerStatsElement = document.getElementById('manager-stats');
    if (managerStatsElement && role === 'manager') {
      managerStatsElement.innerHTML = `
        <p><strong>عدد المقيمين النشطين:</strong> ${numberOfAnnotators}</p>
        <p><strong>المهام المنجزة / الكلية:</strong> ${completedTasks} / ${totalTasks}</p>
        <p><strong>نسبة الإنجاز الكلية:</strong> ${progressPercent}%</p>
        <button onclick="loadProjectDetails()" class="stats-btn" style="margin-top:10px;">🔄 تحديث البيانات</button>
      `;
    }

    const qtypeWrap = document.getElementById('qtype-wrap');
    if (qtypeWrap) {
      qtypeWrap.innerHTML = '';

      project.categories.forEach(category => {
        const categoryId = category.id;
        const categoryLabel = category.label;
        const count = allQuestions.filter(q => q.Category === categoryId).length;

        qtypeWrap.innerHTML += `
          <a href="tasks.html?id=${projectId}&category=${categoryId}" class="qtype-card">
            <h3>${categoryLabel}</h3>
            <p class="small-gray">عدد الأسئلة: ${count}</p>
          </a>
        `;
      });
    }

  } catch (error) {
    document.getElementById('project-name').textContent = "خطأ حاسم";
    document.getElementById('project-summary').textContent = `حدث خطأ حاسم أثناء تحميل أو معالجة البيانات: ${error.message}`;
    document.getElementById('qtype-wrap').innerHTML = "";
  }
}

document.addEventListener('DOMContentLoaded', loadProjectDetails);
