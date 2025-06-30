@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded shadow p-6 mt-6">

  <h2 class="text-2xl font-bold mb-4">Account Balance History</h2>

  <p class="mb-2">Current Balance: ₦{{ number_format($balance, 2) }}</p>

  @if($transactions->count() < 1)
    <p>No transactions yet.</p>
  @else
    <table class="w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-2">Date</th>
          <th class="border p-2">Type</th>
          <th class="border p-2">Amount</th>
          <th class="border p-2">Note</th>
        </tr>
      </thead>
      <tbody>
        @foreach($transactions as $t)
          <tr>
            <td class="border p-2">{{ $t->created_at->format('Y-m-d H:i') }}</td>
            <td class="border p-2">{{ $t->type }}</td>
            <td class="border p-2">₦{{ number_format($t->amount, 2) }}</td>
            <td class="border p-2">{{ $t->description }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@endsection
