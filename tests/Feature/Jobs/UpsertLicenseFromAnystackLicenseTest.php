<?php

namespace Tests\Feature\Jobs;

use App\Enums\Subscription;
use App\Jobs\UpsertLicenseFromAnystackLicense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpsertLicenseFromAnystackLicenseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_license_from_anystack_data()
    {
        $user = User::factory()->create([
            'anystack_contact_id' => 'contact-123',
        ]);

        $now = Date::now()->toImmutable();

        $licenseData = [
            'id' => 'license-123',
            'key' => 'test-license-key-12345',
            'contact_id' => 'contact-123',
            'policy_id' => Subscription::Mini->anystackPolicyId(),
            'name' => null,
            'activations' => 0,
            'max_activations' => 10,
            'suspended' => true,
            'expires_at' => $now->addYear()->toIso8601String(),
            'created_at' => $now->toIso8601String(),
            'updated_at' => $now->toIso8601String(),
        ];

        $job = new UpsertLicenseFromAnystackLicense($licenseData);
        $job->handle();

        $this->assertDatabaseHas('licenses', [
            'anystack_id' => 'license-123',
            'user_id' => $user->id,
            'key' => 'test-license-key-12345',
            'policy_name' => Subscription::Mini->value,
            'is_suspended' => true,
            'expires_at' => $now->addYear(),
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
