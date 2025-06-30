@extends('layouts.app')

@section('page-heading','All Bookings - Staff Cancel')

@section('content')
<div class="max-w-7xl mx-auto p-4 md:p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">All Bookings (Staff Cancel Panel)</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('staff.booking.cancel') }}" class="space-y-2 mb-4">
        <div class="flex flex-wrap gap-4">
            <!-- Start Date -->
            <div>
                <label class="font-semibold">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="border border-gray-300 rounded px-2 py-1">
            </div>
            <!-- End Date -->
            <div>
                <label class="font-semibold">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="border border-gray-300 rounded px-2 py-1">
            </div>
            <!-- Search -->
            <div>
                <label class="font-semibold">Search (Booking ID / Name)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="border border-gray-300 rounded px-2 py-1 w-48"
                       placeholder="e.g. 12 or John">
            </div>
            <!-- Status -->
            <div>
                <label class="font-semibold">Status</label>
                <select name="status" class="border border-gray-300 rounded px-2 py-1">
                    <option value="">-- All --</option>
                    <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="successful" {{ request('status')=='successful' ? 'selected' : '' }}>
                        Successful
                    </option>
                    <option value="canceled" {{ request('status')=='canceled' ? 'selected' : '' }}>
                        Canceled
                    </option>
                </select>
            </div>
            <!-- Sort -->
            <div>
                <label class="font-semibold">Sort By</label>
                <select name="sort" class="border border-gray-300 rounded px-2 py-1">
                    <option value="id" {{ request('sort')=='id' ? 'selected' : '' }}>
                        Booking ID
                    </option>
                    <option value="created_at" {{ request('sort')=='created_at' ? 'selected' : '' }}>
                        Created At
                    </option>
                    <option value="start_date" {{ request('sort')=='start_date' ? 'selected' : '' }}>
                        Start Date
                    </option>
                    <option value="total_amount" {{ request('sort')=='total_amount' ? 'selected' : '' }}>
                        Total Amount
                    </option>
                </select>
            </div>
            <!-- Direction -->
            <div>
                <label class="font-semibold">Direction</label>
                <select name="direction" class="border border-gray-300 rounded px-2 py-1">
                    <option value="asc" {{ request('direction')=='asc' ? 'selected' : '' }}>
                        ASC
                    </option>
                    <option value="desc" {{ request('direction','desc')=='desc' ? 'selected' : '' }}>
                        DESC
                    </option>
                </select>
            </div>
            
            <!-- Submit button -->
            <div>
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded mt-2 md:mt-0">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Desktop Table -->
    <div class="overflow-hidden border border-gray-200 rounded-lg">
      <table class="min-w-full hidden md:table">
        <thead class="bg-blue-900 text-white">
            <tr>
                <th class="px-4 py-2 text-left">ID</th>
                <th class="px-4 py-2 text-left">Client/Guest</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-left">Total Amount</th>
                <th class="px-4 py-2 text-left">Created At</th>
                <th class="px-4 py-2 text-left">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-sm">
            @forelse($bookings as $booking)
                @php
                  // Show either client_name or guest_name
                  $nameToDisplay = $booking->client_name ?: $booking->guest_name;
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $booking->id }}</td>
                    <td class="px-4 py-2">
                        {{ $nameToDisplay }}
                        @if($booking->guest_name)
                            <span class="text-xs text-gray-500"> (guest)</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 capitalize">
                        @if($booking->status === 'pending')
                          <span class="text-orange-600 font-semibold">Pending</span>
                        @elseif($booking->status === 'successful')
                          <span class="text-green-600 font-semibold">Successful</span>
                        @else
                          <span class="text-red-600 font-semibold">Canceled</span>
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        ₦{{ number_format($booking->total_amount, 2) }}
                    </td>
                    <td class="px-4 py-2">
                        {{ $booking->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-4 py-2">
                        @if($booking->status !== 'canceled')
                            <form action="{{ route('staff.booking.cancel.do', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded text-xs"
                                        onclick="return confirm('Cancel booking #{{ $booking->id }} ?');">
                                    Cancel
                                </button>
                            </form>
                        @else
                            <span class="text-gray-500">Canceled</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        No bookings found.
                    </td>
                </tr>
            @endforelse
        </tbody>
      </table>

      <!-- Mobile Card View -->
      <div class="md:hidden">
         @forelse($bookings as $booking)
            @php
              $nameToDisplay = $booking->client_name ?: $booking->guest_name;
            @endphp
            <div class="border-b p-3 hover:bg-gray-50 text-sm">
                <div class="font-bold text-blue-700">
                    Booking #{{ $booking->id }}
                    <span class="ml-2 capitalize">
                        ({{ $booking->status }})
                    </span>
                </div>
                <div class="mt-1">
                    <strong>Name:</strong> {{ $nameToDisplay }}
                    @if($booking->guest_name)
                        <span class="text-xs text-gray-500"> (guest)</span>
                    @endif
                </div>
                <div>
                    <strong>Total Amount:</strong> 
                    ₦{{ number_format($booking->total_amount,2) }}
                </div>
                <div>
                    <strong>Created At:</strong> 
                    {{ $booking->created_at->format('Y-m-d H:i') }}
                </div>
                <div class="mt-2">
                @if($booking->status !== 'canceled')
                    <form action="{{ route('staff.booking.cancel.do', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="bg-red-600 text-white px-3 py-1 rounded text-xs"
                                onclick="return confirm('Cancel booking #{{ $booking->id }} ?');">
                            Cancel
                        </button>
                    </form>
                @else
                    <span class="text-gray-500">Canceled</span>
                @endif
                </div>
            </div>
         @empty
            <div class="p-4 text-center text-gray-500">
                No bookings found.
            </div>
         @endforelse
      </div>
    </div> <!-- end border wrapper -->

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
