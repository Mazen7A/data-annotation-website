/* annotate.js — عرض الأسئلة + حفظ النتائج في localStorage
 * مفتاح التخزين: thq_results_v1:<username>
 * البنية:
 * {
 *   sessions: [{
 *     ts, projectId, type, category, source, total, gradedTotal, correct,
 *     answers: [{ qid, type, picked, text, correct, isGraded }]
 *   }]
 * }
*/

(function () {
  // ---------- Helpers ----------
  function getParam(name) {
    try { const u = new URL(window.location.href); return u.searchParams.get(name) || ""; }
    catch { return ""; }
  }
  function uidFromQuestion(q) {
    const base = (q.id || q.qid || q.question || "").slice(0, 120);
    return btoa(unescape(encodeURIComponent(base))).slice(0, 24);
  }
  function readCurrentUser() {
    try {
      const u = JSON.parse(localStorage.getItem("currentUser") || "{}");
      return u.username || u.email || "guest";
    } catch { return "guest"; }
  }
  function readStore(username) {
    const key = `thq_results_v1:${username}`;
    try { return { key, data: JSON.parse(localStorage.getItem(key) || '{"sessions":[]}') }; }
    catch { return { key, data: { sessions: [] } }; }
  }
  function writeStore(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  }

  // ---------- URL params ----------
  const projectId = (getParam("id") || "SC_GENERAL").toUpperCase();
  const type      = (getParam("type") || "MCQ").toUpperCase();
  const category  = getParam("cat") || "";
  const limit     = parseInt(getParam("limit") || "0", 10) || 0;
  const urlParam  = getParam("url") || "";
  const source    = getParam("source") || (urlParam ? "sheet" : "local");

  // ---------- Elements ----------
  const metaEl   = document.getElementById("meta");
  const titleEl  = document.getElementById("q-title");
  const bodyEl   = document.getElementById("q-body");
  const formEl   = document.getElementById("mcqForm") || document.getElementById("oeForm");
  const btnPrev  = document.getElementById("btnPrev")  || document.querySelector('[data-nav="prev"]');
  const btnNext  = document.getElementById("btnNext")  || document.querySelector('[data-nav="next"]');
  const btnDone  = document.getElementById("btnDone")  || document.querySelector('[data-nav="finish"]');
  const progress = document.getElementById("progress");

  // ---------- تحميل الأسئلة من DATA_SOURCES ----------
  async function loadQuestions() {
    if (!window.DATA_SOURCES) {
      throw new Error("DATA_SOURCES غير محمّل (تأكد من تضمين dataSources.js قبل annotate.js)");
    }
    const DS = window.DATA_SOURCES;

    // 1) حمّل أسئلة المشروع كاملة من المصدر المحدد
    const all = await DS.loadQuestions({ projectId, source, url: urlParam });

    // 2) تطبيع/مواءمة اسم القسم (يدعم عربي/إنجليزي)
    const catParam = (category || "").trim();
    const norm = s => String(s || "").trim().toLowerCase();
    const aliases = {
      "food":                     "الطعام والمشروبات",
      "clothes":                  "الملابس والزي التقليدي",
      "crafts and work":          "الحرف والأعمال اليدوية",
      "celebration":              "الاحتفالات والمناسبات",
      "entertainment":            "الترفيه والفنون",
      "dating":                   "المناسبات الاجتماعية والتعارف"
    };
    const catWanted = aliases[norm(catParam)] || catParam;

    let arr = all.filter(x => {
      const c = (x.Category || x.category || "").trim();
      return norm(c) === norm(catWanted) || norm(c) === norm(catParam);
    });

    // 3) فلترة حسب النوع
    function isMCQ(x) {
      return String(x.type || x.Type || "").toUpperCase().includes("MCQ")
          || ((x.options || []).filter(Boolean).length >= 2);
    }
    function isTF(x) {
      const t = String(x.type || x.Type || "").toUpperCase();
      return t.includes("TRUE") || typeof x.answer === "boolean";
    }
    function isOE(x) {
      const t = String(x.type || x.Type || "").toUpperCase();
      return t.includes("OPEN") || (!isMCQ(x) && !isTF(x));
    }

    switch (type) {
      case "MCQ":        arr = arr.filter(isMCQ); break;
      case "TRUE_FALSE": arr = arr.filter(isTF);  break;
      case "OPEN_ENDED": arr = arr.filter(isOE);  break;
      case "LIST":       arr = arr.filter(q => String(q.type || "").toUpperCase().includes("LIST")); break;
    }

    // 4) Shuffle + limit
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    if (limit > 0 && arr.length > limit) arr = arr.slice(0, limit);

    console.log("Loaded", { projectId, source, catParam, catWanted, type, count: arr.length });
    return arr;
  }

  // ---------- State ----------
  let QUESTIONS = [];
  let state = {
    index: 0,
    answers: [] // {qid, type, picked, text, correct, isGraded}
  };

  function updateMeta() {
    if (!metaEl) return;
    metaEl.textContent =
      `المشروع: ${projectId} | القسم: ${category} | النوع: ${type} | ${state.index + 1} / ${QUESTIONS.length}`;
  }

  function renderCurrent() {
    const q = QUESTIONS[state.index];
    if (!q) return;

    if (titleEl) titleEl.textContent = q.question || q.text || "—";
    if (bodyEl)  bodyEl.textContent  = q.description || "";

    if (formEl) formEl.innerHTML = "";

    if (type === "OPEN_ENDED") {
      const ta = document.createElement("textarea");
      ta.id = "oe-input";
      ta.rows = 5;
      ta.style.width = "100%";
      const prev = state.answers.find(a => a.qid === uidFromQuestion(q));
      if (prev) ta.value = prev.text || "";
      formEl && formEl.appendChild(ta);
    } else if (Array.isArray(q.options)) {
      q.options.filter(Boolean).forEach((opt, i) => {
        const id = `opt_${i}`;
        const div = document.createElement("div");
        div.className = "option";
        div.innerHTML = `
          <label for="${id}" style="display:flex;gap:8px;align-items:center;">
            <input type="radio" name="choice" id="${id}" value="${i}" />
            <span>${opt}</span>
          </label>`;
        formEl && formEl.appendChild(div);
      });
      const prev = state.answers.find(a => a.qid === uidFromQuestion(q));
      if (prev && typeof prev.picked === "number" && formEl) {
        const inp = formEl.querySelector(`input[value="${prev.picked}"]`);
        if (inp) inp.checked = true;
      }
    }

    if (progress) {
      const pct = Math.round(((state.index + 1) / Math.max(1, QUESTIONS.length)) * 100);
      progress.value = pct; progress.max = 100;
    }
    if (btnDone) btnDone.style.display = (state.index === QUESTIONS.length - 1 ? "" : "none");

    updateMeta();
  }

  function captureCurrent() {
    const q = QUESTIONS[state.index];
    if (!q) return;
    const qid = uidFromQuestion(q);
    let rec = state.answers.find(a => a.qid === qid);
    if (!rec) { rec = { qid, type, picked: null, text: "", correct: null, isGraded: false }; state.answers.push(rec); }

    if (type === "OPEN_ENDED") {
      const ta = document.getElementById("oe-input");
      rec.text = (ta && ta.value) ? ta.value.trim() : "";
      rec.isGraded = false;
      rec.correct = null;
    } else {
      const sel = formEl && formEl.querySelector('input[name="choice"]:checked');
      if (sel) {
        rec.picked = Number(sel.value);

        // مفتاح الإجابة (إن وُجد)
        let correctIndex = null;
        if (typeof q.answer === "number") correctIndex = q.answer;
        else if (typeof q.answer === "string") {
          const idx = (q.options || []).findIndex(o => String(o).trim() === String(q.answer).trim());
          correctIndex = idx >= 0 ? idx : null;
        }
        if (correctIndex !== null) {
          rec.correct = (rec.picked === correctIndex);
          rec.isGraded = true;
        } else {
          rec.correct = null;
          rec.isGraded = false;
        }
      }
    }
  }

  async function start() {
    QUESTIONS = await loadQuestions();
    state.index = 0;
    state.answers = [];
    renderCurrent();
  }

  // ---------- Navigation ----------
  if (btnPrev) btnPrev.addEventListener("click", () => {
    captureCurrent();
    if (state.index > 0) { state.index--; renderCurrent(); }
  });
  if (btnNext) btnNext.addEventListener("click", () => {
    captureCurrent();
    if (state.index < QUESTIONS.length - 1) { state.index++; renderCurrent(); }
  });
  if (btnDone) btnDone.addEventListener("click", () => {
    captureCurrent();

    const username = readCurrentUser();
    const { key, data } = readStore(username);

    const graded  = state.answers.filter(a => a.isGraded);
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

    const u = new URL("../home/my-stats.html", location.href);
    u.searchParams.set("flash", "saved");
    location.href = u.toString();
  });

  document.addEventListener("DOMContentLoaded", start);
})();
