@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 mt-6 rounded shadow text-center">
  <img src="/images/success-check.png" alt="Success" class="mx-auto mb-4 w-22 h-22">
  <h2 class="text-2xl font-bold text-green-600 mb-2">Payment Successful!</h2>

  <p>Booking #{{ $booking->id }} has been paid successfully.</p>
  <p>Reference: <strong>{{ $payment->reference }}</strong></p>

  <div class="mt-4">
    @if($booking->guest_name)
      <p>Guest: {{ $booking->guest_name }}</p>
    @elseif($booking->client)
      <p>Client: {{ $booking->client->name }}</p>
    @endif

    @if($booking->subCategory)
      <p>Apartment: {{ $booking->subCategory->name }}</p>
    @endif
  </div>

  <div class="mt-6">
    <a href="{{ route('book.apartment') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded">
      Back to Booking Page
    </a>
  </div>
</div>
@endsection
