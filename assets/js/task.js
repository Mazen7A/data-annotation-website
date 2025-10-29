const DATA_FILES_MAP = {
    'SC_GENERAL': '../assets/data/GENERAL-questions.json', 
    'SC_NORTH': '../assets/data/NORTH-questions.json',
    'SC_SOUTH': '../assets/data/SOUTH-questions.json'
};

let currentTaskIndex = 0;
let tasksList = [];
let selectedRating = null;

function getParam(name) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(name);
}

function updateProgress(currentIndex, totalTasks) {
    const progressElement = document.getElementById('category-progress');
    if (progressElement) {
        const completed = currentIndex;
        const total = totalTasks;
        const percentage = total > 0 ? Math.round((completed / total) * 100) : 0;
        progressElement.textContent = `${completed}/${total} (${percentage}%)`;
    }
}

function selectRating(ratingId) {
    selectedRating = ratingId;
    const optionsContainer = document.getElementById('rating-options-container');
    
    optionsContainer.querySelectorAll('div').forEach(div => {
        div.classList.remove('selected');
    });

    const selectedElement = document.getElementById(`rating-${ratingId}`);
    if (selectedElement) {
        selectedElement.classList.add('selected');
    }
}

function renderRatingOptions(options) {
    const container = document.getElementById('rating-options-container');
    container.innerHTML = '';
    
    options.forEach(option => {
        const optionDiv = document.createElement('div');
        optionDiv.id = `rating-${option.id}`;
        optionDiv.textContent = option.label;
        optionDiv.onclick = () => selectRating(option.id);
        container.appendChild(optionDiv);
    });
}

function displayCurrentTask(projectId, categoryId) {
    const project = window.PROJECTS.find(p => p.id === projectId);
    
    if (tasksList.length === 0 || currentTaskIndex >= tasksList.length) {
        document.getElementById('question-text').textContent = "تهانينا! لا توجد مهام متبقية في هذه الفئة.";
        document.getElementById('submit-btn').disabled = true;
        document.getElementById('skip-btn').disabled = true;
        document.getElementById('rating-options-container').innerHTML = '';
        return;
    }

    const currentTask = tasksList[currentTaskIndex];
    
    document.getElementById('task-page-title').textContent = `${project?.name || 'المشروع'} - تقييم`;
    document.getElementById('project-header-name').textContent = project?.name || 'منصة التقييم';
    document.getElementById('task-title').textContent = `مهمة تقييم: ${project?.name || 'المشروع'}`;
    
    document.getElementById('question-category').textContent = `فئة السؤال: ${currentTask.Category || 'غير محدد'}`; 
    
    document.getElementById('question-text').textContent = currentTask.Question;
    document.getElementById('current-task-id').textContent = currentTask.Question.substring(0, 15) + '...';
    document.getElementById('remaining-tasks').textContent = tasksList.length - currentTaskIndex;
    document.getElementById('task-notes').value = '';

    if (window.RATING_OPTIONS) {
        renderRatingOptions(window.RATING_OPTIONS);
        selectRating(null);
    }
    
    updateProgress(currentTaskIndex, tasksList.length);
}

function submitTask() {
    if (!selectedRating) {
        alert("من فضلك، اختر تقييماً قبل الإرسال.");
        return;
    }

    currentTaskIndex++;
    displayCurrentTask(getParam('id'), getParam('category'));
}

function skipTask() {
    currentTaskIndex++;
    displayCurrentTask(getParam('id'), getParam('category'));
}

async function loadTaskData() {
    const projectId = getParam('id')?.toUpperCase() || ''; 
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
        const response = await fetch(dataPath);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status} for file: ${dataPath}`);
        }
        
        const projectData = await response.json();
        
        tasksList = projectData.filter(task => 
            task.Category === categoryId && task.completed === false 
        );
        
        currentTaskIndex = 0;
        displayCurrentTask(projectId, categoryId);

    } catch (error) {
        document.getElementById('question-text').textContent = `حدث خطأ أثناء تحميل بيانات المهام. يرجى التحقق من مسار ملف JSON: ${error.message}`;
    }
}

document.addEventListener('DOMContentLoaded', loadTaskData);

document.getElementById('submit-btn').addEventListener('click', submitTask);
document.getElementById('skip-btn').addEventListener('click', skipTask);

document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('submit-btn').click();
    } else if (e.key === 's' || e.key === 'S') {
        e.preventDefault();
        document.getElementById('skip-btn').click();
    }
});
