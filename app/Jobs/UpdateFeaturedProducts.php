<?php

namespace App\Jobs;

use App\Models\Collection;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateFeaturedProducts implements ShouldQueue
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
            'type' => Collection::TYPE_FEATURED,
            'name' => 'Featured Products',
            'slug' => 'featured-products',
        ]);

        $featuredProducts = Product::where('is_featured', true)
            ->orderByDesc('updated_at')
            ->limit(20)
            ->pluck('id');

        $collection->products()->sync($featuredProducts);
    }
}
