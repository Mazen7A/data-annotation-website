
(function () {
  // ---------- Helpers ----------
  function getParam(name) {
    try { const u = new URL(window.location.href); return u.searchParams.get(name) || ""; }
    catch { return ""; }
  }
  function uidFromQuestion(q) {
    const base = (q.id || q.qid || q.question || q.text || "").slice(0, 120);
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
  const norm = s => String(s || "").trim().toLowerCase();

  // ---------- URL params ----------
  const projectId = (getParam("id") || "SC_GENERAL").toUpperCase();
  const type      = (getParam("type") || "MCQ").toUpperCase();
  const catParam  = (getParam("cat") || "").trim();
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

  // ---------- Normalization ----------
  function normalizeQuestion(raw) {
    const q = { ...raw };
    // names
    q.type      = q.type      || q.Type      || "";
    q.Category  = q.Category  || q.category  || q.cat || "";
    q.question  = q.question  || q.Question  || q.q   || q.text || "";
    // options
    if (!Array.isArray(q.options)) {
      let opts = q.options || q.Options || q.choices || q.Choices || "";
      if (typeof opts === "string") {
        q.options = String(opts).split(",").map(s => s.trim()).filter(Boolean);
      } else if (Array.isArray(opts)) {
        q.options = opts.filter(Boolean);
      } else if (q.OptionA || q.OptionB) {
        q.options = [q.OptionA, q.OptionB, q.OptionC, q.OptionD].filter(Boolean);
      } else {
        q.options = [];
      }
    }
    // answer: allow index or exact text
    if (q.answer === undefined && q.Answer !== undefined) q.answer = q.Answer;
    if (typeof q.answer === "string" && q.options.length) {
      const idx = q.options.findIndex(o => norm(o) === norm(q.answer));
      if (idx >= 0) q.answer = idx; // حوّل النص إلى فهرس
    }
    return q;
  }

  const aliases = {
    "food":                     "الطعام والمشروبات",
    "foods":                    "الطعام والمشروبات",
    "clothes":                  "الملابس والزي التقليدي",
    "clothing":                 "الملابس والزي التقليدي",
    "crafts":                   "الحرف والأعمال اليدوية",
    "crafts and work":          "الحرف والأعمال اليدوية",
    "celebration":              "الاحتفالات والمناسبات",
    "celebrations":             "الاحتفالات والمناسبات",
    "entertainment":            "الترفيه والفنون",
    "dating":                   "المناسبات الاجتماعية والتعارف",
    "social":                   "المناسبات الاجتماعية والتعارف"
  };

  // ---------- Loading ----------
  async function loadQuestions() {
    if (!window.DATA_SOURCES) {
      throw new Error("DATA_SOURCES غير محمّل (تأكد من تضمين dataSources.js قبل annotate.js)");
    }
    const DS = window.DATA_SOURCES;

    // 1) اجلب كل أسئلة المشروع
    let all = await DS.loadQuestions({ projectId, source, url: urlParam });
    all = all.map(normalizeQuestion);

    // تقرير سريع عن الفئات المتوفرة
    const byCat = {};
    all.forEach(x => { const c = x.Category || ""; byCat[c] = (byCat[c] || 0) + 1; });
    console.log("[annotate] categories count:", byCat);

    // 2) فلترة بالقسم مع فول-باك
    const requested = aliases[norm(catParam)] || catParam;
    let arr = all.filter(x => norm(x.Category) === norm(requested) || norm(x.Category) === norm(catParam));

    // لو فاضي، جرّب تطابق جزئي contains
    if (arr.length === 0 && requested) {
      arr = all.filter(x =>
        norm(x.Category).includes(norm(requested)) || norm(x.Category).includes(norm(catParam))
      );
    }
    // لو ما زال فاضي، استخدم كل الأسئلة مع تنبيه
    let catNote = "";
    if (arr.length === 0) {
      arr = all.slice();
      catNote = `⚠️ لم يتم العثور على قسم باسم "${catParam}" — تم عرض جميع الأقسام مؤقتًا.`;
    }

    // 3) فلترة حسب النوع
    const isMCQ = x => String(x.type || "").toUpperCase().includes("MCQ") || x.options.length >= 2;
    const isTF  = x => String(x.type || "").toUpperCase().includes("TRUE") || typeof x.answer === "boolean";
    const isOE  = x => {
      const t = String(x.type || "").toUpperCase();
      return t.includes("OPEN") || (!isMCQ(x) && !isTF(x));
    };

    if (type === "MCQ")        arr = arr.filter(isMCQ);
    else if (type === "TRUE_FALSE") arr = arr.filter(isTF);
    else if (type === "OPEN_ENDED") arr = arr.filter(isOE);
    else if (type === "LIST")  arr = arr.filter(q => String(q.type || "").toUpperCase().includes("LIST"));

    // 4) Shuffle + limit
    for (let i = arr.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    if (limit > 0 && arr.length > limit) arr = arr.slice(0, limit);

    console.log("[annotate] Loaded", { projectId, source, requested, catParam, type, count: arr.length });

    // عرض ملاحظة بالقمة لو فيه مشكلة قسم
    if (metaEl && catNote) {
      const span = document.createElement("span");
      span.style.color = "#b45309";
      span.style.marginInlineStart = "8px";
      span.textContent = catNote;
      metaEl.appendChild(span);
    }
    return arr;
  }

  // ---------- State ----------
  let QUESTIONS = [];
  let state = { index: 0, answers: [] };

  function updateMeta() {
    if (!metaEl) return;
    const total = QUESTIONS.length || 0;
    metaEl.textContent =
      `المشروع: ${projectId} | القسم: ${catParam || "الكل"} | النوع: ${type} | ${Math.min(state.index + 1, total)} / ${total}`;
  }

  function renderCurrent() {
    const q = QUESTIONS[state.index];
    if (!q) {
      if (titleEl) titleEl.textContent = "لا توجد أسئلة مطابقة …";
      if (formEl)  formEl.innerHTML = "";
      if (btnDone) btnDone.style.display = "none";
      updateMeta();
      return;
    }

    if (titleEl) titleEl.textContent = q.question || "—";
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
    } else {
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
        let correctIndex = null;
        if (typeof q.answer === "number") correctIndex = q.answer;
        else if (typeof q.answer === "string") {
          const idx = (q.options || []).findIndex(o => norm(o) === norm(q.answer));
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
    try {
      QUESTIONS = await loadQuestions();
      state.index = 0;
      state.answers = [];
      renderCurrent();
    } catch (e) {
      console.error(e);
      if (titleEl) titleEl.textContent = "حدث خطأ أثناء تحميل الأسئلة.";
      if (metaEl)  metaEl.textContent  = e.message || String(e);
    }
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
      projectId, type, category: catParam, source,
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
