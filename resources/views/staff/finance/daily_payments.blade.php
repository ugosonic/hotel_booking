{{-- resources/views/staff/finance/daily_payments.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto bg-white p-6 mt-6 rounded shadow">

  <h2 class="text-2xl font-bold mb-4">
    Daily Payments for {{ $dateStr }}
  </h2>

  <!-- Filter form -->
  <form method="GET" action="{{ route('staff.payments.daily') }}" class="flex space-x-4 mb-6">
    <div>
      <label class="font-semibold">Select Date:</label>
      <input type="date" name="date" value="{{ $dateStr }}" class="border rounded px-2" />
    </div>
    <div>
      <label class="font-semibold">Method (Payment):</label>
      <select name="method" class="border rounded px-2">
        <option value="">-- All --</option>
        <option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option>
        <option value="card" {{ request('method')=='card'?'selected':'' }}>Card</option>
        <option value="account_balance" {{ request('method')=='account_balance'?'selected':'' }}>Acct Balance</option>
        <option value="refund" {{ request('method')=='refund'?'selected':'' }}>Refund</option>
      </select>
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded self-end">
      Filter
    </button>
  </form>

  <!-- Booking Payments Table -->
  <h3 class="text-xl font-bold mb-2" style="background-color: #cce8ff; padding: 0.5rem;">
    Booking Payments
  </h3>
  <table class="w-full text-sm border">
    <thead style="background-color: #cce8ff;">
      <tr>
        <th class="p-2 border">ID</th>
        <th class="p-2 border">Booking</th>
        <th class="p-2 border">Method</th>
        <th class="p-2 border">Amount</th>
        <th class="p-2 border">Created</th>
      </tr>
    </thead>
    <tbody>
      @forelse($payments as $index => $p)
        @php
          $bgColor = ($index % 2 === 0) ? 'background-color: #f9f9f9;' : 'background-color: #ffffff;';
          $methodColor = match($p->payment_method) {
              'cash' => 'text-green-600',
              'card' => 'text-purple-600',
              'account_balance' => 'text-blue-600',
              'refund' => 'text-red-600',
              default => 'text-gray-700',
          };
          $amountColor = $p->amount < 0 ? 'text-red-600' : '';
        @endphp
        <tr style="{{ $bgColor }}">
          <td class="border p-2">{{ $p->id }}</td>
          <td class="border p-2">
            @if($p->booking)
              <a href="{{ route('bookings.show',$p->booking->id) }}" class="text-blue-600 underline">
                 #{{ $p->booking->id }}
              </a>
              <span class="text-gray-600 ml-1">
                ({{ $p->booking->client_name ?? 'NoName' }})
              </span>
            @else
              --
            @endif
          </td>
          <td class="border p-2">
            <span class="{{ $methodColor }}">
              {{ $p->payment_method }}
            </span>
          </td>
          <td class="border p-2 {{ $amountColor }}">
            ₦{{ number_format($p->amount,2) }}
          </td>
          <td class="border p-2">
            {{ $p->created_at->format('Y-m-d H:i') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="border p-2 text-center text-gray-500">
            No booking payments found for this day.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-2 text-lg font-semibold">
    Total Booking Payments (Net): 
    <span class="{{ $paymentTotal < 0 ? 'text-red-600' : '' }}">
      ₦{{ number_format($paymentTotal,2) }}
    </span>
  </div>

  <hr class="my-6">

  <!-- Approved Top-Ups Table -->
  <h3 class="text-xl font-bold mb-2" style="background-color: #ffe6e6; padding:0.5rem;">
    Approved Top-ups
  </h3>
  <table class="w-full border text-sm">
    <thead style="background-color: #ffe6e6;">
      <tr>
        <th class="border p-2">ID</th>
        <th class="border p-2">User</th>
        <th class="border p-2">Method</th>
        <th class="border p-2">Amount</th>
        <th class="border p-2">Approved At</th>
      </tr>
    </thead>
    <tbody>
      @forelse($topups as $i => $t)
        @php
          $bgC = ($i % 2 === 0) ? '#f9f9f9' : '#ffffff';
          $methodColor = match($t->method) {
              'cash' => 'text-green-600',
              'bank_transfer' => 'text-blue-600',
              'card' => 'text-purple-600',
              default => 'text-gray-700',
          };
        @endphp
        <tr style="background-color: {{ $bgC }}">
          <td class="border p-2">{{ $t->id }}</td>
          <td class="border p-2">
            @if($t->user)
               {{ $t->user->name }}
            @else
               (User #{{ $t->user_id }})
            @endif
          </td>
          <td class="border p-2">
            <span class="{{ $methodColor }}">
              {{ $t->method }}
            </span>
          </td>
          <td class="border p-2">
            ₦{{ number_format($t->amount,2) }}
          </td>
          <td class="border p-2">
            {{ $t->updated_at->format('Y-m-d H:i') }}
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="border p-2 text-center text-gray-500">
            No top-ups found for this day.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <div class="mt-2 text-lg font-semibold">
    Total Top-ups: ₦{{ number_format($topupTotal,2) }}
  </div>

  <hr class="my-6" />

  @php
  // Sum refunds for the day from booking_cancellations table (using refunded_amount)
  $refundsTotal = \App\Models\BookingCancellation::whereDate('canceled_at', $dateStr)
                  ->sum('refunded_amount');
  @endphp

  <p class="text-lg">
    <strong>Refund total</strong>:
    <span class="text-red-600">
      ₦{{ number_format($refundsTotal,2) }}
    </span>
  </p>

  @php
    // Query system gains for the same date
    use App\Models\SystemGain;
    $systemGainsTotal = SystemGain::whereDate('created_at', $dateStr)->sum('amount');
  @endphp

  <div class="mt-4 text-lg font-semibold">
    System Gains: 
    <span class="text-blue-600">
      ₦{{ number_format($systemGainsTotal, 2) }}
    </span>
  </div>

  <div class="mt-4">
    <h4 class="font-semibold">Calendar or Next/Prev Day:</h4>
    <p class="text-sm text-gray-500">
      (Example: a mini day-by-day nav or a small FullCalendar widget.)
    </p>
  </div>

</div>
@endsection
