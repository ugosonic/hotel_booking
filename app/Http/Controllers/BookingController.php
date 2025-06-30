<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\SubCategoryAvailability;
use App\Models\UserActivity;

class BookingController extends Controller
{
   // ...
    
    public function staffChangeList()
    {
        if (!auth()->user()->isStaff()) {
            abort(403);
        }
    
        $today = Carbon::today()->format('Y-m-d');
    
        // Only show bookings that have a start_date in the future 
        // and are either pending or successful
        $bookings = Booking::with('subCategory')
            ->whereIn('status', ['pending', 'successful'])
            ->where('start_date', '>', $today)
            ->orderBy('start_date', 'asc')
            ->paginate(10);
    
        return view('bookings.staff_change', compact('bookings'));
    }
    
    public function clientChangeList()
    {
        $today = Carbon::today()->format('Y-m-d');
    
        // Only show the client’s own bookings that start in the future
        $bookings = Booking::with('subCategory')
            ->where('client_id', auth()->id())
            ->whereIn('status', ['pending', 'successful'])
            ->where('start_date', '>', $today)
            ->orderBy('start_date', 'asc')
            ->paginate(10);
    
        return view('bookings.client_change', compact('bookings'));
    }
    
    

    /**
     * Show the edit form for an existing booking.
     */
    public function edit(Booking $booking)
    {
        // Only staff or owner can edit.
        if (!auth()->user()->isStaff() && $booking->client_id != auth()->id()) {
            abort(403);
        }

        // Get categories + subcategories for your dropdowns.
        $categories = Category::all();
        $subCategories = SubCategory::with('images')->get();

        $apartmentsJson = $subCategories->map(function ($sub) {
            return [
                'id'   => $sub->id,
                'name' => $sub->name,
                // etc...
                'price' => $sub->price,
                'images' => $sub->images->map(function($img) {
                    return [
                        'id' => $img->id,
                        'image_path' => asset('storage/'.$img->image_path),
                    ];
                })->all(),
            ];
        });

        // If staff, retrieve list of clients
        $clients = [];
        if (auth()->user()->isStaff()) {
            $clients = User::where('role','client')->get();
        }

        // Also retrieve existing extra guests from booking_guests table:
        $extraGuests = DB::table('booking_guests')
            ->where('booking_id', $booking->id)
            ->get(); 
        // This returns a collection of stdClass objects

        return view('bookings.edit', compact(
            'booking', 
            'categories',
            'apartmentsJson',
            'clients',
            'extraGuests'
        ));
    } 
    /**
     * Update an existing booking.
     */
    /**
 * Update an existing booking, e.g. when user extends the stay.
 */
public function update(Request $request, Booking $booking)
{
    // 1) Only staff or the booking’s owner can update
    if (!auth()->user()->isStaff() && $booking->client_id != auth()->id()) {
        abort(403);
    }

    // 2) Validate input
    $validated = $request->validate([
        'booking_type'     => 'nullable|string', // 'client' or 'guest'
        'client_id'        => 'nullable|integer',
        'guest_name'       => 'nullable|string',
        'guest_email'      => 'nullable|email',
        'guest_address'    => 'nullable|string',
        'guest_phone'      => 'nullable|string',
        'guest_dob'        => 'nullable|date',
        'doc_type'         => 'nullable|string',
        'doc_number'       => 'nullable|string',
        'doc_upload'       => 'nullable|file|max:2048',

        'sub_category_id'  => 'required|integer',  // The apartment (sub-cat) to book
        'start_date'       => 'required|date|after_or_equal:today',
        'end_date'         => 'required|date|after:start_date',

        'extra_guests'     => 'nullable', // JSON string for additional guests
    ]);

    // 3) Keep track of the old total so we can see the difference later
    $originalTotal = $booking->total_amount ?? 0;

    // 4) If staff is editing, handle whether it's for a known client or a walk-in guest
    $staffId = auth()->user()->isStaff() ? auth()->id() : null;
    if ($staffId && $request->booking_type === 'client') {
        // Staff booking for an existing client
        $booking->client_id   = $request->client_id;
        $booking->guest_name  = null;
    }
    elseif ($staffId && $request->booking_type === 'guest') {
        // Staff booking for a walk-in
        $booking->client_id   = null;
        $booking->guest_name  = $request->guest_name;
    } else {
        // Normal client user
        // (If needed, ensure that $booking->client_id = auth()->id();)
    }

    // 5) Optional: doc upload
    if ($request->hasFile('doc_upload')) {
        $docPath = $request->file('doc_upload')->store('documents','public');
        $booking->doc_upload = $docPath;
    }

    // 6) Update the “common” guest fields
    $booking->guest_email   = $request->guest_email;
    $booking->guest_address = $request->guest_address;
    $booking->guest_phone   = $request->guest_phone;
    $booking->guest_dob     = $request->guest_dob;
    $booking->doc_type      = $request->doc_type;
    $booking->doc_number    = $request->doc_number;

    // 7) Possibly change the subcategory (the type of apartment)
    $booking->sub_category_id = $request->sub_category_id;

    // 8) Calculate new nights
    $start = \Carbon\Carbon::parse($request->start_date);
    $end   = \Carbon\Carbon::parse($request->end_date);
    $nights = $start->diffInDays($end);
    if ($nights < 1) {
        $nights = 1;
    }

    // 9) Check availability ONLY if the user changed date range
    if ($booking->start_date != $start->format('Y-m-d') 
        || $booking->end_date != $end->format('Y-m-d'))
    {
        $subCatId = $request->sub_category_id;
        $period = new \DatePeriod(
            new \DateTime($start->format('Y-m-d')),
            new \DateInterval('P1D'),
            new \DateTime($end->format('Y-m-d')) // exclusive
        );

        foreach ($period as $dt) {
            $dayStr = $dt->format('Y-m-d');
            $avRow  = \App\Models\SubCategoryAvailability::where('sub_category_id', $subCatId)
                        ->where('date', $dayStr)
                        ->first();

            // If row found => check if is_unavailable or slots <= 0
            if ($avRow) {
                if ($avRow->is_unavailable || $avRow->slots <= 0) {
                    return back()->with('error', "Date {$dayStr} is blocked or has 0 slots.")
                                 ->withInput();
                }
            }

            // Check how many existing bookings overlap this date
            $existingCount = \App\Models\Booking::where('sub_category_id', $subCatId)
                ->whereIn('status', ['pending','successful'])
                ->where('id','!=',$booking->id) // exclude this current booking
                ->where('start_date','<=',$dayStr)
                ->where('end_date','>',$dayStr)
                ->count();

            // If no row -> treat as 1 slot by default
            $slotsForDay = $avRow ? $avRow->slots : 1;
            if ($existingCount >= $slotsForDay) {
                return back()->with('error', "Date {$dayStr} is fully booked. No slot left.")
                             ->withInput();
            }
        }
    }

    // 10) Apply new dates, nights
    $booking->start_date = $start->format('Y-m-d');
    $booking->end_date   = $end->format('Y-m-d');
    $booking->nights     = $nights;

    // 11) Recalculate total
    $subCat = \App\Models\SubCategory::findOrFail($booking->sub_category_id);
    $booking->price        = $subCat->price ?? 0;
    $booking->total_amount = $booking->price * $nights;

    // 12) Save booking changes so total_amount is updated in DB
    $booking->save();

    // 13) Sync additional guests
    //    (If you have a separate booking_guests table)
    $existingGuestRecords = \DB::table('booking_guests')
        ->where('booking_id', $booking->id)
        ->get()
        ->keyBy('id'); // key by ID for easy lookups

    $updatedIds = [];
    if ($request->extra_guests) {
        $guestsArr = json_decode($request->extra_guests, true) ?: [];
        foreach ($guestsArr as $g) {
            if (!empty($g['id'])) {
                // Update existing row
                \DB::table('booking_guests')
                    ->where('id', $g['id'])
                    ->where('booking_id', $booking->id)
                    ->update([
                        'name'       => $g['name']    ?? '',
                        'dob'        => !empty($g['dob']) ? $g['dob'] : null,
                        'phone'      => $g['phone']   ?? '',
                        'address'    => $g['address'] ?? '',
                        'updated_at' => now(),
                    ]);
                $updatedIds[] = $g['id'];
            } else {
                // Insert new row
                $newId = \DB::table('booking_guests')->insertGetId([
                    'booking_id' => $booking->id,
                    'name'       => $g['name']    ?? '',
                    'dob'        => !empty($g['dob']) ? $g['dob'] : null,
                    'phone'      => $g['phone']   ?? '',
                    'address'    => $g['address'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $updatedIds[] = $newId;
            }
        }
    }
    // Delete removed guests
    foreach ($existingGuestRecords as $oldId => $oldRecord) {
        if (!in_array($oldId, $updatedIds)) {
            \DB::table('booking_guests')
                ->where('id', $oldId)
                ->delete();
        }
    }

    // 14) Check if the new total is bigger than old total
    $difference = $booking->total_amount - $originalTotal;  // could be negative if new is cheaper

    if ($difference > 0) {
        // A) If it was already fully paid (status=successful), user owes this new difference
        if ($booking->status === 'successful') {
            // Store how much extra they still owe in difference_due
            $booking->difference_due = $difference;
            $booking->save();

            // Redirect user to payment page to pay the extra
            return redirect()
                ->route('payment.confirm', $booking->id)
                ->with('success', "Booking updated! You now owe an additional ₦{$difference}.");
        } 
        // B) If status is pending, user hasn't fully paid anything yet
        //    => entire new total is the amount they must pay eventually
        else {
            // They still owe everything, so difference_due can be 0
            // because the PaymentController will show total_amount minus 
            // any partial payments that exist
            $booking->difference_due = 0;
            $booking->save();

            // Redirect them to pay
            return redirect()
                ->route('payment.confirm', $booking->id)
                ->with('success', "Booking updated! Your new total is ₦{$booking->total_amount}.");
        }
    }

    // If difference <= 0, no extra to pay
    // Possibly set difference_due to 0 if new total is cheaper or equal
    $booking->difference_due = 0;
    $booking->save();

    // Finally, redirect (if staff, go back to staff list, or if client, go back to client list)
    if (auth()->user()->isStaff()) {
        return redirect()
            ->route('bookings.staff_change')
            ->with('success','Booking updated successfully! No additional payment required.');
    } else {
        return redirect()
            ->route('bookings.client_change')
            ->with('success','Booking updated successfully! No additional payment required.');
    }
}





    /**
     * Delete an existing booking (only for staff).
     */
    public function destroy(Booking $booking)
    {
        if (!auth()->user()->isStaff()) {
            abort(403, 'Unauthorized');
        }
        $booking->delete();
        return redirect()->route('bookings.staff_change')
            ->with('success', 'Booking deleted successfully.');
    }
    /**
     * Show the booking form (with categories, subcategories, etc.)
     */
    public function create()
    {
        // 1) Get all categories to display in the "Apartment Type" (category) select
        $categories = Category::all();

        // 2) Get all subcategories (with images) — we’ll map them into an array
        //    so the Blade can use them as "apartmentsData" or similar.
        $subCategories = SubCategory::with('images')->get();

        // Convert subcategories => JSON-like array
        $apartmentsJson = $subCategories->map(function($sub) {
            return [
                'id'              => $sub->id,
                'name'            => $sub->name,
                'num_rooms'       => $sub->num_rooms,
                'num_toilets'     => $sub->num_toilets,
                'num_sittingrooms'=> $sub->num_sittingrooms,
                'num_kitchens'    => $sub->num_kitchens,
                'num_balconies'   => $sub->num_balconies,
                'free_wifi'       => (bool) $sub->free_wifi,
                'water'           => (bool) $sub->water,
                'electricity'     => (bool) $sub->electricity,
                'price'           => $sub->price,
                'max_slots'       => $sub->max_slots,       // if you have a max_slots column
                'additional_info' => $sub->additional_info,

                // Images relationship => array of { image_path: "..." }
                'images' => $sub->images->map(function($img) {
                    return [
                        'id'          => $img->id,
                        'image_path'  => asset('storage/' . $img->image_path), 
                    ];
                })->all(),
            ];
        });

        // 3) If staff => you want the list of clients to populate the "Select Client" dropdown
        $clients = User::where('role', 'client')->get();

        // 4) Return the booking view
        return view('dashboard.book_apartment', [
            'categories'     => $categories,
            'apartmentsJson' => $apartmentsJson,
            'clients'        => $clients
        ]);
    }

    /**
     * Return existing bookings for a subcategory (used by FullCalendar).
     * Example route: GET /api/subcategories/{id}/calendar
     */
    public function getSubCategoryBookings($id)
    {
        $bookings = Booking::where('sub_category_id', $id)
                           ->whereIn('status', ['pending','successful'])
                           ->get();

        // Map bookings to FullCalendar events
        $events = $bookings->map(function($b) {
            $bgColor = ($b->status === 'pending') ? 'orange' : 'red';
            $textCol = ($b->status === 'pending') ? 'black'  : 'white';

            // FullCalendar uses an "exclusive end" date,
            // so if your logic says checkout is on the end_date day, you may want addDay().
            return [
                'title'            => "Booked ({$b->status})",
                'start'            => $b->start_date,
                'end'              => $b->end_date, 
                'backgroundColor'  => $bgColor,
                'textColor'        => $textCol,
                'allDay'           => true,
            ];
        });

        return response()->json(['events' => $events]);
    }
    public function showSubCategory($subCatId)
    {
        // 1) Eager-load images so we can send them all
        $subCat = SubCategory::with('images')->findOrFail($subCatId);

        // 2) Build an array with any fields your JS needs
        $data = [
            'id'              => $subCat->id,
            'name'            => $subCat->name,
            'num_rooms'       => $subCat->num_rooms,
            'num_toilets'     => $subCat->num_toilets,
            'num_sittingrooms'=> $subCat->num_sittingrooms,
            'num_kitchens'    => $subCat->num_kitchens,
            'num_balconies'   => $subCat->num_balconies,
            'free_wifi'       => (bool)$subCat->free_wifi,
            'water'           => (bool)$subCat->water,
            'electricity'     => (bool)$subCat->electricity,
            'max_slots'       => $subCat->max_slots,
            'price'           => $subCat->price,
            'additional_info' => $subCat->additional_info,

            // Convert each related image to an object with 'image_path'
    
'images' => $subCat->images->map(function($img) {
    return [
        'id'          => $img->id,
        'image_path'  => asset($img->image_path),
    ];
})->all()


        ];

        return response()->json($data);
    }

    /**
     * (B) Return events for FullCalendar combining:
     *     - Bookings (pending/successful)
     *     - Unavailable days (is_unavailable=1 or slots=0)
     */
 
public function fetchCalendar($subCatId)
{
    // 1) Replace this:
    //    $availabilities = \App\Models\ApartmentAvailability::where('apartment_id', $id)->get();
    //    $bookings = \App\Models\Booking::where('apartment_id', $id) ...->get();

    // 2) With this (using SubCategoryAvailability + sub_category_id):
    $availabilities = \App\Models\SubCategoryAvailability::where('sub_category_id', $subCatId)->get();
    $bookings = \App\Models\Booking::where('sub_category_id', $subCatId)
        ->whereIn('status', ['pending','successful'])
        ->get();

    $events = [];

    // Availability => show "Unavailable" if is_unavailable=1 or slots=0,
    // otherwise color-coded "Slots: X".
    foreach ($availabilities as $av) {
        if ($av->is_unavailable || $av->slots <= 0) {
            $events[] = [
                'title'           => 'Unavailable',
                'start'           => $av->date,
                'end'             => $av->date, // same day
                'backgroundColor' => 'black',
                'textColor'       => 'white',
                'allDay'          => true,
            ];
        } else {
            $events[] = [
                'title'           => "Slots: {$av->slots}",
                'start'           => $av->date,
                'end'             => $av->date,
                'backgroundColor' => 'green',
                'textColor'       => 'white',
                'allDay'          => true,
            ];
        }
    }

    // Bookings => block out the date range in orange (pending) or red (successful)
    foreach ($bookings as $b) {
        $bg = ($b->status === 'pending') ? 'orange' : 'red';
        $txt= ($b->status === 'pending') ? 'black'  : 'white';

        $events[] = [
            'title'           => "Booked ({$b->status})",
            'start'           => $b->start_date,
            'end'             => $b->end_date, // exclusive end
            'backgroundColor' => $bg,
            'textColor'       => $txt,
            'allDay'          => true,
        ];
    }

    return response()->json(['events' => $events]);
}

    /**
     * (C) If you want a route to fetch subcategories by category
     */
    public function fetchSubCategories($catId)
    {
        $subcats = SubCategory::where('category_id', $catId)
            ->select('id','name','price') // or more columns if needed
            ->get();

        return response()->json($subcats);
    }


    public function store(Request $request)
    {
        // 1) Validate request
        $request->validate([
            'booking_type'    => 'nullable|string', // 'guest' or 'client'
            'sub_category_id' => 'required|integer',
    
            'start_date'      => 'required|date|after_or_equal:today',
            'end_date'        => 'required|date|after:start_date',
    
            // Guest info
            'guest_name'      => 'nullable|string',
            'guest_email'     => 'nullable|email',
            'guest_address'   => 'nullable|string',
            'guest_phone'     => 'nullable|string',
            'guest_dob'       => 'nullable|date',
    
            // Document
            'doc_type'        => 'nullable|string',
            'doc_number'      => 'nullable|string',
            'doc_upload'      => 'nullable|file|max:2048',
    
            // If staff selected an existing client
            'client_id'       => 'nullable|integer',
    
            // Additional guests JSON
            'extra_guests'    => 'nullable',
        ]);
    
        // 2) Determine staff vs client
        $staffId    = (auth()->check() && auth()->user()->isStaff()) ? auth()->id() : null;
        $clientId   = null;
        $clientName = null;
        $guestName  = null;
    
        // Decide the booking "owner" before creating the record
        if ($staffId && $request->booking_type === 'client') {
            // Staff is making a booking for a known client
            $client = \App\Models\User::findOrFail($request->client_id);
            $clientId   = $client->id;
            $clientName = $client->name;
            $guestName  = null;
        } elseif ($staffId && $request->booking_type === 'guest') {
            // Staff is making a booking for a walk-in guest
            $clientId   = null;
            $clientName = null;
            $guestName  = $request->guest_name;
        } else {
            // A logged-in client making their own booking
            $clientId   = auth()->id();
            $clientName = auth()->user()->name;
            $guestName  = null;
        }
    
        // 3) Handle optional doc upload
        $docPath = null;
        if ($request->hasFile('doc_upload')) {
            $docPath = $request->file('doc_upload')->store('documents', 'public');
        }
    
        // 4) Subcategory & date range
        $subCatId = $request->sub_category_id;
        $start    = \Carbon\Carbon::parse($request->start_date);
        $end      = \Carbon\Carbon::parse($request->end_date);
    
        // Calculate the number of nights
        $nights = $start->diffInDays($end);
        if ($nights < 1) {
            $nights = 1;
        }
    
        // Retrieve the subcategory to get its price
        $subCat = \App\Models\SubCategory::findOrFail($subCatId);
        $pricePerNight = $subCat->price ?? 0;
        $totalAmount   = $pricePerNight * $nights;
    
        // 5) Check each day in the range for availability
        $period = new \DatePeriod(
            new \DateTime($start->format('Y-m-d')),
            new \DateInterval('P1D'),
            new \DateTime($end->format('Y-m-d')) // exclusive
        );

        foreach ($period as $dt) {
            $dayStr = $dt->format('Y-m-d');

            $avRow = SubCategoryAvailability::where('sub_category_id', $subCatId)
                      ->where('date', $dayStr)
                      ->first();

            if ($avRow) {
                if ($avRow->is_unavailable || $avRow->slots <= 0) {
                    return redirect()->route('book.apartment')
                        ->with('error', "Date $dayStr is blocked or has 0 slots. Cannot book.");
                }
                $slotsForDay = $avRow->slots;
            } else {
                // NEW: default 1 slot if no row
                $slotsForDay = 1;
            }

            // Count existing bookings
            $existingCount = Booking::where('sub_category_id', $subCatId)
                ->whereIn('status', ['pending','successful'])
                ->where('start_date','<=',$dayStr)
                ->where('end_date','>',$dayStr)
                ->count();

            if ($existingCount >= $slotsForDay) {
                return redirect()->route('book.apartment')
                    ->with('error', "Date $dayStr is fully booked. No slot left.");
            }
        }
    
        // 6) Create the booking using the variables we decided above
        $booking = \App\Models\Booking::create([
            'staff_id'       => $staffId,
            'client_id'      => $clientId,
            'client_name'    => $clientName,    // Make sure your 'bookings' table has a client_name column
            'guest_name'     => $guestName,
            'guest_email'    => $request->guest_email,
            'guest_address'  => $request->guest_address,
            'guest_phone'    => $request->guest_phone,
            'guest_dob'      => $request->guest_dob,
            'doc_type'       => $request->doc_type,
            'doc_number'     => $request->doc_number,
            'doc_upload'     => $docPath,
    
            'sub_category_id'=> $subCatId,
            'start_date'     => $start->format('Y-m-d'),
            'end_date'       => $end->format('Y-m-d'),
            'nights'         => $nights,
            'status'         => 'pending',
    
            'price'          => $pricePerNight,
            'total_amount'   => $totalAmount,
        ]);

        UserActivity::create([
            'user_id'     => auth()->id(),
            'type'        => 'booking',
            'description' => 'Booked apartment #'.$booking->id.' for date range ...'
          ]);
    // If it's pending, we want to set it to expire in 1 hour:
$booking->pending_expires_at = now()->addHour();
$booking->save();

        // 7) Insert additional guests if needed
        if ($request->extra_guests) {
            $guestsArr = json_decode($request->extra_guests, true) ?: [];
            foreach ($guestsArr as $g) {
                \DB::table('booking_guests')->insert([
                    'booking_id' => $booking->id,
                    'name'       => $g['name']    ?? '',
                    'dob'        => !empty($g['dob']) ? $g['dob'] : null,
                    'phone'      => $g['phone']   ?? '',
                    'address'    => $g['address'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    
        // 8) Redirect
        return redirect()->route('booking.payment', $booking->id)
            ->with('success', 'Booking created! Proceed to payment.');
    }
    
    
}

