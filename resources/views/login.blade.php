@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 px-4">
    <!-- The card container -->
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold mb-4 text-center text-purple-600">Login</h1>
        
        {{-- Login Form --}}
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf

            {{-- Email Field --}}
            <div>
                <label for="email" class="block text-gray-700 font-medium">Email Address</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="Enter your email">
            </div>

            {{-- Password Field --}}
            <div>
                <label for="password" class="block text-gray-700 font-medium">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                    placeholder="Enter your password">
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between">
                <label class="inline-flex items-center text-gray-600">
                    <input type="checkbox" name="remember" class="rounded text-purple-600 focus:ring-2 focus:ring-purple-500">
                    <span class="ml-2">Remember Me</span>
                </label>
                <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-purple-600">
                    Forgot Password?
                </a>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                    class="w-full bg-purple-600 text-white font-semibold py-2 rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                Login
            </button>
        </form>
        
        {{-- Additional Links --}}
        <div class="text-center mt-4">
            <p class="text-sm text-gray-700">
                Don't have an account? 
                <a href="{{ route('register_client') }}" class="text-purple-600 hover:underline">
                    Register as Client
                </a>
               
            </p>
        </div>
    </div>
</div>
@endsection
