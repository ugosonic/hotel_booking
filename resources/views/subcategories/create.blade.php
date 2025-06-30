@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
  <h2 class="text-xl font-bold mb-4">
    Create Subcategory for Category: {{ $category->name }}
  </h2>

  @if($errors->any())
    <div class="bg-red-100 text-red-700 p-3 mb-4">
      <ul>
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('subcategories.store', $category->id) }}" method="POST" class="space-y-4 p-4 border rounded">
    @csrf

    <div>
      <label class="block font-semibold">Subcategory Name</label>
      <input type="text" name="name" class="border-gray-300 rounded w-full" required>
    </div>

    <div>
      <label class="block font-semibold">Price</label>
      <input type="number" step="0.01" name="price" class="border-gray-300 rounded w-full">
    </div>
    <!-- Rooms -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="has_rooms" id="has_rooms" class="h-4 w-4" />
      <label for="has_rooms" class="block text-gray-700">Rooms</label>
      <input type="number" name="num_rooms" placeholder="No. of rooms" class="border-gray-300 rounded w-20" />
    </div>

    <!-- Toilets -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="has_toilets" id="has_toilets" class="h-4 w-4" />
      <label for="has_toilets" class="block text-gray-700">Toilets</label>
      <input type="number" name="num_toilets" placeholder="No. of toilets" class="border-gray-300 rounded w-20" />
    </div>

    <!-- Sittingrooms -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="has_sittingroom" id="has_sittingroom" class="h-4 w-4" />
      <label for="has_sittingroom" class="block text-gray-700">Sitting Room</label>
      <input type="number" name="num_sittingrooms" placeholder="No. of sitting rooms" class="border-gray-300 rounded w-20" />
    </div>

    <!-- Kitchen -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="has_kitchen" id="has_kitchen" class="h-4 w-4" />
      <label for="has_kitchen" class="block text-gray-700">Kitchen</label>
      <input type="number" name="num_kitchens" placeholder="No. of kitchens" class="border-gray-300 rounded w-20" />
    </div>

    <!-- Balcony -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="has_balcony" id="has_balcony" class="h-4 w-4" />
      <label for="has_balcony" class="block text-gray-700">Balcony</label>
      <input type="number" name="num_balconies" placeholder="No. of balconies" class="border-gray-300 rounded w-20" />
    </div>

    <!-- Free wifi, Water, Electricity -->
    <div class="flex items-center space-x-3">
      <input type="checkbox" name="free_wifi" id="free_wifi" class="h-4 w-4" />
      <label for="free_wifi" class="block text-gray-700">Free WiFi</label>

      <input type="checkbox" name="water" id="water" class="h-4 w-4 ml-5" />
      <label for="water" class="block text-gray-700">Water</label>

      <input type="checkbox" name="electricity" id="electricity" class="h-4 w-4 ml-5" />
      <label for="electricity" class="block text-gray-700">24/7 Electricity</label>
    </div>

    <!-- Additional Info -->
    <div>
      <label class="font-semibold block mb-1">Additional Information</label>
      <textarea name="additional_info" rows="3" class="border-gray-300 rounded w-full"></textarea>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
      Save Subcategory
    </button>
  </form>
</div>
@endsection
