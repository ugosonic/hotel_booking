@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
  <h2 class="text-xl font-bold mb-4">
    Availability for "{{ $subCategory->name }}"
  </h2>
  @if(session('success'))
    <div class="bg-green-100 text-green-700 p-2 mb-4">
      {{ session('success') }}
    </div>
  @endif

  <!-- Add a new date row -->
  <form action="{{ route('subcategories.availability.store',$subCategory->id) }}" method="POST" class="p-3 border rounded mb-4">
    @csrf
    <div class="mb-2">
      <label class="font-semibold">Date</label>
      <input type="date" name="date" class="border-gray-300 rounded" required>
    </div>
    <div class="mb-2">
      <label class="font-semibold">Slots</label>
      <input type="number" name="slots" class="border-gray-300 rounded" value="1" min="0" required>
    </div>
    <div class="mb-2">
      <label class="inline-flex items-center">
        <input type="checkbox" name="is_unavailable" value="1" class="mr-2">
        Mark as Unavailable
      </label>
    </div>
    <button class="bg-blue-600 text-white px-3 py-1 rounded">Add / Update</button>
  </form>

  <!-- Table of existing availability -->
  <table class="w-full table-auto">
    <thead>
      <tr class="bg-gray-200">
        <th class="border px-2 py-1">Date</th>
        <th class="border px-2 py-1">Slots</th>
        <th class="border px-2 py-1">Unavailable?</th>
        <th class="border px-2 py-1">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($availabilities as $avail)
      <tr>
        <td class="border px-2 py-1">{{ $avail->date }}</td>
        <td class="border px-2 py-1">{{ $avail->slots }}</td>
        <td class="border px-2 py-1">{{ $avail->is_unavailable ? 'Yes' : 'No' }}</td>
        <td class="border px-2 py-1">
          <!-- Inline form to update -->
          <form action="{{ route('subcategories.availability.update',$avail->id) }}" method="POST" style="display:inline;">
            @csrf
            <label>Slots:</label>
            <input type="number" name="slots" value="{{ $avail->slots }}" class="w-16" min="0">
            <label class="ml-2">
              <input type="checkbox" name="is_unavailable" value="1" {{ $avail->is_unavailable ? 'checked' : '' }}>
              Unavailable
            </label>
            <button class="bg-yellow-600 text-white px-2 py-1 ml-2">Update</button>
          </form>
          <!-- Delete -->
          <form action="{{ route('subcategories.availability.destroy',$avail->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="bg-red-600 text-white px-2 py-1 ml-2" onclick="return confirm('Remove this date?')">
              Delete
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $availabilities->links() }}
  </div>
</div>
@endsection
