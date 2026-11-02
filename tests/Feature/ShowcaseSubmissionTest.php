<?php

namespace Tests\Feature;

use App\Livewire\ShowcaseSubmissionForm;
use App\Models\Showcase;
use App\Models\User;
use App\Notifications\ShowcaseSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ShowcaseSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submitting_a_new_showcase_notifies_support(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class)
            ->set('title', 'My Awesome App')
            ->set('description', 'A great app built with NativePHP.')
            ->set('hasMobile', true)
            ->set('certifiedNativephp', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect();

        $showcase = Showcase::where('title', 'My Awesome App')->first();
        $this->assertNotNull($showcase);
        $this->assertEquals($user->id, $showcase->user_id);

        Notification::assertSentOnDemand(
            ShowcaseSubmitted::class,
            function (ShowcaseSubmitted $notification, array $channels, object $notifiable) use ($showcase) {
                return $notifiable->routes['mail'] === 'support@nativephp.com'
                    && $notification->showcase->id === $showcase->id
                    && $notification->resubmitted === false;
            }
        );
    }

    public function test_editing_an_approved_showcase_sends_it_back_for_review_and_notifies_support(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $showcase = Showcase::factory()->approved()->mobile()->create([
            'user_id' => $user->id,
            'title' => 'Existing App',
            'description' => 'The original description.',
        ]);

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class, ['showcase' => $showcase])
            ->assertSet('isEditing', true)
            ->set('description', 'An updated description.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect();

        $showcase->refresh();
        $this->assertTrue($showcase->isPending());
        $this->assertNull($showcase->approved_at);

        Notification::assertSentOnDemand(
            ShowcaseSubmitted::class,
            function (ShowcaseSubmitted $notification, array $channels, object $notifiable) use ($showcase) {
                return $notifiable->routes['mail'] === 'support@nativephp.com'
                    && $notification->showcase->id === $showcase->id
                    && $notification->resubmitted === true;
            }
        );
    }

    public function test_editing_a_still_pending_showcase_does_not_notify_support(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $showcase = Showcase::factory()->pending()->mobile()->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class, ['showcase' => $showcase])
            ->assertSet('isEditing', true)
            ->set('description', 'A tweaked description.')
            ->call('submit')
            ->assertHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_showcase_notification_email_includes_submission_details(): void
    {
        $user = User::factory()->create(['name' => 'Ada Lovelace']);
        $showcase = Showcase::factory()->mobile()->create([
            'user_id' => $user->id,
            'title' => 'Difference Engine',
            'description' => 'A calculating machine for the modern age.',
        ]);

        $rendered = (new ShowcaseSubmitted($showcase))
            ->toMail(new AnonymousNotifiable)
            ->render()
            ->toHtml();

        $this->assertStringContainsString('Difference Engine', $rendered);
        $this->assertStringContainsString('Ada Lovelace', $rendered);
        $this->assertStringContainsString('Mobile', $rendered);

        $resubmitted = (new ShowcaseSubmitted($showcase, resubmitted: true))
            ->toMail(new AnonymousNotifiable)
            ->render()
            ->toHtml();

        $this->assertStringContainsString('sent back for review', $resubmitted);
    }

    public function test_submitting_both_platforms_stores_platform_specific_screenshots(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class)
            ->set('title', 'Cross-Platform App')
            ->set('description', 'Works everywhere.')
            ->set('hasMobile', true)
            ->set('hasDesktop', true)
            ->set('mobileScreenshots', [UploadedFile::fake()->image('mobile.png')])
            ->set('desktopScreenshots', [UploadedFile::fake()->image('desktop.png')])
            ->set('certifiedNativephp', true)
            ->call('submit')
            ->assertHasNoErrors();

        $showcase = Showcase::where('title', 'Cross-Platform App')->firstOrFail();
        $this->assertCount(1, $showcase->mobile_screenshots);
        $this->assertCount(1, $showcase->desktop_screenshots);
        Storage::disk('public')->assertExists($showcase->mobile_screenshots[0]);
        Storage::disk('public')->assertExists($showcase->desktop_screenshots[0]);
    }

    public function test_submitting_a_single_platform_never_stores_platform_specific_screenshots(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class)
            ->set('title', 'Mobile Only App')
            ->set('description', 'Just mobile.')
            ->set('hasMobile', true)
            ->set('certifiedNativephp', true)
            ->call('submit')
            ->assertHasNoErrors();

        $showcase = Showcase::where('title', 'Mobile Only App')->firstOrFail();
        $this->assertNull($showcase->mobile_screenshots);
        $this->assertNull($showcase->desktop_screenshots);
    }

    public function test_unchecking_a_platform_clears_its_platform_specific_screenshots(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $showcase = Showcase::factory()->both()->withMobileScreenshots(2)->withDesktopScreenshots(2)->create([
            'user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(ShowcaseSubmissionForm::class, ['showcase' => $showcase])
            ->set('hasDesktop', false)
            ->call('submit')
            ->assertHasNoErrors();

        $showcase->refresh();
        $this->assertNull($showcase->mobile_screenshots);
        $this->assertNull($showcase->desktop_screenshots);
    }
}
