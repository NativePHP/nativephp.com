<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\DocsCoverageGapResource\Pages\ListDocsCoverageGaps;
use App\Models\DocsCoverageGap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DocsCoverageGapResourceTest extends TestCase
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
    public function admin_can_view_the_coverage_gap_list(): void
    {
        DocsCoverageGap::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
            ->test(ListDocsCoverageGaps::class)
            ->assertSuccessful();
    }

    #[Test]
    public function admin_can_filter_the_list_to_undocumented_entries(): void
    {
        DocsCoverageGap::factory()->create(['identifier' => 'documented_key', 'documented' => true]);
        DocsCoverageGap::factory()->create(['identifier' => 'missing_key', 'documented' => false]);

        Livewire::actingAs($this->admin)
            ->test(ListDocsCoverageGaps::class)
            ->assertSuccessful()
            ->assertCanSeeTableRecords(DocsCoverageGap::query()->where('documented', false)->get())
            ->filterTable('documented', false)
            ->assertCanSeeTableRecords(DocsCoverageGap::query()->where('documented', false)->get())
            ->assertCanNotSeeTableRecords(DocsCoverageGap::query()->where('documented', true)->get());
    }

    #[Test]
    public function a_non_admin_cannot_access_the_coverage_gap_list(): void
    {
        $user = User::factory()->create(['email' => 'not-an-admin@test.com']);

        $this->actingAs($user)->get('/admin/docs-coverage-gaps')->assertForbidden();
    }

    #[Test]
    public function a_guest_is_redirected_away_from_the_coverage_gap_list(): void
    {
        $this->get('/admin/docs-coverage-gaps')->assertRedirect();
    }
}
