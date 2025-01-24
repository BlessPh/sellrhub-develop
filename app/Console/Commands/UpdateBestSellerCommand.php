<?php

namespace App\Console\Commands;

use App\Jobs\UpdateBestSellers;
use Illuminate\Console\Command;

class  UpdateBestSellerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-best-seller-command';

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
        $this->info('Updating best sellers collection...');
        UpdateBestSellers::dispatch();
        $this->info('Best sellers collection update job dispatched successfully.');
    }
}
