<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Notifications\OrderCanceledNotification;
use App\Notifications\OrderDeliveredNotification;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\OrderRefundedNotification;
use App\Notifications\OrderReturnedNotification;
use App\Notifications\OrderShippedNotification;
use App\Notifications\OrderSuccessNotification;
use Auth;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('items')->get();
    }

    public function store(OrderRequest $request)
    {
        $user = Auth::user();

        $userAddressId = $user->addresses()->first()->id;

        // Calculate total price
        $totalPrice = 0;

        $orderItems = [];

        foreach ($request->validated(['products']) as $product) {
            $productModel = Product::find($product['id']);
            $price = $productModel->product_price * $product['quantity'];
            $totalPrice += $price;

            $orderItems[] = [
                'product_id' => $productModel->id,
                'unit_price' => $productModel->product_price,
                'quantity' => $product['quantity'],
            ];
        }

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'address_id' => $userAddressId,
            'delivery_type_id' => $request->validated(['delivery_type_id']),
            'payment_method_id' => $request->validated(['payment_method_id']),
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // Create Order items
        $order->items()->createMany($orderItems);

        // Notify the seller
        foreach ($orderItems as $orderItem) {
            $product = Product::find($orderItem['product_id']);

            $product->shop->user->notify(new OrderPlacedNotification($order));
        }

        $order->load('items.product.shop', 'user', 'address', 'deliveryType', 'paymentMethod');

        return response()->json([
            'message' => 'Order created successfully!',
            'order' => $order
        ]);
    }

    public function changeOrderStatus(Request $request, $orderId)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'status' => 'required|in:shipped,delivered,success,cancelled,returned,refunded',
        ]);

        $order = Order::findOrFail($orderId);

        // User rights checking when shipping
        if ($validated['status'] == 'shipped' && $user->id !== $order->items()->first()->product->shop->user_id
            || $validated['status'] == 'delivered' && $user->id !== $order->items()->first()->product->shop->user_id
            || $validated['status'] == 'success' && $user->id !== $order->user_id
            || $validated['status'] == 'cancelled' && $user->id !== $order->user_id
            || $validated['status'] == 'returned' && $user->id !== $order->items()->first()->product->shop->user_id
            || $validated['status'] == 'refunded' && $user->id !== $order->user->id
        ) {
            return response()->json([
                'message' => 'Unauthorized action !'
            ], 403);
        }

        $order->update(['status' => $validated['status']]);

        switch ($validated['status']) {
            case 'shipped':
                $order->user->notify(new OrderShippedNotification($order));
                break;
            case 'delivered':
                $order->user->notify(new OrderDeliveredNotification($order));
                break;
            case 'success':
                $order->items()->first()->product->shop->user->notify(new OrderSuccessNotification($order));
                break;
            case 'cancelled':
                $order->items()->first()->product->shop->user->notify(new OrderCanceledNotification());
                break;
            case 'returned':
                $order->user->notify(new OrderReturnedNotification($order));
                break;
            case 'refunded':
                $order->items()->first()->product->shop->user->notify(new OrderRefundedNotification($order));
                break;
        }

        return response()->json([
            'message' => 'Order status updated successfully!',
            'order' => $order
        ]);
    }

    public function show(Order $orderId)
    {
        return Order::with('items.product.shop', 'user', 'address', 'deliveryType', 'paymentMethod')->findOrFail($orderId);
    }

    public function destroy(Order $order)
    {
        Order::where('id', $order->id)->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }

}
