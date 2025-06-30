@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 mt-6 rounded shadow text-center">
  <img src="/images/error-x.png" alt="Error" class="mx-auto mb-4 w-16 h-16">
  <h2 class="text-2xl font-bold text-red-600 mb-2">Payment Failed or Canceled</h2>

  @if(session('error'))
    <p class="text-red-500 font-semibold mb-4">{{ session('error') }}</p>
  @endif

  <p>Booking #{{ $booking->id }} was not fully paid.</p>
  <div class="mt-6 space-x-4">
    <a href="{{ route('book.apartment') }}"
       class="inline-block bg-blue-600 text-white px-4 py-2 rounded">
      Back to Booking
    </a>
    <a href="{{ route('booking.payment', $booking->id) }}"
       class="inline-block bg-yellow-500 text-white px-4 py-2 rounded">
      Retry Payment
    </a>
  </div>
</div>
@endsection
