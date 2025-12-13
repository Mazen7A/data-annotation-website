<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Answer;
use App\Models\Session;
use App\Models\ContactMessage;
use App\Models\Review;

class ManagerDashboardController
{
    /**
     * Show manager dashboard.
     */
    public function index()
    {
        // Get statistics
        $totalProjects = Project::count();
        $totalUsers = User::countByRole('user');
        $totalAnswers = Answer::count();
        $totalReviews = Review::count();
        $pendingMessages = ContactMessage::countByStatus('pending');
        
        // Get recent activity
        $recentProjects = array_slice(Project::getAll(), 0, 5);
        $unreviewedAnswers = Answer::getUnreviewed(10);
        $pendingMessagesData = ContactMessage::getByStatus('pending');
        
        // Get Graph Data
        $projectCategories = Project::getCategoryStats();
        $commitStats = \App\Models\ProjectCommit::getDailyStats();
        
        view('manager/dashboard', [
            'totalProjects' => $totalProjects,
            'totalUsers' => $totalUsers,
            'totalAnswers' => $totalAnswers,
            'totalReviews' => $totalReviews,
            'pendingMessages' => $pendingMessages,
            'recentProjects' => $recentProjects,
            'unreviewedAnswers' => $unreviewedAnswers,
            'pendingMessagesData' => $pendingMessagesData,
            'projectCategories' => $projectCategories,
            'commitStats' => $commitStats
        ]);
    }
}
