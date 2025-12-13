# 🎨 Modern Design System - Setup & Usage Guide

## Quick Setup

### 1. Update Database

Run the schema update to add the settings table and theme preferences:

```bash
mysql -u root -p saudi_culture < database/schema_update.sql
```

Or manually in phpMyAdmin:
1. Open `database/schema_update.sql`
2. Copy and execute the SQL commands

### 2. Install & Build CSS

Install dependencies and build the modern design system:

```bash
npm install
npm run build:css
```

For development with auto-reload:
```bash
npm run watch:css
```

### 3. Verify Files

Ensure these new files exist:
- ✅ `public/assets/css/styles.css` (Generated)
- ✅ `public/assets/js/theme.js`
- ✅ `app/Models/Setting.php`
- ✅ `app/Controllers/ManagerSettingsController.php`
- ✅ `app/Views/manager/settings/index.php`

### 3. Access the Platform

Navigate to: `http://localhost/Saudi-culture/public/`

---

## Features Overview

### 🌓 Dark/Light Mode
- **Toggle Button**: Click the moon/sun icon in the navigation bar
- **Auto Mode**: Automatically follows system preference
- **Persistent**: Choice is saved in browser and user profile

### 🎨 6 Color Schemes
1. **Purple** (Default) - Professional gradient
2. **Blue** - Ocean blue tones
3. **Green** - Natural forest palette
4. **Orange** - Warm sunset colors
5. **Gold** - Elegant royal theme
6. **Night** - Deep indigo/purple

### ✨ Smooth Animations
- Fade in on page load
- Slide animations for navigation
- Scroll-triggered animations
- Hover effects on cards
- Smooth color transitions

### ⚙️ Admin Control
Managers can customize:
- Default color scheme
- Default mode (light/dark/auto)
- Primary and secondary colors
- Enable/disable animations

---

## How to Use

### For Users

**Change Theme Mode:**
1. Click the theme toggle button (moon/sun icon) in navigation
2. Your preference is automatically saved

**Experience Features:**
- Scroll down pages to see fade-in animations
- Hover over cards for smooth effects
- Enjoy responsive design on any device

### For Managers

**Access Settings:**
1. Login as manager
2. Click "الإعدادات" in navigation
3. Or go to: `?route=manager.settings`

**Customize Theme:**
1. Select default color scheme
2. Choose default mode (light/dark/auto)
3. Pick custom primary/secondary colors
4. Toggle animations on/off
5. Click "حفظ الإعدادات"

**Preview Themes:**
- Click color swatches in the preview panel
- See changes instantly
- Test different combinations

---

## Design System Components

### CSS Variables
All colors use CSS custom properties for easy theming:
```css
--theme-primary
--theme-secondary
--bg-primary
--bg-secondary
--text-primary
--text-secondary
```

### Animation Classes
```html
<!-- Fade in -->
<div class="animate-fade-in">Content</div>

<!-- Slide up -->
<div class="animate-slide-up">Content</div>

<!-- Scroll animations -->
<div class="scroll-fade-in">Content</div>

<!-- Delays -->
<div class="animate-fade-in delay-200">Content</div>
```

### Modern Components
```html
<!-- Gradient button -->
<button class="btn btn-primary">Click Me</button>

<!-- Card with hover -->
<div class="card-hover">Card content</div>

<!-- Glassmorphism -->
<div class="glass">Glass effect</div>

<!-- Gradient text -->
<h1 class="gradient-text">Gradient Text</h1>
```

---

## Browser Support

✅ Chrome/Edge (Latest)
✅ Firefox (Latest)
✅ Safari (Latest)
✅ Mobile browsers

**Features:**
- CSS Custom Properties
- CSS Grid & Flexbox
- Modern JavaScript (ES6+)
- Intersection Observer API

---

## Customization Guide

### Add New Color Scheme

1. **Edit `design-system.css`:**
```css
[data-color-scheme="custom"] {
  --theme-primary: #yourcolor;
  --theme-secondary: #yourcolor;
  --theme-accent: #yourcolor;
}
```

2. **Update Settings:**
Add to available_themes in database

3. **Add to Manager Settings:**
Update the select dropdown in `manager/settings/index.php`

### Modify Animations

Edit `design-system.css` keyframes:
```css
@keyframes yourAnimation {
  from { /* start state */ }
  to { /* end state */ }
}
```

### Change Default Colors

Update in `schema_update.sql`:
```sql
('primary_color', '#yourcolor', 'color'),
('secondary_color', '#yourcolor', 'color')
```

---

## Performance Tips

1. **Animations**: Disable on low-end devices
2. **Images**: Optimize before upload
3. **Caching**: Browser caches CSS/JS automatically
4. **Loading**: Use skeleton loaders for better UX

---

## Troubleshooting

### Theme Not Changing
- Clear browser cache
- Check JavaScript console for errors
- Verify `theme.js` is loaded

### Colors Not Updating
- Run database schema update
- Check CSS file is loaded
- Inspect CSS variables in DevTools

### Animations Not Working
- Check "enable_animations" setting
- Verify CSS file path
- Test in different browser

### Dark Mode Issues
- Check `data-theme` attribute on `<html>`
- Verify CSS variables are defined
- Test system preference detection

---

## API Reference

### JavaScript Functions

```javascript
// Change theme
ThemeManager.setTheme('dark');
ThemeManager.toggleTheme();

// Change color scheme
ThemeManager.setColorScheme('blue');

// Show toast notification
showToast('Success message', 'success');
showToast('Error message', 'error');

// Loading overlay
showLoading();
hideLoading();
```

### PHP Functions

```php
// Get setting
Setting::get('default_theme', 'purple');

// Set setting
Setting::set('default_theme', 'blue', 'text');

// Get all theme settings
Setting::getThemeSettings();
```

---

## What's New

### Enhanced Layout
- Modern navigation with icons
- Mobile-responsive menu
- Theme toggle button
- Smooth transitions
- Better footer design

### Home Page
- Animated hero section
- Live statistics
- Feature cards
- How it works section
- Additional features grid
- Call-to-action sections

### Manager Settings
- Theme customization panel
- Live preview
- Color pickers
- Animation toggle
- One-click theme switching

---

## Next Steps

### Recommended Enhancements
1. Add more color schemes
2. Create custom animations
3. Add theme presets
4. Implement theme export/import
5. Add accessibility options

### Optional Features
- Font size controls
- Contrast adjustments
- Reduced motion mode
- Custom CSS injection
- Theme scheduling

---

## Support

For issues or questions:
- Check browser console for errors
- Verify all files are uploaded
- Test in incognito mode
- Review this guide

---

**Version**: 2.0.0
**Last Updated**: 2025-12-02
**Status**: ✅ Production Ready

Enjoy your modern, professional platform! 🎨
