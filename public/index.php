<?php
// Start the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Custom Autoloader for the App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/'; // app folder is one level up from public

    // Only process classes in the App namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Convert namespace to full file path
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Include global helper functions
require_once __DIR__ . '/../app/helpers/helpers.php';

// Include the web routes file so routes are available globally
require_once __DIR__ . '/../app/routes/web.php';
