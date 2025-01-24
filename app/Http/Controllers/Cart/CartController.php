<?php

namespace App\Http\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CartRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index($cartId)
    {

        try {
            $cart = Cart::with('cartItems.product')->findOrFail($cartId);
            if (!$cart) {
                return response()->json(['message' => 'Cart not found'], 404);
            }
            return response()->json(['cart' => $cart], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        try {
            // Check the connected User
            $userId = auth()->id();

            // Check if product exist in database
            $product = Product::find($request->product_id);
            if (!$product)
            {
                return response()->json(['message' => 'Product not found'], 404);
            }

            // Check if User has a Cart
            $cart = Cart::firstOrCreate(['user_id' => $userId]);

            // Check if the Product is already in Cart
            $cartItem = $cart->cartItems()->where('product_id', $request->product_id)->first();

            if ($cartItem)
            {
                $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);
            } else {
                $cart->cartItems()->create(['product_id' => $request->product_id, 'quantity' => $request->quantity]);
            }

            $cart->load('cartItems.product');

            return response()->json([
                'message' => 'Cart added successfully',
                'cart' => $cart,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function show(Cart $cart)
    {
        $cart = Cart::where('id', $cart->id)->first();

        return response()->json([$cart]);
    }

    public function removeItem(Request $request, $cartItemId)
    {
        try {
            $cartItem = CartItem::findOrFail($cartItemId);

            // Check if the item belongs to logged users cart
            if ($cartItem->cart->user_id !== auth()->id()) {
                return response()->json([
                    'message' => 'Unauthorized'
                ], 403);
            }
            $cartItem->delete();

            return response()->json([
                'message' => 'Item removed successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function destroy(Cart $cart)
    {
        Cart::where('id', $cart->id)->delete();

        return response()->json([
            'message' => 'Item removed from cart successfully'
        ]);
    }
}
