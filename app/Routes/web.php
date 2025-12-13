<?php

// Middleware helper function
function middleware($middleware, $callback)
{
    return function () use ($middleware, $callback) {
        // 'auth' middleware: Check if the user is logged in
        if ($middleware === 'auth') {
            if (!auth()->isUser()) {
                header("Location: " . route('login'));
                exit;
            }
        }
        // 'manager' middleware: Check if the user is a manager
        if ($middleware === 'manager') {
            if (!auth()->isManager()) {
                header("Location: " . route('home'));
                exit;
            }
        }
        // If all checks pass, call the route callback
        $callback();
    };
}

// Import controllers
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProjectController;
use App\Controllers\QuestionController;
use App\Controllers\ProfileController;
use App\Controllers\ContactController;
use App\Controllers\PasswordResetController;
use App\Controllers\ManagerDashboardController;
use App\Controllers\ManagerProjectController;
use App\Controllers\ManagerQuestionController;
use App\Controllers\ManagerReviewController;
use App\Controllers\ManagerContactController;
use App\Controllers\ManagerUserController;
use App\Controllers\ManagerSettingsController;

// Routes definition
$routes = [
    // ========================================
    // Public Routes
    // ========================================
    'home' => function () {
        $controller = new \App\Controllers\HomeController();
        $controller->index();
    },
    
    'about' => function () {
        view('about');
    },
    
    'contact' => function () {
        $controller = new ContactController();
        $controller->index();
    },
    
    'contact.submit' => function () {
        $controller = new ContactController();
        $controller->submit();
    },

    // ========================================
    // Authentication Routes
    // ========================================
    'login' => function () {
        if (auth()->check()) {
            header("Location: " . route('dashboard'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->login();
        } else {
            view('auth/login');
        }
    },

    'register' => function () {
        if (auth()->check()) {
            header("Location: " . route('dashboard'));
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new AuthController();
            $controller->register();
        } else {
            view('auth/register');
        }
    },

    'logout' => function () {
        $controller = new AuthController();
        $controller->logout();
    },
    
    // Password Reset Routes
    'password.forgot' => function () {
        $controller = new PasswordResetController();
        $controller->showForgotForm();
    },
    
    'password.send-code' => function () {
        $controller = new PasswordResetController();
        $controller->sendResetCode();
    },
    
    'password.reset' => function () {
        $controller = new PasswordResetController();
        $controller->showResetForm();
    },
    
    'password.update' => function () {
        $controller = new PasswordResetController();
        $controller->resetPassword();
    },

    // ========================================
    // User Routes (require authentication)
    // ========================================
    'dashboard' => middleware('auth', function () {
        $controller = new DashboardController();
        $controller->index();
    }),

    'projects' => middleware('auth', function () {
        $controller = new ProjectController();
        $controller->index();
    }),

    'projects.show' => middleware('auth', function () {
        $controller = new ProjectController();
        $controller->show();
    }),

    'projects.start' => middleware('auth', function () {
        $controller = new ProjectController();
        $controller->start();
    }),

    'projects.comment' => middleware('auth', function () {
        $controller = new ProjectController();
        $controller->addComment();
    }),

    'projects.comment.delete' => middleware('auth', function () {
        $controller = new ProjectController();
        $controller->deleteComment();
    }),

    'questions' => middleware('auth', function () {
        $controller = new QuestionController();
        $controller->index();
    }),

    'questions.submit' => middleware('auth', function () {
        $controller = new QuestionController();
        $controller->submit();
    }),

    'profile' => middleware('auth', function () {
        $controller = new ProfileController();
        $controller->show();
    }),

    'profile.edit' => middleware('auth', function () {
        $controller = new ProfileController();
        $controller->edit();
    }),

    'profile.update' => middleware('auth', function () {
        $controller = new ProfileController();
        $controller->update();
    }),

    'profile.password' => middleware('auth', function () {
        $controller = new ProfileController();
        $controller->updatePassword();
    }),

    // ========================================
    // Manager Routes (require manager role)
    // ========================================
    'manager.dashboard' => middleware('manager', function () {
        $controller = new ManagerDashboardController();
        $controller->index();
    }),

    // Manager Projects
    'manager.projects' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->index();
    }),

    'manager.projects.create' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->create();
    }),
    'manager.projects.show' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->show();
    }),

    'manager.projects.store' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->store();
    }),

    'manager.projects.edit' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->edit();
    }),

    'manager.projects.update' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->update();
    }),

    'manager.projects.attach-bank' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->attachBankQuestions();
    }),

    'manager.projects.delete' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->delete();
    }),

    'manager.projects.commits' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->commits();
    }),

    // Manager Projects Import (from database/data.php)
    'manager.projects.import-data' => middleware('manager', function () {
        $controller = new ManagerProjectController();
        $controller->importData();
    }),

    // Manager Questions
    'manager.questions' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->index();
    }),

    'manager.questions.create' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->create();
    }),

    'manager.questions.store' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->store();
    }),

    // Import questions in bulk
    'manager.questions.import' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->importForm();
    }),

    'manager.questions.import.store' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->importStore();
    }),

    'manager.questions.edit' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->edit();
    }),

    'manager.questions.update' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->update();
    }),

    'manager.questions.delete' => middleware('manager', function () {
        $controller = new ManagerQuestionController();
        $controller->delete();
    }),

    // Manager Reviews
    'manager.reviews' => middleware('manager', function () {
        $controller = new ManagerReviewController();
        $controller->index();
    }),

    'manager.reviews.show' => middleware('manager', function () {
        $controller = new ManagerReviewController();
        $controller->show();
    }),

    'manager.reviews.review' => middleware('manager', function () {
        $controller = new ManagerReviewController();
        $controller->review();
    }),

    // Manager Users
    'manager.users' => middleware('manager', function () {
        $controller = new ManagerUserController();
        $controller->index();
    }),

    'manager.users.show' => middleware('manager', function () {
        $controller = new ManagerUserController();
        $controller->show();
    }),
    
    'manager.users.update-role' => middleware('manager', function () {
        $controller = new ManagerUserController();
        $controller->updateRole();
    }),

    'manager.users.delete' => middleware('manager', function () {
        $controller = new ManagerUserController();
        $controller->delete();
    }),

    // Manager Contact Messages
    'manager.messages' => middleware('manager', function () {
        $controller = new ManagerContactController();
        $controller->index();
    }),

    'manager.messages.show' => middleware('manager', function () {
        $controller = new ManagerContactController();
        $controller->show();
    }),

    'manager.messages.update_status' => middleware('manager', function () {
        $controller = new ManagerContactController();
        $controller->updateStatus();
    }),

    'manager.messages.reply' => middleware('manager', function () {
        $controller = new ManagerContactController();
        $controller->reply();
    }),

    'manager.messages.delete' => middleware('manager', function () {
        $controller = new ManagerContactController();
        $controller->delete();
    }),

    // Bank questions management
    'manager.bank.questions' => middleware('manager', function () {
        $controller = new \App\Controllers\BankQuestionController();
        $controller->index();
    }),
    'manager.bank.questions.store' => middleware('manager', function () {
        $controller = new \App\Controllers\BankQuestionController();
        $controller->store();
    }),
    'manager.bank.questions.delete' => middleware('manager', function () {
        $controller = new \App\Controllers\BankQuestionController();
        $controller->delete();
    }),

    // Manager Settings
    'manager.settings' => middleware('manager', function () {
        $controller = new ManagerSettingsController();
        $controller->index();
    }),

    'manager.settings.update_theme' => middleware('manager', function () {
        $controller = new ManagerSettingsController();
        $controller->updateTheme();
    }),
];

// Route dispatcher
$route = isset($_GET['route']) ? $_GET['route'] : 'home';
if (isset($routes[$route]) && is_callable($routes[$route])) {
    $routes[$route]();
} else {
    echo "المسار '{$route}' غير موجود.";
}
