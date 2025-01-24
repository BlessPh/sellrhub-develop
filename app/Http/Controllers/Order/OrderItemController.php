<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderItemRequest;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function store(OrderItemRequest $request)
    {
        $orderItem = OrderItem::create($request->validated());

        return response()->json([
            'message' => 'Order item created successfully',
            'orderItem' => $orderItem
        ]);
    }

    public function index(Order $orderId)
    {
        $items = OrderItem::where('order_id', $orderId)->with('product')->get();

        return response()->json($items);
    }

    public function update(OrderItemRequest $request, OrderItem $orderItem)
    {
        $orderItem = OrderItem::where('id', $orderItem->id)->update($request->validated());

        return response()->json([
            'message' => 'Order item updated successfully',
            'orderItem' => $orderItem
        ]);
    }

    public function destroy(OrderItem $orderItem)
    {
        OrderItem::where('id', $orderItem->id)->delete();

        return response()->json([
            'message' => 'Order item deleted successfully'
        ]);
    }
}
