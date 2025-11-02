(function(){
  function getParam(name) {
    try {
      const u = new URL(window.location.href);
      return u.searchParams.get(name) || "";
    } catch(e){ return ""; }
  }
  function uidFromQuestion(q) {
    const base = (q.id || q.qid || q.question || "").slice(0,120);
    return btoa(unescape(encodeURIComponent(base))).slice(0,24);
  }
  function readCurrentUser(){
    try{
      const u = JSON.parse(localStorage.getItem('currentUser') || '{}');
      return u.username || u.email || 'guest';
    }catch{ return 'guest'; }
  }
  function readStore(username){
    const key = `thq_results_v1:${username}`;
    try{ return {key, data: JSON.parse(localStorage.getItem(key) || '{"sessions":[]}')}; }
    catch{ return {key, data:{sessions:[]}}; }
  }
  function writeStore(key, data){
    localStorage.setItem(key, JSON.stringify(data));
  }

  const projectId = (getParam('id') || 'SC_GENERAL').toUpperCase();
  const type      = (getParam('type') || 'MCQ').toUpperCase();
  const category  = getParam('cat') || '';
  const limit     = parseInt(getParam('limit') || '0', 10) || 0;
  const source    = getParam('source') || 'local';

  const metaEl   = document.getElementById('meta');
  const titleEl  = document.getElementById('q-title');
  const bodyEl   = document.getElementById('q-body');
  const formEl   = document.getElementById('mcqForm') || document.getElementById('oeForm');
  const btnPrev  = document.getElementById('btnPrev') || document.querySelector('[data-nav="prev"]');
  const btnNext  = document.getElementById('btnNext') || document.querySelector('[data-nav="next"]');
  const btnDone  = document.getElementById('btnDone') || document.querySelector('[data-nav="finish"]');
  const progress = document.getElementById('progress');

  async function loadQuestions(){
    if (!window.DataSources || !window.DataSources.fetchAny) {
      throw new Error('dataSources.js (fetchAny) غير محمّل');
    }
    const {items} = await window.DataSources.fetchAny({ projectId, source });
    let arr = items.filter(x => (x.Category || x.category) === category);

    function isMCQ(x){ return (String(x.type||x.Type||'').toUpperCase().includes('MCQ')) || (Array.isArray(x.options) && x.options.filter(Boolean).length >= 2); }
    function isTF(x){ const t = String(x.type||x.Type||'').toUpperCase(); return t.includes('TRUE') || (typeof x.answer === 'boolean'); }
    function isOE(x){ const t = String(x.type||x.Type||'').toUpperCase(); return t.includes('OPEN') || (!isMCQ(x) && !isTF(x)); }
    arr = arr.filter(x => {
      switch(type){
        case 'MCQ': return isMCQ(x);
        case 'TRUE_FALSE': return isTF(x);
        case 'OPEN_ENDED': return isOE(x);
        case 'LIST': return String(x.type||'').toUpperCase().includes('LIST');
        default: return true;
      }
    });

    for (let i=arr.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [arr[i],arr[j]]=[arr[j],arr[i]]; }
    if (limit>0 && arr.length>limit) arr = arr.slice(0, limit);
    return arr;
  }

  let QUESTIONS = [];
  let state = {
    index: 0,
    answers: []
  };

  function updateMeta(){
    if (!metaEl) return;
    metaEl.textContent = `المشروع: ${projectId} | القسم: ${category} | النوع: ${type} | ${state.index+1} / ${QUESTIONS.length}`;
  }

  function renderCurrent(){
    const q = QUESTIONS[state.index];
    if (!q) return;

    titleEl.textContent = (q.question || q.text || '—');
    bodyEl.textContent  = q.description || '';
    formEl.innerHTML = '';

    if (type === 'OPEN_ENDED'){
      const ta = document.createElement('textarea');
      ta.id = 'oe-input';
      ta.rows = 5; ta.style.width='100%';
      const prev = state.answers.find(a => a.qid === uidFromQuestion(q));
      if (prev) ta.value = prev.text || '';
      formEl.appendChild(ta);
    } else if (Array.isArray(q.options)){
      q.options.filter(Boolean).forEach((opt, i)=>{
        const id = `opt_${i}`;
        const div = document.createElement('div');
        div.className = 'option';
        div.innerHTML = `
          <label for="${id}" style="display:flex;gap:8px;align-items:center;">
            <input type="radio" name="choice" id="${id}" value="${i}" />
            <span>${opt}</span>
          </label>`;
        formEl.appendChild(div);
      });
      const prev = state.answers.find(a => a.qid === uidFromQuestion(q));
      if (prev && typeof prev.picked === 'number'){
        const inp = formEl.querySelector(`input[value="${prev.picked}"]`);
        if (inp) inp.checked = true;
      }
    }

    if (progress){
      const pct = Math.round(((state.index+1) / Math.max(1, QUESTIONS.length)) * 100);
      progress.value = pct; progress.max = 100;
    }
    updateMeta();
  }

  function captureCurrent(){
    const q = QUESTIONS[state.index];
    if (!q) return;
    const qid = uidFromQuestion(q);
    let rec = state.answers.find(a => a.qid === qid);
    if (!rec){ rec = { qid, type, picked: null, text:'', correct: null, isGraded: false }; state.answers.push(rec); }

    if (type === 'OPEN_ENDED'){
      const ta = document.getElementById('oe-input');
      rec.text = (ta && ta.value) ? ta.value.trim() : '';
      rec.isGraded = false;
      rec.correct = null;
    } else {
      const sel = formEl.querySelector('input[name="choice"]:checked');
      if (sel){
        rec.picked = Number(sel.value);
        let correctIndex = null;
        if (typeof q.answer === 'number') correctIndex = q.answer;
        else if (typeof q.answer === 'string'){
          const idx = (q.options||[]).findIndex(o => String(o).trim() === String(q.answer).trim());
          correctIndex = idx >=0 ? idx : null;
        }
        if (correctIndex !== null){
          rec.correct = (rec.picked === correctIndex);
          rec.isGraded = true;
        } else {
          rec.correct = null;
          rec.isGraded = false;
        }
      }
    }
  }

  async function start(){
    QUESTIONS = await loadQuestions();
    state.index = 0;
    state.answers = [];
    renderCurrent();
  }

  if (btnPrev) btnPrev.addEventListener('click', () => {
    captureCurrent();
    if (state.index>0){ state.index--; renderCurrent(); }
  });
  if (btnNext) btnNext.addEventListener('click', () => {
    captureCurrent();
    if (state.index < QUESTIONS.length-1){ state.index++; renderCurrent(); }
  });
  if (btnDone) btnDone.addEventListener('click', () => {
    captureCurrent();
    const username = readCurrentUser();
    const {key, data} = readStore(username);

    const graded = state.answers.filter(a => a.isGraded);
    const correct = graded.filter(a => a.correct === true).length;

    const session = {
      ts: Date.now(),
      projectId, type, category, source,
      total: QUESTIONS.length,
      gradedTotal: graded.length,
      correct,
      answers: state.answers
    };

    data.sessions.push(session);
    writeStore(key, data);

    const u = new URL('../home/my-stats.html', location.href);
    u.searchParams.set('flash', 'saved');
    location.href = u.toString();
  });

  document.addEventListener('DOMContentLoaded', start);
})();
