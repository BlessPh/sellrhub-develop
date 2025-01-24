<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Illuminate\Database\Eloquent\Collection;
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

        if ($request->hasFile('image'))
        {
            $image = $request->file('image');
            $randomName = Str::random(30) . '.' . $image->getClientOriginalExtension();
            $path = Storage::disk('public')->put("category", $image);
            $category->images()->create([
                'url' => "storage/" . $path,
            ]);
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
        Category::where('id', $category->id)->update($request->validated());

        return $category->fresh();
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
