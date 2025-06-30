<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\SubCategoryImage;
use App\Models\SubCategoryAvailability;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SubCategoryController extends Controller
{
    // List subcategories (10 per page)
    public function index()
    {
        // load subcategories with images
        $subcats = SubCategory::with('images')
            ->orderBy('id','desc')
            ->paginate(10);

        return view('subcategories.index', compact('subcats'));
    }

    // Show create form for a given catId
    public function create($catId)
    {
        $category = Category::findOrFail($catId);
        return view('subcategories.create', compact('category'));
    }

    // Store new subcategory
    public function store(Request $request, $catId)
    {
        $category = Category::findOrFail($catId);

        $request->validate([
            'name' => 'required|string|max:255',
            'price'=> 'nullable|numeric',
        ]);

        // Build a path
        $slug = Str::slug($request->name,'_');
        $subCatFolder = "uploads/subcat_".$slug;

        // Create subcat
        SubCategory::create([
            'category_id' => $category->id,
            'name'        => $request->name,
            'price'       => $request->price ?? 0,
            'file_path'   => $subCatFolder,

            'has_rooms'       => $request->has('has_rooms'),
            'num_rooms'       => $request->num_rooms,
            'has_toilets'     => $request->has('has_toilets'),
            'num_toilets'     => $request->num_toilets,
            'has_sittingroom' => $request->has('has_sittingroom'),
            'num_sittingrooms'=> $request->num_sittingrooms,
            'has_kitchen'     => $request->has('has_kitchen'),
            'num_kitchens'    => $request->num_kitchens,
            'has_balcony'     => $request->has('has_balcony'),
            'num_balconies'   => $request->num_balconies,
            'free_wifi'       => $request->has('free_wifi'),
            'water'           => $request->has('water'),
            'electricity'     => $request->has('electricity'),
            'additional_info' => $request->additional_info,
        ]);

        return redirect()->route('categories.index')
                         ->with('success','Subcategory created!');
    }

    // Edit subcategory
    public function edit(SubCategory $subCategory)
    {
        return view('subcategories.edit', compact('subCategory'));
    }

    // Update subcategory
    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price'=> 'nullable|numeric'
        ]);

        $subCategory->update([
            'name' => $request->name,
            'price'=> $request->price ?? 0,

            'has_rooms'       => $request->has('has_rooms'),
            'num_rooms'       => $request->num_rooms,
            'has_toilets'     => $request->has('has_toilets'),
            'num_toilets'     => $request->num_toilets,
            'has_sittingroom' => $request->has('has_sittingroom'),
            'num_sittingrooms'=> $request->num_sittingrooms,
            'has_kitchen'     => $request->has('has_kitchen'),
            'num_kitchens'    => $request->num_kitchens,
            'has_balcony'     => $request->has('has_balcony'),
            'num_balconies'   => $request->num_balconies,
            'free_wifi'       => $request->has('free_wifi'),
            'water'           => $request->has('water'),
            'electricity'     => $request->has('electricity'),
            'additional_info' => $request->additional_info,
        ]);

        return redirect()->route('categories.index')
                         ->with('success','Subcategory updated!');
    }

    // Delete subcategory
    public function destroy(SubCategory $subCategory)
    {
        $subCategory->delete();
        return redirect()->route('categories.index')
                         ->with('success','Subcategory removed!');
    }

    // =========== IMAGES ===========
    public function storeImages(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'images.*' => 'image|max:2048'
        ]);

        if($request->hasFile('images')){
            foreach($request->file('images') as $img){
                $origName = $img->getClientOriginalName();
                $filename = Str::random(5).'_'.$origName;
                $storedPath = $img->storeAs($subCategory->file_path, $filename,'public');

                SubCategoryImage::create([
                    'sub_category_id' => $subCategory->id,
                    'image_path'      => 'storage/'.$storedPath
                ]);
            }
        }
        return redirect()->route('categories.index')->with('success','Images uploaded!');
    }

    public function destroyImage(SubCategoryImage $image)
    {
        $image->delete();
        return redirect()->route('categories.index')
                         ->with('success','Image removed!');
    }

    // =========== AVAILABILITY ===========
    public function availabilityIndex(SubCategory $subCategory)
    {
        $availabilities = $subCategory->availabilities()
            ->orderBy('date','asc')
            ->paginate(10);

        return view('subcategories.availability', compact('subCategory','availabilities'));
    }

    public function availabilityStore(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'date'           => 'required|date|after_or_equal:today',
            'slots'          => 'required|integer|min:0',
            'is_unavailable' => 'sometimes|in:0,1', 
        ]);

        $avail = SubCategoryAvailability::firstOrNew([
            'sub_category_id' => $subCategory->id,
            'date'            => $request->date,
        ]);
        $avail->slots = $request->slots;
        $avail->is_unavailable = $request->is_unavailable ? true : false;
        $avail->save();

        return redirect()->route('subcategories.availability.index',$subCategory->id)
                         ->with('success','Availability updated.');
    }

    public function availabilityUpdate(Request $request, SubCategoryAvailability $availability)
    {
        $request->validate([
            'slots'          => 'required|integer|min:0',
            'is_unavailable' => 'sometimes|in:0,1',
        ]);

        $availability->slots = $request->slots;
        $availability->is_unavailable = $request->is_unavailable ? true : false;
        $availability->save();

        return back()->with('success','Availability updated!');
    }

    public function availabilityDestroy(SubCategoryAvailability $availability)
    {
        $subCatId = $availability->sub_category_id;
        $availability->delete();

        return redirect()
            ->route('subcategories.availability.index',$subCatId)
            ->with('success','Date availability removed!');
    }
}
