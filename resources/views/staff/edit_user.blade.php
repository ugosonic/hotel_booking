@extends('layouts.app')

@section('page-heading','Edit User')

@section('content')
<div class="max-w-md mx-auto bg-white p-6 mt-6 rounded shadow">
  <h2 class="text-2xl font-bold mb-4">Edit User</h2>

  <form action="{{ route('staff.update_profile',$user->id) }}" method="POST" class="space-y-4">
    @csrf
    <div>
      <label class="font-semibold">Name</label>
      <input type="text" name="name" value="{{ old('name',$user->name) }}"
             class="border w-full rounded p-2">
    </div>
    <div>
      <label class="font-semibold">Email</label>
      <input type="email" name="email" value="{{ old('email',$user->email) }}"
             class="border w-full rounded p-2">
    </div>

    <div class="flex items-center space-x-2">
      <input type="checkbox" name="send_password_link" value="1" id="sendPassLink">
      <label for="sendPassLink">Send password reset link?</label>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
  </form>
</div>
@endsection
