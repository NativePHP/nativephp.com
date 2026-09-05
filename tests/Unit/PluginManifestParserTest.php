<?php

namespace Tests\Unit;

use App\Services\PluginManifestParser;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PluginManifestParserTest extends TestCase
{
    private PluginManifestParser $parser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new PluginManifestParser;
    }

    #[Test]
    public function sdk_constraint_reads_from_composer_require(): void
    {
        $this->assertSame('^2.0', $this->parser->sdkConstraint(['require' => ['nativephp/mobile' => '^2.0']]));
        $this->assertNull($this->parser->sdkConstraint(['require' => []]));
        $this->assertNull($this->parser->sdkConstraint(null));
    }

    #[Test]
    public function min_versions_read_from_nativephp_json(): void
    {
        $data = ['ios' => ['min_version' => '18.0'], 'android' => ['min_version' => 29]];

        $this->assertSame('18.0', $this->parser->iosMinVersion($data));
        $this->assertSame('29', $this->parser->androidMinVersion($data));
        $this->assertNull($this->parser->iosMinVersion(null));
        $this->assertNull($this->parser->androidMinVersion(null));
    }

    #[Test]
    public function permission_manifest_extracts_the_relevant_subset(): void
    {
        $manifest = $this->parser->permissionManifest([
            'android' => [
                'permissions' => ['android.permission.CAMERA'],
                'features' => [['name' => 'android.hardware.camera', 'required' => true]],
            ],
            'ios' => [
                'capabilities' => ['push-notifications'],
                'entitlements' => ['com.apple.developer.healthkit' => true],
                'background_modes' => ['fetch'],
                'info_plist' => ['NSCameraUsageDescription' => 'Used for scanning.'],
            ],
        ]);

        $this->assertSame(['android.permission.CAMERA'], $manifest['android_permissions']);
        $this->assertSame(['push-notifications'], $manifest['ios_capabilities']);
        $this->assertSame(['fetch'], $manifest['ios_background_modes']);
        $this->assertArrayHasKey('com.apple.developer.healthkit', $manifest['ios_entitlements']);
    }

    #[Test]
    public function permission_manifest_defaults_to_empty_arrays_when_manifest_is_missing(): void
    {
        $manifest = $this->parser->permissionManifest(null);

        $this->assertSame([], $manifest['android_permissions']);
        $this->assertSame([], $manifest['ios_capabilities']);
    }

    #[Test]
    public function missing_usage_descriptions_flags_unmatched_android_permissions_when_ios_is_supported(): void
    {
        $manifest = $this->parser->permissionManifest([
            'android' => ['permissions' => ['android.permission.CAMERA', 'android.permission.INTERNET']],
        ]);

        $missing = $this->parser->missingUsageDescriptions($manifest, supportsIos: true);

        $this->assertCount(1, $missing);
        $this->assertSame('android.permission.CAMERA', $missing[0]['permission']);
        $this->assertSame('NSCameraUsageDescription', $missing[0]['expected_key']);
    }

    #[Test]
    public function missing_usage_descriptions_is_empty_when_the_key_is_declared(): void
    {
        $manifest = $this->parser->permissionManifest([
            'android' => ['permissions' => ['android.permission.CAMERA']],
            'ios' => ['info_plist' => ['NSCameraUsageDescription' => 'Used for scanning.']],
        ]);

        $this->assertSame([], $this->parser->missingUsageDescriptions($manifest, supportsIos: true));
    }

    #[Test]
    public function missing_usage_descriptions_is_skipped_when_the_plugin_does_not_support_ios(): void
    {
        $manifest = $this->parser->permissionManifest([
            'android' => ['permissions' => ['android.permission.CAMERA']],
        ]);

        $this->assertSame([], $this->parser->missingUsageDescriptions($manifest, supportsIos: false));
    }

    #[Test]
    public function has_expanded_permissions_is_false_with_no_previous_version(): void
    {
        $current = $this->parser->permissionManifest(['android' => ['permissions' => ['android.permission.CAMERA']]]);

        $this->assertFalse($this->parser->hasExpandedPermissions(null, $current));
    }

    #[Test]
    public function has_expanded_permissions_detects_a_new_android_permission(): void
    {
        $previous = $this->parser->permissionManifest([]);
        $current = $this->parser->permissionManifest(['android' => ['permissions' => ['android.permission.CAMERA']]]);

        $this->assertTrue($this->parser->hasExpandedPermissions($previous, $current));
    }

    #[Test]
    public function has_expanded_permissions_detects_a_new_ios_entitlement(): void
    {
        $previous = $this->parser->permissionManifest([]);
        $current = $this->parser->permissionManifest(['ios' => ['entitlements' => ['com.apple.developer.healthkit' => true]]]);

        $this->assertTrue($this->parser->hasExpandedPermissions($previous, $current));
    }

    #[Test]
    public function has_expanded_permissions_is_false_when_nothing_new_was_added(): void
    {
        $previous = $this->parser->permissionManifest(['android' => ['permissions' => ['android.permission.CAMERA']]]);
        $current = $this->parser->permissionManifest(['android' => ['permissions' => ['android.permission.CAMERA']]]);

        $this->assertFalse($this->parser->hasExpandedPermissions($previous, $current));
    }

    #[Test]
    public function has_expanded_permissions_is_false_when_permissions_are_only_removed(): void
    {
        $previous = $this->parser->permissionManifest([
            'android' => ['permissions' => ['android.permission.CAMERA', 'android.permission.RECORD_AUDIO']],
        ]);
        $current = $this->parser->permissionManifest(['android' => ['permissions' => ['android.permission.CAMERA']]]);

        $this->assertFalse($this->parser->hasExpandedPermissions($previous, $current));
    }
}
