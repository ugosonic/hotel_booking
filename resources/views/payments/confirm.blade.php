@extends('layouts.app') 

@section('content')
@php
    $userBalance = 0;
    if (auth()->check() && !auth()->user()->isStaff()) {
        $userBalance = auth()->user()->accountBalance->balance ?? 0;
    }
@endphp

@if($difference_due > 0)
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 mb-4">
  <p class="text-yellow-800">
    <strong>Extended Booking:</strong> 
    You have already paid part of your booking, and now owe an additional ₦{{ number_format($difference_due, 2) }}.
  </p>
</div>
@endif

<div class="max-w-4xl mx-auto bg-white rounded shadow p-6 mt-6">
  <h2 class="text-2xl font-bold mb-4">Booking Confirmation</h2>

  <div class="flex flex-col md:flex-row md:space-x-4 mb-6">
    <div class="md:w-1/2">
      <h3 class="text-lg font-semibold mb-2">Booking Details</h3>
      <p><strong>Booking #:</strong> {{ $booking->id }}</p>
      <p><strong>Check-in:</strong> {{ $booking->start_date }}</p>
      <p><strong>Check-out:</strong> {{ $booking->end_date }}</p>
      <p><strong>Nights:</strong> {{ max($booking->nights,1) }}</p>

      @if($subCategory)
        <p><strong>Apartment:</strong> {{ $subCategory->name }}</p>
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
            <li>{{ $g->name }} 
              @if($g->dob) 
                (DOB: {{ $g->dob }}) 
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    </div>
    <div class="md:w-1/2">
      <h3 class="text-lg font-semibold mb-2">Cost Summary</h3>
      @if($difference_due > 0)
        <p class="text-xl text-red-600 font-extrabold mt-1">
          Additional Due: ₦{{ number_format($displayAmount, 2) }}
        </p>
      @else
        <p><strong>Price Per Night:</strong> ₦ {{ number_format($subCategory->price ?? 0,2) }}</p>
        <p class="text-xl text-red-600 font-extrabold mt-1">
          Total: ₦{{ number_format($displayAmount, 2) }}
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
            <span>Card (Paystack)</span>
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
        <p><strong>Your Balance:</strong> ₦{{ number_format($userBalance,2) }}</p>
      @endif

      <!-- We'll post the "final_payable" in a hidden input if partial logic is needed -->
      <input type="hidden" name="final_payable" id="finalPayableInput" value="{{ $displayAmount }}">

      <!-- Card Payment Section -->
      <div id="cardSection" class="bg-gray-50 p-4 rounded mb-2">
        <h4 class="font-semibold mb-2">Card Payment</h4>

        <!-- The user can input details. Paystack's standard inline flow already 
             shows a pop-up for card details. But if you want to pre-collect them, 
             you can create your own fields like below.  -->
        <div class="mb-2">
          <label for="emailField" class="block">Email</label>
          <input type="email" id="emailField" class="border rounded w-full p-2"
                 value="{{ $booking->guest_email ?? ($booking->client->email ?? '') }}">
        </div>
        <div class="mb-2">
          <label for="nameField" class="block">Name on Card</label>
          <input type="text" id="nameField" class="border rounded w-full p-2"
                 value="{{ $booking->guest_name ?? ($booking->client->name ?? '') }}">
        </div>

        <!-- If you REALLY want to capture card number, expiry, CVV in your own form:
             (This is typically not recommended unless you do "Charge Authorization" 
              and comply with PCI-DSS. The simpler method is the Paystack popup 
              that handles these fields for you.)
        -->
        <div class="mb-2">
          <label for="cardNumber" class="block">Card Number</label>
          <input type="text" id="cardNumber" class="border rounded w-full p-2" placeholder="4084 0840 8408 4081">
        </div>
        <div class="flex space-x-2 mb-2">
          <div>
            <label for="expiryMonth" class="block">Exp. Month</label>
            <input type="text" id="expiryMonth" class="border rounded w-full p-2" placeholder="MM">
          </div>
          <div>
            <label for="expiryYear" class="block">Exp. Year</label>
            <input type="text" id="expiryYear" class="border rounded w-full p-2" placeholder="YY">
          </div>
          <div>
            <label for="cardCvc" class="block">CVC</label>
            <input type="text" id="cardCvc" class="border rounded w-full p-2" placeholder="123">
          </div>
        </div>

        <!-- The "Pay Now" triggers Paystack logic -->
        <button type="button" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                onclick="payWithPaystackCustom()">
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

      <!-- Hidden field to store the Paystack reference -->
      <input type="hidden" name="paystack_ref" id="paystackRefInput" />
    </form>
  </div>
</div>

<!-- Paystack inline script -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
  const paystackPubKey = "{{ $paystackPubKey }}";
  const amountToPay    = parseFloat("{{ $displayAmount }}") || 0;

  function toggleMethod(val) {
    // Hide all sections initially
    document.getElementById('cardSection').classList.add('hidden');
    document.getElementById('cashButton').classList.add('hidden');
    document.getElementById('acctBalanceButton').classList.add('hidden');

    // Show based on selection
    if(val==='cash') {
      document.getElementById('cashButton').classList.remove('hidden');
    }
    else if(val==='account_balance') {
      document.getElementById('acctBalanceButton').classList.remove('hidden');
    }
    else {
      document.getElementById('cardSection').classList.remove('hidden');
    }
  }

  // Example of a "custom form" approach:
  function payWithPaystackCustom() {
    if (amountToPay <= 0) {
      alert("Nothing to pay!");
      return;
    }

    // Grab user inputs from the fields
    const email       = document.getElementById('emailField').value;
    const name        = document.getElementById('nameField').value || 'Guest';
    const cardNumber  = document.getElementById('cardNumber').value.replace(/\s+/g, '');
    const expiryMonth = document.getElementById('expiryMonth').value;
    const expiryYear  = document.getElementById('expiryYear').value;
    const cardCvc     = document.getElementById('cardCvc').value;

    // This part can be done in multiple ways:
    // 1) Paystack "inline" popup normally, ignoring your custom fields, or
    // 2) "Charge Authorization" approach, sending cardNumber, expiry, cvc, etc. to Paystack.
    // Below, we'll just show the standard inline approach, which handles the card details in a secure popup:

    let handler = PaystackPop.setup({
      key: paystackPubKey,
      email: email,
      amount: amountToPay * 100, // in kobo
      currency: 'NGN',
      ref: 'booking_{{ $booking->id }}_'+(new Date().getTime()),
      metadata: {
        custom_fields: [
          {
            display_name: "Name",
            variable_name: "cardName",
            value: name
          }
        ]
      },
      callback: function(response) {
        // Payment was successful, store the reference
        document.getElementById('paystackRefInput').value = response.reference;
        // Submit the form to your server
        document.getElementById('paymentForm').submit();
      },
      onClose: function() {
        alert("Transaction cancelled.");
      }
    });

    // If you truly want to pass your custom fields (card number, etc.) to Paystack manually,
    // you would use Paystack's "create charge" endpoint. But that's more advanced 
    // and requires your server to handle additional steps.

    handler.openIframe();
  }
</script>
@endsection
