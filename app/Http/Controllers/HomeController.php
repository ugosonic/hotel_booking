<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;

class HomeController extends Controller
{
    public function __construct()
    {
        // If you want everyone (including guests) to access the categories/subcategories,
        // you might remove or modify this middleware for those particular routes.
        $this->middleware('auth')->except([
            'index', 'getCategories', 'getSubCategories', 'getSubCategory'
        ]);
    }

    // Show the main home page (Blade view)
    public function index()
    {
        return view('home');
    }

    // 1) Fetch all categories
    public function getCategories()
    {
        $categories = Category::select('id', 'name')->get();
    
        return response()->json($categories);
    }
    
    // 2) Fetch subcategories for a given category
    public function getSubCategories($id)
    {
        $subcategories = SubCategory::where('category_id', $id)->get();
        return response()->json($subcategories);
    }

    // 3) Fetch a single subcategory with details (and images if you have a relation)
    public function getSubCategory($id)
    {
        $subCategory = SubCategory::with('images')->findOrFail($id);
        return response()->json($subCategory);
    }
    
}
