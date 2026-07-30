<?php

namespace Tests\Feature;

use App\Support\JumpApp;
use Tests\TestCase;

class ApplinksTest extends TestCase
{
    public function test_assetlinks_exposes_the_jump_android_identity(): void
    {
        $this->get('/.well-known/assetlinks.json')
            ->assertStatus(200)
            ->assertJsonFragment(['package_name' => JumpApp::ANDROID_PACKAGE])
            ->assertJsonFragment(['sha256_cert_fingerprints' => [JumpApp::ANDROID_SHA256]]);
    }

    public function test_apple_app_site_association_exposes_the_jump_ios_identity(): void
    {
        $this->get('/.well-known/apple-app-site-association')
            ->assertStatus(200)
            ->assertJsonFragment(['appIDs' => [JumpApp::IOS_APP_ID]]);
    }

    public function test_apple_app_site_association_scopes_to_docs_deep_links(): void
    {
        $this->get('/.well-known/apple-app-site-association')
            ->assertStatus(200)
            ->assertJsonFragment(['/' => '/docs/*']);
    }
}
