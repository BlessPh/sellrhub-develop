<?php

namespace App\Http\Controllers\Review;

use App\Http\Controllers\Controller;
use App\Http\Requests\Review\ReviewRequest;
use App\Http\Requests\Shop\ShopRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(ReviewRequest $request)
    {
        if (!$request->validated(['shop_id']) && !$request->validated(['product_id']))
        {
            return response()->json([
                'error' => 'Review must be chained to a product or shop',
            ]);
        }

        // Create review
        $review = Review::create([
            'user_id' => auth()->id(),
            'shop_id' => $request->validated(['shop_id']) ?? null,
            'product_id' => $request->validated(['product_id']) ?? null,
            'rating' => $request->validated(['rating']),
            'comment' => $request->validated(['comment']),
        ]);

        return response()->json([
            'message' => 'Review created successfully',
            'review' => $review,
        ], 201);
    }

    public function destroy(Review $review)
    {
        return Review::where('id', $review->id)->delete();
    }
}
