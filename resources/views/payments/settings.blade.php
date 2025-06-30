@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto bg-white p-6">
  <h2 class="text-2xl font-bold mb-4">Payment Settings</h2>

  <!-- Link to create bank details -->
  <a href="{{ route('staff.bank_details.create') }}"
     class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-3">
    Create Bank Detail
  </a>
  <br>

  <!-- Link to pending topups -->
  <a href="{{ route('staff.topups.index') }}"
     class="inline-block bg-green-600 text-white px-4 py-2 rounded mb-3">
    View Pending Top-Ups
  </a>
  <br>

  <!-- Link to daily payments -->
  <a href="{{ route('staff.payments.daily') }}"
     class="inline-block bg-purple-600 text-white px-4 py-2 rounded mb-3">
    View Daily Payments
  </a>
  <br>

  <!-- Toggle Email Notification -->
  <form method="POST" action="{{ route('staff.payment.toggle_email') }}" class="mt-4">
    @csrf
    @if($setting && $setting->topup_email_notification)
      <p class="mb-2">
        <strong>Email Notifications:</strong> 
        Currently <span class="text-green-600">ON</span>
      </p>
      <button type="submit"
              class="bg-red-500 text-white px-3 py-1 rounded">
        Turn Off
      </button>
    @else
      <p class="mb-2">
        <strong>Email Notifications:</strong> 
        Currently <span class="text-red-600">OFF</span>
      </p>
      <button type="submit"
              class="bg-blue-500 text-white px-3 py-1 rounded">
        Turn On
      </button>
    @endif
  </form>




</div>
@endsection
