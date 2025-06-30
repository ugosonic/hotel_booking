@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white shadow mt-6">
  <h2 class="text-2xl font-bold mb-4">Manage Bank Details</h2>

  <a href="{{ route('staff.bank_details.create') }}" 
     class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
    Add New Bank
  </a>

  <div class="mt-4">
    @foreach($details as $bd)
      <div class="border p-2 mb-2 flex items-center justify-between">
        <div>
          <strong>{{ $bd->bank_name }}</strong> 
          <span class="ml-2 text-gray-700">{{ $bd->account_name }} ({{ $bd->account_number }})</span>
        </div>
        <form action="{{ route('staff.bank_details.destroy',$bd->id) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" 
                  class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                  onclick="return confirm('Delete this bank detail?')">
            Delete
          </button>
        </form>
      </div>
    @endforeach
  </div>
</div>
@endsection
