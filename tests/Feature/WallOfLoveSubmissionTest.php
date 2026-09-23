<?php

namespace Tests\Feature;

use App\Livewire\WallOfLoveSubmissionForm;
use App\Models\User;
use App\Models\WallOfLoveSubmission;
use App\Notifications\WallOfLoveSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class WallOfLoveSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_a_new_wall_of_love_story_notifies_support(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(WallOfLoveSubmissionForm::class)
            ->set('name', 'Grace Hopper')
            ->set('company', 'US Navy')
            ->set('url', 'https://example.com')
            ->set('testimonial', 'NativePHP shipped my app in a weekend.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect();

        $submission = WallOfLoveSubmission::where('name', 'Grace Hopper')->first();
        $this->assertNotNull($submission);
        $this->assertEquals($user->id, $submission->user_id);

        Notification::assertSentOnDemand(
            WallOfLoveSubmitted::class,
            function (WallOfLoveSubmitted $notification, array $channels, object $notifiable) use ($submission) {
                return $notifiable->routes['mail'] === 'support@nativephp.com'
                    && $notification->submission->id === $submission->id;
            }
        );
    }

    public function test_wall_of_love_notification_email_includes_submission_details(): void
    {
        $user = User::factory()->create();
        $submission = WallOfLoveSubmission::factory()->create([
            'user_id' => $user->id,
            'name' => 'Grace Hopper',
            'company' => 'US Navy',
            'testimonial' => 'NativePHP shipped my app in a weekend.',
        ]);

        $rendered = (new WallOfLoveSubmitted($submission))
            ->toMail(new AnonymousNotifiable)
            ->render()
            ->toHtml();

        $this->assertStringContainsString('Grace Hopper', $rendered);
        $this->assertStringContainsString('US Navy', $rendered);
        $this->assertStringContainsString('NativePHP shipped my app in a weekend.', $rendered);
    }
}
