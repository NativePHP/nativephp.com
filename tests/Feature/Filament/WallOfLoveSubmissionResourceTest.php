<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\WallOfLoveSubmissionResource\Pages\EditWallOfLoveSubmission;
use App\Filament\Resources\WallOfLoveSubmissionResource\Pages\ListWallOfLoveSubmissions;
use App\Models\User;
use App\Models\WallOfLoveSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WallOfLoveSubmissionResourceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        config(['filament.users' => ['admin@test.com']]);
    }

    public function test_list_page_renders_successfully(): void
    {
        WallOfLoveSubmission::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListWallOfLoveSubmissions::class)
            ->assertSuccessful();
    }

    public function test_edit_page_renders_for_approved_submission(): void
    {
        $submission = WallOfLoveSubmission::factory()->approved()->create();

        Livewire::actingAs($this->admin)
            ->test(EditWallOfLoveSubmission::class, ['record' => $submission->getRouteKey()])
            ->assertSuccessful();
    }

    public function test_edit_page_renders_for_pending_submission(): void
    {
        $submission = WallOfLoveSubmission::factory()->pending()->create();

        Livewire::actingAs($this->admin)
            ->test(EditWallOfLoveSubmission::class, ['record' => $submission->getRouteKey()])
            ->assertSuccessful();
    }

    public function test_edit_page_renders_for_promoted_submission(): void
    {
        $submission = WallOfLoveSubmission::factory()->approved()->promoted()->create();

        Livewire::actingAs($this->admin)
            ->test(EditWallOfLoveSubmission::class, ['record' => $submission->getRouteKey()])
            ->assertSuccessful();
    }
}
