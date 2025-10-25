const DATA_URL = '../assets/data/south-questions.json';

async function loadProjectDetails() {
  try {
    const response = await fetch(DATA_URL);
    const data = await response.json();

    const numberOfAnnotators = new Set(data.map(q => q.annotator)).size;
    const completedTasks = data.filter(q => q.completed).length;
    const progressPercent = ((completedTasks / data.length) * 100).toFixed(1);

    document.getElementById('num-annotators').textContent = numberOfAnnotators;
    document.getElementById('completed-tasks').textContent = completedTasks;
    document.getElementById('progress-percent').textContent = `${progressPercent}%`;

  } catch (error) {
    console.error('فشل في تحميل البيانات:', error);
  }
}

window.onload = loadProjectDetails;
