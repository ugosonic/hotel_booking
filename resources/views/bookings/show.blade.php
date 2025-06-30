@extends('layouts.app')

@section('page-heading', "Booking #{$booking->id} Details")

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded shadow p-6">

  <h2 class="text-2xl font-bold mb-4">
    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
    Booking Details
  </h2>

  <div class="space-y-3 text-gray-700 text-sm">

    <!-- ID & Status -->
    <p>
      <strong>Booking ID:</strong> {{ $booking->id }}
      @if($booking->status)
        <span class="ml-2 inline-block px-2 py-1 text-white text-xs rounded 
                    @if($booking->status==='pending') bg-yellow-500 
                     @elseif($booking->status==='successful') bg-green-600 
                     @else bg-gray-500 @endif">
          {{ ucfirst($booking->status) }}
        </span>
      @endif
    </p>

    <!-- staff_id / client_id to show Booked By? (we might replicate logic from index) -->
    @php
      $staffName = null;
      if($booking->staff_id){
          $u = \App\Models\User::find($booking->staff_id);
          $staffName = $u ? $u->name : "Staff #{$booking->staff_id}";
      }
    @endphp

    <p>
      <strong>Booked By:</strong>
      @if($staffName)
        {{ $staffName }}
      @else
        Client or Self
      @endif
    </p>

    <!-- If client_name is not null -->
    @if($booking->client_name)
    <p>
      <i class="fas fa-user text-gray-500 mr-1"></i>
      <strong>Client Name:</strong> {{ $booking->client_name }}
    </p>
    @endif

    <!-- If guest_name is not null -->
    @if($booking->guest_name)
    <p>
      <i class="fas fa-user text-gray-500 mr-1"></i>
      <strong>Guest Name:</strong> {{ $booking->guest_name }}
    </p>
    @endif

    @if($booking->guest_email)
    <p>
      <i class="fas fa-envelope text-gray-500 mr-1"></i>
      <strong>Email:</strong> {{ $booking->guest_email }}
    </p>
    @endif

    @if($booking->guest_phone)
    <p>
      <i class="fas fa-phone text-gray-500 mr-1"></i>
      <strong>Phone:</strong> {{ $booking->guest_phone }}
    </p>
    @endif

    @if($booking->guest_address)
    <p>
      <i class="fas fa-map-marker-alt text-gray-500 mr-1"></i>
      <strong>Address:</strong> {{ $booking->guest_address }}
    </p>
    @endif

    @if($booking->guest_dob)
    <p>
      <i class="fas fa-birthday-cake text-gray-500 mr-1"></i>
      <strong>DOB:</strong> {{ $booking->guest_dob }}
    </p>
    @endif

    @if($booking->doc_type || $booking->doc_number)
    <p>
      <i class="fas fa-id-card text-gray-500 mr-1"></i>
      <strong>Document:</strong> 
      {{ $booking->doc_type ?? '' }}
      @if($booking->doc_number)
        (#{{ $booking->doc_number }})
      @endif
    </p>
    @endif

    @if($booking->doc_upload)
    <p>
      <i class="fas fa-file-upload text-gray-500 mr-1"></i>
      <strong>Document Upload:</strong> 
      <a href="{{ asset('storage/'.$booking->doc_upload) }}" target="_blank" 
         class="text-blue-600 underline">
         View File
      </a>
    </p>
    @endif

    <!-- Start/End Date in different color -->
    <p>
      <strong>Start Date:</strong> 
      <span class="text-green-600 font-semibold">
        {{ $booking->start_date }}
      </span>
      <strong class="ml-4">End Date:</strong> 
      <span class="text-red-600 font-semibold">
        {{ $booking->end_date }}
      </span>
    </p>

    <p>
      <strong>Nights:</strong> {{ $booking->nights }}
    </p>

    <!-- Price & total_amount -->
    <p>
      <strong>Price per night:</strong> ₦{{ number_format($booking->price,2) }}
      <br>
      <strong>Total Amount:</strong> ₦{{ number_format($booking->total_amount,2) }}
    </p>

    <!-- If there's a subCategory relationship => show apartment details -->
    @if($booking->subCategory)
      <hr class="my-3">
      <h3 class="text-lg font-semibold">
        <i class="fas fa-building text-gray-500 mr-1"></i>
        Apartment Details
      </h3>
      <p><strong>Name:</strong> {{ $booking->subCategory->name }}</p>
      <!-- Example features: -->
      @php
        $features = [];
        if($booking->subCategory->num_rooms) {
          $features[] = $booking->subCategory->num_rooms.' room(s)';
        }
        if($booking->subCategory->num_toilets) {
          $features[] = $booking->subCategory->num_toilets.' toilet(s)';
        }
        if($booking->subCategory->free_wifi) {
          $features[] = 'Free WiFi';
        }
        // etc...
      @endphp

      @if(count($features))
        <p class="mt-2">
          <strong>Features:</strong> {{ implode(', ',$features) }}
        </p>
      @endif
    @endif

    <!-- Payment History -->
    <hr class="my-3">
    <h3 class="text-lg font-semibold">
      <i class="fas fa-history text-gray-500 mr-1"></i>
      Payment History
    </h3>
    @php
      $payments = \App\Models\Payment::where('booking_id',$booking->id)
                  ->orderBy('id','desc')->get();
    @endphp
    @if($payments->count() < 1)
      <p>No payments yet.</p>
    @else
      <table class="w-full text-xs mt-2 border">
        <thead class="bg-gray-100 text-left">
          <tr>
            <th class="p-2">Date</th>
            <th class="p-2">Method</th>
            <th class="p-2">Amount</th>
            <th class="p-2">Status</th>
            <th class="p-2">Reference</th>
          </tr>
        </thead>
        <tbody>
          @foreach($payments as $pay)
            <tr class="border-b">
              <td class="p-2">{{ $pay->created_at->format('Y-m-d H:i') }}</td>
              <td class="p-2">{{ $pay->payment_method }}</td>
              <td class="p-2">₦{{ number_format($pay->amount,2) }}</td>
              <td class="p-2">
                <span class="@if($pay->status==='successful') text-green-600 
                             @else text-gray-600 @endif">
                  {{ ucfirst($pay->status) }}
                </span>
              </td>
              <td class="p-2">{{ $pay->reference }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif

  </div><!-- end space-y-3 -->

</div>
@endsection
