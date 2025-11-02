const LOCAL_FILES = {
  SC_GENERAL: '../assets/data/GENERAL-questions.json',
  SC_NORTH:   '../assets/data/NORTH-questions.json',
  SC_SOUTH:   '../assets/data/SOUTH-questions.json',
  SC_EAST:    '../assets/data/EAST-questions.json'
};

function normalizeRow(row = {}) {
  const type = String(row.type || row.Type || '').trim().toUpperCase();
  const category = row.category || row.Category || row.cat || '';
  const q = row.question || row.Question || row.q || row.text || '';

  let options = [];
  if (Array.isArray(row.options || row.Options || row.choices || row.Choices)) {
    options = (row.options || row.Options || row.choices || row.Choices).filter(Boolean);
  }
  if (!options.length) {
    const opts = row.options || row.Options || row.choices || row.Choices || '';
    if (opts && typeof opts === 'string') {
      options = String(opts).split(',').map(s => s.trim()).filter(Boolean);
    }
  }
  if (!options.length) {
    const pickKey = (names) => {
      const keys = Object.keys(row);
      for (const n of names) {
        const k = keys.find(k => k.toLowerCase() === n.toLowerCase());
        if (k && row[k]) return row[k];
      }
      return '';
    };
    const maybe = [
      pickKey(['OptionA','optiona','ChoiceA','choicea','Choice1','choice1']),
      pickKey(['OptionB','optionb','ChoiceB','choiceb','Choice2','choice2']),
      pickKey(['OptionC','optionc','ChoiceC','choicec','Choice3','choice3']),
      pickKey(['OptionD','optiond','ChoiceD','choiced','Choice4','choice4'])
    ].filter(Boolean);
    if (maybe.length) options = maybe;
  }

  let ans = row.answer ?? row.Answer ?? row.correct ?? row.Correct ?? '';
  if (typeof ans === 'string') {
    const trimmed = ans.trim();
    if (/^\d+$/.test(trimmed)) ans = +trimmed;
    else if (['TRUE','FALSE'].includes(trimmed.toUpperCase())) {
      ans = trimmed.toLowerCase() === 'true';
    } else {
      const up = trimmed.toUpperCase();
      const abcd = { A:0, B:1, C:2, D:3 };
      if (abcd[up] !== undefined) ans = abcd[up];
    }
  }

  return { type, category, question: q, options, answer: ans };
}

async function fetchFromGitHubRaw(rawUrl) {
  const res = await fetch(rawUrl, { cache: 'no-cache' });
  if (!res.ok) throw new Error(`GitHub HTTP ${res.status}`);
  const data = await res.json();

  if (Array.isArray(data))            return data.map(normalizeRow);
  if (Array.isArray(data.items))      return data.items.map(normalizeRow);
  if (Array.isArray(data.questions))  return data.questions.map(normalizeRow);

  let list = [];
  if (Array.isArray(data.sections)) {
    data.sections.forEach(s => (s.items || []).forEach(i => list.push(i)));
  }
  return list.map(normalizeRow);
}

function csvToObjects(csvText) {
  const rows = csvText
    .split(/\r?\n/)
    .filter(line => line.trim() !== '')
    .map(line => line.split(',').map(x => x.replace(/^"|"$/g, '').trim()));

  const headers = (rows.shift() || []).map(h => h.trim());
  return rows.map(r => {
    const o = {};
    headers.forEach((h, i) => (o[h] = (r[i] || '').trim()));
    return o;
  });
}

async function fetchFromGoogleSheetCsv(sheetCsvUrl) {
  const res = await fetch(sheetCsvUrl, { cache: 'no-cache' });
  if (!res.ok) throw new Error(`Sheet HTTP ${res.status}`);
  const text = await res.text();
  return csvToObjects(text).map(normalizeRow);
}

async function fetchFromLocal(projectId) {
  const path = LOCAL_FILES[projectId] || LOCAL_FILES.SC_GENERAL;
  const res = await fetch(path, { cache: 'no-cache' });
  if (!res.ok) throw new Error(`Local HTTP ${res.status}`);
  const data = await res.json();
  const arr  = Array.isArray(data) ? data : (Array.isArray(data.items) ? data.items : []);
  return arr.map(normalizeRow);
}

async function loadQuestions({ projectId, source, url }) {
  if (source === 'sheet'  && url) return await fetchFromGoogleSheetCsv(url);
  if (source === 'github' && url) return await fetchFromGitHubRaw(url);
  return await fetchFromLocal(projectId);
}

function filterByCategory(items, cat) {
  if (!cat) return items;
  const key = String(cat).toLowerCase();
  return items.filter(x => String(x.category || '').toLowerCase() === key);
}

function onlyMCQ(items) {
  return items.filter(x => (x.type || '').includes('MCQ') || (x.options || []).filter(Boolean).length >= 2);
}

function onlyTrueFalse(items) {
  return items.filter(x => (x.type || '').includes('TRUE') || typeof x.answer === 'boolean');
}

function takeTenRandom(items, seedKey = '') {
  const rand = (s) => {
    let x = 0;
    for (let i = 0; i < s.length; i++) x = (x * 31 + s.charCodeAt(i)) >>> 0;
    return () => ((x = (1103515245 * x + 12345) >>> 0) / 2 ** 32);
  };
  const r = rand(seedKey || String(Date.now()));
  const clone = [...items];
  clone.sort(() => r() - 0.5);
  return clone.slice(0, Math.min(10, clone.length));
}

window.DATA_SOURCES = {
  loadQuestions,
  fetchFromGoogleSheetCsv,
  fetchFromGitHubRaw,
  fetchFromLocal,
  normalizeRow,
  filterByCategory,
  onlyMCQ,
  onlyTrueFalse,
  takeTenRandom
};
