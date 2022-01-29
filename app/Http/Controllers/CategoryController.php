<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = Category::select(['*']);

        if($request->name) {
            $categories->where('name', 'like', '%'.$request->name.'%');
        }

        return CategoryResource::collection($categories->orderBy('id', 'desc')->paginate(5));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $category = $request->all();

        $category = Category::create($category);

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $newCategory = $request->all();

        $category->update($newCategory);

        return (new CategoryResource(Category::find($category->id)))->response()->setStatusCode(201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return new CategoryResource($category);
    }

    public function publicIndex(Request $request) {
        $categories = Category::select(['*']);

        if($request->name) {
            $categories->where('name', 'like', '%'.$request->name.'%');
        }

        $categories->where('enabled', true);

        return CategoryResource::collection($categories->orderBy('id', 'desc')->paginate(5));
    }

    public function publicShow(Category $category) {
        if($category['enabled'] == false) {
            return response()->json(['error' => 'No puede acceder a esta categoria'], 403);
        }

        return new CategoryResource($category);
    } 
}
