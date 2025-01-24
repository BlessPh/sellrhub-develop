<?php

namespace App\Jobs;

use App\Models\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class UpdateBestSellers implements ShouldQueue
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
            'type' => Collection::TYPE_BEST_SELLER,
            'name' => 'Best Sellers',
            'slug' => 'best-sellers',
        ]);

        $bestSellerProducts = DB::table('order_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', now()->subDays(30))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(20)
            ->pluck('product_id');

        $collection->products()->sync($bestSellerProducts);
    }
}
