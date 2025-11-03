(function () {
  // ---------- Helpers ----------
  const norm = s => String(s||"").trim().toLowerCase();
  function getParam(name){try{const u=new URL(location.href);return u.searchParams.get(name)||"";}catch{return"";}}
  function uidFromQuestion(q){const b=(q.id||q.qid||q.question||q.text||"").slice(0,120);return btoa(unescape(encodeURIComponent(b))).slice(0,24);}
  function currentUserName(){ try{const u=JSON.parse(localStorage.getItem("currentUser")||"{}"); return u.username||u.email||"guest";}catch{return"guest";} }
  function readStore(key){ try{return JSON.parse(localStorage.getItem(key)||'{"sessions":[]}');}catch{return{sessions:[]}} }
  function writeStore(key,data){ localStorage.setItem(key, JSON.stringify(data)); }

  // ---------- URL ----------
  const projectId=(getParam("id")||"SC_GENERAL").toUpperCase();
  const type=(getParam("type")||"MCQ").toUpperCase();
  const catParam=(getParam("cat")||"").trim();
  const limit=parseInt(getParam("limit")||"0",10)||0;
  const urlParam=getParam("url")||"";
  const source=getParam("source")||(urlParam?"sheet":"local");

  // ---------- DOM ----------
  const metaEl=document.getElementById("meta");
  const titleEl=document.getElementById("q-title");
  const bodyEl=document.getElementById("q-body");
  const formEl=document.getElementById("mcqForm")||document.getElementById("oeForm");
  const btnPrev=document.getElementById("btnPrev");
  const btnNext=document.getElementById("btnNext");
  const btnDone=document.getElementById("btnDone");
  const progress=document.getElementById("progress");
  const fbBox=document.getElementById("per-question-feedback");
  const fbEmoji=document.getElementById("q-emoji");
  const fbC=document.getElementById("q-correct-count");
  const fbW=document.getElementById("q-wrong-count");

  // ---------- Question normalization ----------
  function normalizeQuestion(raw){
    const q={...raw};
    q.type=q.type||q.Type||"";
    q.Category=q.Category||q.category||q.cat||"";
    q.question=q.question||q.Question||q.q||q.text||"";
    if(!Array.isArray(q.options)){
      let opts=q.options||q.Options||q.choices||q.Choices||"";
      if(typeof opts==="string"){ q.options=String(opts).split(",").map(s=>s.trim()).filter(Boolean); }
      else if(Array.isArray(opts)){ q.options=opts.filter(Boolean); }
      else if(q.OptionA||q.OptionB){ q.options=[q.OptionA,q.OptionB,q.OptionC,q.OptionD].filter(Boolean); }
      else { q.options=[]; }
    }
    if(q.answer===undefined && q.Answer!==undefined) q.answer=q.Answer;
    if(typeof q.answer==="string" && q.options.length){
      const idx=q.options.findIndex(o=>norm(o)===norm(q.answer));
      if(idx>=0) q.answer=idx;
    }
    return q;
  }

  // ---------- Load ----------
  async function loadQuestions(){
    if(!window.DATA_SOURCES) throw new Error("dataSources.js غير محمّل");
    const DS=window.DATA_SOURCES;
    let all=(await DS.loadQuestions({projectId,source,url:urlParam})).map(normalizeQuestion);

    let arr=catParam? all.filter(x=>norm(x.Category)===norm(catParam) || norm(x.Category).includes(norm(catParam))) : all.slice();

    // النوع
    const isMCQ=x=>String(x.type||"").toUpperCase().includes("MCQ") || (x.options||[]).length>=2;
    const isTF =x=>String(x.type||"").toUpperCase().includes("TRUE") || typeof x.answer==="boolean";
    if(type==="MCQ") arr=arr.filter(isMCQ);
    else if(type==="TRUE_FALSE") arr=arr.filter(isTF);

    // shuffle + limit
    for(let i=arr.length-1;i>0;i--){const j=Math.floor(Math.random()*(i+1));[arr[i],arr[j]]=[arr[j],arr[i]];}
    if(limit>0 && arr.length>limit) arr=arr.slice(0,limit);
    return arr;
  }

  // ---------- State ----------
  let QUESTIONS=[];
  let state={ index:0, answers:[] };              // answers: {qid, picked, correct, isGraded, locked}
  const stats={ correct:0, wrong:0 };
  let evaluated=[], evaluatedOutcome=[];           // per-question evaluation flags

  // ---------- UI helpers ----------
  function updateMeta(){
    const total=QUESTIONS.length||0;
    if(metaEl) metaEl.textContent=`المشروع: ${projectId} | القسم: ${catParam||"الكل"} | النوع: ${type} | ${Math.min(state.index+1,total)} / ${total}`;
  }
  function hidePerQuestionFeedback(){ if(!fbBox) return; fbBox.classList.remove("ok","bad"); fbBox.style.display="none"; }
  function showPerQuestionFeedback(isCorrect){
    if(!fbBox||!fbEmoji||!fbC||!fbW) return;
    fbBox.classList.remove("ok","bad");
    if(isCorrect){ fbBox.classList.add("ok"); fbEmoji.textContent="✅"; fbEmoji.setAttribute("aria-label","correct"); }
    else { fbBox.classList.add("bad"); fbEmoji.textContent="❌"; fbEmoji.setAttribute("aria-label","wrong"); }
    fbC.textContent=stats.correct; fbW.textContent=stats.wrong;
  }
  function disableNextIfNoSelection(){
    if(!btnNext || !formEl) return;
    const hasSel = !!(formEl.querySelector('input[name="choice"]:checked') ||
                      formEl.querySelector('input[name="mcqOption"]:checked') ||
                      formEl.querySelector('input[name="tfOption"]:checked'));
    btnNext.disabled = !hasSel;
  }
  function wireChoiceRequired(){
    if(!formEl) return;
    disableNextIfNoSelection();
    formEl.addEventListener('change', disableNextIfNoSelection, { once:false });
  }
  function lockCurrentOptions(){
    if(!formEl) return;
    [...formEl.querySelectorAll('input[type="radio"]')].forEach(inp=>inp.disabled=true);
  }
  function unlockCurrentOptions(){
    if(!formEl) return;
    [...formEl.querySelectorAll('input[type="radio"]')].forEach(inp=>inp.disabled=false);
  }

  function renderCurrent(){
    const q=QUESTIONS[state.index];
    if(!q){ if(titleEl) titleEl.textContent="لا توجد أسئلة"; if(formEl) formEl.innerHTML=""; hidePerQuestionFeedback(); updateMeta(); return; }
    if(titleEl) titleEl.textContent=q.question||"—";
    if(bodyEl)  bodyEl.textContent=q.description||"";
    if(formEl)  formEl.innerHTML="";

    // ارسم خيارات باسم موحّد choice
    (q.options||[]).forEach((opt,i)=>{
      const id=`opt_${i}`;
      const div=document.createElement("div");
      div.className="option";
      div.innerHTML=`
        <label for="${id}" style="display:flex;gap:8px;align-items:center;">
          <input type="radio" name="choice" id="${id}" value="${i}"/>
          <span>${opt}</span>
        </label>`;
      formEl.appendChild(div);
    });

    // إعادة اختيار سابق + قفل إن كان مُقيّم مسبقًا
    const prev=state.answers.find(a=>a.qid===uidFromQuestion(q));
    if(prev){
      if(typeof prev.picked==="number"){
        const inp=formEl.querySelector(`input[name="choice"][value="${prev.picked}"]`);
        if(inp) inp.checked=true;
      }
      if(prev.locked){ lockCurrentOptions(); showPerQuestionFeedback(!!prev.correct); }
      else { hidePerQuestionFeedback(); }
    }else{
      hidePerQuestionFeedback();
    }

    if(progress){
      const pct=Math.round(((state.index+1)/Math.max(1,QUESTIONS.length))*100);
      progress.value=pct; progress.max=100;
    }
    if(btnDone) btnDone.style.display=(state.index===QUESTIONS.length-1?"":"none");

    updateMeta();
    wireChoiceRequired(); // زر «التالي» يُعطّل فقط إذا ما فيه اختيار
  }

  function captureCurrent(){
    const q=QUESTIONS[state.index]; if(!q) return;
    const qid=uidFromQuestion(q);
    let rec=state.answers.find(a=>a.qid===qid);
    if(!rec){ rec={ qid, type, picked:null, correct:null, isGraded:false, locked:false }; state.answers.push(rec); }

    const sel = (formEl.querySelector('input[name="choice"]:checked')
              || formEl.querySelector('input[name="mcqOption"]:checked')
              || formEl.querySelector('input[name="tfOption"]:checked'));
    if(sel && !rec.locked){ // لا نحدّث الاختيار إذا السؤال مقفول
      rec.picked=Number(sel.value);
      let correctIndex=null;
      if(typeof q.answer==="number") correctIndex=q.answer;
      else if(typeof q.answer==="string"){
        const idx=(q.options||[]).findIndex(o=>norm(o)===norm(q.answer));
        correctIndex=(idx>=0)?idx:null;
      }
      if(correctIndex!==null){
        rec.correct=(rec.picked===correctIndex);
        rec.isGraded=true;
      }else{
        rec.correct=null; rec.isGraded=false;
      }
    }
  }

  // يُقيّم السؤال (أول ضغط) ويعرض الإيموجي ويقفل الاختيارات ولا ينتقل
  function evaluateCurrentQuestion(){
    const q=QUESTIONS[state.index]; if(!q) return null;
    const qid=uidFromQuestion(q);
    // تأكد فيه اختيار
    const hasSel = !!(formEl.querySelector('input[name="choice"]:checked') ||
                      formEl.querySelector('input[name="mcqOption"]:checked') ||
                      formEl.querySelector('input[name="tfOption"]:checked'));
    if(!hasSel) return null;

    captureCurrent();
    const rec=state.answers.find(a=>a.qid===qid);
    if(!rec) return null;

    // إذا سبق قيّمناه: فقط أعرض الفيدباك، ولا تعيد العد
    if(evaluated[state.index]){
      showPerQuestionFeedback(!!rec.correct);
      return !!rec.correct;
    }

    // تقييم أول مرة
    if(rec.isGraded===true){
      const isCorrect=!!rec.correct;
      if(isCorrect) stats.correct++; else stats.wrong++;
      evaluated[state.index]=true;
      evaluatedOutcome[state.index]=isCorrect;

      // اقفل الاختيارات بعد كشف النتيجة (لا تغيير بعدها)
      rec.locked = true;
      lockCurrentOptions();
      showPerQuestionFeedback(isCorrect);
      return isCorrect;
    }

    return null;
  }

  // ---------- Navigation ----------
  function goPrev(){ 
    // مسموح ترجع، العرض فقط
    if(state.index>0){ state.index--; renderCurrent(); }
  }
  function goNext(){
    if(state.index<QUESTIONS.length-1){ state.index++; renderCurrent(); }
  }

  if(btnPrev) btnPrev.addEventListener("click", goPrev);

  // سلوك زر "التالي":
  // - أول ضغط مع اختيار ⇒ قيّم + أعرض الإيموجي + اقفل الاختيارات + لا تنتقل
  // - ضغطة ثانية (نفس السؤال مُقيّم) ⇒ انتقل للسؤال التالي
  if(btnNext) btnNext.addEventListener("click", ()=>{
    // ممنوع بدون اختيار (الزر أصلاً يُعطّل بواسطة wireChoiceRequired)
    const hasSel = !!(formEl.querySelector('input[name="choice"]:checked') ||
                      formEl.querySelector('input[name="mcqOption"]:checked') ||
                      formEl.querySelector('input[name="tfOption"]:checked'));
    if(!hasSel) return;

    const alreadyEvaluated = !!evaluated[state.index];
    const res = evaluateCurrentQuestion(); // يعرض الفيدباك ويقفل إن كانت أول مرة
    if(res === null) return;

    if(alreadyEvaluated){
      hidePerQuestionFeedback();
      goNext();
    } else {
      // أول نقرة: نظهر الفيدباك فقط ولا ننتقل
      // يبقى المستخدم على نفس السؤال، وزر التالي يبقى مفعّل
    }
  });

  if(btnDone) btnDone.addEventListener("click", ()=>{
    // لو السؤال الحالي غير مُقيّم و فيه اختيار، قيّمه أولاً
    if(!evaluated[state.index]){
      const maybe = evaluateCurrentQuestion();
      if(maybe === null) return; // ما فيه اختيار
    }
    const username=currentUserName();
    const key=`thq_results_v1:${username}`;
    const store=readStore(key);
    const graded=state.answers.filter(a=>a.isGraded);
    const correct=graded.filter(a=>a.correct===true).length;
    store.sessions.push({
      ts:Date.now(), projectId, type, category:catParam, source,
      total:QUESTIONS.length, gradedTotal:graded.length, correct,
      perQuestionCorrect:stats.correct, perQuestionWrong:stats.wrong,
      answers:state.answers
    });
    writeStore(key, store);
    const u=new URL("../home/my-stats.html", location.href);
    u.searchParams.set("flash","saved"); location.href=u.toString();
  });

  // ---------- Boot ----------
  async function start(){
    try{
      QUESTIONS=await loadQuestions();
      state.index=0; state.answers=[];
      evaluated=Array(QUESTIONS.length).fill(false);
      evaluatedOutcome=Array(QUESTIONS.length).fill(null);
      stats.correct=0; stats.wrong=0;
      renderCurrent();
    }catch(e){
      console.error(e);
      if(titleEl) titleEl.textContent="حدث خطأ أثناء تحميل الأسئلة.";
      if(metaEl)  metaEl.textContent=e.message||String(e);
    }
  }
  document.addEventListener("DOMContentLoaded", start);
})();
