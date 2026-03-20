<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductLicense;
use App\Models\User;
use App\Notifications\ProductGranted;
use Illuminate\Console\Command;

class GrantProduct extends Command
{
    protected $signature = 'products:grant
        {product : The product slug}
        {user : The user email}
        {--dry-run : Preview what would happen without making changes}
        {--no-email : Grant the product without sending a notification email}';

    protected $description = 'Grant a product to a user by email';

    public function handle(): int
    {
        $product = Product::where('slug', $this->argument('product'))->first();

        if (! $product) {
            $this->error("Product not found: {$this->argument('product')}");

            return Command::FAILURE;
        }

        $user = User::where('email', $this->argument('user'))->first();

        if (! $user) {
            $this->error("User not found: {$this->argument('user')}");

            return Command::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $noEmail = $this->option('no-email');

        $this->info("Product: {$product->name} (slug: {$product->slug})");
        $this->info("User: {$user->email}");

        if ($dryRun) {
            $this->warn('[DRY RUN] No changes will be made.');
        }

        $this->newLine();

        // Check if user already has a license for this product
        $existingLicense = ProductLicense::where('user_id', $user->id)
            ->where('product_id', $product->id)
            ->exists();

        if ($existingLicense) {
            $this->warn("User {$user->email} already has a license for this product.");

            return Command::SUCCESS;
        }

        if (! $dryRun) {
            ProductLicense::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'price_paid' => 0,
                'currency' => 'USD',
                'is_comped' => true,
                'purchased_at' => now(),
            ]);

            if (! $noEmail) {
                $user->notify(new ProductGranted($product));
            }
        }

        $this->info("Granted to {$user->email}");

        if ($dryRun) {
            $this->warn('This was a dry run. Run again without --dry-run to apply changes.');
        }

        return Command::SUCCESS;
    }
}
