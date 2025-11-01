(() => {
  const $ = (sel, root = document) => root.querySelector(sel);
  const params        = new URLSearchParams(location.search);
  const projectFullId = params.get('id') || 'SC_GENERAL';
  const projectId     = projectFullId.replace(/^SC_/, '');
  const role          = params.get('role') || 'user';
  const qType         = (params.get('type') || '').toUpperCase();
  const category      = params.get('cat') || '';
  const username      = JSON.parse(localStorage.getItem('currentUser') || '{}').username || 'guest';

  const source   = (params.get('source') || '').toLowerCase();
  const remoteUrl= params.get('url');
  const limit    = +(params.get('limit') || 10);

  const DATA_FILES = {
    GENERAL: '../assets/data/GENERAL-questions.json',
    NORTH:   '../assets/data/NORTH-questions.json',
    EAST:    '../assets/data/EAST-questions.json',
    SOUTH:   '../assets/data/SOUTH-questions.json'
  };

  const LS_KEY = (extra='') => `answers:${projectId}:${username}:${qType}${extra ? ':'+extra : ''}`;

  const elTitle     = $('#page-title');
  const elMeta      = $('#meta');
  const elProgress  = $('#progress');
  const elCounter   = $('#counter');
  const elContainer = $('#question-container');
  const btnPrev     = $('#btn-prev');
  const btnNext     = $('#btn-next');
  const btnFinish   = $('#btn-finish');

  let questions = [];
  let idx = +params.get('idx') || 0;

  function formatTypeTitle(t) {
    switch (t) {
      case 'MCQ': return 'أسئلة اختيار من متعدد';
      case 'TRUE_FALSE': return 'أسئلة صح / خطأ';
      case 'LIST': return 'أسئلة القائمة';
      case 'OPEN_ENDED': return 'أسئلة مفتوحة';
      default: return 'جلسة التوسيم';
    }
  }

  function getAllAnswers() {
    try { return JSON.parse(localStorage.getItem(LS_KEY(category)) || '{}'); }
    catch { return {}; }
  }
  function setAnswer(i, value) {
    const all = getAllAnswers();
    all[i] = value;
    localStorage.setItem(LS_KEY(category), JSON.stringify(all));
  }
  function getAnswer(i) { return getAllAnswers()[i]; }

  async function loadQuestions() {
    let all = [];

    if (remoteUrl && (source === 'github' || source === 'sheet')) {
      if (!window.DATA_SOURCES) throw new Error('dataSources.js غير مُحمّل');
      if (source === 'github') all = await window.DATA_SOURCES.fetchFromGitHubRaw(remoteUrl);
      if (source === 'sheet')  all = await window.DATA_SOURCES.fetchFromGoogleSheetCsv(remoteUrl);
    } else {
      const url = DATA_FILES[projectId] || DATA_FILES.GENERAL;
      const res = await fetch(url);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();
      const sections = Array.isArray(data.sections) ? data.sections : [];
      sections.forEach(sec => {
        if (!category || sec.key === category) (sec.items || []).forEach(it => all.push({ section: sec, ...it }));
      });
    }

    all = all.filter(q => (String(q.type || '').toUpperCase() === qType));

    const seed = `${projectId}|${qType}|${username}|${category || 'ALL'}`;
    const take10 = (window.DATA_SOURCES?.takeTenRandom || ((x) => x.slice(0, 10)))(all, seed).slice(0, limit);

    questions = take10.map(q => ({
      type: String(q.type || '').toUpperCase(),
      question: q.question || '',
      options: q.options || [],
      answer: q.answer
    }));

    elTitle.textContent = formatTypeTitle(qType);
    elMeta.textContent  = `عدد الأسئلة المعروضة: ${questions.length}`;
    updateProgress();
    render();
  }

  function render() {
    elContainer.innerHTML = '';
    if (!questions.length) {
      elContainer.innerHTML = `<div class="card"><p>لا توجد أسئلة لهذا النوع.</p></div>`;
      btnPrev.disabled = true;
      btnNext.disabled = true;
      btnFinish.style.display = 'inline-flex';
      return;
    }

    if (idx < 0) idx = 0;
    if (idx > questions.length - 1) idx = questions.length - 1;

    const q = questions[idx];
    const saved = getAnswer(idx);

    elCounter.textContent = `السؤال ${idx + 1} من ${questions.length}`;
    elProgress.max = 100;
    elProgress.value = Math.round(((idx + 1) / questions.length) * 100);

    if (qType === 'MCQ') {
      const opts = (q.options || []).map((opt, i) => `
        <label class="choice">
          <input type="radio" name="ans" value="${i}" ${+saved === i ? 'checked' : ''}>
          <span>${opt}</span>
        </label>`).join('');
      elContainer.innerHTML = `
        <div class="card">
          <h3>${q.question || '—'}</h3>
          <div class="choices">${opts}</div>
        </div>`;
    }

    if (qType === 'TRUE_FALSE') {
      elContainer.innerHTML = `
        <div class="card">
          <h3>${q.question || '—'}</h3>
          <div class="choices">
            <label class="choice"><input type="radio" name="ans" value="true"  ${saved === 'true'  ? 'checked' : ''}> صح</label>
            <label class="choice"><input type="radio" name="ans" value="false" ${saved === 'false' ? 'checked' : ''}> خطأ</label>
          </div>
        </div>`;
    }

    if (qType === 'LIST') {
      const opts = (q.options || []).map(opt => `<option value="${opt}">${opt}</option>`).join('');
      elContainer.innerHTML = `
        <div class="card">
          <h3>${q.question || '—'}</h3>
          <select id="list-select" multiple size="${Math.min(6, (q.options || []).length || 4)}">${opts}</select>
        </div>`;
      if (Array.isArray(saved)) {
        const sel = $('#list-select');
        [...sel.options].forEach(o => { if (saved.includes(o.value)) o.selected = true; });
      }
    }

    if (qType === 'OPEN_ENDED') {
      elContainer.innerHTML = `
        <div class="card">
          <h3>${q.question || '—'}</h3>
          <textarea id="open-answer" rows="6" placeholder="أدخل إجابتك هنا..."></textarea>
        </div>`;
      if (saved) $('#open-answer').value = saved;
    }

    btnPrev.disabled = idx === 0;
    btnNext.disabled = idx === questions.length - 1;
    btnFinish.style.display = idx === questions.length - 1 ? 'inline-flex' : 'none';
  }

  function persistCurrent() {
    if (!questions.length) return;
    if (qType === 'MCQ') {
      const v = $('input[name="ans"]:checked')?.value;
      if (v !== undefined) setAnswer(idx, +v);
    }
    if (qType === 'TRUE_FALSE') {
      const v = $('input[name="ans"]:checked')?.value;
      if (v) setAnswer(idx, v);
    }
    if (qType === 'LIST') {
      const sel = $('#list-select');
      if (sel) {
        const values = [...sel.selectedOptions].map(o => o.value);
        setAnswer(idx, values);
      }
    }
    if (qType === 'OPEN_ENDED') {
      const txt = $('#open-answer')?.value?.trim();
      if (txt !== undefined) setAnswer(idx, txt);
    }
  }

  function updateProgress() {
    if (!questions.length) {
      elCounter.textContent = 'لا توجد أسئلة';
      elProgress.value = 0;
      return;
    }
    elCounter.textContent = `السؤال ${idx + 1} من ${questions.length}`;
    elProgress.value      = Math.round(((idx + 1) / questions.length) * 100);
  }

  btnPrev?.addEventListener('click', () => { persistCurrent(); idx--; render(); });
  btnNext?.addEventListener('click', () => { persistCurrent(); idx++; render(); });

  btnFinish?.addEventListener('click', () => {
    persistCurrent();
    const ans = getAllAnswers();
    const answered = Object.values(ans).filter(v =>
      (Array.isArray(v) && v.length) || (typeof v === 'string' && v.trim()) || (v === 0 || !!v)
    ).length;

    alert(`تم حفظ الجلسة ✅\nأجبت على ${answered} من ${questions.length} سؤال.\nيمكنك الخروج بأمان.`);
    location.href = `../home/project-details.html?id=${projectFullId}&role=${role}`;
  });

  loadQuestions().catch(e => {
    elContainer.innerHTML = `<div class="card error">تعذر تحميل الأسئلة: ${e.message}</div>`;
    console.error(e);
  });
})();
