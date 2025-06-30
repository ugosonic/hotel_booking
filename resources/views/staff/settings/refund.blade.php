@extends('layouts.app')
@include('partials.sidebar') {{-- the new mini-sidebar --}}
@section('page-heading','Staff - Refund Settings')

@section('content')

@include('partials.sidebar') {{-- the new mini-sidebar --}}
<div class="max-w-md mx-auto bg-white p-6 rounded shadow mt-6">
    <h2 class="text-xl font-bold mb-4">Refund Percentage Settings</h2>

    <form action="{{ route('staff.refund.update') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="refund_percentage" class="block font-semibold mb-2">Refund Percentage (%)</label>
            <input type="number" name="refund_percentage" id="refund_percentage" step="0.01" 
                   value="{{ old('refund_percentage', $settings->refund_percentage ?? 0) }}"
                   class="w-full border-gray-300 rounded" />
            @error('refund_percentage')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            Save
        </button>
    </form>
</div>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
