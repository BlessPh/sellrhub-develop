<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Shop;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Product::with('images', 'colors', 'reviews')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            $shop = Shop::where('user_id', auth()->id())->first();

            if (!$shop)
            {
                return response()->json(['error' => 'User don\'t have any Shop'], 404);
            }

            $data['shop_id'] = $shop->id;

            $product = Product::create($data);

            // Colors save
            if ($request->has('product_colors'))
            {
                foreach ($request->validated(['product_colors']) as $colors)
                {
                    $product->colors()->create([
                        'code' => $colors
                    ]);
                }

            }

            // Images save
            if ($request->hasFile('product_images')) {
                try {
                    foreach ($request->file('product_images') as $image) {
                        // Save the image to the 'products' directory in public storage
                        $path = $image->store('products', 'public');

                        // Create a new record for the product image in the database
                        $product->images()->create([
                            'url' => Storage::url($path),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging purposes
                    Log::error("Error uploading product images: " . $e->getMessage());
                }
            }


            // Video save
            if ($request->hasFile('product_video')) {
                try {
                    // Store the product video in the 'products/videos' directory
                    $path = $request->file('product_video')->store('products/videos', 'public');

                    // Update the product with the generated video URL
                    $product->update([
                        'video_url' => Storage::url($path),
                    ]);
                } catch (\Exception $e) {
                    // Log the error for debugging
                    Log::error("Error uploading the product video: " . $e->getMessage());
                }
            }



            $product->load('images', 'colors');

            return response()->json([
                'message' => 'Product created successfully',
                'product' => $product
            ]);
        }catch (\Exception $e){
            return Response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        if (auth()->check()) {
            $userId = auth()->id();

            // Vérifier si l'utilisateur a déjà vu ce produit aujourd'hui
            $hasViewed = DB::table('user_product_views')
                ->where('user_id', $userId)
                ->where('product_id', $product->id)
                ->whereDate('created_at', today())
                ->exists();

            if (!$hasViewed) {
                // Enregistrer la vue
                DB::table('user_product_views')->insert([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Incrémenter le compteur
                $product->increment('views_count');
            }
        } else {
            // Incrémenter directement pour les visiteurs non connectés
            $product->increment('views_count');
        }

        $product->load('images', 'colors');

        return response()->json([
            'product' => $product
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $product = Product::with(['images', 'colors'])->where('id', $product->id)->first();

            $product->update($request->validated());

            if ($request->has('product_colors'))
            {
                $product->colors()->delete();

                foreach ($request->product_colors as $color)
                {
                    $product->colors()->create([
                        'code' => $color
                    ])->fresh();
                }
            }

            if ($request->has('product_images')) {
                try {
                    // Delete all existing images for the product
                    $product->images()->delete();

                    // Loop through the provided images and create new records
                    foreach ($request->product_images as $image) {
                        // Create a new image record for the product
                        $product->images()->create([
                            'url' => $image,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Log the error for debugging purposes
                    Log::error("Error uploading product images: " . $e->getMessage());
                    // Optional: You can add a user-friendly response or notification here
                }
            }


            return response()->json([
                'message' => 'Product updated successfully',
                'product' => $product
            ], 200);
        }catch (\Exception $e){
            return Response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        try {
            $product = Product::findOrFail($product->id);

            $product->images()->delete();

            $product->colors()->delete();

            $product->delete();

            return response()->json([
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Exception $e){
            return Response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getRecommendedProducts($productId)
    {
        $product = Product::findOrFail($productId);

        $categorySuggestions = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(5)
            ->get();

        $trendingSuggestions = Product::orderBy('views_count', 'desc')
            ->where('id', '!=', $product->id)
            ->limit(5)
            ->get();

        return response()->json([
            'categorySuggestions' => $categorySuggestions,
            'trendingSuggestions' => $trendingSuggestions,
        ]);
    }


    public function reviewsShow(Product $product)
    {
        return Product::with('reviews')->where('id', $product->id)->get();
    }
}
