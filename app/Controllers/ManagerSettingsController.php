<?php
namespace App\Controllers;

use App\Models\Setting;

class ManagerSettingsController
{
    /**
     * Show settings dashboard.
     */
    public function index()
    {
        $settings = Setting::getAll();
        $themeSettings = Setting::getThemeSettings();
        
        view('manager/settings/index', [
            'settings' => $settings,
            'themeSettings' => $themeSettings
        ]);
    }

    /**
     * Update theme settings.
     */
    public function updateTheme()
    {
        $defaultTheme = $_POST['default_theme'] ?? 'purple';
        $defaultMode = $_POST['default_mode'] ?? 'auto';
        $enableAnimations = isset($_POST['enable_animations']);
        $primaryColor = $_POST['primary_color'] ?? '#667eea';
        $secondaryColor = $_POST['secondary_color'] ?? '#764ba2';
        
        Setting::set('default_theme', $defaultTheme, 'text');
        Setting::set('default_mode', $defaultMode, 'text');
        Setting::set('enable_animations', $enableAnimations, 'boolean');
        Setting::set('primary_color', $primaryColor, 'color');
        Setting::set('secondary_color', $secondaryColor, 'color');
        
        $_SESSION['success'] = 'تم تحديث إعدادات المظهر بنجاح';
        header('Location: ' . route('manager.settings'));
        exit;
    }

    /**
     * Update available themes.
     */
    public function updateAvailableThemes()
    {
        $themes = $_POST['themes'] ?? [];
        
        Setting::set('available_themes', $themes, 'json');
        
        $_SESSION['success'] = 'تم تحديث المظاهر المتاحة بنجاح';
        header('Location: ' . route('manager.settings'));
        exit;
    }
}
