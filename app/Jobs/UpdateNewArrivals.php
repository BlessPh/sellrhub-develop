<?php

namespace App\Jobs;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateNewArrivals implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $collection = Collection::firstOrCreate([
            'type' => Collection::TYPE_NEW_ARRIVALS,
            'name' => 'New Arrivals',
            'slug' => 'new-arrivals',
        ]);

        $newProducts = Product::where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(20)
            ->pluck('id');

        $collection->products()->sync($newProducts);
    }
}
