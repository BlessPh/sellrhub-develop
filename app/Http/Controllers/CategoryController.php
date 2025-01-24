<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Collection
    {
        return Category::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();

        $category = Category::create($validated);
        // dd($category);

        if ($request->hasFile('image')) {
            try {
                // Store the image in the 'category' directory in public storage
                $path = $request->file('image')->store('category', 'public');

                // Create a new record for the category image in the database
                $category->images()->create([
                    'url' => Storage::url($path),
                ]);
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error("Error uploading category image: " . $e->getMessage());
            }
        }


        $category->load('images');

        return response()->json([
            "message" => "category created",
            "category" => $category
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return Category::where('id', $category->id)->first();
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): ?Category
    {
        try {
            // If the request contains an image, handle the image upload
            if ($request->hasFile('image')) {
                // Store the new image and get the path
                $path = $request->file('image')->store('categories', 'public');

                // Update the category with the new image URL
                $category->update([
                    'image' => Storage::url($path),
                ]);
            }

            // Update the rest of the category fields
            $category->update($request->validated());

            // Return the updated category
            return $category->fresh();
        } catch (\Exception $e) {
            // Log any errors for debugging purposes
            Log::error("Error updating category: " . $e->getMessage());
            return null; // Optional: You can return a custom error response here
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): array
    {
        Category::where('id', $category->id)->delete();

        return ['message' => 'Category deleted successfully'];
    }

    public function getProductsByCategory(Category $category): Collection
    {
        return Category::with('products')->where('id', $category->id)->get();
    }

    public function categoriesWithProducts(): Collection
    {
        return Category::with('products')->get();
    }
}
