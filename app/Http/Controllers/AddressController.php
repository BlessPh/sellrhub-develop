<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShopAddressRequest;
use App\Http\Requests\UserAddressRequest;
use App\Models\Address;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function getUserAddress()
    {
        $user = Auth::user();

        if (!$user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $address = $user->addresses()->get();

        return response()->json($address);
    }
    public function addUserAddress(UserAddressRequest $request)
    {
        $user = Auth()->user();

        $address = $user->addresses()->create($request->validated());

        return response()->json([
            'message' => 'Address added successfully',
            'address' => $address
        ]);
    }

    public function updateUserAddress(UserAddressRequest $request,Address $address)
    {
        if ($address->addressable_type !== User::class || $address->addressable_id !== Auth::id()) {
            return response()->json([
                'error' => 'Address does not belong to this user'
            ], 403);
        }

        $address->update($request->validated());

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address
        ], 200);
    }

    public function deleteUserAddress(Address $address)
    {
        if ($address->addressable_type !== User::class || $address->addressable_id !== Auth::id()) {
            return response()->json([
                'error' => 'Address does not belong to this user'
            ], 403);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }

    // Shop Address

    public function getShopAddress()
    {
        $user = Auth::user();

        if (!$user){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shop = $user->shop;

        if (!$shop){
            return response()->json(['error' => 'User do not have a shop'], 404);
        }

        $address = $shop->addresses()->get();

        return response()->json([
            'user' => $user,
            'address' => $address
        ]);
    }

    public function addShopAddress(ShopAddressRequest $request)
    {
        $shop = Auth::user()->shop;

        if (!$shop) {
            return response()->json([
                'error' => 'User does not have a shop'
            ]);
        }

        $address = $shop->addresses()->create($request->validated());

        return response()->json([
            'message' => 'Address added successfully',
            'address' => $address
        ]);
    }

    public function updateShopAddress(ShopAddressRequest $request, Address $address)
    {
        $shop = Auth::user()->shop;

        if (!$shop || $address->addressable_type !== Shop::class || $address->addressable_id !== $shop->id)
        {
            return response()->json([
                'error' => 'Address does not belong to this user'
            ]);
        }

        $address->update($request->validated());

        return response()->json([
            'message' => 'Address updated successfully',
            'address' => $address
        ]);
    }

    public function deleteShopAddress(Address $address)
    {
        $shop = Auth::user()->shop;

        if (!$shop || $address->addressable_type !== Shop::class || $address->addressable_id !== $shop->id)
        {
            return response()->json([
                'error' => 'Address does not belong to this user'
            ]);
        }

        $address->delete();

        return response()->json([
            'message' => 'Address deleted successfully'
        ]);
    }
}
