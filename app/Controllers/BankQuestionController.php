<?php
namespace App\Controllers;

use App\Models\BankQuestion;
use App\Models\BankQuestionOption;

class BankQuestionController
{
    public function index()
    {
        $type = $_GET['type'] ?? null;
        $category = $_GET['category'] ?? null;
        $search = $_GET['q'] ?? null;
        $questions = BankQuestion::filter($type, $category, $search);
        $counts = BankQuestion::countByType();
        view('manager/questions/bank/index', [
            'questions' => $questions,
            'filterType' => $type,
            'filterCategory' => $category,
            'searchTerm' => $search,
            'counts' => $counts
        ]);
    }

    public function store()
    {
        $questionText = trim($_POST['question_text'] ?? '');
        $type = $_POST['question_type'] ?? '';
        $category = $_POST['category'] ?? null;
        $options = $_POST['options'] ?? [];
        $correctOptions = $_POST['correct_options'] ?? [];
        $answerText = $_POST['answer_text'] ?? '';

        if ($questionText === '' || $type === '') {
            $_SESSION['error'] = 'نص السؤال والنوع مطلوبان';
            header('Location: ' . route('manager.bank.questions'));
            exit;
        }

        // Normalize type
        $type = $this->mapType($type);

        $bankId = BankQuestion::create([
            'question_text' => $questionText,
            'question_type' => $type,
            'category' => $category,
            'media_url' => null
        ]);

        if (!$bankId) {
            $_SESSION['error'] = 'تعذر إنشاء السؤال';
            header('Location: ' . route('manager.bank.questions'));
            exit;
        }

        if (in_array($type, ['mcq', 'true_false'])) {
            if ($type === 'true_false' && empty($options)) {
                $options = ['صحيح', 'خطأ'];
            }
            foreach ($options as $idx => $optText) {
                if (trim($optText) === '') {
                    continue;
                }
                BankQuestionOption::create([
                    'bank_question_id' => $bankId,
                    'option_text' => $optText,
                    'is_correct' => in_array($idx, $correctOptions) ? 1 : 0
                ]);
            }
        } elseif (in_array($type, ['open', 'list'])) {
            if (trim($answerText) !== '') {
                BankQuestionOption::create([
                    'bank_question_id' => $bankId,
                    'option_text' => trim($answerText),
                    'is_correct' => 1
                ]);
            }
        }

        $_SESSION['success'] = 'تمت إضافة السؤال إلى بنك الأسئلة';
        header('Location: ' . route('manager.bank.questions'));
        exit;
    }

    public function delete()
    {
        $id = $_POST['id'] ?? 0;
        BankQuestion::delete($id);
        $_SESSION['success'] = 'تم حذف السؤال من البنك';
        header('Location: ' . route('manager.bank.questions'));
        exit;
    }

    private function mapType(string $raw): string
    {
        $raw = strtolower(trim($raw));
        if (in_array($raw, ['mcq', 'mcq_one', 'mcq_single'], true)) return 'mcq';
        if (in_array($raw, ['true_false', 'tf', 'boolean'], true)) return 'true_false';
        if (in_array($raw, ['list', 'lists'], true)) return 'list';
        if (in_array($raw, ['open', 'open_ended', 'text'], true)) return 'open';
        return 'mcq';
    }
}
