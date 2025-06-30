@extends('layouts.app')

@section('page-heading', 'My Upcoming Bookings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold mb-6">My Upcoming Bookings</h2>

    @if($bookings->isEmpty())
        <p class="text-red-600">No upcoming bookings.</p>
    @else
        <div class="overflow-hidden rounded-lg border border-gray-300">
            <!-- Table for md+ screens -->
            <table class="min-w-full hidden md:table">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">ID</th>
                        <th class="px-4 py-2 text-left">Apartment</th>
                        <th class="px-4 py-2 text-left">Start Date</th>
                        <th class="px-4 py-2 text-left">End Date</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50 text-sm">
                            <td class="px-4 py-2">{{ $booking->id }}</td>
                            <td class="px-4 py-2">
                                {{ optional($booking->subCategory)->name }}
                            </td>
                            <td class="px-4 py-2 text-blue-600">
                                {{ $booking->start_date }}
                            </td>
                            <td class="px-4 py-2 text-purple-600">
                                {{ $booking->end_date }}
                            </td>
                            <td class="px-4 py-2 capitalize">
                                @if($booking->status==='pending')
                                    <span class="text-orange-600 font-semibold">Pending</span>
                                @elseif($booking->status==='successful')
                                    <span class="text-green-600 font-semibold">Successful</span>
                                @else
                                    {{ $booking->status }}
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                <a href="{{ route('bookings.edit', $booking->id) }}"
                                   class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                   Change
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Card layout for small screens -->
            <div class="md:hidden">
                @foreach($bookings as $booking)
                    <div class="border-b p-3 hover:bg-gray-50 text-sm">
                        <div class="font-bold text-blue-700">
                            Booking #{{ $booking->id }}
                            <span class="ml-2 capitalize">({{ $booking->status }})</span>
                        </div>
                        <div>
                            <strong>Apartment:</strong>
                            {{ optional($booking->subCategory)->name }}
                        </div>
                        <div>
                            <strong>Dates:</strong> 
                            <span class="text-blue-500">{{ $booking->start_date }}</span> 
                            to 
                            <span class="text-purple-500">{{ $booking->end_date }}</span>
                        </div>
                        <!-- Action -->
                        <div class="mt-2">
                            <a href="{{ route('bookings.edit', $booking->id) }}"
                               class="bg-blue-600 text-white px-3 py-1 rounded text-xs">
                               Change
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <!-- Pagination -->
        <div class="mt-4">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
