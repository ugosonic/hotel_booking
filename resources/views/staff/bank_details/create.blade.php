@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-6 bg-white shadow mt-6">
  <h2 class="text-2xl font-bold mb-4">Add New Bank Detail</h2>

  <form action="{{ route('staff.bank_details.store') }}" method="POST">
    @csrf
    <div class="mb-4">
      <label class="font-semibold">Bank Name</label>
      <input type="text" name="bank_name" class="w-full border p-2 rounded"
             placeholder="e.g. GTBank" />
    </div>
    <div class="mb-4">
      <label class="font-semibold">Account Name</label>
      <input type="text" name="account_name" class="w-full border p-2 rounded"
             placeholder="e.g. John Doe" />
    </div>
    <div class="mb-4">
      <label class="font-semibold">Account Number</label>
      <input type="text" name="account_number" class="w-full border p-2 rounded"
             placeholder="e.g. 0123456789" />
    </div>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      Save
    </button>
  </form>
</div>
@endsection
