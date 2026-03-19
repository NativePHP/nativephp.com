<?php

namespace Database\Seeders;

use App\Models\Showcase;
use Illuminate\Database\Seeder;

class ShowcaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Approved showcases with screenshots
        Showcase::factory(2)->approved()->mobile()->withTallScreenshots(3)->create();
        Showcase::factory(2)->approved()->mobile()->create();
        Showcase::factory(2)->approved()->desktop()->withWideScreenshots(3)->create();
        Showcase::factory(2)->approved()->desktop()->create();
        Showcase::factory(2)->approved()->both()->withTallScreenshots(2)->create();

        // Recently approved (will show as "new") with screenshots
        Showcase::factory(2)->recentlyApproved()->mobile()->withTallScreenshots(4)->create();
        Showcase::factory(2)->recentlyApproved()->desktop()->withWideScreenshots(3)->create();
        Showcase::factory(1)->recentlyApproved()->both()->withWideScreenshots(2)->create();

        // Pending review
        Showcase::factory(2)->pending()->mobile()->withTallScreenshots(2)->create();
        Showcase::factory(2)->pending()->desktop()->withWideScreenshots(2)->create();
        Showcase::factory(1)->pending()->both()->create();
    }
}
