<?php

if (!function_exists('auth')) {
    /**
     * Returns the full Auth instance so its methods can be used globally.
     *
     * @return \App\Auth\Auth
     */
    function auth() {
        return \App\Auth\Auth::instance();
    }
}

if (!function_exists('route')) {
    /**
     * Generate a URL based on a route name.
     * All routes will be funneled through the base path (e.g., "/riday_al_khadra/public/index.php")
     * with a "route" query parameter.
     *
     * @param string $name   The route name (e.g., "dashboard", "profile")
     * @param array  $params Optional parameters to be appended as a query string.
     * @return string
     */
    function route($name, $params = []) {
        // Define the base URL for your site (adjust this if your public folder name changes)
        $base = '/Saudi-culture/public';
        $url = $base . '/index.php?route=' . urlencode($name);
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        return $url;
    }
}


if (!function_exists('asset')) {
    /**
     * Returns the URL for an asset located in the public/assets folder.
     *
     * @param string $path The relative path to the asset (e.g., "css/style.css")
     * @return string
     */
    function asset($path) {
        return './assets/' . ltrim($path, '/');
    }
}

if (!function_exists('view')) {
    /**
     * Loads a view file from the app/views folder and passes data to it.
     *
     * @param string $name The view file name (without the .php extension)
     * @param array  $data Associative array of data to be extracted into variables.
     * @return void
     */
    function view($name, $data = []) {
        // Assuming this file is located in app/helpers, then the views folder is at:
        $viewFile = dirname(__DIR__) . '/views/' . $name . '.php';
        if (file_exists($viewFile)) {
            extract($data);
            
            // Start output buffering to capture view content
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            
            // If layout exists, use it
            $layoutFile = dirname(__DIR__) . '/views/layouts/app.php';
            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo "View '{$name}' not found.";
        }
    }
}

if (!function_exists('viewWithoutLayout')) {
    /**
     * Loads a view file without layout.
     *
     * @param string $name The view file name (without the .php extension)
     * @param array  $data Associative array of data to be extracted into variables.
     * @return void
     */
    function viewWithoutLayout($name, $data = []) {
        $viewFile = dirname(__DIR__) . '/views/' . $name . '.php';
        if (file_exists($viewFile)) {
            extract($data);
            include $viewFile;
        } else {
            echo "View '{$name}' not found.";
        }
    }
}
