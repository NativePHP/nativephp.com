---
title: Splash Screen/Icons
order: 500
---

## Overview

App icons and splash screens are the first things users see when interacting with your mobile app. NativePHP Mobile makes it easy to customize both elements to create a professional, branded experience.

## App Icons

### Basic Icon Setup

Place a single high-resolution icon file at: `public/icon.png`

### Requirements
- **Format:** PNG
- **Size:** 1024 × 1024 pixels  
- **Shape:** Square
- **Background:** Transparent or solid (your choice)
- **Content:** Should work well at small sizes

```
your-laravel-app/
├── public/
│   └── icon.png          ← Place your 1024x1024 icon here
├── app/
└── ...
```

### Automatic Icon Generation

NativePHP automatically generates all required icon sizes for both platforms:

#### iOS Icon Sizes (Generated Automatically)
- App Store: 1024x1024
- iPhone: 60x60, 120x120, 180x180
- iPad: 76x76, 152x152, 167x167
- Settings: 29x29, 58x58, 87x87
- Spotlight: 40x40, 80x80, 120x120

#### Android Icon Sizes (Generated Automatically)
- mdpi: 48x48
- hdpi: 72x72
- xhdpi: 96x96
- xxhdpi: 144x144
- xxxhdpi: 192x192

### Icon Design Best Practices

```php
// Example of checking if custom icon exists
class IconValidator
{
    public function validateIcon(): array
    {
        $iconPath = public_path('icon.png');
        
        if (!file_exists($iconPath)) {
            return ['valid' => false, 'message' => 'Icon file not found'];
        }
        
        $imageInfo = getimagesize($iconPath);
        
        if (!$imageInfo) {
            return ['valid' => false, 'message' => 'Invalid image file'];
        }
        
        [$width, $height] = $imageInfo;
        
        if ($width !== 1024 || $height !== 1024) {
            return [
                'valid' => false, 
                'message' => "Icon must be 1024x1024px, got {$width}x{$height}px"
            ];
        }
        
        if ($imageInfo['mime'] !== 'image/png') {
            return ['valid' => false, 'message' => 'Icon must be PNG format'];
        }
        
        return ['valid' => true, 'message' => 'Icon is valid'];
    }
}
```

#### Design Guidelines
1. **Keep it simple** - Icons must be recognizable at 16x16 pixels
2. **Avoid text** - Text becomes unreadable at small sizes
3. **Use strong contrast** - Ensure visibility on various backgrounds
4. **Make it memorable** - Unique shape or color helps recognition
5. **Test at multiple sizes** - Check how it looks when scaled down
6. **Consider platform conventions** - iOS prefers rounded corners (applied automatically)

## Splash Screens

### Splash Screen Configuration

Splash screens are shown while your app loads. NativePHP provides built-in splash screen support that you can customize.

### Default Splash Screen

By default, NativePHP creates a simple splash screen using your app icon and name. No additional configuration required.

### Custom Splash Screen

Create custom splash screen assets:

```
your-laravel-app/
├── public/
│   ├── icon.png
│   ├── splash-logo.png        ← Optional custom splash logo
│   └── splash-background.png  ← Optional background image
├── app/
└── ...
```

### Splash Screen Specifications

#### iOS Launch Images
- **iPhone:** Various sizes for different devices
- **iPad:** Portrait and landscape orientations
- **Safe Areas:** Account for notches and home indicators

#### Android Splash Screens
- **Centered Logo:** Displayed on colored background
- **Responsive:** Adapts to different screen sizes and orientations
- **Theme-aware:** Can adapt to light/dark themes

### Configuration Options

```php
// config/nativephp.php
return [
    'app' => [
        'name' => 'Your App Name',
        'splash' => [
            'background_color' => '#ffffff',
            'logo_path' => 'splash-logo.png',
            'show_loading' => true,
        ],
    ],
];
```

### Dynamic Splash Screens

```php
class SplashScreenManager
{
    public function configureSplashScreen(): void
    {
        $isDarkMode = $this->getUserPreference('dark_mode');
        
        $splashConfig = [
            'background_color' => $isDarkMode ? '#000000' : '#ffffff',
            'logo_path' => $isDarkMode ? 'logo-dark.png' : 'logo-light.png',
            'show_loading' => true,
        ];
        
        $this->updateSplashConfiguration($splashConfig);
    }
    
    private function getUserPreference(string $key): bool
    {
        return session($key, false);
    }
    
    private function updateSplashConfiguration(array $config): void
    {
        // Update runtime splash configuration
        config(['nativephp.app.splash' => $config]);
    }
}
```

## Asset Compilation

### CSS and JavaScript Assets

Your mobile app runs locally, so all assets must be compiled before deployment.

```bash
# Always compile assets before building
npm run build

# Then build your mobile app
php artisan native:run
```

### Build Process Integration

```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "build:mobile": "vite build --mode mobile",
    "mobile:prepare": "npm run build:mobile && php artisan optimize"
  }
}
```

### Asset Optimization for Mobile

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['lodash', 'axios'],
                    ui: ['@headlessui/vue', '@heroicons/vue'],
                }
            }
        },
        chunkSizeWarningLimit: 1000,
    },
});
```

## Development Workflow

### Hot Reload (Experimental)

For development, you can use the experimental watch flag:

```bash
php artisan native:run --watch
```

### Watch Limitations

Currently supports:
- ✅ Blade templates
- ✅ Livewire components  
- ✅ PHP files
- ❌ Compiled CSS/JS assets
- ❌ Vite builds
- ❌ Inertia.js apps

### Recommended Development Flow

```bash
# For Blade/Livewire apps
php artisan native:run --watch

# For apps with compiled assets (Vue, React, Inertia)
npm run dev &           # Run in background
npm run build           # Build assets
php artisan native:run  # Launch app
```

## Testing Icons and Splash Screens

### Icon Testing Checklist

```php
class AssetTestSuite
{
    public function testIconExists(): void
    {
        $this->assertFileExists(public_path('icon.png'));
    }
    
    public function testIconDimensions(): void
    {
        $iconPath = public_path('icon.png');
        [$width, $height] = getimagesize($iconPath);
        
        $this->assertEquals(1024, $width);
        $this->assertEquals(1024, $height);
    }
    
    public function testIconFormat(): void
    {
        $iconPath = public_path('icon.png');
        $imageInfo = getimagesize($iconPath);
        
        $this->assertEquals('image/png', $imageInfo['mime']);
    }
    
    public function testSplashConfiguration(): void
    {
        $splashConfig = config('nativephp.app.splash');
        
        $this->assertIsArray($splashConfig);
        $this->assertArrayHasKey('background_color', $splashConfig);
    }
}
```

### Visual Testing

1. **Test on multiple devices** - Check how icons look on different screen sizes
2. **Test both orientations** - Portrait and landscape modes
3. **Test theme variations** - Light and dark modes
4. **Test loading states** - Ensure splash screens display properly
5. **Performance testing** - Monitor app launch times

## Platform-Specific Considerations

### iOS
- Icons automatically get rounded corners
- Supports Dynamic Type for accessibility
- Requires specific launch image sizes
- Splash screens adapt to safe areas

### Android
- Icons can be adaptive (foreground + background)
- Supports vector drawables for splash screens
- Material Design guidelines apply
- Supports theme-aware splash screens

### Cross-Platform Assets

```
public/
├── icon.png              # Universal app icon
├── icon-android.png      # Android-specific (optional)
├── icon-ios.png          # iOS-specific (optional)
├── splash-logo.png       # Universal splash logo
├── splash-android.png    # Android splash (optional)
└── splash-ios.png        # iOS splash (optional)
```

## Best Practices

### Icon Design
1. **Start with vector graphics** - Use SVG or AI files for source
2. **Export high quality** - Use 1024x1024 PNG with no compression
3. **Test readability** - Check visibility at 16x16 pixels
4. **Maintain brand consistency** - Match your web/desktop icons
5. **Consider accessibility** - Ensure sufficient contrast

### Splash Screen Design
1. **Keep it simple** - Splash screens should load quickly
2. **Match your brand** - Use consistent colors and typography
3. **Don't include text** - Text may not scale properly
4. **Consider loading states** - Show progress if app takes time to load
5. **Test performance** - Long splash screens hurt user experience

### Asset Management
1. **Optimize file sizes** - Compress images without quality loss
2. **Use appropriate formats** - PNG for icons, WebP for photos
3. **Version your assets** - Track changes to visual elements
4. **Automate generation** - Script the creation of multiple sizes
5. **Test regularly** - Verify assets display correctly after changes