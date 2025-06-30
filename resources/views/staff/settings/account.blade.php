@extends('layouts.app')

@section('content')


    @include('partials.sidebar') {{-- the new mini-sidebar --}}


<div class="max-w-xl mx-auto bg-white p-6">

    <h2 class="text-2xl font-bold mb-4">Account Settings</h2>

  

    <form method="POST" action="{{ route('staff.settings.account.update') }}">
        @csrf

        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block font-semibold mb-1">Name</label>
            <input type="text" name="name" id="name"
                   class="w-full border-gray-300 rounded"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
              <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">Email</label>
            <input type="email" name="email" id="email"
                   class="w-full border-gray-300 rounded"
                   value="{{ old('email', $user->email) }}" required>
            @error('email')
              <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block font-semibold mb-1">New Password (optional)</label>
            <input type="password" name="password" id="password"
                   class="w-full border-gray-300 rounded">
            @error('password')
              <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Confirmation -->
        <div class="mb-4">
            <label for="password_confirmation" class="block font-semibold mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                   class="w-full border-gray-300 rounded">
        </div>

        <!-- Submit -->
        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded">
          Update Account
        </button>
    </form>

</div>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
