<!-- resources/views/dashboard/settings.blade.php -->
@extends('layouts.app')

@section('content')
<div class="flex">
    <!-- Sidebar -->
    @include('partials.sidebar')

    <!-- Main content area -->
    <div class="flex-1 p-6">
        <h2 class="text-2xl font-bold mb-4">Settings</h2>
        <p class="text-gray-700">User-specific or staff-specific settings area.</p>
    </div>
</div>
@endsection
