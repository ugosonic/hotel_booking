@extends('layouts.app')

@section('content')
<div class="flex">
    {{-- Include the sidebar --}}
    @include('partials.sidebar')

    <div class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-4">Settings</h2>

        @if(auth()->user()->isStaff())
            <p class="text-gray-700 mb-6">Staff-specific settings go here.</p>
            <!-- For example, staff site settings or staff info -->
        @else
            <p class="text-gray-700 mb-6">Client-specific settings go here.</p>
            <!-- For example, client profile update -->
        @endif

        <p class="text-gray-600">
            Common settings for both can appear here, or separate by user role.
        </p>
    </div>
</div>
@endsection
