@extends('layouts.app')

@section('page-heading','Account Management')

@section('content')
<div class="max-w-7xl mx-auto p-6 bg-white rounded shadow">
  <h2 class="text-2xl font-bold mb-4">Account Management</h2>

  <div class="flex space-x-4 mb-6">
    <!-- Tab for staff -->
    <a href="{{ route('staff.account_management',['role'=>'staff']) }}"
       class="px-4 py-2 rounded {{ $role==='staff'?'bg-blue-600 text-white':'bg-gray-200' }}">
       Staff
    </a>
    <!-- Tab for clients -->
    <a href="{{ route('staff.account_management',['role'=>'client']) }}"
       class="px-4 py-2 rounded {{ $role==='client'?'bg-blue-600 text-white':'bg-gray-200' }}">
       Clients
    </a>
  </div>
  
                <a href="{{ route('register_staff') }}" class="text-purple-600 hover:underline">
                    Register as Staff
                </a>.

  <!-- Search -->
  <form method="GET" action="{{ route('staff.account_management') }}" class="mb-4">
    <input type="hidden" name="role" value="{{ $role }}">
    <label>Search: </label>
    <input type="text" name="search" class="border rounded p-1" value="{{ request('search') }}">
    <button class="bg-blue-600 text-white px-3 py-1 rounded">Go</button>
  </form>

  @if($users->count() < 1)
    <p>No users found for {{ $role }}.</p>
  @else
    <table class="w-full border text-sm">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-2 text-left">ID</th>
          <th class="p-2 text-left">Name</th>
          <th class="p-2 text-left">Email</th>
          <th class="p-2 text-left">Status</th>
          <th class="p-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $u)
          <tr class="border-b">
            <td class="p-2">{{ $u->id }}</td>
            <td class="p-2">{{ $u->name }}</td>
            <td class="p-2">{{ $u->email }}</td>
            <td class="p-2">{{ $u->status }}</td>
            <td class="p-2 space-x-2">
               <a href="{{ route('staff.show_profile',$u->id) }}"
                  class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                  View Profile
               </a>
               <a href="{{ route('staff.edit_profile',$u->id) }}"
                  class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                  Edit
               </a>
               @if($u->status==='active')
                  <form action="{{ route('staff.suspend',$u->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-red-500 text-white px-2 py-1 rounded text-xs"
                            onclick="return confirm('Suspend this user?')">
                      Suspend
                    </button>
                  </form>
               @else
                  <form action="{{ route('staff.unsuspend',$u->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs"
                            onclick="return confirm('Unsuspend this user?')">
                      Unsuspend
                    </button>
                  </form>
               @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-4">
      {{ $users->links() }}
    </div>
  @endif
</div>
@endsection
