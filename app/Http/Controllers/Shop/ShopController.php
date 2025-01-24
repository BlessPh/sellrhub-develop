<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShopRequest;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index()
    {
        return Shop::with('addresses')->get();
    }

    public function store(ShopRequest $request)
    {
        $shop = Shop::create([
            'user_id' => auth()->id(),
            'name' => $request->validated(['name']),
            'description' => $request->validated(['description']),
            'logo' => $request->validated(['logo']),
            'payment_method' => $request->validated(['payment_method']),
            'delivery_type' => $request->validated(['delivery_type']),
        ]);

        // Associate payment methods and delivery types
        $shop->paymentMethods()->sync($request->validated(['payment_method']));

        $shop->deliveryTypes()->sync($request->validated(['delivery_type']));

        $user = auth()->user();

        if (!$user->hasRole('seller')) {
            $user->assignRole('seller');
        }

        if ($request->hasFile('logo')) {
            try {
                $path = $request->file('logo')->store('shops/logos', 'public');

                $shop->update([
                    'logo' => Storage::url($path),
                ]);
            } catch (\Exception $e) {
                Log::error("Error occurred while uploading logo image : " . $e->getMessage());
            }
        }


        if ($request->hasFile('image_url')) {
            foreach ($request->file('image_url') as $image) {
                try {
                    $path = $image->store('shops/images', 'public');
                    $shop->images()->create([
                        'url' => Storage::url($path),
                    ]);
                } catch (\Exception $e) {
                    Log::error("Error occurred while uploading shop images : " . $e->getMessage());
                }
            }
        }



        $shop->load('deliveryTypes', 'paymentMethods', 'images');

        return response()->json([
            'message' => 'Shop created successfully',
            'shop' => $shop
        ]);
    }

    public function productsShow(Shop $shop)
    {
        return Shop::with('products')->where('id', $shop->id)->get();
    }

    public function reviewsShow(Shop $shop)
    {
        return Shop::with('reviews')->where('id', $shop->id)->get();
    }

    public function ordersShow(Shop $shop)
    {
        return Shop::with('orders')->where('id', $shop->id)->get();
    }
}
