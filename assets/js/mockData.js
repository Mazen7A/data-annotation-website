/* assets/js/mockData.js - عربي + متوافق بدون ES Modules */
var PROJECTS = [
    {
        id: "SC_NATIONAL",
        name: "الثقافة السعودية: العادات الوطنية (على مستوى المملكة)",
        userDescription: "شارك في تقييم وتصنيف العادات والتقاليد المشتركة في جميع مناطق المملكة، مثل آداب الأكل والتحيات، لبناء قاعدة بيانات تمثل الثقافة السعودية العامة.",
        managerDescription: "منصة شاملة لمتابعة سير عمل مشروع العادات الوطنية. يمكنك مراقبة أداء الين، تتبع دقة التصنيفات، واستعراض الإحصائيات.",
        totalQuestions: 10,
        annotatorsCount: 18,
        questionTypes: ["أسئلة مفتوحة", "اختيار من متعدد", "قائمة اختيار", "صحيح/خطأ"] 
    },
    {
        id: "SC_CENTRAL",
        name: "الثقافة السعودية: المنطقة الوسطى (نجد)",
        userDescription: "يركز هذا المشروع على التراث النجدي في وسط المملكة، بما في ذلك الأكلات الشعبية والأزياء التقليدية والفنون مثل العرضة والسامري. مهمتك هي توثيق العادات الدقيقة.",
        managerDescription: "متابعة وتقييم مدى تطابق التقييمات في مشروع المنطقة الوسطى. يمكنك استخراج التقارير اللازمة لتقييم كفاءة فريق العمل.",
        totalQuestions: 10,
        annotatorsCount: 12,
        questionTypes: ["أسئلة مفتوحة", "اختيار من متعدد", "قائمة اختيار", "صحيح/خطأ"] 
    },
    {
        id: "SC_EASTERN",
        name: "الثقافة السعودية: المنطقة الشرقية",
        userDescription: "يتناول هذا المشروع التراث الساحلي والبحري للمنطقة الشرقية، مثل أكلات البلاليط والساقو، والحرف مثل صناعة السفن وحياكة البشوت. الهدف هو توثيق العادات والأنشطة البحرية.",
        managerDescription: "لوحة تحكم لمراقبة جودة البيانات في مشروع المنطقة الشرقية. يمكنك مراجعة تقدم الين بانتظام.",
        totalQuestions: 10,
        annotatorsCount: 15,
        questionTypes: ["أسئلة مفتوحة", "اختيار من متعدد", "قائمة اختيار", "صحيح/خطأ"] 
    },
    {
        id: "SC_NORTHERN",
        name: "الثقافة السعودية: المنطقة الشمالية",
        userDescription: "يستعرض هذا المشروع العادات والتقاليد في شمال المملكة، بما في ذلك المأكولات مثل المنسف والمريس، وأساليب الضيافة، والملابس مثل السديرية والشرش، والفنون الشعبية مثل الدحة والسامري. ساهم في توثيق التراث الشمالي.",
        managerDescription: "متابعة سير العمل في مشروع المنطقة الشمالية. يجب التركيز على متابعة مدى دقة الإجابات في هذا القسم.",
        totalQuestions: 10,
        annotatorsCount: 9,
        questionTypes: ["أسئلة مفتوحة", "اختيار من متعدد", "قائمة اختيار", "صحيح/خطأ"] 
    }
];

// === UI Cleaner: remove stray '1'/'-' artifacts near top-right & duplicate titles ===
document.addEventListener('DOMContentLoaded', () => {
  try {
    // remove top-level text nodes "1" or "-" (rare build artifacts)
    const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
    const bad = [];
    while (walker.nextNode()) {
      const n = walker.currentNode;
      if (n.parentElement && ['SCRIPT','STYLE'].includes(n.parentElement.tagName)) continue;
      const t = (n.textContent || '').trim();
      if (t === '1' || t === '-') bad.push(n);
    }
    bad.forEach(n => n.parentNode && n.parentNode.removeChild(n));

    // remove elements stuck at top-right that only contain '1' or '-'
    document.querySelectorAll('body *').forEach(el => {
      const rect = el.getBoundingClientRect();
      const t = (el.textContent || '').trim();
      if ((t === '1' || t === '-') && rect.top < 140 && rect.right > window.innerWidth - 140) {
        el.remove();
      }
    });

    // ensure no extra site-title besides header block
    document.querySelectorAll('div.site-title:not(header .site-title)').forEach(e => e.remove());
  } catch(e){ /* no-op */ }
});
