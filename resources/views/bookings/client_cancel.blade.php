@extends('layouts.app')

@section('page-heading', 'Cancel My Bookings')

@section('content')
<div class="max-w-6xl mx-auto bg-white p-6 rounded shadow">

    <h2 class="text-xl font-bold mb-4">My Bookings (Cancel/Refund)</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('booking.cancel') }}" class="mb-4 space-y-2">
        <div class="flex space-x-4">
            <div>
                <label class="font-semibold">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="border-gray-300 rounded" />
            </div>
            <div>
                <label class="font-semibold">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="border-gray-300 rounded" />
            </div>
        </div>

        <div class="flex space-x-4 mt-2">
            <div>
                <label class="font-semibold">Search (ID / Guest Name)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="border-gray-300 rounded w-48" placeholder="Booking ID or name" />
            </div>
            <div>
                <label class="font-semibold">Status</label>
                <select name="status" class="border-gray-300 rounded">
                    <option value="">-- All --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                </select>
            </div>
        </div>

        <!-- Sorting -->
        <div class="flex space-x-4 mt-2">
            <div>
                <label class="font-semibold">Sort By</label>
                <select name="sort" class="border-gray-300 rounded">
                    <option value="id" {{ request('sort')=='id' ? 'selected' : '' }}>Booking ID</option>
                    <option value="created_at" {{ request('sort')=='created_at' ? 'selected' : '' }}>Created Date</option>
                    <option value="start_date" {{ request('sort')=='start_date' ? 'selected' : '' }}>Start Date</option>
                    <option value="total_amount" {{ request('sort')=='total_amount' ? 'selected' : '' }}>Total Amount</option>
                </select>
            </div>
            <div>
                <label class="font-semibold">Direction</label>
                <select name="direction" class="border-gray-300 rounded">
                    <option value="asc" {{ request('direction')=='asc' ? 'selected' : '' }}>ASC</option>
                    <option value="desc" {{ request('direction','desc')=='desc' ? 'selected' : '' }}>DESC</option>
                </select>
            </div>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded mt-2">
            Filter
        </button>
    </form>

    <!-- Display bookings in a table -->
    <table class="w-full text-sm border-collapse">
        <thead>
            <tr class="border-b bg-gray-50">
                <th class="py-2 px-2 text-left">ID</th>
                <th class="py-2 px-2 text-left">Status</th>
                <th class="py-2 px-2 text-left">Price (Per Night)</th>
                <th class="py-2 px-2 text-left">Total Amount</th>
                <th class="py-2 px-2 text-left">Created At</th>
                <th class="py-2 px-2 text-left">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                <tr class="border-b hover:bg-gray-100">
                    <td class="py-2 px-2">{{ $booking->id }}</td>
                    <td class="py-2 px-2">{{ $booking->status }}</td>
                    <td class="py-2 px-2">
                        ₦{{ number_format($booking->price, 2) }}
                    </td>
                    <td class="py-2 px-2">
                        ₦{{ number_format($booking->total_amount, 2) }}
                    </td>
                    <td class="py-2 px-2">
                        {{ $booking->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="py-2 px-2">
                        @if($booking->status !== 'canceled')
                            <form action="{{ route('booking.cancel.do', $booking->id) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded"
                                        onclick="return confirm('Are you sure you want to cancel this booking?');">
                                    Cancel
                                </button>
                            </form>
                        @else
                            <span class="text-gray-500">Already Canceled</span>
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

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
