<?php
/**
 * Import projects/questions/options from JSON files into MySQL using existing schema.
 * - No schema changes
 * - Uses PDO
 * - Idempotent-ish: لا ينشئ مشروع بنفس الاسم مرتين، ويحاول عدم تكرار نفس السؤال.
 */

///////////////////////////////////////////////////////////////////////////
// 1) إعدادات قاعدة البيانات
///////////////////////////////////////////////////////////////////////////

$DB_HOST = 'localhost';
$DB_NAME = 'saudi_culture';
$DB_USER = 'root';
$DB_PASS = '';
$DEFAULT_MANAGER_ID = 2; // id مستخدم manager موجود في جدول users

try {
    $pdo = new PDO(
        "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

///////////////////////////////////////////////////////////////////////////
// 2) تعريف المشاريع (نسخة PHP من window.PROJECTS)
///////////////////////////////////////////////////////////////////////////

$PROJECTS_CONFIG = [
    [
        'code' => 'SC_GENERAL',
        'name' => 'المشروع الوطني العام',
        'managerDescription' => 'ملخص المشروع الوطني لمديري التقييم: يتضمن أسئلة مفتوحة واختيار من متعدد تغطي جميع الفئات العامة.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة العامة حول العادات والتقاليد الوطنية.',
        'questionTypes' => ['Open-ended', 'MCQ (one correct)'],
        'annotatorsCount' => 15,
        'stats' => ['completedTasks' => 120, 'totalTasks' => 250],
        'categories' => [
            ['id' => 'Food', 'label' => 'الطعام والمشروبات'],
            ['id' => 'Clothes', 'label' => 'الملابس والزي التقليدي'],
            ['id' => 'Crafts and Work', 'label' => 'الحرف والأعمال اليدوية'],
            ['id' => 'Celebration', 'label' => 'الاحتفالات والمناسبات'],
            ['id' => 'Entertainment', 'label' => 'الترفيه والفنون'],
            ['id' => 'Dating', 'label' => 'المناسبات الاجتماعية والتعارف'],
        ],
    ],
    [
        'code' => 'SC_SOUTH',
        'name' => 'المشروع الجنوبي: عادات وتقاليد المنطقة الجنوبية',
        'managerDescription' => 'ملخص المشروع الجنوبي لمديري التقييم: تركيز على تنوع العادات في مناطق عسير ونجران وجازان والباحة.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الجنوبية.',
        'questionTypes' => ['Open-ended', 'MCQ (one correct)', 'MCQ (multiple correct)'],
        'annotatorsCount' => 8,
        'stats' => ['completedTasks' => 60, 'totalTasks' => 100],
        'categories' => [
            ['id' => 'Food', 'label' => 'أطباق المنطقة الجنوبية'],
            ['id' => 'Clothes', 'label' => 'أزياء المنطقة الجنوبية'],
            ['id' => 'Crafts and Work', 'label' => 'حرف وصناعات المنطقة الجنوبية'],
            ['id' => 'Celebration', 'label' => 'احتفالات المنطقة الجنوبية'],
            ['id' => 'Entertainment', 'label' => 'فنون وأهازيج المنطقة الجنوبية'],
            ['id' => 'Dating', 'label' => 'التعارف والزواج في الجنوب'],
        ],
    ],
    [
        'code' => 'SC_NORTH',
        'name' => 'المشروع الشمالي: عادات وتقاليد المنطقة الشمالية',
        'managerDescription' => 'ملخص المشروع الشمالي لمديري التقييم: يغطي عادات مناطق الحدود الشمالية والجوف وحائل وتبوك.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الشمالية.',
        'questionTypes' => ['Open-ended', 'MCQ (one correct)'],
        'annotatorsCount' => 6,
        'stats' => ['completedTasks' => 35, 'totalTasks' => 80],
        'categories' => [
            ['id' => 'Food', 'label' => 'أطباق المنطقة الشمالية'],
            ['id' => 'Clothes', 'label' => 'أزياء المنطقة الشمالية'],
            ['id' => 'Celebration', 'label' => 'احتفالات المنطقة الشمالية'],
            ['id' => 'Dating', 'label' => 'التعارف والزواج في الشمال'],
        ],
    ],
    [
        'code' => 'SC_EAST',
        'name' => 'المشروع الشرقي: عادات وتقاليد المنطقة الشرقية',
        'managerDescription' => 'ملخص المشروع الشرقي لمديري التقييم: يركز على عادات مناطق الدمام والخبر والأحساء والجبيل.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الشرقية.',
        'questionTypes' => ['Open-ended', 'MCQ (multiple correct)'],
        'annotatorsCount' => 10,
        'stats' => ['completedTasks' => 80, 'totalTasks' => 150],
        'categories' => [
            ['id' => 'Food', 'label' => 'أطباق المنطقة الشرقية'],
            ['id' => 'Clothes', 'label' => 'أزياء المنطقة الشرقية'],
            ['id' => 'Crafts and Work', 'label' => 'صناعات المنطقة الشرقية'],
            ['id' => 'Entertainment', 'label' => 'تراث المنطقة الشرقية'],
        ],
    ],
    [
        'code' => 'SC_CENTRAL',
        'name' => 'المشروع الأوسط: عادات وتقاليد المنطقة الوسطى',
        'managerDescription' => 'ملخص المشروع الأوسط لمديري التقييم: يركز على عادات مناطق الرياض والقصيم ونجد.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الوسطى.',
        'questionTypes' => ['Open-ended', 'MCQ (one correct)', 'MCQ (multiple correct)'],
        'annotatorsCount' => 12,
        'stats' => ['completedTasks' => 90, 'totalTasks' => 180],
        'categories' => [
            ['id' => 'Food', 'label' => 'أطباق المنطقة الوسطى'],
            ['id' => 'Clothes', 'label' => 'أزياء المنطقة الوسطى'],
            ['id' => 'Heritage', 'label' => 'تراث المنطقة الوسطى'],
            ['id' => 'Dialect', 'label' => 'لهجة المنطقة الوسطى'],
        ],
    ],
    [
        'code' => 'SC_WEST',
        'name' => 'المشروع الغربي: عادات وتقاليد المنطقة الغربية',
        'managerDescription' => 'ملخص المشروع الغربي لمديري التقييم: يغطي عادات مناطق مكة والمدينة وجدة والطائف.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول عادات وتقاليد المنطقة الغربية.',
        'questionTypes' => ['Open-ended', 'MCQ (one correct)'],
        'annotatorsCount' => 14,
        'stats' => ['completedTasks' => 110, 'totalTasks' => 200],
        'categories' => [
            ['id' => 'Food', 'label' => 'أطباق المنطقة الغربية'],
            ['id' => 'Clothes', 'label' => 'أزياء المنطقة الغربية'],
            ['id' => 'Heritage', 'label' => 'تراث المنطقة الغربية'],
        ],
    ],
    [
        'code' => 'SC_WORDS',
        'name' => 'مشروع الكلمات: اللهجات السعودية',
        'managerDescription' => 'ملخص مشروع الكلمات لمديري التقييم: يتضمن أسئلة حول الكلمات والمصطلحات في اللهجات السعودية المختلفة.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول معاني الكلمات والمصطلحات في اللهجات السعودية.',
        'questionTypes' => ['MCQ (one correct)'],
        'annotatorsCount' => 20,
        'stats' => ['completedTasks' => 450, 'totalTasks' => 1000],
        'categories' => [
            ['id' => 'Dialect', 'label' => 'اللهجات السعودية'],
        ],
    ],
    [
        'code' => 'SC_PHRASES',
        'name' => 'مشروع العبارات: التعابير الشعبية',
        'managerDescription' => 'ملخص مشروع العبارات لمديري التقييم: يركز على العبارات والتعابير الشعبية في المناطق السعودية.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول العبارات والتعابير الشعبية السعودية.',
        'questionTypes' => ['MCQ (one correct)'],
        'annotatorsCount' => 15,
        'stats' => ['completedTasks' => 200, 'totalTasks' => 400],
        'categories' => [
            ['id' => 'Dialect', 'label' => 'التعابير الشعبية'],
        ],
    ],
    [
        'code' => 'SC_PROVERBS',
        'name' => 'مشروع الأمثال: الأمثال الشعبية السعودية',
        'managerDescription' => 'ملخص مشروع الأمثال لمديري التقييم: يتضمن أسئلة حول الأمثال الشعبية السعودية ومعانيها.',
        'userDescription' => 'المهمة: تقييم جودة إجابات الأسئلة حول الأمثال الشعبية السعودية.',
        'questionTypes' => ['MCQ (one correct)'],
        'annotatorsCount' => 8,
        'stats' => ['completedTasks' => 50, 'totalTasks' => 100],
        'categories' => [
            ['id' => 'Proverbs', 'label' => 'الأمثال الشعبية'],
        ],
    ],
];

///////////////////////////////////////////////////////////////////////////
// 3) Helpers
///////////////////////////////////////////////////////////////////////////

function map_project_category(string $code): string
{
    return match ($code) {
        'GENERAL-questions'  => 'وطني عام',
        'SOUTH-questions'  =>'المنطقة الجنوبية',
        'NORTH-questions'  =>'المنطقة الشمالية',
        'EAST-questions'  => 'المنطقة الشرقية',
        'CENTRAL-questions'  => 'المنطقة الوسطى',
        'WEST-questions'  =>'المنطقة الغربية',
        'WORDS-questions'  =>'مشروع الكلمات',
        'PHRASES-questions'  => 'مشروع العبارات',
        'PROVERBS-questions'  => 'مشروع الأمثال',
        default       => 'غير محدد',
    };
}

/**
 * تحويل نوع السؤال من JSON إلى ENUM الموجود في DB: ('mcq','true_false','open','list')
 */
function map_question_type(string $rawType): string
{
    $raw = strtoupper(trim($rawType));

    if (in_array($raw, ['MCQ', 'MCQ_ONE', 'MCQ_SINGLE'], true)) {
        return 'mcq';
    }
    if (in_array($raw, ['MCQ_MULTIPLE', 'MULTI', 'MULTIPLE'], true)) {
        return 'mcq';
    }
    if (in_array($raw, ['OPEN_ENDED', 'OPEN'], true)) {
        return 'open';
    }
    if (in_array($raw, ['TRUE_FALSE', 'TF'], true)) {
        return 'true_false';
    }
    if (in_array($raw, ['LIST', 'LISTS'], true)) {
        return 'list';
    }

    return 'mcq';
}

/**
 * يحوّل answer للـ MCQ/MCQ_MULTIPLE إلى set من indices صحيحة
 *  - int => index مباشرة
 *  - "A and B" أو "A, B, C and D" => حروف تتحول إلى indices
 *  - أو نص الخيار نفسه
 */
function parse_mcq_answer(mixed $answer, array $options): array
{
    $correct = [];

    // رقم => index
    if (is_int($answer) || is_float($answer)) {
        $idx = (int)$answer;
        if ($idx >= 0 && $idx < count($options)) {
            $correct[] = $idx;
        } else {
            echo "⚠ Warning: answer index out of range: {$answer} / len=" . count($options) . PHP_EOL;
        }
        return array_values(array_unique($correct));
    }

    // نص
    if (is_string($answer)) {
        $ansStr = trim($answer);

        // حروف A,B,C,...
        $letters = [];
        foreach (str_split($ansStr) as $ch) {
            $chUp = strtoupper($ch);
            if ($chUp >= 'A' && $chUp <= 'Z') {
                $letters[] = $chUp;
            }
        }
        if (!empty($letters)) {
            foreach ($letters as $ch) {
                $idx = ord($ch) - ord('A');
                if ($idx >= 0 && $idx < count($options)) {
                    $correct[] = $idx;
                }
            }
            return array_values(array_unique($correct));
        }

        // تطابق نص الخيار
        $lowered = mb_strtolower($ansStr, 'UTF-8');
        foreach ($options as $i => $opt) {
            $optStr = (string)$opt;
            if (mb_strtolower(trim($optStr), 'UTF-8') === $lowered) {
                $correct[] = $i;
            }
        }
    }

    return array_values(array_unique($correct));
}

/**
 * Map project code to a prepared image stored under public/uploads/projects.
 * Returns relative path (to public) or null if not found.
 */
function map_project_image(string $code): ?string
{
    $map = [
        'SC_GENERAL'   => 'الوطني.jpeg',
        'SC_SOUTH'     => 'الجنوبي.jpeg',
        'SC_NORTH'     => 'شمالي.jpeg',
        'SC_EAST'      => 'الشرقي.jpeg',
        'SC_CENTRAL'   => 'الوسطي.jpeg',
        'SC_WEST'      => 'الغربي.jpeg',
        'SC_WORDS'     => 'اللهجات.jpeg',
        'SC_PHRASES'   => 'العبارات.jpeg',
        'SC_PROVERBS'  => 'الامثال.jpeg',
    ];

    if (!isset($map[$code])) {
        return null;
    }

    $relativePath = 'uploads/projects/' . $map[$code];
    $fullPath     = __DIR__ . '/../public/' . $relativePath;

    return file_exists($fullPath) ? $relativePath : null;
}

/**
 * Map project code to a representative location in Saudi Arabia.
 * Returns ['lat' => float, 'lng' => float, 'name' => string] or null.
 */
function map_project_location(string $code): ?array
{
    $map = [
        'SC_GENERAL'  => [24.7136, 46.6753, 'المملكة العربية السعودية - الرياض'],
        'SC_SOUTH'    => [18.2465, 42.5117, 'المنطقة الجنوبية - أبها'],
        'SC_NORTH'    => [27.5114, 41.7208, 'المنطقة الشمالية - حائل'],
        'SC_EAST'     => [26.4207, 50.0888, 'المنطقة الشرقية - الدمام'],
        'SC_CENTRAL'  => [24.7136, 46.6753, 'المنطقة الوسطى - الرياض'],
        'SC_WEST'     => [21.4858, 39.1925, 'المنطقة الغربية - جدة'],
        'SC_WORDS'    => [24.7136, 46.6753, 'لهجات سعودية - الرياض'],
        'SC_PHRASES'  => [24.7136, 46.6753, 'تعابير شعبية - الرياض'],
        'SC_PROVERBS' => [24.7136, 46.6753, 'أمثال شعبية - الرياض'],
    ];

    if (!isset($map[$code])) {
        return null;
    }

    [$lat, $lng, $name] = $map[$code];
    return [
        'lat'  => $lat,
        'lng'  => $lng,
        'name' => $name,
    ];
}

/**
 * Get or create project in DB and return its ID.
 * لا نغيّر الـ schema ونستخدم فقط الحقول الموجودة.
 */
function get_or_create_project(PDO $pdo, array $proj, int $defaultManagerId): int
{
    $name        = $proj['name'];
    $summary     = $proj['managerDescription'] ?? null;
    $description = $proj['userDescription'] ?? null;
    $category    = map_project_category($proj['code']);
    $imageUrl    = map_project_image($proj['code']);
    $location    = map_project_location($proj['code']);
    $lat         = $location['lat']  ?? null;
    $lng         = $location['lng']  ?? null;
    $locName     = $location['name'] ?? null;

    // هل المشروع موجود؟
    $stmt = $pdo->prepare("SELECT id, image_url, latitude, longitude, location_name FROM projects WHERE name = :name LIMIT 1");
    $stmt->execute(['name' => $name]);
    $row = $stmt->fetch();

    if ($row) {
        // If project exists but image is missing and a mapped image is available, update it.
        if (empty($row['image_url']) && $imageUrl !== null) {
            $pdo->prepare("UPDATE projects SET image_url = :img WHERE id = :id")
                ->execute(['img' => $imageUrl, 'id' => $row['id']]);
            echo "ℹ Updated image for existing project: {$name}" . PHP_EOL;
        }
        // Backfill location if missing and mapped.
        if ((empty($row['latitude']) || empty($row['longitude'])) && $lat !== null && $lng !== null) {
            $pdo->prepare("
                UPDATE projects 
                SET latitude = :lat, longitude = :lng, location_name = :loc 
                WHERE id = :id
            ")->execute(['lat' => $lat, 'lng' => $lng, 'loc' => $locName, 'id' => $row['id']]);
            echo "ℹ Updated location for existing project: {$name}" . PHP_EOL;
        }
        echo "ℹ Project exists: {$name} (id={$row['id']})" . PHP_EOL;
        return (int)$row['id'];
    }

    // إنشاء مشروع جديد
    $sql = "
        INSERT INTO projects
            (name, summary, description, category, image_url, latitude, longitude, location_name, created_by, total_questions)
        VALUES
            (:name, :summary, :description, :category, :image_url, :latitude, :longitude, :location_name, :created_by, 0)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'name'        => $name,
        'summary'     => $summary,
        'description' => $description,
        'category'    => $category,
        'image_url'   => $imageUrl,
        'latitude'    => $lat,
        'longitude'   => $lng,
        'location_name' => $locName,
        'created_by'  => $defaultManagerId,
    ]);

    $projectId = (int)$pdo->lastInsertId();
    echo "✅ Created project: {$name} (id={$projectId})" . PHP_EOL;

    return $projectId;
}

/**
 * استيراد أسئلة مشروع واحد من ملف JSON:
 * - يدرج في questions
 * - يدرج الخيارات في question_options
 */
function import_project_questions_from_json(PDO $pdo, int $projectId, string $jsonFilePath): void
{
    if (!file_exists($jsonFilePath)) {
        echo "⚠ JSON file not found: {$jsonFilePath}" . PHP_EOL;
        return;
    }

    $jsonContent = file_get_contents($jsonFilePath);
    $data        = json_decode($jsonContent, true);

    if (!is_array($data)) {
        echo "⚠ Invalid JSON format in: {$jsonFilePath}" . PHP_EOL;
        return;
    }

    $insertQuestionStmt = $pdo->prepare("
        INSERT INTO questions (project_id, question_text, question_type, category, media_url)
        VALUES (:project_id, :question_text, :question_type, :category, :media_url)
    ");

    $selectExistingQuestionStmt = $pdo->prepare("
        SELECT id FROM questions WHERE project_id = :project_id AND question_text = :question_text LIMIT 1
    ");

    $insertOptionStmt = $pdo->prepare("
        INSERT INTO question_options (question_id, option_text, is_correct)
        VALUES (:question_id, :option_text, :is_correct)
    ");

    $insertedCount = 0;

    foreach ($data as $item) {
        $typeRaw  = $item['type'] ?? '';
        $dbQType  = map_question_type($typeRaw);
        $category = $item['Category'] ?? null;
        $question = trim((string)($item['question'] ?? ''));

        if ($question === '') {
            continue;
        }

        $options = $item['options'] ?? [];
        if (!is_array($options)) {
            $options = [];
        }

        $answer = $item['answer'] ?? null;

        // نتأكد ما ندخل نفس السؤال مرتين لنفس المشروع
        $selectExistingQuestionStmt->execute([
            'project_id'    => $projectId,
            'question_text' => $question,
        ]);
        $existing = $selectExistingQuestionStmt->fetch();
        if ($existing) {
            // ممكن تتجاهله أو تطبع رسالة
            echo "ℹ Skipping duplicate question for project {$projectId}: {$question}" . PHP_EOL;
            continue;
        }

        // إدخال السؤال
        $insertQuestionStmt->execute([
            'project_id'    => $projectId,
            'question_text' => $question,
            'question_type' => $dbQType,
            'category'      => $category,
            'media_url'     => null,
        ]);
        $questionId = (int)$pdo->lastInsertId();

        // التعامل مع الخيارات
        if ($dbQType === 'mcq' || $dbQType === 'true_false') {
            $correctIndices = parse_mcq_answer($answer, $options);

            foreach ($options as $idx => $optText) {
                $optStr    = (string)$optText;
                $isCorrect = in_array($idx, $correctIndices, true) ? 1 : 0;

                $insertOptionStmt->execute([
                    'question_id' => $questionId,
                    'option_text' => $optStr,
                    'is_correct'  => $isCorrect,
                ]);
            }
        } elseif ($dbQType === 'open' || $dbQType === 'list') {
            // نخزن الإجابة النموذجية كخيار واحد في question_options (اختياري)
            if ($answer !== null && trim((string)$answer) !== '') {
                $modelAnswer = trim((string)$answer);
                $insertOptionStmt->execute([
                    'question_id' => $questionId,
                    'option_text' => $modelAnswer,
                    'is_correct'  => 1,
                ]);
            }
        }

        $insertedCount++;
    }

    // تحديث total_questions
    $pdo->prepare("
        UPDATE projects
        SET total_questions = (
            SELECT COUNT(*) FROM questions WHERE project_id = :pid
        )
        WHERE id = :pid
    ")->execute(['pid' => $projectId]);

    echo "✅ Imported {$insertedCount} questions from " . basename($jsonFilePath) . " into project id={$projectId}" . PHP_EOL;
}

///////////////////////////////////////////////////////////////////////////
// 4) MAIN: لف على المشاريع واقرأ ملف JSON لكل واحد
///////////////////////////////////////////////////////////////////////////

/**
 * نفترض أن:
 *  لكل مشروع ملف JSON باسم: CODE-questions.json داخل مجلد data/
 *   مثال:
 *    NORTH-questions.json  -> لـ "المشروع الشمالي"
 *    SOUTH-questions.json  -> ...
 */
$baseJsonDir = __DIR__ . '/data';

// Map project codes to actual JSON filenames
$codeToJsonFile = [
    'SC_GENERAL' => 'GENERAL-questions.json',
    'SC_SOUTH' => 'SOUTH-questions.json',
    'SC_NORTH' => 'NORTH-questions.json',
    'SC_EAST' => 'EAST-questions.json',
    'SC_CENTRAL' => 'CENTRAL-questions.json',
    'SC_WEST' => 'WEST-questions.json',
    'SC_WORDS' => 'words-questions.json',      // lowercase
    'SC_PHRASES' => 'phrases-questions.json',  // lowercase
    'SC_PROVERBS' => 'proverbs-questions.json', // lowercase
];

foreach ($PROJECTS_CONFIG as $proj) {
    $projectCode = $proj['code'];
    $projectId   = get_or_create_project($pdo, $proj, $DEFAULT_MANAGER_ID);

    // Get the actual JSON filename for this project code
    $jsonFilename = $codeToJsonFile[$projectCode] ?? ($projectCode . '.json');
    $jsonFile = $baseJsonDir . '/' . $jsonFilename;

    import_project_questions_from_json($pdo, $projectId, $jsonFile);
}

echo PHP_EOL . "🎉 Done importing all projects/questions from JSON." . PHP_EOL;
