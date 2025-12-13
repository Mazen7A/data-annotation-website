<?php
namespace App\Controllers;

use App\Models\Session;
use App\Models\Question;
use App\Models\Answer;

class QuestionController
{
    /**
     * Show questions for a session.
     */
    public function index()
    {
        $sessionId = $_GET['session_id'] ?? 0;
        $session = Session::getById($sessionId);
        
        if (!$session || $session->user_id != auth()->id()) {
            $_SESSION['error'] = 'الجلسة غير موجودة';
            header('Location: ' . route('dashboard'));
            exit;
        }
        
        // Get next unanswered question
        $question = Question::getNextUnanswered($session->project_id, auth()->id());
        
        if (!$question) {
            // All questions answered
            Session::complete($sessionId);
            $_SESSION['success'] = 'تهانينا! لقد أكملت جميع الأسئلة';

            // Grade summary
            $answersWithStatus = \App\Models\Answer::getByUserAndProjectWithStatus(auth()->id(), $session->project_id);
            $gradeTotal = count($answersWithStatus);
            $gradeCorrect = 0;
            foreach ($answersWithStatus as $ans) {
                if ($ans->status['type'] === 'correct') {
                    $gradeCorrect++;
                }
            }
            $gradePercent = $gradeTotal > 0 ? round(($gradeCorrect / $gradeTotal) * 100) : 0;
            $_SESSION['grade_summary'] = [
                'project_id' => $session->project_id,
                'total' => $gradeTotal,
                'correct' => $gradeCorrect,
                'percent' => $gradePercent,
                'answers' => $answersWithStatus
            ];

            header('Location: ' . route('dashboard'));
            exit;
        }
        
        // Get total questions and answered count
        $totalQuestions = Question::countByProject($session->project_id);
        $answeredCount = Answer::countByUser(auth()->id());
        
        view('user/questions/index', [
            'session' => $session,
            'question' => $question,
            'totalQuestions' => $totalQuestions,
            'progress' => $session->progress
        ]);
    }

    /**
     * Submit an answer.
     */
    public function submit()
    {
        $sessionId = $_POST['session_id'] ?? 0;
        $questionId = $_POST['question_id'] ?? 0;
        $userId = auth()->id();
        
        $session = Session::getById($sessionId);
        if (!$session || $session->user_id != $userId) {
            $_SESSION['error'] = 'الجلسة غير موجودة';
            header('Location: ' . route('dashboard'));
            exit;
        }
        
        $question = Question::getById($questionId);
        if (!$question || $question->project_id != $session->project_id) {
            $_SESSION['error'] = 'السؤال غير موجود';
            header('Location: ' . route('questions', ['session_id' => $sessionId]));
            exit;
        }
        
        // Check if already answered
        if (Answer::hasAnswered($userId, $questionId)) {
            $_SESSION['error'] = 'لقد أجبت على هذا السؤال مسبقاً';
            header('Location: ' . route('questions', ['session_id' => $sessionId]));
            exit;
        }
        
        // Prepare answer data based on question type
        $answerData = [
            'user_id' => $userId,
            'question_id' => $questionId
        ];
        
        switch ($question->question_type) {
            case 'mcq':
            case 'true_false':
                $selectedOptions = $_POST['selected_options'] ?? [];
                if (empty($selectedOptions)) {
                    $_SESSION['error'] = 'يرجى اختيار إجابة';
                    header('Location: ' . route('questions', ['session_id' => $sessionId]));
                    exit;
                }
                $answerData['selected_options'] = $selectedOptions;
                break;
                
            case 'open':
                $answerText = $_POST['answer_text'] ?? '';
                if (empty(trim($answerText))) {
                    $_SESSION['error'] = 'يرجى كتابة إجابة';
                    header('Location: ' . route('questions', ['session_id' => $sessionId]));
                    exit;
                }
                $answerData['answer_text'] = $answerText;
                break;
                
            case 'list':
                $answerText = $_POST['answer_text'] ?? '';
                if (empty(trim($answerText))) {
                    $_SESSION['error'] = 'يرجى كتابة الإجابات';
                    header('Location: ' . route('questions', ['session_id' => $sessionId]));
                    exit;
                }
                $answerData['answer_text'] = $answerText;
                break;
        }
        
        // Save answer
        $answerId = Answer::create($answerData);
        
        if ($answerId) {
            // Update session progress
            Session::calculateProgress($sessionId);
            
            $_SESSION['success'] = 'تم حفظ الإجابة بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء حفظ الإجابة';
        }
        
        header('Location: ' . route('questions', ['session_id' => $sessionId]));
        exit;
    }
}
