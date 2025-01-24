<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all file in the storage/app/public folder';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $publicPath = storage_path('app/public');

        File::cleanDirectory($publicPath);

        $this->info('All files have been cleared');

        return 0;
    }
}
