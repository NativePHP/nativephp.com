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
        // 10 approved showcases (mix of platforms)
        Showcase::factory(4)->approved()->mobile()->create();
        Showcase::factory(4)->approved()->desktop()->create();
        Showcase::factory(2)->approved()->both()->create();

        // 5 recently approved (will show as "new")
        Showcase::factory(2)->recentlyApproved()->mobile()->create();
        Showcase::factory(2)->recentlyApproved()->desktop()->create();
        Showcase::factory(1)->recentlyApproved()->both()->create();

        // 5 pending review
        Showcase::factory(2)->pending()->mobile()->create();
        Showcase::factory(2)->pending()->desktop()->create();
        Showcase::factory(1)->pending()->both()->create();
    }
}
