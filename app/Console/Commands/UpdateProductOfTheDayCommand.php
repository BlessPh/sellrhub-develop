<?php

namespace App\Console\Commands;

use App\Jobs\UpdateProductOfTheDay;
use Illuminate\Console\Command;

class UpdateProductOfTheDayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-product-of-the-day-command';

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
        $this->info('Updating product of the day collection...');
        UpdateProductOfTheDay::dispatch();
        $this->info('Product of the day collection update job dispatched successfully.');
    }
}
