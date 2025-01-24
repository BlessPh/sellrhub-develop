<?php

namespace App\Jobs;

use App\Models\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateProductOfTheDay implements ShouldQueue
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
            'type' => Collection::TYPE_PRODUCT_OF_DAY,
            'name' => 'Product of the Day',
            'slug' => 'product-of-day',
        ]);

        $productOfDay = DB::table('product_promotion')
            ->join('promotions', 'promotions.id', '=', 'product_promotion.promotion_id')
            ->where('promotions.status', 'active')
            ->where('promotions.starts_at', '<=', now())
            ->where('promotions.ends_at', '>=', now())
            ->orderBy('product_promotion.promotional_price')
            ->first();

        if ($productOfDay) {
            $collection->products()->sync([$productOfDay->product_id]);
        }
    }
}
