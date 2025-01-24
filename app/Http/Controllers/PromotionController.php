<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Mail\PromotionMail;
use App\Models\Product;
use App\Models\Promotion;
use App\Notifications\PromotionNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    public function index()
    {
        try {
            $promotions = Promotion::with(['products', 'shop'])->get();

            return response()->json([
                'message' => 'Promotions fetched successfully',
                'data' => $promotions,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch promotions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StorePromotionRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = \Auth::user();

            dd($user);

            $shop = $user->shop;


            if (!$shop)
            {
                return response()->json([
                    'error' => 'You don\'t have a shop'
                ], 403);
            }

            $followers = $shop->followers()->get();

            // Attach products with calculated promotional prices
            $products = $request->validated(['products'], []);

            $shopProducts = $shop->products()->whereIn('id', $products)->pluck('id')->toArray();

            if (count($shopProducts) !== count($products)) {
                return response()->json([
                    'error' => 'One or more products do not belong to your shop'
                ], 403);
            }

            $promoCode = Promotion::generatePromoCode(
                $request->input('initiator_name'),
                $request->input('code_length', 8)
            );

            $promotion = Promotion::create([
                'title' => $request->validated(['title']),
                'images' => $request->validated(['images']),
                'description' => $request->validated(['description']),
                'initiator_name' => $request->validated(['initiator_name']),
                'code_length' => $request->validated(['code_length']),
                'discount_percentage' => $request->validated(['discount_percentage']),
                'shop_id' => $shop->id,
                'products' => $request->validated(['products']),
                'starts_at' => $request->validated(['starts_at']),
                'ends_at' => $request->validated(['ends_at']),
                'promo_code' => $promoCode,
            ]);

            if ($request->hasFile('images')) {
                try {
                    foreach ($request->file('images') as $image) {
                        // Store the image in the 'promotions' directory in public storage
                        $path = $image->store('promotions', 'public');

                        // Create a new record for the promotion image in the database
                        $promotion->images()->create([
                            'url' => Storage::url($path),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging
                    Log::error("Error uploading promotion images: " . $e->getMessage());
                    // Optional: Add a user-friendly response or notification here
                }
            }


            $promotionalPrices = [];

            foreach ($products as $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $promotionalPrices[$productId] = [
                        'promotional_price' => $promotion->calculatePromotionalPrice($product->product_price)
                    ];
                }
            }

            $promotion->products()->attach($promotionalPrices);

            $promotion->load(['images']);

            foreach ($followers as $follower) {
                Mail::to($follower->email)->send(new PromotionMail($promotion, $shop));
                $follower->notify(new PromotionNotification($promotion));
            }

            DB::commit();

            return response()->json([
                'message' => 'Promotion created successfully',
                'promotion' => $promotion->load('products')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create promotion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProductsOnPromotion()
    {
        try {
            $promotions = Promotion::with(['products' => function ($query) {
                $query->select('products.*', 'products.product_price', 'product_promotion.promotional_price')
                    ->withPivot('promotional_price');
            }])->get();

            if ($promotions->isEmpty()) {
                return response()->json([
                    'message' => 'No promotions found'
                ], 404);
            }

            return response()->json([
                'message' => 'Promotions retrieved successfully',
                'promotions' => $promotions
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve promotions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
