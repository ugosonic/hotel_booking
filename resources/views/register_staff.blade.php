<!-- resources/views/register_staff.blade.php -->
@extends('layouts.app')

@section('content')
    @include('navbar')

    <div class="min-h-screen bg-gradient-to-r from-green-50 via-yellow-50 to-blue-50 flex items-center justify-center">
        <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full">
            <h1 class="text-2xl font-bold mb-6 text-center text-purple-600">Staff Registration</h1>
            
            {{-- Check for success message --}}
           <!-- Success Alert -->
@if(session('success'))
    <div 
        id="successAlert" 
        class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 my-4 mx-auto max-w-2xl rounded"
    >
        <p class="font-bold mb-1">Success</p>
        <p>{{ session('success') }}</p>
    </div>
@endif

<!-- Error Alert (if you want to show first validation error) -->
@if($errors->any())
    <div 
        id="errorAlert" 
        class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 my-4 mx-auto max-w-2xl rounded"
    >
        <p class="font-bold mb-1">Error</p>
        <!-- You can display all errors or just the first -->
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <form action="{{ route('register_staff.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-gray-700 font-medium">Full Name</label>
                    <input type="text" name="name" id="name" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 font-medium">Email Address</label>
                    <input type="email" name="email" id="email" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-medium">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>

                <div>
                <label for="password_confirmation" class="block text-gray-700 font-medium">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>


                <button type="submit"
                    class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition">
                    Register
                </button>
            </form>
        </div>
    </div>
@endsection

<script>
    // Hide the success alert after 5 seconds
    setTimeout(() => {
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            successAlert.style.display = 'none';
        }
    }, 5000);

    // Hide the error alert after 5 seconds (optional)
    setTimeout(() => {
        const errorAlert = document.getElementById('errorAlert');
        if (errorAlert) {
            errorAlert.style.display = 'none';
        }
    }, 5000);
</script>