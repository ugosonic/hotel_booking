{{-- resources/views/show_profile.blade.php --}}
@extends('layouts.app')

@section('page-heading','User Profile')

@section('content')
<div class="max-w-5xl mx-auto mt-8 bg-white rounded shadow p-6">
    
<div class="flex justify-end mb-4">
  <a href="{{ route('topup.form') }}?user_id={{ $user->id }}" 
     class="bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-600">
    Top Up This Account
  </a>
</div>
   {{-- Header section --}}
   <div class="flex items-center space-x-4 mb-6">
       {{-- Avatar with initial letter (or an image if you have user->avatar) --}}
       <div class="w-16 h-16 flex items-center justify-center 
                   bg-blue-100 rounded-full text-blue-600 text-2xl font-bold">
           {{ strtoupper(substr($user->name,0,1)) }}
       </div>


       <div>
           <h2 class="text-3xl font-extrabold text-gray-800 mb-1">
             {{ $user->name }}
           </h2>
           <p class="text-gray-500">{{ $user->email }}</p>
           <p class="inline-block mt-1 px-2 py-0.5 rounded-full 
                     {{ $user->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
             {{ ucfirst($user->status) }}
           </p>
       </div>
   </div>


   {{-- Info row: role, created date, maybe more fields --}}
   <div class="grid grid-cols-2 gap-4 mb-8">
      <div>
         <h4 class="text-sm text-gray-600 font-semibold">Role</h4>
         <p class="text-base text-gray-800">{{ ucfirst($user->role) }}</p>
      </div>
      <div>
         <h4 class="text-sm text-gray-600 font-semibold">Joined On</h4>
         <p class="text-base text-gray-800">
           {{ $user->created_at->format('Y-m-d') }}
         </p>
      </div>
      @if($user->accountBalance)
      <div>
         <h4 class="text-sm text-gray-600 font-semibold">Balance</h4>
         <p class="text-base text-indigo-600 font-bold">
           ₦{{ number_format($user->accountBalance->balance,2) }}
         </p>
      </div>
      @endif
      {{-- If staff, you can show special fields here --}}
   </div>

   <hr class="mb-6">

   {{-- Activity feed heading --}}
   <h3 class="text-xl font-bold text-purple-700 mb-3">Recent Activity</h3>

@if($activities->count() < 1)
  <p class="text-gray-500">No recent actions recorded.</p>
@else
  <div class="space-y-4">
    @foreach($activities as $act)
      <!-- show each activity row -->
      <div class="flex items-start space-x-2 border-l-4 pl-3 py-2
           @if($act->type==='login')       border-green-400
           @elseif($act->type==='logout')  border-red-400
           @elseif($act->type==='booking') border-purple-400
           @elseif($act->type==='payment') border-blue-400
           @endif">
        <!-- icon or text type -->
        <div>
          <strong>{{ $act->type }}</strong>
          <span class="text-gray-600 text-sm">
            ({{ $act->created_at->format('Y-m-d H:i') }})
          </span>
        </div>
        <div>{{ $act->description }}</div>
      </div>
    @endforeach
  </div>

  <!-- pagination links -->
  <div class="mt-4">
    {{ $activities->links() }}
  </div>
@endif

</div>
@endsection
