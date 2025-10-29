window.PROJECTS = [
  {
      id: "SC_GENERAL", 
      name: "المشروع الوطني العام",
      managerDescription: "ملخص المشروع الوطني لمديري التقييم: يتضمن أسئلة مفتوحة واختيار من متعدد تغطي جميع الفئات العامة.",
      userDescription: "المهمة: تقييم جودة إجابات الأسئلة العامة حول العادات والتقاليد الوطنية.",
      questionTypes: ["Open-ended", "MCQ (one correct)"],
      annotatorsCount: 15,
      stats: { completedTasks: 120, totalTasks: 250 },
      categories: [
          { id: "Food", label: "الطعام والمشروبات" },
          { id: "Clothes", label: "الملابس والزي التقليدي" },
          { id: "Crafts and Work", label: "الحرف والأعمال اليدوية" },
          { id: "Celebration", label: "الاحتفالات والمناسبات" },
          { id: "Entertainment", label: "الترفيه والفنون" },
          { id: "Dating", label: "المناسبات الاجتماعية والتعارف" }
      ]
  },
  {
      id: "SC_SOUTH", 
      name: "المشروع الجنوبي: عادات وتقاليد المنطقة الجنوبية",
      managerDescription: "ملخص المشروع الجنوبي لمديري التقييم: تركيز على تنوع العادات في مناطق عسير ونجران وجازان والباحة.",
      userDescription: "المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الجنوبية.",
      questionTypes: ["Open-ended", "MCQ (one correct)", "MCQ (multiple correct)"],
      annotatorsCount: 8,
      stats: { completedTasks: 60, totalTasks: 100 },
      categories: [
          { id: "Food", label: "أطباق المنطقة الجنوبية" },
          { id: "Clothes", label: "أزياء المنطقة الجنوبية" },
          { id: "Crafts and Work", label: "حرف وصناعات المنطقة الجنوبية" },
          { id: "Celebration", label: "احتفالات المنطقة الجنوبية" },
          { id: "Entertainment", label: "فنون وأهازيج المنطقة الجنوبية" },
          { id: "Dating", label: "التعارف والزواج في الجنوب" }
      ]
  },
  {
      id: "SC_NORTH", 
      name: "المشروع الشمالي: عادات وتقاليد المنطقة الشمالية",
      managerDescription: "ملخص المشروع الشمالي لمديري التقييم: يغطي عادات مناطق الحدود الشمالية والجوف وحائل وتبوك.",
      userDescription: "المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الشمالية.",
      questionTypes: ["Open-ended", "MCQ (one correct)"],
      annotatorsCount: 6,
      stats: { completedTasks: 35, totalTasks: 80 },
      categories: [
          { id: "Food", label: "أطباق المنطقة الشمالية" },
          { id: "Clothes", label: "أزياء المنطقة الشمالية" },
          { id: "Celebration", label: "احتفالات المنطقة الشمالية" },
          { id: "Dating", label: "التعارف والزواج في الشمال" }
      ]
  },
  {
      id: "SC_EAST", 
      name: "المشروع الشرقي: عادات وتقاليد المنطقة الشرقية",
      managerDescription: "ملخص المشروع الشرقي لمديري التقييم: يركز على عادات مناطق الدمام والخبر والأحساء والجبيل.",
      userDescription: "المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الشرقية.",
      questionTypes: ["Open-ended", "MCQ (multiple correct)"],
      annotatorsCount: 10,
      stats: { completedTasks: 80, totalTasks: 150 },
      categories: [
          { id: "Food", label: "أطباق المنطقة الشرقية" },
          { id: "Clothes", label: "أزياء المنطقة الشرقية" },
          { id: "Crafts and Work", label: "صناعات المنطقة الشرقية" },
          { id: "Entertainment", label: "تراث المنطقة الشرقية" }
      ]
  }
];

window.RATING_OPTIONS = [
  { id: 'perfect', label: 'إجابة ممتازة (100%)', score: 100 },
  { id: 'good', label: 'إجابة جيدة (75%)', score: 75 },
  { id: 'average', label: 'إجابة مقبولة (50%)', score: 50 },
  { id: 'poor', label: 'إجابة ضعيفة (25%)', score: 25 },
  { id: 'irrelevant', label: 'غير ذات صلة (0%)', score: 0 }
];
