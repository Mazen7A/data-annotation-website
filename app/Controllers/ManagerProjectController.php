<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\ProjectCommit;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\BankQuestion;
use App\Models\BankQuestionOption;

class ManagerProjectController
{
    /**
     * List all projects.
     */
    public function index()
    {
        $projects = Project::getAll();
        view('manager/projects/index', ['projects' => $projects]);
    }

    /**
     * Show project details with stats.
     */
    public function show()
    {
        $projectId = $_GET['id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        // Get project stats
        $stats = [
            'total_questions' => $project->total_questions,
            'total_answers' => \App\Models\Answer::countByProject($projectId),
            'total_participants' => \App\Models\Session::countByProject($projectId),
            'completion_rate' => 0, // Calculate if needed
            'category_stats' => \App\Models\Question::countByCategory($projectId),
            'type_stats' => \App\Models\Question::countByType($projectId)
        ];

        // Get recent activity
        $commits = ProjectCommit::getByProject($projectId, 10);

        view('manager/projects/show', [
            'project' => $project,
            'stats' => $stats,
            'commits' => $commits
        ]);
    }

    /**
     * Show create project form.
     */
    public function create()
    {
        // Bank filters
        $bankType = $_GET['bank_type'] ?? null;
        $bankCategory = $_GET['bank_category'] ?? null;
        $bankSearch = $_GET['bank_q'] ?? null;
        $bankQuestions = BankQuestion::filter($bankType, $bankCategory, $bankSearch);

        view('manager/projects/create', [
            'bankQuestions' => $bankQuestions,
            'bankType' => $bankType,
            'bankCategory' => $bankCategory,
            'bankSearch' => $bankSearch
        ]);
    }

    /**
     * Store new project.
     */
    public function store()
    {
        $name = $_POST['name'] ?? '';
        $summary = $_POST['summary'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
        $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? $_POST['longitude'] : null;
        $locationName = isset($_POST['location_name']) && $_POST['location_name'] !== '' ? $_POST['location_name'] : null;

        if (empty($name)) {
            $_SESSION['error'] = 'اسم المشروع مطلوب';
            header('Location: ' . route('manager.projects.create'));
            exit;
        }

        // Handle image upload
        $imageUrl = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageUrl = $this->uploadImage($_FILES['image']);
        }

        $projectId = Project::create([
            'name' => $name,
            'summary' => $summary,
            'description' => $description,
            'category' => $category,
            'image_url' => $imageUrl,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName,
            'created_by' => auth()->id()
        ]);

        if ($projectId) {
            // Attach selected bank questions if any
            $selectedBank = $_POST['bank_question_ids'] ?? [];
            if (!empty($selectedBank)) {
                $this->copyBankQuestionsToProject($projectId, $selectedBank);
                Project::update($projectId, [
                    'total_questions' => Question::countByProject($projectId)
                ]);
            }
            // Log commit
            ProjectCommit::create($projectId, auth()->id(), "إنشاء المشروع: $name");
            
            $_SESSION['success'] = 'تم إنشاء المشروع بنجاح';
            header('Location: ' . route('manager.projects'));
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إنشاء المشروع';
            header('Location: ' . route('manager.projects.create'));
        }
        exit;
    }

    /**
     * Show edit project form.
     */
    public function edit()
    {
        $projectId = $_GET['id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        // Bank filters
        $bankType = $_GET['bank_type'] ?? null;
        $bankCategory = $_GET['bank_category'] ?? null;
        $bankSearch = $_GET['bank_q'] ?? null;
        $bankQuestions = BankQuestion::filter($bankType, $bankCategory, $bankSearch);

        view('manager/projects/edit', [
            'project' => $project,
            'bankQuestions' => $bankQuestions,
            'bankType' => $bankType,
            'bankCategory' => $bankCategory,
            'bankSearch' => $bankSearch
        ]);
    }

    /**
     * Update project.
     */
    public function update()
    {
        $projectId = $_POST['id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $name = $_POST['name'] ?? '';
        $summary = $_POST['summary'] ?? '';
        $description = $_POST['description'] ?? '';
        $category = $_POST['category'] ?? '';
        $latitude = isset($_POST['latitude']) && $_POST['latitude'] !== '' ? $_POST['latitude'] : null;
        $longitude = isset($_POST['longitude']) && $_POST['longitude'] !== '' ? $_POST['longitude'] : null;
        $locationName = isset($_POST['location_name']) && $_POST['location_name'] !== '' ? $_POST['location_name'] : null;

        if (empty($name)) {
            $_SESSION['error'] = 'اسم المشروع مطلوب';
            header('Location: ' . route('manager.projects.edit', ['id' => $projectId]));
            exit;
        }

        $data = [
            'name' => $name,
            'summary' => $summary,
            'description' => $description,
            'category' => $category,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'location_name' => $locationName
        ];

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $data['image_url'] = $this->uploadImage($_FILES['image']);
        }

        $result = Project::update($projectId, $data);

        if ($result) {
            // Attach bank questions if provided
            $selectedBank = $_POST['bank_question_ids'] ?? [];
            if (!empty($selectedBank)) {
                $this->copyBankQuestionsToProject($projectId, $selectedBank);
                Project::update($projectId, [
                    'total_questions' => Question::countByProject($projectId)
                ]);
            }
            // Log commit
            ProjectCommit::create($projectId, auth()->id(), "تحديث المشروع: $name");
            
            $_SESSION['success'] = 'تم تحديث المشروع بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء التحديث';
        }

        header('Location: ' . route('manager.projects'));
        exit;
    }

    /**
     * Delete project.
     */
    public function delete()
    {
        $projectId = $_POST['id'] ?? 0;
        $result = Project::delete($projectId);

        if ($result) {
            $_SESSION['success'] = 'تم حذف المشروع بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء الحذف';
        }

        header('Location: ' . route('manager.projects'));
        exit;
    }

    /**
     * Show project commits/history.
     */
    public function commits()
    {
        $projectId = $_GET['id'] ?? 0;
        $project = Project::getById($projectId);

        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $commits = ProjectCommit::getByProject($projectId);

        view('manager/projects/commits', [
            'project' => $project,
            'commits' => $commits
        ]);
    }

    /**
     * Import projects/questions from database/data.php (manager trigger).
     */
    public function importData()
    {
        $scriptPath = __DIR__ . '/../../database/data.php';

        if (!file_exists($scriptPath)) {
            $_SESSION['error'] = 'ملف الاستيراد غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        // Buffer output to avoid dumping script logs to the UI.
        ob_start();
        include $scriptPath;
        ob_end_clean();

        $_SESSION['success'] = 'تم تشغيل استيراد المشاريع والأسئلة من ملف البيانات';
        header('Location: ' . route('manager.projects'));
        exit;
    }

    /**
     * Attach selected bank questions to a project.
     */
    public function attachBankQuestions()
    {
        $projectId = $_POST['project_id'] ?? 0;
        $selected = $_POST['bank_question_ids'] ?? [];

        $project = Project::getById($projectId);
        if (!$project) {
            $_SESSION['error'] = 'المشروع غير موجود';
            header('Location: ' . route('manager.projects'));
            exit;
        }

        $added = 0;
        $added = $this->copyBankQuestionsToProject($projectId, $selected);

        if ($added > 0) {
            Project::update($projectId, [
                'total_questions' => Question::countByProject($projectId)
            ]);
            $_SESSION['success'] = "تم إضافة {$added} سؤالاً من البنك إلى المشروع";
        } else {
            $_SESSION['error'] = 'لم يتم اختيار أي أسئلة من البنك';
        }

        header('Location: ' . route('manager.projects.edit', ['id' => $projectId]));
        exit;
    }

    /**
     * Upload project image.
     */
    private function uploadImage($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/projects/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'project_' . time() . '_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return 'uploads/projects/' . $filename;
        }

        return null;
    }

    /**
     * Copy selected bank questions into a project.
     */
    private function copyBankQuestionsToProject(int $projectId, array $selected): int
    {
        $added = 0;
        $unique = array_unique(array_filter($selected));
        foreach ($unique as $bankId) {
            $bankQ = BankQuestion::getById($bankId);
            if (!$bankQ) {
                continue;
            }

            $newQuestionId = Question::create([
                'project_id'    => $projectId,
                'question_text' => $bankQ->question_text,
                'question_type' => $bankQ->question_type,
                'category'      => $bankQ->category,
                'media_url'     => $bankQ->media_url
            ]);

            if (!$newQuestionId) {
                continue;
            }

            $bankOptions = BankQuestionOption::getByQuestion($bankId);
            if (!empty($bankOptions)) {
                foreach ($bankOptions as $opt) {
                    QuestionOption::create([
                        'question_id' => $newQuestionId,
                        'option_text' => $opt->option_text,
                        'is_correct'  => $opt->is_correct
                    ]);
                }
            }

            $added++;
        }
        return $added;
    }
}
