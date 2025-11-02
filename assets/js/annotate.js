
/* annotate.js - renders MCQ / OPEN_ENDED using DATA_SOURCES */
(function(){
  const $ = (sel) => document.querySelector(sel);
  const byId = (id) => document.getElementById(id);
  const u = new URL(location.href);
  const projectId = (u.searchParams.get('id') || 'SC_GENERAL').toUpperCase();
  const type      = (u.searchParams.get('type') || 'MCQ').toUpperCase();
  const cat       = u.searchParams.get('cat') || '';
  const source    = u.searchParams.get('source') || '';
  const url       = u.searchParams.get('url') || '';
  const limit     = parseInt(u.searchParams.get('limit') || '10', 10);

  const state = { items: [], idx: 0, answers: {} };

  function setMeta() {
    const counter = byId('counter');
    const meta    = byId('meta');
    const prog    = byId('progress');
    if (counter) counter.textContent = `${state.idx+1} / ${state.items.length}`;
    if (prog) { prog.max = Math.max(1, state.items.length); prog.value = state.idx+1; }
    if (meta) meta.textContent = `الفئة: ${cat || 'الكل'} — النوع: ${type}`;
  }

  function render() {
    const qArea = byId('question-container');
    if (!qArea) return;
    qArea.innerHTML = '';

    const item = state.items[state.idx];
    if (!item) { qArea.innerHTML = '<p class="muted">لا توجد أسئلة متاحة.</p>'; return; }

    const title = document.createElement('h2');
    title.textContent = item.question || '—';
    qArea.appendChild(title);

    if (type === 'OPEN_ENDED') {
      const ta = document.createElement('textarea');
      ta.id = 'answer';
      ta.style.width = '100%';
      ta.rows = 6;
      ta.placeholder = 'اكتب إجابتك هنا...';
      ta.value = state.answers[state.idx] || '';
      ta.addEventListener('input', (e)=> state.answers[state.idx] = e.target.value);
      qArea.appendChild(ta);
    } else {
      // MCQ (and default)
      const ul = document.createElement('div');
      (item.options || []).forEach((opt, i) => {
        const id = `opt_${i}`;
        const wrap = document.createElement('label');
        wrap.style.display = 'block';
        wrap.style.margin = '8px 0';
        wrap.innerHTML = `
          <input type="radio" name="mcq" value="${i}" id="${id}"> ${opt}
        `;
        ul.appendChild(wrap);
      });
      qArea.appendChild(ul);
      // restore selection
      const saved = state.answers[state.idx];
      if (saved !== undefined) {
        const radio = qArea.querySelector(`input[name="mcq"][value="${saved}"]`);
        if (radio) radio.checked = true;
      }
      qArea.addEventListener('change', (e)=>{
        if (e.target && e.target.name === 'mcq') {
          state.answers[state.idx] = parseInt(e.target.value, 10);
        }
      });
    }
    setMeta();
    byId('btn-prev')?.toggleAttribute('disabled', state.idx === 0);
    byId('btn-next')?.toggleAttribute('disabled', state.idx >= state.items.length - 1);
    byId('btn-finish')?.style.setProperty('display', state.idx >= state.items.length - 1 ? 'inline-block' : 'none');
  }

  function next(){ if (state.idx < state.items.length-1) { state.idx++; render(); } }
  function prev(){ if (state.idx > 0) { state.idx--; render(); } }
  function finish(){
    alert('تم إنهاء الجلسة. سيتم حفظ إجاباتك مؤقتًا في هذه الصفحة فقط.');
  }

  async function init(){
    const all = await window.DATA_SOURCES.loadQuestions({ projectId, source, url });
    let items = window.DATA_SOURCES.filterByCategory(all, cat);
    if (type === 'MCQ') items = window.DATA_SOURCES.onlyMCQ(items);
    else if (type === 'TRUE_FALSE') items = window.DATA_SOURCES.onlyTrueFalse(items);
    const seed = `${projectId}-${cat}-${type}`;
    state.items = window.DATA_SOURCES.takeNRandom(items, isFinite(limit)? limit: 10, seed);
    state.idx = 0;

    byId('btn-prev')?.addEventListener('click', prev);
    byId('btn-next')?.addEventListener('click', next);
    byId('btn-finish')?.addEventListener('click', finish);
    render();
  }

  document.addEventListener('DOMContentLoaded', init);
})();
