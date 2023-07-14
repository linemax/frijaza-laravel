<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $size = 5;
        $request->validate([
            'size' => ['integer'],
        ]);
        if ($request->has('size')) {
            if ($request->integer('size') === -1) {
                $size = Category::all()->count();
            } else {
                $size = $request->size;
            }
        }
        return Category::orderBy('created_at', 'desc')->withAllowedRelationships($request->query('with'))->paginate($size);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        Category::create($request->validated());
        return response(status: 200);
    }
    /**
     * Display the specified resource.
     */
    public function show(Category $category, Request $request)
    {
        return Category::where('id', $category->id)->withAllowedRelationships($request->query('with'))->first();
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        return response(status: 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response(status: 204);
    }
}
