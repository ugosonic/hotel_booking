<!-- resources/views/dashboard/client_dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Client Dashboard</h2>

     <!-- Colorful container for Account Balance -->
     <div class="p-6 mb-6 rounded-lg text-white" 
         style="background: linear-gradient(to right, #4e54c8, #8f94fb);">
        <h3 class="text-xl font-semibold">Your Account Balance</h3>
        <p class="text-2xl mt-2">
        ₦ {{ number_format($balance, 2) }}
        </p>
        <!-- top up link or button -->
        <a href="{{ route('topup.form') }}"
           class="inline-block bg-white text-indigo-600 font-bold px-4 py-2 mt-4 rounded shadow hover:bg-gray-200">
           Top Up Payment
        </a>
    </div>
    
  <!-- Show the current booking info IF $currentBooking is not null -->
  @if(isset($currentBooking))
    <div class="mt-12 bg-white shadow p-6 rounded">
        <h3 class="text-xl font-bold mb-4">Your Current Booking</h3>

        <div class="flex flex-col md:flex-row md:space-x-6">
            <!-- Image Slider -->
            @php
                $images = $currentBooking->subCategory->images ?? [];
            @endphp
            <div class="md:w-1/2">
                @if(count($images) > 0)
                    <div class="relative w-full h-64 overflow-hidden rounded" id="bookingSlider">
                        <div id="sliderTrack" class="flex transition-transform duration-300">
                            @foreach($images as $img)
                              <div class="w-full flex-shrink-0">
                                <img src="{{ asset($img->image_path) }}"
                                     alt="Apartment Image"
                                     class="w-full h-64 object-cover" />
                              </div>
                            @endforeach
                        </div>
                        <!-- Arrows (only if multiple images) -->
                        @if(count($images) > 1)
                        <button id="prevBtn"
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 
                                       bg-black bg-opacity-50 text-white px-2 py-1 rounded">
                            ‹
                        </button>
                        <button id="nextBtn"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 
                                       bg-black bg-opacity-50 text-white px-2 py-1 rounded">
                            ›
                        </button>
                        @endif
                    </div>
                @else
                    <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-500">
                        No images available
                    </div>
                @endif
            </div>

            <!-- Booking Details -->
            <div class="md:w-1/2 mt-4 md:mt-0">
            <p><strong>Apartment:</strong> 
   {{ optional($currentBooking->subCategory)->name ?? 'N/A' }}
</p>
                <p><strong>Start Date:</strong> 
                   <span class="text-blue-600">{{ $currentBooking->start_date }}</span>
                </p>
                <p><strong>End Date:</strong> 
                   <span class="text-purple-600">{{ $currentBooking->end_date }}</span>
                   <small class="text-gray-500">(Checkout by 12pm)</small>
                </p>
                @php
                    $start = \Carbon\Carbon::parse($currentBooking->start_date);
                    $end   = \Carbon\Carbon::parse($currentBooking->end_date);
                    $nights = $start->diffInDays($end);
                    if ($nights < 1) $nights = 1;
                @endphp
                <p><strong>Nights:</strong> {{ $nights }}</p>
                
                <!-- Payment reference (most recent) -->
                @php
                    $latestPayment = $currentBooking->payments->sortByDesc('created_at')->first();
                @endphp
                @if($latestPayment)
                    <p><strong>Payment Reference:</strong> {{ $latestPayment->reference }}</p>
                @else
                    <p class="text-sm text-gray-500">No payment reference found.</p>
                @endif

                <!-- Additional Info if needed -->
                <p class="mt-2 text-sm text-gray-700">
                <strong>Features:</strong>
@if(optional($currentBooking->subCategory)->num_rooms)
  {{ optional($currentBooking->subCategory)->num_rooms }} room(s),
@endif

@if(optional($currentBooking->subCategory)->free_wifi)
  Free WiFi,
@endif
                   ... etc ...
                </p>
            </div>
        </div>
    </div>
    @endif
</div>


<!-- Existing code for "Current Booking" ... -->

   <!-- NEXT PENDING BOOKING -->
    @if(isset($nextPendingBooking) && $nextPendingBooking)
    <div class="mt-12 bg-white shadow p-6 rounded">
        <h3 class="text-xl font-bold mb-4">Your Next Pending Booking</h3>

        <div class="flex flex-col md:flex-row md:space-x-6">
            <!-- Pending Booking Images -->
            @php
                $pendingImages = $nextPendingBooking->subCategory->images ?? [];
            @endphp
            <div class="md:w-1/2">
                @if(count($pendingImages) > 0)
                    <div class="relative w-full h-64 overflow-hidden rounded" id="nextBookingSlider">
                        <div id="nextSliderTrack" class="flex transition-transform duration-300">
                            @foreach($pendingImages as $img)
                                <div class="w-full flex-shrink-0">
                                    <img src="{{ asset($img->image_path) }}"
                                         alt="Apartment Image"
                                         class="w-full h-64 object-cover" />
                                </div>
                            @endforeach
                        </div>
                        @if(count($pendingImages) > 1)
                        <button id="nextPrevBtn"
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 
                                       bg-black bg-opacity-50 text-white px-2 py-1 rounded">
                            ‹
                        </button>
                        <button id="nextNextBtn"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 
                                       bg-black bg-opacity-50 text-white px-2 py-1 rounded">
                            ›
                        </button>
                        @endif
                    </div>
                @else
                    <div class="w-full h-64 bg-gray-100 flex items-center justify-center text-gray-500">
                        No images available
                    </div>
                @endif
            </div>

            <!-- Pending Booking Details -->
            <div class="md:w-1/2 mt-4 md:mt-0">
                <p><strong>Apartment:</strong> 
                    {{ $nextPendingBooking->subCategory->name ?? 'N/A' }}
                </p>
                <p><strong>Start Date:</strong> 
                    <span class="text-blue-600">{{ $nextPendingBooking->start_date }}</span>
                </p>
                <p><strong>End Date:</strong> 
                    <span class="text-purple-600">{{ $nextPendingBooking->end_date }}</span>
                    <small class="text-gray-500">(Checkout by 12pm)</small>
                </p>
                @php
                    $pStart  = \Carbon\Carbon::parse($nextPendingBooking->start_date);
                    $pEnd    = \Carbon\Carbon::parse($nextPendingBooking->end_date);
                    $pNights = $pStart->diffInDays($pEnd);
                    if ($pNights < 1) $pNights = 1;

                    // Payment info for pending booking
                    $pendingPaid = $nextPendingBooking->payments
                                   ->where('status','successful')
                                   ->sum('amount');
                    $pendingDue  = $nextPendingBooking->total_amount - $pendingPaid;
                    if ($pendingDue < 0) $pendingDue = 0;
                @endphp
                <p><strong>Nights:</strong> {{ $pNights }}</p>
                <p><strong>Price Per Night:</strong> ₦{{ number_format($nextPendingBooking->price,2) }}</p>
                <p><strong>Total Amount:</strong> ₦{{ number_format($nextPendingBooking->total_amount,2) }}</p>
                <p><strong>Amount Paid:</strong> ₦{{ number_format($pendingPaid,2) }}</p>
                <p><strong>Amount Due:</strong> ₦{{ number_format($pendingDue,2) }}</p>

                

                <!-- Countdown Timer if pending_expires_at is set -->
                @if($nextPendingBooking->pending_expires_at)
                <p class="mt-2">
                  <strong>Time Left to Pay:</strong>
                  <span id="pendingCountdown" class="text-red-600 ml-2"></span>
                </p>
                @endif

                <!-- Action Buttons: Pay Now, Cancel -->
                <div class="mt-4 flex space-x-2">
                    <!-- "Pay Now" => booking.payment -->
                    <a href="{{ route('payment.confirm', $nextPendingBooking->id) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                       Pay Now
                    </a>
                    <!-- Cancel booking form -->
                    <form action="{{ route('bookings.destroy', $nextPendingBooking->id) }}"
                          method="POST"
                          onsubmit="return confirm('Cancel this pending booking?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Countdown + Slider Script for next pending -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Pending slider
        const nextTrack = document.getElementById('nextSliderTrack');
        if (nextTrack) {
            let idx = 0;
            const slides = nextTrack.children.length;
            const prevBtn = document.getElementById('nextPrevBtn');
            const nextBtn = document.getElementById('nextNextBtn');

            function updatePosition() {
                nextTrack.style.transform = `translateX(-${idx * 100}%)`;
            }
            if (prevBtn && nextBtn) {
                prevBtn.addEventListener('click', () => {
                    if (idx > 0) {
                        idx--;
                        updatePosition();
                    }
                });
                nextBtn.addEventListener('click', () => {
                    if (idx < slides - 1) {
                        idx++;
                        updatePosition();
                    }
                });
            }
        }

        // Countdown
        @if($nextPendingBooking->pending_expires_at)
          const expiresAt = new Date("{{ $nextPendingBooking->pending_expires_at }}").getTime();
          const countdownEl = document.getElementById('pendingCountdown');
          if (countdownEl) {
            const interval = setInterval(() => {
              const now = new Date().getTime();
              const diff = expiresAt - now;
              if (diff <= 0) {
                clearInterval(interval);
                countdownEl.textContent = "Expired!";
              } else {
                const mins = Math.floor(diff / 1000 / 60);
                const secs = Math.floor((diff / 1000) % 60);
                countdownEl.textContent = mins + "m : " + secs + "s";
              }
            }, 1000);
          }
        @endif
    });
    </script>
    @endif
    <!-- End Next Pending Booking -->

    <!-- Add some spacing before the grid icons -->
    <div class="mt-12"></div>



    <!-- Grid of big icons/links -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        
        <!-- Book Apartment -->
        <a href="{{ route('book.apartment') }}" 
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <!-- Example icon: Using a Tailwind or Heroicon placeholder -->
            <svg class="h-12 w-12 text-purple-600 mb-2" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <!-- icon path -->
              <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2h-4a2 2..."/>
            </svg>
            <span class="font-semibold">Book Apartment</span>
        </a>

        <!-- Change Booking -->
        <a href="{{ route('bookings.client_change') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-600 mb-2" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
              <!-- icon path -->
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
            </svg>
            <span class="font-semibold">Change Booking</span>
        </a>
      
        <!-- View Bookings -->
        <a href="{{ route('bookings.index') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg class="h-12 w-12 text-green-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"> <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
            </svg>
            <span class="font-semibold">View Bookings</span>
        </a>
    
        <!-- Cancel Bookings -->
        <a href="{{ route('booking.cancel') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg class="h-12 w-12 text-red-600 mb-2" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold">Cancel Bookings</span>
        </a>
       
<!-- Payment History link -->
<a href="{{ route('client.payment_history') }}"
   class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
    <svg class="h-12 w-12 text-indigo-600 mb-2" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
      <!-- path -->
      <path d="M2 4h20M2 10h20M2 16h20" /> 
    </svg>
    <span class="font-semibold">Payment History</span>
</a>

        <!-- Payment Settings -->
        <a href="{{ route('payment.settings') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg class="h-12 w-12 text-yellow-600 mb-2"  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
  <path  d="M12 6.75a5.25 5.25 0 0 1 6.775-5.025.75.75 0 0 1 .313 1.248l-3.32 3.319c.063.475.276.934.641 1.299.365.365.824.578 1.3.64l3.318-3.319a.75.75 0 0 1 1.248.313 5.25 5.25 0 0 1-5.472 6.756c-1.018-.086-1.87.1-2.309.634L7.344 21.3A3.298 3.298 0 1 1 2.7 16.657l8.684-7.151c.533-.44.72-1.291.634-2.309A5.342 5.342 0 0 1 12 6.75ZM4.117 19.125a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75h-.008a.75.75 0 0 1-.75-.75v-.008Z" clip-rule="evenodd" />
</svg>
</svg>
            <span class="font-semibold">Payment Settings</span>
        </a>
    </div>

    <!-- Link to Settings page -->
    <div class="mt-8">
        <a href="{{ route('client.settings') }}" 
           class="inline-block bg-purple-600 text-white px-6 py-2 rounded hover:bg-purple-700 transition">
            Go to Settings
        </a>
    </div>
</div>

<!-- Simple slider JS if multiple images -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('sliderTrack');
    if (!track) return;

    let currentIndex = 0;
    const slides = track.children.length;
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    function updateSlider() {
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
    if (prevBtn && nextBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateSlider();
            }
        });
        nextBtn.addEventListener('click', () => {
            if (currentIndex < slides - 1) {
                currentIndex++;
                updateSlider();
            }
        });
    }
});




</script>
@endsection. 