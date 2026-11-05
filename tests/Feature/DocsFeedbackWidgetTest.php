<?php

namespace Tests\Feature;

use App\Livewire\DocsFeedbackWidget;
use App\Models\DocsFeedback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsFeedbackWidgetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_submits_helpful_feedback_with_no_comment(): void
    {
        Livewire::test(DocsFeedbackWidget::class, [
            'platform' => 'mobile',
            'version' => '4',
            'page' => 'docs/mobile/4/getting-started/introduction',
        ])
            ->call('vote', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $feedback = DocsFeedback::first();
        $this->assertNotNull($feedback);
        $this->assertTrue($feedback->helpful);
        $this->assertNull($feedback->comment);
        $this->assertSame('mobile', $feedback->platform);
    }

    #[Test]
    public function it_submits_unhelpful_feedback_with_a_comment(): void
    {
        Livewire::test(DocsFeedbackWidget::class, [
            'platform' => 'mobile',
            'version' => '4',
            'page' => 'docs/mobile/4/getting-started/introduction',
        ])
            ->call('vote', false)
            ->set('comment', 'Could not find how to configure push notifications.')
            ->call('submit')
            ->assertHasNoErrors();

        $feedback = DocsFeedback::first();
        $this->assertFalse($feedback->helpful);
        $this->assertSame('Could not find how to configure push notifications.', $feedback->comment);
    }

    #[Test]
    public function it_stores_a_hashed_ip_not_the_raw_address(): void
    {
        Livewire::test(DocsFeedbackWidget::class, [
            'platform' => 'mobile',
            'version' => '4',
            'page' => 'docs/mobile/4/getting-started/introduction',
        ])
            ->call('vote', true)
            ->call('submit');

        $feedback = DocsFeedback::first();
        $this->assertNotNull($feedback->ip_hash);
        $this->assertSame(64, strlen($feedback->ip_hash));
    }

    #[Test]
    public function it_rate_limits_repeated_submissions_from_the_same_ip(): void
    {
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(DocsFeedbackWidget::class, [
                'platform' => 'mobile',
                'version' => '4',
                'page' => 'docs/mobile/4/getting-started/introduction',
            ])
                ->call('vote', true)
                ->call('submit')
                ->assertHasNoErrors();
        }

        Livewire::test(DocsFeedbackWidget::class, [
            'platform' => 'mobile',
            'version' => '4',
            'page' => 'docs/mobile/4/getting-started/introduction',
        ])
            ->call('vote', true)
            ->call('submit')
            ->assertHasErrors('form');

        $this->assertSame(5, DocsFeedback::count());
    }
}
