<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $keyword = $request->input('query');

        $category = Category::where('category_name', 'LIKE', "%{$keyword}%")->get();

        $products = Product::where('product_name', 'LIKE', "%{$keyword}%")
            ->orWhere('product_description', 'LIKE', "%{$keyword}%")->get();

        return response()->json([
            "category" => $category,
            "products" => $products,
        ]);
    }
}
