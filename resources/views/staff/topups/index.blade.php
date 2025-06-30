@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow mt-6">
  <h2 class="text-2xl font-bold mb-4">Pending Top-Up Requests</h2>

  @foreach($topups as $tp)
    <div class="border p-3 mb-3">
      <p><strong>User #{{ $tp->user_id }}</strong> 
         wants to top up ₦{{ number_format($tp->amount,2) }} via 
         <em>{{ $tp->method }}</em>.</p>
      
      @if($tp->method==='bank_transfer' && $tp->bankDetail)
        <p class="text-sm text-gray-600 mt-1">
          Bank: {{ $tp->bankDetail->bank_name }} /
          {{ $tp->bankDetail->account_name }} ({{ $tp->bankDetail->account_number }})
        </p>
        @if($tp->screenshot_path)
        <div class="mt-1">
          <a href="{{ asset('storage/app/public/'.$tp->screenshot_path) }}" target="_blank"
             class="text-blue-600 underline">View Screenshot</a>
        </div>
        @endif
      @endif

      <div class="mt-2 space-x-2">
        <!-- Approve btn -->
        <form action="{{ route('staff.topups.approve',$tp->id) }}" method="POST" class="inline">
          @csrf
          <button type="submit"
                  class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
                  onclick="return confirm('Approve this top-up?')">
            Approve
          </button>
        </form>
        <!-- Reject btn -->
        <form action="{{ route('staff.topups.reject',$tp->id) }}" method="POST" class="inline">
          @csrf
          <button type="submit"
                  class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                  onclick="return confirm('Reject this top-up?')">
            Reject
          </button>
        </form>
      </div>
    </div>
  @endforeach

  <div class="mt-4">
    {{ $topups->links() }}
  </div>
</div>
  <!-- 
    2) Display the paginated history of accepted/rejected top-ups 
       at the bottom of this page:
   -->
   <hr class="my-6">

<h3 class="text-xl font-bold mb-3">Accepted/Rejected Top-Up History</h3>
@if($historyTopups->count() < 1)
  <p class="text-gray-500">No accepted or rejected top-ups found.</p>
@else
  @foreach($historyTopups as $ht)
    <div class="border p-3 mb-3">
      <p>
        <strong>TopUp #{{ $ht->id }}</strong> – 
        User #{{ $ht->user_id }} – 
        ₦{{ number_format($ht->amount,2) }} ({{ $ht->method }}) 
        <br>
        Status: <span class="font-semibold">{{ ucfirst($ht->status) }}</span> 
        @if($ht->approved_by)
          (by staff #{{ $ht->approved_by }})
        @endif
      </p>
      <p class="text-sm text-gray-500">
        Requested on {{ $ht->created_at->format('Y-m-d H:i') }}  
        @if($ht->status !== 'pending')
          &nbsp;|&nbsp; Finalized on {{ $ht->updated_at->format('Y-m-d H:i') }}
        @endif
      </p>
    </div>
  @endforeach

  <div class="mt-4">
    {{ $historyTopups->links() }}
  </div>
@endif
@endsection
