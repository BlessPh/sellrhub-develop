<?php

namespace App\Console\Commands;

use App\Jobs\UpdateFeaturedProducts;
use Illuminate\Console\Command;

class UpdateFeaturedProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-featured-products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Updating featured products collection...');
        UpdateFeaturedProducts::dispatch();
        $this->info('Featured products collection update job dispatched successfully.');
    }
}
