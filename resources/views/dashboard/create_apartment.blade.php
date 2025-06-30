@extends('layouts.app')

@section('page-heading')
    <h1 class="page-heading">Create an Apartment</h1>
    @if(session('success'))
        <div class="alert bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection

@section('content')
<div class="md:ml-64 flex">
    @include('partials.sidebar')

    <div class="flex-1 p-6">
        
        <form action="{{ route('apartment.store') }}" method="POST" class="space-y-4 bg-white p-6 rounded shadow">
            @csrf

            <!-- Apartment Name -->
            <div>
                <label class="font-semibold block mb-1">Apartment Name</label>
                <input type="text" name="apartment_name" 
                       class="w-full border-gray-300 rounded" required />
            </div>

            <!-- Rooms -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="has_rooms" id="has_rooms" class="h-4 w-4" />
                <label for="has_rooms" class="block text-gray-700">Rooms</label>
                <input type="number" name="num_rooms" placeholder="No. of rooms"
                       class="border-gray-300 rounded w-20" />
            </div>

            <!-- Toilets -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="has_toilets" id="has_toilets" class="h-4 w-4" />
                <label for="has_toilets" class="block text-gray-700">Toilets</label>
                <input type="number" name="num_toilets" placeholder="No. of toilets" class="border-gray-300 rounded w-20" />
            </div>

            <!-- Sitting rooms -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="has_sittingroom" id="has_sittingroom" class="h-4 w-4" />
                <label for="has_sittingroom" class="block text-gray-700">Sitting Room</label>
                <input type="number" name="num_sittingrooms" placeholder="No. of sitting rooms"
                       class="border-gray-300 rounded w-20" />
            </div>

            <!-- Kitchen -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="has_kitchen" id="has_kitchen" class="h-4 w-4" />
                <label for="has_kitchen" class="block text-gray-700">Kitchen</label>
                <input type="number" name="num_kitchens" placeholder="No. of kitchens"
                       class="border-gray-300 rounded w-20" />
            </div>

            <!-- Balcony -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="has_balcony" id="has_balcony" class="h-4 w-4" />
                <label for="has_balcony" class="block text-gray-700">Balcony</label>
                <input type="number" name="num_balconies" placeholder="No. of balconies"
                       class="border-gray-300 rounded w-20" />
            </div>

            <!-- Other checkboxes: free wifi, water, 24/7 electricity -->
            <div class="flex items-center space-x-3">
                <input type="checkbox" name="free_wifi" id="free_wifi" class="h-4 w-4" />
                <label for="free_wifi" class="block text-gray-700">Free WiFi</label>

                <input type="checkbox" name="water" id="water" class="h-4 w-4 ml-5" />
                <label for="water" class="block text-gray-700">Water</label>

                <input type="checkbox" name="electricity" id="electricity" class="h-4 w-4 ml-5" />
                <label for="electricity" class="block text-gray-700">24/7 Electricity</label>
            </div>

            <!-- Price -->
            <div>
                <label class="font-semibold block mb-1">Price</label>
                <input type="number" name="price" step="0.01" class="w-full border-gray-300 rounded" required />
            </div>

            <!-- Additional Information -->
            <div>
                <label class="font-semibold block mb-1">Additional Information</label>
                <textarea name="additional_info" rows="3" class="w-full border-gray-300 rounded"></textarea>
            </div>

            <button type="submit" 
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                Save Apartment
            </button>
        </form>
    </div>
</div>
@endsection
