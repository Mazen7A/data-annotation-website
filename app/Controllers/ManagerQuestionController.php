<?php
namespace App\Controllers;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Project;
use App\Models\ProjectCommit;
use App\Database\DB;

class ManagerQuestionController
{
    /**
     * List questions for a project.
     */
    public function index()
    {
        $projectId = $_GET['project_id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $questions = Question::getByProject($projectId);

        view('manager/questions/index', [
            'project' => $project,
            'questions' => $questions
        ]);
    }

    /**
     * Show create question form.
     */
    public function create()
    {
        $projectId = $_GET['project_id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        view('manager/questions/create', ['project' => $project]);
    }

    /**
     * Store new question.
     */
    public function store()
    {
        $projectId = $_POST['project_id'] ?? 0;
        $questionText = $_POST['question_text'] ?? '';
        $questionType = $_POST['question_type'] ?? '';
        $category = $_POST['category'] ?? '';

        if (empty($questionText) || empty($questionType)) {
            $_SESSION['error'] = 'نص السؤال ونوعه مطلوبان';
            header('Location: ' . route('manager.questions.create', ['project_id' => $projectId]));
            exit;
        }

        // Handle media upload
        $mediaUrl = null;
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $mediaUrl = $this->uploadMedia($_FILES['media']);
        }

        $questionId = Question::create([
            'project_id' => $projectId,
            'question_text' => $questionText,
            'question_type' => $questionType,
            'category' => $category,
            'media_url' => $mediaUrl
        ]);

        if ($questionId) {
            // Handle options for MCQ and True/False
            if (in_array($questionType, ['mcq', 'true_false'])) {
                $options = $_POST['options'] ?? [];
                $correctOptions = $_POST['correct_options'] ?? [];

                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        QuestionOption::create([
                            'question_id' => $questionId,
                            'option_text' => $optionText,
                            'is_correct' => in_array($index, $correctOptions) ? 1 : 0
                        ]);
                    }
                }
            }

            // Increment project question count
            Project::incrementQuestionCount($projectId);

            // Log commit
            ProjectCommit::create($projectId, auth()->id(), "إضافة سؤال جديد");

            $_SESSION['success'] = 'تم إضافة السؤال بنجاح';
            header('Location: ' . route('manager.questions', ['project_id' => $projectId]));
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إضافة السؤال';
            header('Location: ' . route('manager.questions.create', ['project_id' => $projectId]));
        }
        exit;
    }

    /**
     * Show edit question form.
     */
    public function edit()
    {
        $questionId = $_GET['id'] ?? 0;
        $question = Question::getById($questionId);

        if (!$question) {
            $_SESSION['error'] = 'السؤال غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $project = Project::getById($question->project_id);

        view('manager/questions/edit', [
            'question' => $question,
            'project' => $project
        ]);
    }

    /**
     * Update question.
     */
    public function update()
    {
        $questionId = $_POST['id'] ?? 0;
        $question = Question::getById($questionId);

        if (!$question) {
            $_SESSION['error'] = 'السؤال غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $questionText = $_POST['question_text'] ?? '';
        $questionType = $_POST['question_type'] ?? '';
        $category = $_POST['category'] ?? '';

        if (empty($questionText) || empty($questionType)) {
            $_SESSION['error'] = 'نص السؤال ونوعه مطلوبان';
            header('Location: ' . route('manager.questions.edit', ['id' => $questionId]));
            exit;
        }

        $data = [
            'question_text' => $questionText,
            'question_type' => $questionType,
            'category' => $category
        ];

        // Handle media upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $data['media_url'] = $this->uploadMedia($_FILES['media']);
        }

        $result = Question::update($questionId, $data);

        if ($result) {
            // Update options if MCQ or True/False
            if (in_array($questionType, ['mcq', 'true_false'])) {
                // Delete existing options
                QuestionOption::deleteByQuestion($questionId);

                // Add new options
                $options = $_POST['options'] ?? [];
                $correctOptions = $_POST['correct_options'] ?? [];

                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        QuestionOption::create([
                            'question_id' => $questionId,
                            'option_text' => $optionText,
                            'is_correct' => in_array($index, $correctOptions) ? 1 : 0
                        ]);
                    }
                }
            }

            // Log commit
            ProjectCommit::create($question->project_id, auth()->id(), "تحديث سؤال");

            $_SESSION['success'] = 'تم تحديث السؤال بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
        }

        header('Location: ' . route('manager.questions', ['project_id' => $question->project_id]));
        exit;
    }

    /**
     * Delete question.
     */
    public function delete()
    {
        $questionId = $_POST['id'] ?? 0;
        $question = Question::getById($questionId);

        if (!$question) {
            $_SESSION['error'] = 'السؤال غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $projectId = $question->project_id;
        $result = Question::delete($questionId);

        if ($result) {
            // Decrement project question count
            Project::decrementQuestionCount($projectId);

            // Log commit
            ProjectCommit::create($projectId, auth()->id(), "حذف سؤال");

            $_SESSION['success'] = 'تم حذف السؤال بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء الحذف';
        }

        header('Location: ' . route('manager.questions', ['project_id' => $projectId]));
        exit;
    }

    /**
     * Upload question media.
     */
    private function uploadMedia($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/questions/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'question_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/questions/' . $filename;
        }

        return null;
    }

    /**
     * Show bulk import form for questions.
     */
    public function importForm()
    {
        $projects = Project::getAll();
        view('manager/questions/import', ['projects' => $projects]);
    }

    /**
     * Handle bulk upload of questions (JSON file).
     */
    public function importStore()
    {
        $targetProject = $_POST['project_id'] ?? 'bank';

        // Resolve project ID (existing or bank project)
        if ($targetProject === 'bank') {
            $projectId = $this->getBankProjectId();
        } else {
            $projectId = (int)$targetProject;
        }

        $project = Project::getById($projectId);
        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود أو لم يتم اختياره';
            header('Location: ' . route('manager.questions.import'));
            exit;
        }

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'يرجى اختيار ملف أسئلة صالح (JSON)';
            header('Location: ' . route('manager.questions.import'));
            exit;
        }

        $tmpPath = $_FILES['file']['tmp_name'];
        $json = file_get_contents($tmpPath);
        $items = json_decode($json, true);

        if (!is_array($items)) {
            $_SESSION['error'] = 'صيغة الملف غير صحيحة، يجب أن يكون JSON يحتوي على قائمة أسئلة';
            header('Location: ' . route('manager.questions.import'));
            exit;
        }

        $inserted = 0;
        foreach ($items as $item) {
            $questionText = trim((string)($item['question'] ?? ''));
            $typeRaw      = trim((string)($item['type'] ?? ''));
            $category     = $item['category'] ?? ($item['Category'] ?? null);
            $options      = $item['options'] ?? [];
            $answer       = $item['answer'] ?? null;

            if ($questionText === '' || $typeRaw === '') {
                continue;
            }

            $dbType = $this->mapType($typeRaw);
            // Always store in bank tables
            $this->storeInBank($questionText, $dbType, $category, $options, $answer);

            // Optionally also add to a specific project (including bank project)
            $questionId = Question::create([
                'project_id'    => $projectId,
                'question_text' => $questionText,
                'question_type' => $dbType,
                'category'      => $category,
                'media_url'     => null
            ]);

            if (!$questionId) {
                continue;
            }

            // Options handling
            if ($dbType === 'mcq' || $dbType === 'true_false') {
                // Fallback for true/false: auto add options if missing
                if (($dbType === 'true_false') && empty($options)) {
                    $options = ['صحيح', 'خطأ'];
                }

                $correctIndices = $this->parseMcqAnswer($answer, $options);
                foreach ($options as $idx => $optText) {
                    $optStr = (string)$optText;
                    $isCorrect = in_array($idx, $correctIndices, true) ? 1 : 0;
                    QuestionOption::create([
                        'question_id' => $questionId,
                        'option_text' => $optStr,
                        'is_correct'  => $isCorrect
                    ]);
                }
            } elseif ($dbType === 'open' || $dbType === 'list') {
                if ($answer !== null && trim((string)$answer) !== '') {
                    QuestionOption::create([
                        'question_id' => $questionId,
                        'option_text' => trim((string)$answer),
                        'is_correct'  => 1
                    ]);
                }
            }

            $inserted++;
        }

        // Update count
        Project::update($projectId, [
            'total_questions' => Question::countByProject($projectId)
        ]);

        $_SESSION['success'] = "تم استيراد {$inserted} سؤالاً بنجاح";
        header('Location: ' . route('manager.questions', ['project_id' => $projectId]));
        exit;
    }

    /**
     * Map raw type string to DB enum.
     */
    private function mapType(string $raw): string
    {
        $raw = strtolower(trim($raw));
        if (in_array($raw, ['mcq', 'mcq_one', 'mcq_single'], true)) {
            return 'mcq';
        }
        if (in_array($raw, ['true_false', 'tf', 'boolean', 'bool'], true)) {
            return 'true_false';
        }
        if (in_array($raw, ['list', 'lists'], true)) {
            return 'list';
        }
        if (in_array($raw, ['open', 'open_ended', 'text'], true)) {
            return 'open';
        }
        return 'mcq';
    }

    /**
     * Parse MCQ answers similar to importer.
     */
    private function parseMcqAnswer($answer, array $options): array
    {
        $correct = [];
        if (is_int($answer) || is_float($answer)) {
            $idx = (int)$answer;
            if ($idx >= 0 && $idx < count($options)) {
                $correct[] = $idx;
            }
            return array_values(array_unique($correct));
        }
        if (is_string($answer)) {
            $ansStr = trim($answer);
            // letters A,B,C
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
            // match text
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
     * Ensure a bank project exists to hold unassigned questions.
     */
    private function getBankProjectId(): int
    {
        // Try to find by name
        $existing = Project::search('بنك الأسئلة العام');
        if (!empty($existing)) {
            return $existing[0]->id;
        }
        $projectId = Project::create([
            'name' => 'بنك الأسئلة العام',
            'summary' => 'مشروع عام لحفظ الأسئلة المرفوعة من المديرين.',
            'description' => 'حافظ للأسئلة العامة بكل أنواعها.',
            'category' => 'بنك',
            'image_url' => null,
            'created_by' => auth()->id() ?? 1
        ]);
        return (int)$projectId;
    }

    /**
     * Store question into bank tables (bank_questions/bank_question_options).
     */
    private function storeInBank(string $text, string $type, ?string $category, array $options, $answer): void
    {
        $db = DB::getConnection();

        // Insert question
        $stmt = $db->prepare("
            INSERT INTO bank_questions (question_text, question_type, category, media_url)
            VALUES (?, ?, ?, NULL)
        ");
        $stmt->execute([$text, $type, $category]);
        $bankQuestionId = (int)$db->lastInsertId();

        // Options handling for bank
        if ($type === 'mcq' || $type === 'true_false') {
            if ($type === 'true_false' && empty($options)) {
                $options = ['صحيح', 'خطأ'];
            }
            $correctIndices = $this->parseMcqAnswer($answer, $options);
            $optStmt = $db->prepare("
                INSERT INTO bank_question_options (bank_question_id, option_text, is_correct)
                VALUES (?, ?, ?)
            ");
            foreach ($options as $idx => $optText) {
                $optStmt->execute([
                    $bankQuestionId,
                    (string)$optText,
                    in_array($idx, $correctIndices, true) ? 1 : 0
                ]);
            }
        } elseif ($type === 'open' || $type === 'list') {
            if ($answer !== null && trim((string)$answer) !== '') {
                $optStmt = $db->prepare("
                    INSERT INTO bank_question_options (bank_question_id, option_text, is_correct)
                    VALUES (?, ?, 1)
                ");
                $optStmt->execute([$bankQuestionId, trim((string)$answer)]);
            }
        }
    }
}
