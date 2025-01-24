<?php

namespace App\Http\Controllers;

use App\Http\Requests\WishlistRequest;
use App\Models\Wishlist;
use Auth;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product')->where('user_id', Auth::id())->get();

        return response()->json($wishlist);
    }

    public function add(WishlistRequest $request)
    {
        Wishlist::create([
            'user_id' => Auth::user()->id,
            'product_id' => $request->validated(['product_id']),
        ]);

        return response()->json([
            'message' => 'Product added to wishlist'
        ]);
    }

    public function remove($id)
    {
        $wishlistItem = Wishlist::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $wishlistItem->delete();

        return response()->json([
            'message' => 'Product removed from wishlist'
        ]);
    }
}
