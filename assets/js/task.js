const DATA_FILES_MAP = {
  'SC_GENERAL': '../assets/data/GENERAL-questions.json',
  'SC_NORTH':   '../assets/data/NORTH-questions.json',
  'SC_SOUTH':   '../assets/data/SOUTH-questions.json',
  'SC_EAST':    '../assets/data/EAST-questions.json'
};

let currentTaskIndex = 0;
let tasksList = [];
let selectedRating = null;

function getParam(name) {
  const urlParams = new URLSearchParams(window.location.search);
  return urlParams.get(name);
}

function updateProgress(currentIndex, totalTasks) {
  const el = document.getElementById('category-progress');
  if (!el) return;
  const completed = currentIndex;
  const total = totalTasks;
  const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
  el.textContent = `${completed}/${total} (${percentage}%)`;
}

function selectRating(ratingId) {
  selectedRating = ratingId;
  const container = document.getElementById('rating-options-container');
  if (!container) return;
  container.querySelectorAll('div').forEach(d => d.classList.remove('selected'));
  if (ratingId) {
    const selected = document.getElementById(`rating-${ratingId}`);
    if (selected) selected.classList.add('selected');
  }
}

function renderRatingOptions(options) {
  const container = document.getElementById('rating-options-container');
  if (!container) return;
  container.innerHTML = '';
  options.forEach(option => {
    const div = document.createElement('div');
    div.id = `rating-${option.id}`;
    div.textContent = option.label;
    div.onclick = () => selectRating(option.id);
    container.appendChild(div);
  });
}

function displayCurrentTask(projectId) {
  const project = (window.PROJECTS || []).find(p => p.id === projectId);

  if (!tasksList.length || currentTaskIndex >= tasksList.length) {
    const qt = document.getElementById('question-text');
    if (qt) qt.textContent = "تهانينا! لا توجد مهام متبقية في هذه الفئة.";
    document.getElementById('submit-btn')?.setAttribute('disabled', 'disabled');
    document.getElementById('skip-btn')?.setAttribute('disabled', 'disabled');
    const roc = document.getElementById('rating-options-container'); 
    if (roc) roc.innerHTML = '';
    updateProgress(tasksList.length, tasksList.length);
    return;
  }

  const currentTask = tasksList[currentTaskIndex];

  document.getElementById('task-page-title').textContent = `${project?.name || 'المشروع'} - تقييم`;
  document.getElementById('project-header-name').textContent = project?.name || 'منصة التقييم';
  document.getElementById('task-title').textContent = `مهمة تقييم: ${project?.name || 'المشروع'}`;
  document.getElementById('question-category').textContent = `فئة السؤال: ${currentTask.Category || 'غير محدد'}`;
  document.getElementById('question-text').textContent = currentTask.Question || '—';
  document.getElementById('current-task-id').textContent = (currentTask.Question || '').substring(0, 15) + '...';
  document.getElementById('remaining-tasks').textContent = tasksList.length - currentTaskIndex;
  document.getElementById('task-notes').value = '';

  renderRatingOptions(window.RATING_OPTIONS || []);
  selectRating(null);
  updateProgress(currentTaskIndex, tasksList.length);
}

function submitTask() {
  if (!selectedRating) {
    alert('من فضلك اختر تقييماً قبل الإرسال.');
    return;
  }
  currentTaskIndex++;
  displayCurrentTask(getParam('id')?.toUpperCase());
}

function skipTask() {
  currentTaskIndex++;
  displayCurrentTask(getParam('id')?.toUpperCase());
}

async function loadTaskData() {
  const projectId = (getParam('id') || '').toUpperCase();
  const categoryId = getParam('category');

  if (!projectId || !categoryId) {
    document.getElementById('question-text').textContent = "خطأ: لم يتم تحديد المشروع أو الفئة بشكل صحيح في الرابط.";
    return;
  }

  const dataPath = DATA_FILES_MAP[projectId];
  if (!dataPath) {
    document.getElementById('question-text').textContent = `خطأ: مسار بيانات غير معروف للمشروع ${projectId}`;
    return;
  }

  try {
    const response = await fetch(dataPath, { cache: 'no-cache' });
    if (!response.ok) throw new Error(`HTTP ${response.status} للملف: ${dataPath}`);

    const projectData = await response.json();
    tasksList = projectData.filter(task => task.Category === categoryId).slice(0, 10);
    currentTaskIndex = 0;
    displayCurrentTask(projectId);

    document.getElementById('submit-btn')?.addEventListener('click', submitTask);
    document.getElementById('skip-btn')?.addEventListener('click', skipTask);

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        submitTask();
      } else if (e.key === 's' || e.key === 'S') {
        e.preventDefault();
        skipTask();
      }
    });
  } catch (err) {
    document.getElementById('question-text').textContent =
      `حدث خطأ أثناء تحميل بيانات المهام. تحقق من مسار ملف JSON: ${err.message}`;
  }
}

document.addEventListener('DOMContentLoaded', loadTaskData);
