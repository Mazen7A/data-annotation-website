
function normalizeRow(row) {
    const type = String(row.type || row.Type || '').trim().toUpperCase();
    const q = row.question || row.Question || row.q || '';
    const opts = row.options || row.Options || row.choices || row.Choices || '';
    const options = Array.isArray(opts)
        ? opts
        : String(opts).split(',').map(s => s.trim()).filter(Boolean);

    let ans = row.answer ?? row.Answer ?? row.correct ?? row.Correct ?? '';
    if (typeof ans === 'string' && /^\d+$/.test(ans)) ans = +ans;
    if (typeof ans === 'string' && ['TRUE', 'FALSE'].includes(ans.toUpperCase()))
        ans = ans.toLowerCase() === 'true';

    return { type, question: q, options, answer: ans };
}

// ===== GitHub RAW JSON =====
async function fetchFromGitHubRaw(rawUrl) {
    const res = await fetch(rawUrl, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`GitHub HTTP ${res.status}`);
    const data = await res.json();

    if (Array.isArray(data)) return data.map(normalizeRow);
    if (Array.isArray(data.items)) return data.items.map(normalizeRow);
    if (Array.isArray(data.questions)) return data.questions.map(normalizeRow);

    let list = [];
    if (Array.isArray(data.sections)) {
        data.sections.forEach(s => (s.items || []).forEach(i => list.push(i)));
    }
    return list.map(normalizeRow);
}


async function fetchFromGoogleSheetCsv(sheetCsvUrl) {
    const res = await fetch(sheetCsvUrl, { cache: 'no-cache' });
    if (!res.ok) throw new Error(`Sheet HTTP ${res.status}`);
    const text = await res.text();

    const rows = text.split(/\r?\n/).filter(Boolean).map(r => {
        return r.split(',').map(x => x.replace(/^"|"$/g, '').trim());
    });

    const headers = rows.shift().map(h => h.trim());
    const objs = rows.map(r => {
        const o = {};
        headers.forEach((h, i) => (o[h] = (r[i] || '').trim()));
        return o;
    });
    return objs.map(normalizeRow);
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

window.DATA_SOURCES = { fetchFromGitHubRaw, fetchFromGoogleSheetCsv, takeTenRandom };
