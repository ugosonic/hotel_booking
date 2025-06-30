<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;   // for DB::table() insert
use Illuminate\Support\Facades\Auth;

class ApartmentBookingController extends Controller
{
    public function create()
    {
        $apartments = Apartment::all();

        // JSON for blade
        $apartmentsJson = $apartments->map(function($apt){
            return [
                'id'                => $apt->id,
                'apartment_name'    => $apt->apartment_name,
                'has_rooms'         => (bool) $apt->has_rooms,
                'num_rooms'         => $apt->num_rooms,
                'has_toilets'       => (bool) $apt->has_toilets,
                'num_toilets'       => $apt->num_toilets,
                'has_sittingroom'   => (bool) $apt->has_sittingroom,
                'num_sittingrooms'  => $apt->num_sittingrooms,
                'has_kitchen'       => (bool) $apt->has_kitchen,
                'num_kitchens'      => $apt->num_kitchens,
                'has_balcony'       => (bool) $apt->has_balcony,
                'num_balconies'     => $apt->num_balconies,
                'free_wifi'         => (bool) $apt->free_wifi,
                'water'             => (bool) $apt->water,
                'electricity'       => (bool) $apt->electricity,
                'price'             => $apt->price,
                'additional_info'   => $apt->additional_info,
            ];
        });

        // If staff: fetch clients
        $clients = \App\Models\User::where('role','client')->get();

        return view('dashboard.book_apartment', [
            'apartments'     => $apartments,
            'apartmentsJson' => $apartmentsJson,
            'clients'        => $clients
        ]);
    }

    /**
     * Return the existing bookings (pending or successful) as JSON for FullCalendar
     */
    public function getBookings($id)
    {
        $bookings = Booking::where('apartment_id',$id)
                           ->whereIn('status',['pending','successful'])
                           ->get();

        $events = $bookings->map(function($b){
            $bgColor = $b->status==='pending' ? 'orange' : 'red';
            $textCol = $b->status==='pending' ? 'black'  : 'white';
            // FullCalendar expects an "exclusive" end date
            // So if booking ends 2025-02-10, that day is checkout => not blocked
            // If you want to block the entire day of the 10th, do addDay().
            return [
                'title' => "Booked ($b->status)",
                'start' => $b->start_date,
                'end'   => $b->end_date,  // exclusive
                'backgroundColor' => $bgColor,
                'textColor'       => $textCol,
                'allDay'          => true
            ];
        });
        return response()->json(['bookings'=>$events]);
    }

    /**
     * Store the booking
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_type'   => 'nullable|string', // 'guest' or 'client'
            'apartment_id'   => 'required|integer',
            'start_date'     => 'required|date|after_or_equal:today',
            'end_date'       => 'required|date|after:start_date',
            'guest_name'     => 'nullable|string',
            'guest_email'    => 'nullable|email',
            'guest_address'  => 'nullable|string',
            'guest_phone'    => 'nullable|string',
            'guest_dob'      => 'nullable|date',
            'doc_type'       => 'nullable|string',
            'doc_number'     => 'nullable|string',
            'doc_upload'     => 'nullable|file|max:2048',
            'client_id'      => 'nullable|integer',
            'extra_guests'   => 'nullable',
        ]);

        // Staff vs. client
        $staffId  = (Auth::check() && Auth::user()->isStaff()) ? Auth::id() : null;
        $clientId = null;
        $guestName= null;

        if($staffId && $request->booking_type === 'client') {
            // staff picked an existing client
            $clientId = $request->client_id;
        }
        elseif($staffId && $request->booking_type === 'guest') {
            // staff => walkin guest
            $guestName = $request->guest_name;
        }
        else {
            // normal client user
            $clientId  = Auth::id();
            $guestName = Auth::user()->name;
        }

        // doc upload
        $docPath = null;
        if($request->hasFile('doc_upload')) {
            $docPath = $request->file('doc_upload')->store('documents','public');
        }

        // Overlap check?
        // For brevity, we skip or you can do:
        // if(Booking::where('apartment_id',$request->apartment_id)->where(...)->exists()){...}

        // Calculate nights
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $nights= $start->diffInDays($end);
        if($nights<1){ $nights=1; } // fallback

        // Create main booking
        $booking = Booking::create([
            'staff_id'      => $staffId,
            'client_id'     => $clientId,
            'guest_name'    => $guestName,
            'guest_email'   => $request->guest_email,
            'guest_address' => $request->guest_address,
            'guest_phone'   => $request->guest_phone,
            'guest_dob'     => $request->guest_dob,
            'doc_type'      => $request->doc_type,
            'doc_number'    => $request->doc_number,
            'doc_upload'    => $docPath,
            'apartment_id'  => $request->apartment_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'nights'        => $nights,
            'status'        => 'pending'
        ]);

        // Additional guests from JSON
        if($request->extra_guests) {
            $guestsArr = json_decode($request->extra_guests,true) ?: [];
            foreach($guestsArr as $g) {
                DB::table('booking_guests')->insert([
                    'booking_id' => $booking->id,
                    'name'       => $g['name']    ?? '',
                    'dob'        => $g['dob']     ?? null,
                    'phone'      => $g['phone']   ?? '',
                    'address'    => $g['address'] ?? '',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return redirect()->route('booking.payment',$booking->id)
                         ->with('success','Booking created! Proceed to payment.');
    }
}
