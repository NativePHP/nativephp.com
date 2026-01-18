<?php

namespace Database\Factories;

use App\Enums\PluginStatus;
use App\Enums\PluginType;
use App\Models\Plugin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Plugin>
 */
class PluginFactory extends Factory
{
    protected $model = Plugin::class;

    /**
     * @var array<int, string>
     */
    protected array $pluginPrefixes = [
        'nativephp',
        'laravel',
        'acme',
        'awesome',
        'super',
        'native',
        'mobile',
        'app',
    ];

    /**
     * @var array<int, string>
     */
    protected array $pluginSuffixes = [
        'camera',
        'biometrics',
        'push-notifications',
        'geolocation',
        'bluetooth',
        'nfc',
        'contacts',
        'calendar',
        'health-kit',
        'share',
        'in-app-purchase',
        'admob',
        'analytics',
        'crashlytics',
        'deep-links',
        'local-auth',
        'secure-storage',
        'file-picker',
        'image-picker',
        'video-player',
        'audio-player',
        'speech-to-text',
        'text-to-speech',
        'barcode-scanner',
        'qr-code',
        'maps',
        'payments',
        'social-auth',
        'firebase',
        'sentry',
        'offline-sync',
        'background-tasks',
        'sensors',
        'haptics',
        'clipboard',
        'device-info',
        'network-info',
        'battery',
        'screen-brightness',
        'orientation',
        'keyboard',
        'status-bar',
        'splash-screen',
        'app-icon',
        'widgets',
    ];

    /**
     * @var array<int, string>
     */
    protected array $descriptions = [
        'A powerful plugin that integrates seamlessly with your NativePHP Mobile application, providing essential native functionality.',
        'Easily add native capabilities to your Laravel mobile app with this simple-to-use plugin.',
        'This plugin bridges the gap between PHP and native platform APIs, giving you full control.',
        'Unlock advanced mobile features with minimal configuration. Works on both iOS and Android.',
        'A production-ready plugin built with performance and reliability in mind.',
        'Simplify complex native integrations with this well-documented and tested plugin.',
        'Built by experienced mobile developers, this plugin follows best practices for both platforms.',
        'Zero-config setup that just works. Install via Composer and start using immediately.',
        'Comprehensive feature set with granular permissions control for enhanced security.',
        'Lightweight and fast, this plugin has minimal impact on your app\'s performance.',
    ];

    public function definition(): array
    {
        $vendor = fake()->randomElement($this->pluginPrefixes);
        $package = fake()->randomElement($this->pluginSuffixes);

        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->numerify("{$vendor}/{$package}-###"),
            'repository_url' => "https://github.com/{$vendor}/{$package}",
            'webhook_secret' => bin2hex(random_bytes(32)),
            'description' => fake()->randomElement($this->descriptions),
            'ios_version' => fake()->randomElement(['15.0+', '16.0+', '14.0+', '17.0+', null]),
            'android_version' => fake()->randomElement(['12+', '13+', '11+', '14+', null]),
            'type' => PluginType::Free,
            'status' => PluginStatus::Pending,
            'featured' => false,
            'rejection_reason' => null,
            'approved_at' => null,
            'approved_by' => null,
            'created_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'updated_at' => fn (array $attrs) => $attrs['created_at'],
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PluginStatus::Pending,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PluginStatus::Approved,
            'approved_at' => fake()->dateTimeBetween($attributes['created_at'], 'now'),
            'approved_by' => User::factory(),
            'rejection_reason' => null,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PluginStatus::Rejected,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => fake()->randomElement([
                'Package not found on Packagist. Please ensure your package is published.',
                'Plugin does not meet our quality standards. Please review our plugin guidelines.',
                'Missing required documentation. Please add a README with installation instructions.',
                'Security concerns identified. Please address the issues and resubmit.',
                'Plugin name conflicts with an existing package. Please choose a different name.',
                'Incomplete implementation. Some advertised features are not working as expected.',
            ]),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PluginType::Free,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => PluginType::Paid,
        ]);
    }

    public function withoutDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => null,
        ]);
    }
}
