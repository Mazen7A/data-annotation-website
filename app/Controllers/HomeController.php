<?php
namespace App\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Answer;
use App\Models\Session;

class HomeController extends Controller
{
    public function index()
    {
        // Fetch statistics
        $stats = [
            'projects' => Project::count(),
            'users' => User::countByRole('user'),
            'answers' => Answer::count(),
            'sessions' => Session::count()
        ];

        // Fetch latest projects (limit 3)
        // Note: We'll need to add a limit to the getAll method or create a new one
        // For now, we'll fetch all and slice in PHP, but ideally we should add a method to Project model
        $allProjects = Project::getAll();
        $latestProjects = array_slice($allProjects, 0, 3);

        // Fetch top contributors
        $topContributors = User::getTopContributors(4);
        
        view('home', [
            'stats' => $stats,
            'latestProjects' => $latestProjects,
            'topContributors' => $topContributors,
            'title' =>'منصة إثراء الثقافة السعودية'
        ]);
    }
}
