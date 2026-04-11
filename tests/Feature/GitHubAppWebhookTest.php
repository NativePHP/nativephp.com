<?php

namespace Tests\Feature;

use App\Models\GitHubInstallation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GitHubAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected string $webhookSecret = 'test_webhook_secret';

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.github_app.webhook_secret' => $this->webhookSecret]);
    }

    protected function signPayload(string $payload): string
    {
        return 'sha256='.hash_hmac('sha256', $payload, $this->webhookSecret);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $response = $this->postJson('/webhooks/github-app', ['action' => 'created'], [
            'X-GitHub-Event' => 'installation',
            'X-Hub-Signature-256' => 'sha256=invalid',
        ]);

        $response->assertStatus(403);
    }

    public function test_webhook_rejects_missing_signature(): void
    {
        $response = $this->postJson('/webhooks/github-app', ['action' => 'created'], [
            'X-GitHub-Event' => 'installation',
        ]);

        $response->assertStatus(403);
    }

    public function test_installation_created_event_creates_record(): void
    {
        $user = User::factory()->withGitHubApp()->create(['github_id' => '99999']);

        $payload = [
            'action' => 'created',
            'installation' => [
                'id' => 12345,
                'account' => [
                    'login' => 'testuser',
                    'type' => 'User',
                    'id' => 54321,
                ],
                'repository_selection' => 'all',
                'repositories' => [],
            ],
            'sender' => [
                'id' => 99999,
            ],
        ];

        $payloadJson = json_encode($payload);
        $signature = $this->signPayload($payloadJson);

        $response = $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'installation',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payloadJson);

        $response->assertOk();
        $response->assertJson(['status' => 'created']);

        $this->assertDatabaseHas('github_installations', [
            'user_id' => $user->id,
            'installation_id' => 12345,
            'account_login' => 'testuser',
            'account_type' => 'User',
            'selection_type' => 'all',
        ]);
    }

    public function test_installation_deleted_event_removes_record(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'installation_id' => 12345,
        ]);

        $payload = [
            'action' => 'deleted',
            'installation' => [
                'id' => 12345,
                'account' => ['login' => 'testuser', 'type' => 'User', 'id' => 1],
                'repository_selection' => 'all',
            ],
            'sender' => ['id' => 99999],
        ];

        $payloadJson = json_encode($payload);
        $signature = $this->signPayload($payloadJson);

        $response = $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'installation',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payloadJson);

        $response->assertOk();
        $this->assertDatabaseMissing('github_installations', ['installation_id' => 12345]);
    }

    public function test_installation_suspend_event_updates_record(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->create([
            'user_id' => $user->id,
            'installation_id' => 12345,
        ]);

        $payload = [
            'action' => 'suspend',
            'installation' => [
                'id' => 12345,
                'account' => ['login' => 'testuser', 'type' => 'User', 'id' => 1],
                'repository_selection' => 'all',
            ],
            'sender' => ['id' => 99999],
        ];

        $payloadJson = json_encode($payload);
        $signature = $this->signPayload($payloadJson);

        $response = $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'installation',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payloadJson);

        $response->assertOk();
        $installation->refresh();
        $this->assertNotNull($installation->suspended_at);
    }

    public function test_installation_unsuspend_event_clears_suspension(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->suspended()->create([
            'user_id' => $user->id,
            'installation_id' => 12345,
        ]);

        $payload = [
            'action' => 'unsuspend',
            'installation' => [
                'id' => 12345,
                'account' => ['login' => 'testuser', 'type' => 'User', 'id' => 1],
                'repository_selection' => 'all',
            ],
            'sender' => ['id' => 99999],
        ];

        $payloadJson = json_encode($payload);
        $signature = $this->signPayload($payloadJson);

        $response = $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'installation',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payloadJson);

        $response->assertOk();
        $installation->refresh();
        $this->assertNull($installation->suspended_at);
    }

    public function test_installation_repositories_event_updates_repos(): void
    {
        $user = User::factory()->withGitHubApp()->create();
        $installation = GitHubInstallation::factory()->selectedRepos(['testuser/repo-a'])->create([
            'user_id' => $user->id,
            'installation_id' => 12345,
            'account_login' => 'testuser',
        ]);

        $payload = [
            'action' => 'added',
            'installation' => ['id' => 12345],
            'repository_selection' => 'selected',
            'repositories_added' => [
                ['full_name' => 'testuser/repo-b'],
            ],
            'repositories_removed' => [],
            'sender' => ['id' => 99999],
        ];

        $payloadJson = json_encode($payload);
        $signature = $this->signPayload($payloadJson);

        $response = $this->call('POST', '/webhooks/github-app', [], [], [], [
            'HTTP_X_GITHUB_EVENT' => 'installation_repositories',
            'HTTP_X_HUB_SIGNATURE_256' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ], $payloadJson);

        $response->assertOk();
        $installation->refresh();
        $this->assertContains('testuser/repo-a', $installation->repository_selection);
        $this->assertContains('testuser/repo-b', $installation->repository_selection);
    }
}
