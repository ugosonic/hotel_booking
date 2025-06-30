<!-- resources/views/dashboard/staff_dashboard.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">Staff Dashboard</h2>
    @if($pendingCount > 0)
  <div class="text-red-600 font-bold">
    You have {{ $pendingCount }} pending top‐ups to approve.
  </div>
@endif

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
        <a href="{{ route('bookings.staff_change') }}"
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
        <a href="{{ route('staff.booking.cancel') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg class="h-12 w-12 text-red-600 mb-2" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
            </svg>
            <span class="font-semibold">Cancel Bookings</span>
        </a>

        <!-- Create Apartment -->
        <a href="{{ route('categories.index') }}"
           class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
            <svg class="h-12 w-12 text-red-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
</svg>
            <span class="font-semibold">Create Apartment</span>
        </a>

        <!-- Link to daily payments history -->
        <a href="{{ route('staff.payments.daily') }}"
   class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
    <svg class="h-12 w-12 text-green-600 mb-2" fill="none" 
         stroke="currentColor" stroke-width="1.5" 
         viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M21 12c0-.943-.777-1.71-1.723-1.71H4.723C3.777 10.29 
               3 11.057 3 12v4.29c0 .944.777 1.71 1.723 1.71h14.554c.946 0 
               1.723-.766 1.723-1.71V12zm0-4.29v-.72c0-.944-.777-1.71-1.723-1.71H4.723C3.777 
               5.28 3 6.046 3 6.99v.72"/>
      <path stroke-linecap="round" stroke-linejoin="round" 
            d="M7.5 12a3.75 3.75 0 007.5 0 3.75 
               3.75 0 00-7.5 0z"/>
    </svg>
    <span class="font-semibold">View Daily Payment</span>
</a>


       
<!-- staff_dashboard.blade.php snippet -->
<!-- link for "Account Management" with an icon -->
<a href="{{ route('staff.account_management') }}"
   class="flex flex-col items-center bg-white p-6 rounded shadow hover:shadow-md transition">
    <svg class="h-12 w-12 text-pink-600 mb-2" fill="none" stroke="currentColor" stroke-width="2"
         viewBox="0 0 24 24">
       <!-- icon path -->
       <path d="M4 6h16M4 10h16M4 14h16M4 18h16" />
    </svg>
    <span class="font-semibold">Account Management</span>
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

    <div class="mt-8">
        <!-- Staff might have a different Settings route? -->
        <a href="{{ route('client.settings') }}" 
           class="inline-block bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700 transition">
            Go to Settings
        </a>
    </div>
</div>
@endsection
