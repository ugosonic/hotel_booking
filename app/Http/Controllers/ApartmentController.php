<?php

namespace App\Http\Controllers;

use App\Models\Apartment; // important
use Illuminate\Http\Request;

class ApartmentController extends Controller
{
    public function create()
    {
        return view('dashboard.create_apartment');
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'apartment_name' => 'required|string',
            'price'          => 'required|numeric',
            // etc. if you want
        ]);

        // Create
        $apartment = Apartment::create([
            'apartment_name' => $request->apartment_name,
            'has_rooms'      => $request->has('has_rooms'),
            'num_rooms'      => $request->num_rooms,
            'has_toilets'    => $request->has('has_toilets'),
            'num_toilets'    => $request->num_toilets,
            'has_sittingroom' => $request->has('has_sittingroom'),
            'num_sittingrooms' => $request->num_sittingrooms,
            'has_kitchen'    => $request->has('has_kitchen'),
            'num_kitchens'   => $request->num_kitchens,
            'has_balcony'    => $request->has('has_balcony'),
            'num_balconies'  => $request->num_balconies,
            'free_wifi'      => $request->has('free_wifi'),
            'water'          => $request->has('water'),
            'electricity'    => $request->has('electricity'),
            'price'          => $request->price,
            'additional_info'=> $request->additional_info,
        ]);

        return redirect()->back()->with('success','Apartment saved successfully!');
    }
}
