<?php
namespace App\Controllers;

use App\Models\Answer;
use App\Models\Review;
use App\Models\Project;

class ManagerReviewController
{
    /**
     * List answers pending review.
     */
    public function index()
    {
        $projectId = $_GET['project_id'] ?? null;
        
        if ($projectId) {
            $answers = Answer::getByProject($projectId);
            $project = Project::getById($projectId);
        } else {
            $answers = Answer::getUnreviewed(100);
            $project = null;
        }

        view('manager/reviews/index', [
            'answers' => $answers,
            'project' => $project
        ]);
    }

    /**
     * Show answer for review.
     */
    public function show()
    {
        $answerId = $_GET['id'] ?? 0;
        $answer = Answer::getById($answerId);

        if (!$answer) {
            $_SESSION['error'] = 'الإجابة غير موجودة';
            header('Location: ' . route('manager.reviews'));
            exit;
        }

        // Get existing review if any
        $review = Review::getByAnswer($answerId);

        view('manager/reviews/show', [
            'answer' => $answer,
            'review' => $review
        ]);
    }

    /**
     * Submit or update review.
     */
    public function review()
    {
        $answerId = $_POST['answer_id'] ?? 0;
        $status = $_POST['status'] ?? 'approved';
        $notes = $_POST['notes'] ?? '';

        $answer = Answer::getById($answerId);
        if (!$answer) {
            $_SESSION['error'] = 'الإجابة غير موجودة';
            header('Location: ' . route('manager.reviews'));
            exit;
        }

        // Convert status to score (approved = 10, rejected = 0)
        $score = $status === 'approved' ? 10 : 0;

        // Check if review already exists
        $existingReview = Review::getByAnswer($answerId);

        if ($existingReview) {
            // Update existing review
            $result = Review::update($existingReview->id, [
                'score' => $score,
                'comment' => $notes
            ]);
        } else {
            // Create new review
            $result = Review::create([
                'answer_id' => $answerId,
                'reviewer_id' => auth()->id(),
                'score' => $score,
                'comment' => $notes
            ]);
        }

        if ($result) {
            $_SESSION['success'] = 'تم حفظ التقييم بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء حفظ التقييم';
        }

        header('Location: ' . route('manager.reviews'));
        exit;
    }
}
