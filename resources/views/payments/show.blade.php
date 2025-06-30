@extends('layouts.app')

@section('content')
@php
    // The "normal" full cost from the controller is $totalCost
    // The difference (if any) from session or from the controller is $difference_due
    // Decide how much we *display* as "amount to pay" on the UI:
    $diff = $difference_due ?? 0;
    // If $diff > 0 => user owes only $diff
    // else => user owes $totalCost
    $displayAmount = ($diff > 0) ? $diff : $totalCost;

    // If normal user has some balance, we show it:
    $userBalance = 0;
    if (auth()->check() && !auth()->user()->isStaff()) {
        // Example: If you have "accountBalance" relationship
        $userBalance = auth()->user()->accountBalance->balance ?? 0;
    }
@endphp

@if($diff > 0)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
  <p class="text-yellow-800">
    <strong>Extended Booking:</strong> You had already paid some amount.  
    You now owe an additional <span class="font-semibold">
      ₦{{ number_format($diff, 2) }}</span>
  </p>
</div>
@endif

<div class="max-w-4xl mx-auto bg-white rounded shadow p-6 mt-6">
  <h2 class="text-2xl font-bold mb-4">Bookings Confirmation</h2>

  <div class="flex flex-col md:flex-row md:space-x-4 mb-6">
    <div class="md:w-1/2">
      <h3 class="text-lg font-semibold mb-2">Booking Details</h3>
      <p><strong>Booking #:</strong> {{ $booking->id }}</p>
      <p><strong>Check-in:</strong> {{ $booking->start_date }}</p>
      <p><strong>Check-out:</strong> {{ $booking->end_date }}</p>
      <p><strong>Nights:</strong> {{ max($booking->nights,1) }}</p>

      @if($booking->subCategory)
        <p><strong>Apartment:</strong> {{ $booking->subCategory->name }}</p>
      @endif

      @if($booking->guest_name)
        <p><strong>Guest Name:</strong> {{ $booking->guest_name }}</p>
      @elseif($booking->client)
        <p><strong>Client Name:</strong> {{ $booking->client->name }}</p>
      @endif

      @if($extraGuests && $extraGuests->count())
        <p class="mt-2 font-semibold">Additional Guests:</p>
        <ul class="list-disc ml-5">
          @foreach($extraGuests as $g)
            <li>{{ $g->name }} @if($g->dob) (DOB: {{ $g->dob }}) @endif</li>
          @endforeach
        </ul>
      @endif
    </div>
    <div class="md:w-1/2">
      <h3 class="text-lg font-semibold mb-2">Cost Summary</h3>
      @if($diff > 0)
        <p class="text-xl text-red-600 font-extrabold mt-1">
          Additional Due: ₦ {{ number_format($displayAmount, 2) }}
        </p>
      @else
        <p><strong>Price Per Night:</strong> ₦ {{ number_format($subCategory->price ?? 0,2) }}</p>
        <p class="text-xl text-red-600 font-extrabold mt-1">
          Total: ₦ {{ number_format($displayAmount, 2) }}
        </p>
      @endif
    </div>
  </div>

  <!-- Payment Methods -->
  <div class="border-t pt-4">
    <h3 class="text-lg font-bold mb-3">Choose Payment Method</h3>

    <form action="{{ route('payment.process', $booking->id) }}" method="POST" id="paymentForm">
      @csrf

      @if(auth()->check() && auth()->user()->isStaff())
        <!-- Staff can do "cash" or "card" -->
        <div class="flex items-center space-x-4 mb-4">
          <label class="inline-flex items-center">
            <input type="radio" name="payment_method" value="card" checked 
                   class="mr-2" onchange="toggleMethod(this.value)">
            <span>Card (Flutterwave)</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="payment_method" value="cash" 
                   class="mr-2" onchange="toggleMethod(this.value)">
            <span>Cash</span>
          </label>
        </div>
      @else
        <!-- Normal user => card or account balance -->
        <div class="flex items-center space-x-4 mb-4">
          <label class="inline-flex items-center">
            <input type="radio" name="payment_method" value="card" checked
                   class="mr-2" onchange="toggleMethod(this.value)">
            <span>Pay with Card</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="payment_method" value="account_balance"
                   class="mr-2" onchange="toggleMethod(this.value)">
            <span>Use Account Balance</span>
          </label>
        </div>
        <p><strong>Your Balance:</strong> ₦ {{ number_format($userBalance,2) }}</p>
      @endif

      <!-- We'll post the "final_payable" in a hidden input if partial logic is needed -->
      <input type="hidden" name="final_payable" id="finalPayableInput" value="{{ $displayAmount }}">

      <!-- Card Payment Section -->
      <div id="cardSection" class="bg-gray-50 p-4 rounded mb-2">
        <h4 class="font-semibold mb-2">Card Payment</h4>
        <button type="button" 
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                onclick="payWithFlutterwave()">
          Pay Now
        </button>
      </div>

      <!-- Cash button hidden by default -->
      <button id="cashButton" type="submit"
              class="hidden bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
              onclick="return confirm('Confirm cash payment?')">
        Pay with Cash
      </button>

      <!-- Account balance usage button hidden by default -->
      <button id="acctBalanceButton" type="submit"
              class="hidden bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
              onclick="return confirm('Use Account Balance?')">
        Use Balance
      </button>

      <!-- hidden field to store the flutterwave ref -->
      <input type="hidden" name="flutterwave_ref" id="flutterwaveRefInput" />
    </form>
  </div>
</div>

<script src="https://checkout.flutterwave.com/v3.js"></script>
<script>
  const flwPubKey    = "{{ $flwPubKey }}";
  const amountToPay  = parseFloat("{{ $displayAmount }}") || 0;

  function toggleMethod(val) {
    document.getElementById('cardSection').classList.add('hidden');
    document.getElementById('cashButton').classList.add('hidden');
    document.getElementById('acctBalanceButton').classList.add('hidden');

    if(val==='cash') {
      document.getElementById('cashButton').classList.remove('hidden');
    }
    else if(val==='account_balance') {
      document.getElementById('acctBalanceButton').classList.remove('hidden');
    }
    else {
      // default = card
      document.getElementById('cardSection').classList.remove('hidden');
    }
  }

  function payWithFlutterwave() {
    // We'll pay the entire "amountToPay" or leftover if we wanted partial usage
    if(amountToPay <= 0) {
      alert("Nothing to pay!");
      return;
    }
    FlutterwaveCheckout({
      public_key: flwPubKey,
      tx_ref: 'booking_{{ $booking->id }}_'+(new Date().getTime()),
      amount: amountToPay,
      currency: 'NGN',
      payment_options: 'card',
      customer: {
        email: "{{ $booking->guest_email ?? ($booking->client->email ?? 'example@test.com') }}",
        name:  "{{ $booking->guest_name ?? ($booking->client->name ?? 'Guest') }}"
      },
      callback: function (data) {
        // store ref
        document.getElementById('flutterwaveRefInput').value = data.transaction_id;
        // submit
        document.getElementById('paymentForm').submit();
      },
      onclose: function() {
        // do nothing or show alert
      }
    });
  }
</script>
@endsection
