@extends('layouts.app')

@section('content')
<div class="container mx-auto">
  <h2 class="text-xl font-bold mb-4">
     Manage Availability for "{{ $subCategory->name }}"
  </h2>

  <!-- Form to add new date availability -->
  <form action="{{ route('availability.store',$subCategory->id) }}" method="POST" class="p-4 border rounded mb-4">
    @csrf
    <div class="mb-2">
      <label class="block font-semibold">Date</label>
      <input type="date" name="date" class="border-gray-300 rounded" required>
    </div>
    <div class="mb-2">
      <label class="block font-semibold">Slots</label>
      <input type="number" name="slots" class="border-gray-300 rounded" value="1" min="0" required>
    </div>
    <div class="mb-2">
      <label class="inline-flex items-center">
        <input type="checkbox" name="is_unavailable" class="mr-2">
        Mark as Unavailable
      </label>
    </div>
    <button class="bg-blue-600 text-white px-4 py-1 rounded">Add / Update</button>
  </form>

  <!-- List existing rows (10 per page) -->
  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-2 py-1 border">Date</th>
        <th class="px-2 py-1 border">Slots</th>
        <th class="px-2 py-1 border">Unavailable?</th>
        <th class="px-2 py-1 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($availabilities as $a)
      <tr>
        <td class="border px-2 py-1">{{ $a->date }}</td>
        <td class="border px-2 py-1">{{ $a->slots }}</td>
        <td class="border px-2 py-1">{{ $a->is_unavailable ? 'Yes' : 'No' }}</td>
        <td class="border px-2 py-1">
          <!-- A small inline form to update availability -->
          <form action="{{ route('availability.update',$a->id) }}" method="POST" style="display:inline;">
            @csrf
            <label>Slots:</label>
            <input type="number" name="slots" value="{{ $a->slots }}" class="w-16">
            <label class="ml-2">
              <input type="checkbox" name="is_unavailable" {{ $a->is_unavailable ? 'checked' : '' }}>
              Unavailable
            </label>
            <button class="bg-yellow-500 text-white px-2 py-1 ml-2">Save</button>
          </form>

          <!-- Delete button -->
          <form action="{{ route('availability.destroy',$a->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="bg-red-600 text-white px-2 py-1 ml-2" onclick="return confirm('Delete this date?')">
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
