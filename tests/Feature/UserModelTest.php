<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_filament_name_returns_name_when_present(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);

        $this->assertSame('John Doe', $user->getFilamentName());
    }

    public function test_get_filament_name_returns_display_name_when_name_is_null(): void
    {
        $user = User::factory()->create(['name' => null, 'display_name' => 'Custom Name']);

        $this->assertSame('Custom Name', $user->getFilamentName());
    }

    public function test_get_filament_name_returns_email_when_name_and_display_name_are_null(): void
    {
        $user = User::factory()->create(['name' => null, 'display_name' => null]);

        $this->assertSame($user->email, $user->getFilamentName());
    }

    public function test_get_filament_name_always_returns_string(): void
    {
        $user = User::factory()->create(['name' => null, 'display_name' => null]);

        $this->assertIsString($user->getFilamentName());
    }
}
