<?php

namespace App\Console\Commands;

use App\Jobs\UpdateNewArrivals;
use Illuminate\Console\Command;

class UpdateNewArrivalsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-new-arrivals-command';

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
        $this->info('Updating new arrivals collection...');
        UpdateNewArrivals::dispatch();
        $this->info('New arrivals collection update job dispatched successfully.');
    }
}
