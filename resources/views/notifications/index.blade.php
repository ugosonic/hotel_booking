@extends('layouts.app')

@section('content')

@include('partials.sidebar') {{-- the new mini-sidebar --}}
<div class="max-w-xl mx-auto bg-white p-6">

    <h2 class="text-2xl font-bold mb-4">Notification Settings</h2>



    <form method="POST" action="{{ route('notifications.update') }}">
        @csrf

        <label class="flex items-center mb-3">
            <input type="checkbox" name="login_notification" 
                   class="mr-2"
                   {{ $settings->login_notification ? 'checked' : '' }}>
            <span>Login Notification</span>
        </label>

        <label class="flex items-center mb-3">
            <input type="checkbox" name="password_changed_notification" 
                   class="mr-2"
                   {{ $settings->password_changed_notification ? 'checked' : '' }}>
            <span>Password Changed Notification</span>
        </label>

        <label class="flex items-center mb-3">
            <input type="checkbox" name="payment_error_notification" 
                   class="mr-2"
                   {{ $settings->payment_error_notification ? 'checked' : '' }}>
            <span>Payment Error Notification</span>
        </label>

        <label class="flex items-center mb-3">
            <input type="checkbox" name="payment_success_notification" 
                   class="mr-2"
                   {{ $settings->payment_success_notification ? 'checked' : '' }}>
            <span>Payment Success Notification</span>
        </label>

        <label class="flex items-center mb-3">
            <input type="checkbox" name="pending_topup_notification" 
                   class="mr-2"
                   {{ $settings->pending_topup_notification ? 'checked' : '' }}>
            <span>Pending Top-Up Notification</span>
        </label>

        <label class="flex items-center mb-3">
            <input type="checkbox" name="registration_welcome_notification" 
                   class="mr-2"
                   {{ $settings->registration_welcome_notification ? 'checked' : '' }}>
            <span>Registration Welcome Notification</span>
        </label>

        <button type="submit"
                class="bg-blue-600 text-white px-4 py-2 rounded">
            Save Settings
        </button>
    </form>

</div>
<script src="//unpkg.com/alpinejs" defer></script>
@endsection

