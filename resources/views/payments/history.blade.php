@extends('layouts.app')

@section('page-heading','Payment History')

@section('content')
<div class="max-w-5xl mx-auto bg-white p-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-4">Payment History</h2>

  <!-- if you want optional filters here, e.g. date range -->
  <form method="GET" action="{{ route('client.payment_history') }}" class="flex space-x-4 mb-4">
    <div>
      <label class="font-semibold">Start Date</label>
      <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded p-1">
    </div>
    <div>
      <label class="font-semibold">End Date</label>
      <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded p-1">
    </div>
    <button class="bg-blue-600 text-white px-4 py-1 rounded self-end">Filter</button>
  </form>

  @if($payments->count() < 1)
    <p>No payment records found.</p>
  @else
    <table class="w-full border text-sm">
    <thead style="background-color: #cce8ff;">
        <tr>
          <th class="p-2 text-left">Date</th>
          <th class="p-2 text-left">Method</th>
          <th class="p-2 text-right">Amount</th>
          <th class="p-2">Status</th>
          <th class="p-2 text-left">Reference</th>
          <th class="p-2 text-left">Balance After?</th>
        </tr>
      </thead>
      <tbody>
        @foreach($payments as $pay)
          <tr class="border-b">
            <td class="p-2">{{ $pay->created_at->format('Y-m-d H:i') }}</td>
            <td class="p-2">{{ $pay->payment_method }}</td>
            <td class="p-2 text-right">₦{{ number_format($pay->amount,2) }}</td>
            <td class="p-2 text-center">{{ ucfirst($pay->status) }}</td>
            <td class="p-2">{{ $pay->reference }}</td>
            <td class="p-2">
                <!-- If you track new balance in Payment or a separate table? 
                     You can display it here if your Payment table has e.g. 'balance_after' column. -->
                @if(!empty($pay->balance_after))
                  ₦{{ number_format($pay->balance_after,2) }}
                @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4">
      {{ $payments->links() }}
    </div>
  @endif
</div>
@endsection
