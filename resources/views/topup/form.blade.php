@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-2xl font-bold mb-4">Top Up Your Account</h2>

    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif
    @if(session('info'))
      <div class="bg-yellow-100 text-yellow-800 p-3 rounded mb-4">
        {{ session('info') }}
      </div>
    @endif
    @if(session('error'))
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        {{ session('error') }}
      </div>
    @endif

    <form action="{{ route('topup.submit') }}" method="POST" enctype="multipart/form-data" id="topupForm">
      @csrf

      <!-- Amount -->
      <div class="mb-4">
        <label class="font-semibold block mb-1">Amount (₦)</label>
        <input type="number" name="amount" class="w-full border border-gray-300 rounded p-2"
               required placeholder="Enter amount" value="{{ old('amount') }}" />
      </div>

      <!-- Payment Method Radio -->
      <div class="mb-4">
        <label class="font-semibold block mb-1">Payment Method</label>
        <div class="flex space-x-4">
          <label class="inline-flex items-center">
            <input type="radio" name="method" value="bank_transfer" 
                   class="mr-2" onclick="toggleMethod('bank')" />
            <span>Bank Transfer</span>
          </label>
          <label class="inline-flex items-center">
            <input type="radio" name="method" value="card" 
                   class="mr-2" onclick="toggleMethod('card')" />
            <span>Card (Paystack)</span>
          </label>
          @if(auth()->user()->isStaff())
          <label class="inline-flex items-center">
            <input type="radio" name="method" value="cash" 
                   class="mr-2" onclick="toggleMethod('cash')" />
            <span>Cash</span>
          @endif
        </div>
      </div>

      <!-- Bank Transfer fields -->
      <div id="bankSection" class="hidden mb-4 border border-gray-200 p-3 rounded">
        <label class="block font-semibold mb-1">Select Bank Detail</label>
        @forelse($bankDetails as $bd)
          <label class="block mb-1">
            <input type="radio" name="bank_detail_id" value="{{ $bd->id }}" class="mr-2" />
            <span class="text-sm">
              {{ $bd->bank_name }} - {{ $bd->account_name }} ({{ $bd->account_number }})
            </span>
          </label>
        @empty
          <p class="text-sm text-gray-500">No bank details configured by staff</p>
        @endforelse

        <div class="mt-2">
          <label class="font-semibold mb-1 block">Upload Payment Screenshot</label>
          <input type="file" name="screenshot" class="block" />
        </div>
      </div>

      <!-- Card section (Paystack Inline) -->
      <div id="cardSection" class="hidden mb-4 border border-gray-200 p-3 rounded">
        <p class="text-sm text-gray-700 mb-2">
          You selected <strong>Card Payment</strong> via Paystack. Please confirm your name & email below, then click “Pay Now.”
        </p>
        <div class="mb-2">
          <label for="cardEmail" class="block font-semibold">Email</label>
          <input type="email" id="cardEmail" class="border rounded w-full p-2"
                 value="{{ auth()->user()->email ?? 'test@example.com' }}">
        </div>
        <div class="mb-2">
          <label for="cardName" class="block font-semibold">Name</label>
          <input type="text" id="cardName" class="border rounded w-full p-2"
                 value="{{ auth()->user()->name ?? 'Guest' }}">
        </div>
        <button type="button"
                class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
                onclick="payWithPaystack()">
          Pay Now
        </button>
      </div>

      <!-- Cash section (staff only) -->
      <div id="cashSection" class="hidden mb-4 border border-gray-200 p-3 rounded">
        <p class="text-sm text-gray-700">
          You selected <strong>Cash</strong>. (Only staff can do this.)
        </p>
      </div>

      <!-- Hidden field for Paystack reference -->
      <input type="hidden" name="paystack_ref" id="paystackRefInput" />

      <button type="submit" 
              class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        Submit Top Up
      </button>
    </form>
</div>

<!-- Paystack inline script -->
<script src="https://js.paystack.co/v1/inline.js"></script>
<script>
const paystackPublicKey = "{{ $paystackPubKey ?? 'pk_test_xxx' }}";

function toggleMethod(method) {
  document.getElementById('bankSection').classList.add('hidden');
  document.getElementById('cardSection').classList.add('hidden');
  document.getElementById('cashSection').classList.add('hidden');

  if(method==='bank') {
    document.getElementById('bankSection').classList.remove('hidden');
  } else if(method==='card'){
    document.getElementById('cardSection').classList.remove('hidden');
  } else if(method==='cash'){
    document.getElementById('cashSection').classList.remove('hidden');
  }
}

function payWithPaystack() {
  const amountField = document.querySelector('input[name="amount"]');
  const amountVal   = parseFloat(amountField.value) || 0;
  if(amountVal <= 0) {
    alert("Please enter a valid amount!");
    return;
  }

  // Grab user inputs (name, email) from the card section
  const cardEmail = document.getElementById('cardEmail').value;
  const cardName  = document.getElementById('cardName').value;

  let handler = PaystackPop.setup({
    key: paystackPublicKey,
    email: cardEmail,
    amount: amountVal * 100, // in kobo
    currency: "NGN",
    ref: 'topup_'+(new Date().getTime()),
    metadata: {
      custom_fields: [
        {
          display_name: "Customer Name",
          variable_name: "customer_name",
          value: cardName
        }
      ]
    },
    callback: function(response) {
      // Payment was successful; store the reference, then submit form
      document.getElementById('paystackRefInput').value = response.reference;
      document.getElementById('topupForm').submit();
    },
    onClose: function() {
      alert("Transaction cancelled.");
    }
  });

  handler.openIframe();
}
</script>
@endsection
