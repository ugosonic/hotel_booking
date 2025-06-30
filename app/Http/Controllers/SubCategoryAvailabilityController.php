<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use App\Models\SubCategoryAvailability;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SubCategoryAvailabilityController extends Controller
{
    public function index(SubCategory $subCategory)
    {
        // Show a list of availability rows for this subCategory
        // 10 per page
        $availabilities = $subCategory->availabilities()
            ->orderBy('date','asc')
            ->paginate(10);

        return view('availability.index', compact('subCategory','availabilities'));
    }

    public function store(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'date'           => 'required|date|after_or_equal:today',
            'slots'          => 'required|integer|min:0',
            'is_unavailable' => 'boolean',
        ]);

        // upsert or create
        $avail = SubCategoryAvailability::firstOrNew([
            'sub_category_id' => $subCategory->id,
            'date'            => $request->date,
        ]);
        $avail->slots         = $request->slots;
        $avail->is_unavailable = $request->is_unavailable ? 1 : 0;
        $avail->save();

        return redirect()->route('availability.index', $subCategory->id)
                         ->with('success','Availability updated.');
    }

    public function update(Request $request, SubCategoryAvailability $availability)
    {
        $request->validate([
            'slots' => 'required|integer|min:0',
            'is_unavailable' => 'boolean'
        ]);

        $availability->update([
            'slots'          => $request->slots,
            'is_unavailable' => $request->is_unavailable ? 1 : 0,
        ]);

        return back()->with('success','Availability updated.');
    }

    public function destroy(SubCategoryAvailability $availability)
    {
        $subCatId = $availability->sub_category_id;
        $availability->delete();

        return redirect()
            ->route('availability.index', $subCatId)
            ->with('success','Date availability deleted.');
    }
}
