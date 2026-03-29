<?php

namespace Tests\Feature;

use App\Features\ShowAuthButtons;
use App\Livewire\WallOfLoveSubmissionForm;
use App\Models\User;
use App\Models\WallOfLoveSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Pennant\Feature;
use Livewire\Livewire;
use Tests\TestCase;

class WallOfLoveEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Feature::define(ShowAuthButtons::class, true);
    }

    public function test_owner_can_access_edit_page(): void
    {
        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/dashboard/wall-of-love/{$submission->id}/edit");

        $response->assertStatus(200);
        $response->assertSee('Edit Your Listing');
    }

    public function test_non_owner_gets_403(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get("/dashboard/wall-of-love/{$submission->id}/edit");

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_edit_page(): void
    {
        $submission = WallOfLoveSubmission::factory()->create();

        $response = $this->get("/dashboard/wall-of-love/{$submission->id}/edit");

        $response->assertRedirect('/login');
    }

    public function test_owner_can_update_company_name(): void
    {
        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create([
            'user_id' => $user->id,
            'company' => 'Old Company',
        ]);

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class, ['submission' => $submission])
            ->assertSet('isEditing', true)
            ->assertSet('company', 'Old Company')
            ->set('company', 'New Company')
            ->call('submit');

        $this->assertDatabaseHas('wall_of_love_submissions', [
            'id' => $submission->id,
            'company' => 'New Company',
        ]);
    }

    public function test_owner_can_update_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create([
            'user_id' => $user->id,
            'photo_path' => null,
        ]);

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class, ['submission' => $submission])
            ->set('photo', UploadedFile::fake()->image('avatar.jpg'))
            ->call('submit');

        $submission->refresh();
        $this->assertNotNull($submission->photo_path);
        Storage::disk('public')->assertExists($submission->photo_path);
    }

    public function test_approval_status_is_not_reset_on_edit(): void
    {
        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->approved()->create([
            'user_id' => $user->id,
            'company' => 'Old Company',
        ]);

        $originalApprovedAt = $submission->approved_at;
        $originalApprovedBy = $submission->approved_by;

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class, ['submission' => $submission])
            ->set('company', 'Updated Company')
            ->call('submit');

        $submission->refresh();
        $this->assertEquals('Updated Company', $submission->company);
        $this->assertEquals($originalApprovedAt->toDateTimeString(), $submission->approved_at->toDateTimeString());
        $this->assertEquals($originalApprovedBy, $submission->approved_by);
    }

    public function test_edit_mode_only_shows_company_and_photo_fields(): void
    {
        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create(['user_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class, ['submission' => $submission])
            ->assertSee('Company')
            ->assertSee('Photo')
            ->assertDontSee('Name *')
            ->assertDontSee('Website or Social Media URL')
            ->assertDontSee('Your story or testimonial');
    }

    public function test_owner_can_remove_existing_photo(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create([
            'user_id' => $user->id,
            'photo_path' => 'wall-of-love-photos/old-photo.jpg',
        ]);

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class, ['submission' => $submission])
            ->assertSet('existingPhoto', 'wall-of-love-photos/old-photo.jpg')
            ->call('removeExistingPhoto')
            ->assertSet('existingPhoto', null)
            ->call('submit');

        $submission->refresh();
        $this->assertNull($submission->photo_path);
    }
}
