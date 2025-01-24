<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shop\ShopRequest;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;
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

        if ($request->hasFile('logo'))
        {
            $logo = $request->file('logo');
            $randomName = Str::random(30) . '.' . $logo->getClientOriginalExtension();
            $path = Storage::disk('public')->put("shops/logos", $logo);
            $shop->update(['logo' => "storage/" . $path]);
        }

        if ($request->hasFile('image_url'))
        {
            foreach ($request->file('image_url') as $image) {
                $randomName = Str::random(30) . '.' . $image->getClientOriginalExtension();
                $path = Storage::disk('public')->put("shops/images", $image);
                $shop->images()->create([
                    'url' => "storage/" . $path,
                ]);
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
