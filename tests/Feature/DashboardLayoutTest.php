<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_name_with_apostrophe_is_not_double_escaped_in_dashboard(): void
    {
        $user = User::factory()->create([
            'name' => "Timmy D'Hooghe",
        ]);

        $response = $this->withoutVite()->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('D&amp;#039;Hooghe', false);
        $response->assertSee("Timmy D'Hooghe");
    }
}
