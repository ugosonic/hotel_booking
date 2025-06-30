<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('id','desc')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'price'=>'nullable|numeric'
        ]);

        $slug = Str::slug($request->name,'_');
        $fakePath = "uploads/category_$slug";

        Category::create([
            'name'=>$request->name,
            'price'=>$request->price ?? 0,
            'file_path'=>$fakePath
        ]);

        return redirect()->route('categories.index')
                         ->with('success','Category created!');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'price'=>'nullable|numeric'
        ]);
        $category->update([
            'name'=>$request->name,
            'price'=>$request->price ?? 0
        ]);
        return redirect()->route('categories.index')->with('success','Category updated!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success','Category deleted!');
    }
}
