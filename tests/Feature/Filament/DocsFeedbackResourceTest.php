<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\DocsFeedbackResource\Pages\ListDocsFeedback;
use App\Filament\Resources\DocsFeedbackResource\Pages\ViewDocsFeedback;
use App\Models\DocsFeedback;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsFeedbackResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    #[Test]
    public function admin_can_view_the_feedback_list(): void
    {
        DocsFeedback::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListDocsFeedback::class)
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_view_a_single_feedback_entry_including_its_comment(): void
    {
        $feedback = DocsFeedback::factory()->create(['comment' => 'Needed a Windows example.']);

        Livewire::actingAs($this->admin)
            ->test(ViewDocsFeedback::class, ['record' => $feedback->getRouteKey()])
            ->assertSuccessful()
            ->assertSee('Needed a Windows example.');
    }

    #[Test]
    public function a_non_admin_cannot_access_the_feedback_list(): void
    {
        $user = User::factory()->create(['email' => 'not-an-admin@test.com']);

        $this->actingAs($user)->get('/admin/docs-feedback')->assertForbidden();
    }

    #[Test]
    public function a_guest_is_redirected_away_from_the_feedback_list(): void
    {
        $this->get('/admin/docs-feedback')->assertRedirect();
    }
}
