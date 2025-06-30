@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-xl font-bold mb-4">Edit Subcategory #{{ $subCategory->id }}</h2>

    <form action="{{ route('subcategories.update',$subCategory->id) }}" method="POST" class="space-y-4 p-4 border rounded">
        @csrf

        <div>
            <label class="font-semibold block mb-1">Apartment (Subcategory) Name</label>
            <input type="text" name="name"
                   value="{{ old('name',$subCategory->name) }}"
                   class="border-gray-300 rounded w-full" required>
        </div>
        <div>
            <label class="font-semibold block mb-1">Price</label>
            <input type="number" step="0.01" name="price"
                   value="{{ old('price',$subCategory->price) }}"
                   class="border-gray-300 rounded w-full">
        </div>

        <!-- Rooms -->
        <div class="flex items-center space-x-3">
            <input type="checkbox" name="has_rooms" id="has_rooms" class="h-4 w-4"
                   @if(old('has_rooms',$subCategory->has_rooms)) checked @endif />
            <label for="has_rooms" class="block text-gray-700">Rooms</label>
            <input type="number" name="num_rooms"
                   value="{{ old('num_rooms',$subCategory->num_rooms) }}"
                   class="border-gray-300 rounded w-20"
                   placeholder="No. of rooms" />
        </div>

        <!-- Repeat for has_toilets, has_sittingroom, etc. -->
        <div class="flex items-center space-x-3">
            <input type="checkbox" name="has_toilets" id="has_toilets" class="h-4 w-4"
                   @if(old('has_toilets',$subCategory->has_toilets)) checked @endif />
            <label for="has_toilets" class="block text-gray-700">Toilets</label>
            <input type="number" name="num_toilets"
                   value="{{ old('num_toilets',$subCategory->num_toilets) }}"
                   class="border-gray-300 rounded w-20"
                   placeholder="No. of toilets" />
        </div>

        <!-- ... etc. for other fields ... -->

        <div>
            <label class="font-semibold block mb-1">Additional Info</label>
            <textarea name="additional_info" rows="3" class="border-gray-300 rounded w-full">
                {{ old('additional_info',$subCategory->additional_info) }}
            </textarea>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection
