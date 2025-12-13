<?php
namespace App\Controllers;

use App\Models\Session;
use App\Models\Answer;
use App\Models\Project;

class DashboardController
{
    /**
     * Show user dashboard.
     */
    public function index()
    {
        $userId = auth()->id();
        
        // Get active sessions
        $activeSessions = Session::getActive($userId);
        
        // Get completed sessions
        $completedSessions = Session::getCompleted($userId);
        
        // Get statistics
        $totalAnswers = Answer::countByUser($userId);
        $totalCompleted = Session::countCompleted($userId);
        $completedStats = [];
        foreach ($completedSessions as $session) {
            $completedStats[$session->project_id] = Answer::statsByUserAndProject($userId, $session->project_id);
        }
        
        // Get all projects for browsing
        $allProjects = Project::getAll();
        
        view('user/dashboard', [
            'activeSessions' => $activeSessions,
            'completedSessions' => $completedSessions,
            'totalAnswers' => $totalAnswers,
            'totalCompleted' => $totalCompleted,
            'allProjects' => $allProjects,
            'completedStats' => $completedStats
        ]);
    }
}
