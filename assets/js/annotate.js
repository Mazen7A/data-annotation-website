import { loadQuestions } from './dataSources.js';

const $ = (s, p = document) => p.querySelector(s);

function getParams() {
  const p = new URLSearchParams(location.search);
  return {
    projectId: (p.get('id') || 'SC_GENERAL').toUpperCase(),
    type:      (p.get('type') || 'MCQ').toUpperCase(),
    role:      (p.get('role') || 'user').toLowerCase(),
    cat:        p.get('cat') || p.get('category') || '',
    source:     p.get('source') || undefined,
    url:        p.get('url') || undefined,
    limit:    +(p.get('limit') || 10)
  };
}

function normalizeMCQ(rec) {
  const text = rec.Question || rec.question || rec.Q || rec.text || '';
  const category = rec.Category || rec.category || '';

  const keys = Object.keys(rec);
  const tryKey = (names) => names.find(n => keys.some(k => k.toLowerCase() === n.toLowerCase()));
  const pick = (name) => {
    const k = keys.find(k => k.toLowerCase() === name.toLowerCase());
    return k ? rec[k] : '';
  };

  const choices = [];
  ['optiona','optionb','optionc','optiond','choice1','choice2','choice3','choice4'].forEach(k=>{
    const val = pick(k);
    if (val) choices.push(val);
  });

  let correctIndex = -1;
  const ans = rec.Answer || rec.answer || rec.Correct || rec.correct || '';
  if (ans) {
    const norm = String(ans).trim().toUpperCase();
    const abcd = {A:0,B:1,C:2,D:3};
    if (Object.prototype.hasOwnProperty.call(abcd, norm)) correctIndex = abcd[norm];
    else if (/^[1-4]$/.test(norm)) correctIndex = +norm - 1;
    else {
      const i = choices.findIndex(c => String(c).trim() === String(ans).trim());
      if (i >= 0) correctIndex = i;
    }
  }

  return { text, category, choices, correctIndex };
}

function toFallbackMCQ(rec) {
  return {
    text: rec.Question || rec.question || rec.Q || rec.text || '—',
    category: rec.Category || rec.category || '',
    choices: ['إجابة ممتازة (100%)', 'إجابة جيدة (75%)', 'إجابة مقبولة (50%)', 'إجابة ضعيفة (25%)'],
    correctIndex: -1,
    fallback: true
  };
}

function filterByTypeAndCat(all, wantedType, cat) {
  const hasType = all.some(r => r.Type || r.type);
  let list = all;

  if (hasType) {
    list = all.filter(r => {
      const t = (r.Type || r.type || '').toString().toUpperCase();
      return wantedType === 'MCQ' ? t.includes('MCQ') || t.includes('CHOICE')
                                  : t.includes(wantedType) || t.includes('TRUE/FALSE');
    });
  }

  if (cat) {
    list = list.filter(r => (r.Category || r.category || '').toString().toLowerCase() === cat.toLowerCase());
  }
  return list;
}

async function runMCQ() {
  const { projectId, source, url, limit, cat } = getParams();

  const elTitle   = $('#page-title');
  const elProg    = $('#progress');
  const elCount   = $('#counter');
  const elMeta    = $('#meta');
  const elQArea   = $('#question-container');
  const btnPrev   = $('#btn-prev');
  const btnNext   = $('#btn-next');
  const btnFinish = $('#btn-finish');

  if (elTitle) elTitle.textContent = 'أسئلة اختيار من متعدد';

  const all = await loadQuestions({ projectId, source, url, limit: 9999 });

  const filtered = filterByTypeAndCat(all, 'MCQ', cat);

  let items = filtered.map(r => {
    const m = normalizeMCQ(r);
    if ((m.choices || []).filter(Boolean).length >= 2) return m;
    return toFallbackMCQ(r);
  });

  items = items.slice(0, limit);

  if (!items.length) {
    elQArea.innerHTML = `
      <div class="card" style="padding:16px">لا توجد أسئلة لهذا النوع.</div>
    `;
    if (elCount) elCount.textContent = 'عدد الأسئلة المعروضة: 0';
    return;
  }

  let idx = 0;
  const total = items.length;

  function render() {
    const it = items[idx];
    if (elMeta) elMeta.textContent = `الفئة: ${it.category || '—'} • السؤال ${idx+1} من ${total}`;
    if (elCount) elCount.textContent = `عدد الأسئلة المعروضة: ${total}`;
    if (elProg) { elProg.max = total; elProg.value = idx+1; }

    elQArea.innerHTML = `
      <div class="card" style="padding:16px">
        <div style="font-size:1.1rem;line-height:1.9">${it.text}</div>
        <div id="choices" style="margin-top:12px"></div>
        ${it.fallback ? `<div style="margin-top:8px;color:#6b7280;font-size:.9rem">* لا تتوفر خيارات أصلية في المصدر؛ تم عرض سُلّم تقييم بديل.</div>` : ''}
      </div>
    `;

    const holder = $('#choices', elQArea);
    it.choices.forEach((c, i) => {
      const btn = document.createElement('button');
      btn.className = 'btn';
      btn.style.cssText = 'display:block;width:100%;text-align:right;margin:6px 0;';
      btn.textContent = c;
      btn.onclick = () => {
        next();
      };
      holder.appendChild(btn);
    });

    btnPrev.style.visibility = (idx === 0) ? 'hidden' : 'visible';
    btnNext.style.display    = (idx === total-1) ? 'none'   : 'inline-block';
    btnFinish.style.display  = (idx === total-1) ? 'inline-block' : 'none';
  }

  function prev(){ if (idx>0) { idx--; render(); } }
  function next(){ if (idx<total-1) { idx++; render(); } }
  function finish(){ history.back(); }

  btnPrev.addEventListener('click', prev);
  btnNext.addEventListener('click', next);
  btnFinish.addEventListener('click', finish);

  render();
}

(async function main(){
  const { type } = getParams();
  if (type === 'MCQ') {
    await runMCQ();
  } else {
    const area = document.getElementById('question-container');
    if (area) area.innerHTML = `<div class="card" style="padding:16px">نوع الصفحة لا يطابق النوع المطلوب: ${type}</div>`;
  }
})();
