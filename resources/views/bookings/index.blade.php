@extends('layouts.app')

@section('page-heading','View Bookings')

@section('content')
<h1 class="text-xl font-bold mb-4">View Bookings</h1>
<div class="max-w-6xl mx-auto p-4 md:p-6 bg-white rounded shadow">

    <!-- Filter Form -->
    <form method="GET" action="{{ route('bookings.index') }}" class="space-y-2 mb-4">
        @csrf
        <div class="flex flex-wrap items-end gap-4">
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
                <label class="font-semibold">Search (Name / ID)</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="border border-gray-300 rounded px-2 py-1 w-48"
                       placeholder="Name or ID...">
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

            @if(auth()->user()->isStaff())
                <!-- Client ID -->
                <div>
                    <label class="font-semibold">Client ID</label>
                    <input type="number" name="client_id" value="{{ request('client_id') }}"
                           class="border border-gray-300 rounded px-2 py-1"
                           placeholder="Client user_id">
                </div>
                <!-- Guest Only -->
                <div class="flex items-center">
                    <input type="checkbox" name="guest_only" value="1"
                           {{ request('guest_only')=='1' ? 'checked' : '' }}
                           class="mr-1">
                    <label class="font-semibold">Guest Only</label>
                </div>
            @endif
        </div>

        <!-- Sorting -->
        <div class="flex flex-wrap items-end gap-4 mt-2">
            <!-- Sort By -->
            <div>
                <label class="font-semibold">Sort By</label>
                <select name="sort" class="border border-gray-300 rounded px-2 py-1">
                    <option value="created_at" {{ request('sort')=='created_at' ? 'selected' : '' }}>
                        Created Date
                    </option>
                    <option value="start_date" {{ request('sort')=='start_date' ? 'selected' : '' }}>
                        Start Date
                    </option>
                </select>
            </div>
            <!-- Direction -->
            <div>
                <label class="font-semibold">Direction</label>
                <select name="direction" class="border border-gray-300 rounded px-2 py-1">
                    <option value="asc" {{ request('direction')=='asc' ? 'selected' : '' }}>ASC</option>
                    <option value="desc" {{ request('direction','desc')=='desc' ? 'selected' : '' }}>
                        DESC
                    </option>
                </select>
            </div>
            <!-- Submit -->
            <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded mt-2 md:mt-0">
                Filter
            </button>
        </div>
    </form>

    <!-- Responsive container -->
    <div class="overflow-hidden rounded-lg border border-gray-200">
        <!-- For big screens, show a table -->
        <table class="min-w-full hidden md:table">
            <thead>
                <tr class="bg-blue-900 text-white">
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Booked By</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Amount Paid</th>
                    <th class="px-4 py-2 text-left">Amount Due</th>
                    <th class="px-4 py-2 text-left">Payment Method</th>
                    <th class="px-4 py-2 text-left">Countdown</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                    @php
                        // 1) Display name logic
                        if(auth()->user()->isStaff()) {
                            $displayName = $booking->guest_name ?: $booking->client_name;
                        } else {
                            $displayName = $booking->client_name ?: $booking->guest_name;
                        }

                        // 2) Booked by
                        $bookedBy = '';
                        if($booking->staff_id) {
                            $staffUser = \App\Models\User::find($booking->staff_id);
                            $bookedBy  = $staffUser ? $staffUser->name : 'Staff #'.$booking->staff_id;
                        } else {
                            $bookedBy  = auth()->user()->isStaff() ? 'by client' : 'Self';
                        }

                        // 3) Amount Paid
                        $totalPaid = \App\Models\Payment::where('booking_id',$booking->id)
                                      ->where('status','successful')
                                      ->sum('amount');

                        // 4) Payment method
                        $method = '';
                        if($booking->status === 'successful') {
                            $lastPayment = \App\Models\Payment::where('booking_id',$booking->id)
                                            ->where('status','successful')
                                            ->orderBy('id','desc')->first();
                            $method = $lastPayment ? $lastPayment->payment_method : '';
                        }

                        // 5) Countdown (minutes + seconds)
                        $countdownText = '';
                        if($booking->status==='pending' && !empty($booking->countdownSeconds)) {
                            $mins = floor($booking->countdownSeconds / 60);
                            $secs = $booking->countdownSeconds % 60;
                            $countdownText = "{$mins}m : {$secs}s left";
                        }

                        // 6) Amount Due
                        $balanceDue = $booking->total_amount - $totalPaid;
                        if($balanceDue < 0) $balanceDue = 0;
                    @endphp

                    <tr class="hover:bg-gray-50 text-sm">
                        <td class="px-4 py-2">{{ $booking->id }}</td>
                        <td class="px-4 py-2">{{ $displayName }}</td>
                        <td class="px-4 py-2">{{ $bookedBy }}</td>
                        <td class="px-4 py-2 capitalize">
                            @if($booking->status==='pending')
                                <span class="text-orange-600 font-semibold">Pending</span>
                            @elseif($booking->status==='successful')
                                <span class="text-green-600 font-semibold">Successful</span>
                            @else
                                <span class="text-red-600 font-semibold">Canceled</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">₦{{ number_format($totalPaid,2) }}</td>
                        <td class="px-4 py-2">₦{{ number_format($balanceDue,2) }}</td>
                        <td class="px-4 py-2">
                            @if($booking->status==='successful')
                                {{ $method }}
                            @endif
                        </td>
                        <td class="px-4 py-2 text-red-600">
                            {{ $countdownText }}
                        </td>
                        <td class="px-4 py-2">
                            <!-- VIEW button -->
                            <a href="{{ route('bookings.show', $booking->id) }}"
                               class="inline-block bg-blue-500 text-white px-2 py-1 rounded text-xs">
                               View
                            </a>
                            <!-- If pending => Delete, Pay Now, (extend if staff) -->
                            @if($booking->status==='pending')
                                <form action="{{ route('bookings.destroy', $booking->id) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="bg-red-500 text-white px-2 py-1 rounded text-xs"
                                            onclick="return confirm('Delete this booking?')">
                                        Delete
                                    </button>
                                </form>

                                <a href="{{ route('booking.payment', $booking->id) }}"
                                   class="bg-green-600 text-white px-2 py-1 rounded text-xs">
                                   Pay Now
                                </a>

                                @if(auth()->user()->isStaff())
                                    <form action="{{ route('bookings.extend', $booking->id) }}"
                                          method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="hours" value="2" />
                                        <button type="submit"
                                                class="bg-yellow-600 text-white px-2 py-1 rounded text-xs"
                                                onclick="return confirm('Extend by 2 hours?')">
                                            Extend
                                        </button>
                                    </form>
                                @endif
                            @endif

                            <!-- If successful => change booking -->
                            @if($booking->status==='successful')
                                <a href="{{ route('bookings.edit', $booking->id) }}"
                                   class="bg-purple-500 text-white px-2 py-1 rounded text-xs">
                                   Change
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-gray-500">
                            No bookings found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- For small screens, show a "card style" list -->
        <div class="md:hidden">
            @forelse($bookings as $booking)
                @php
                    // same logic as above
                    if(auth()->user()->isStaff()) {
                        $displayName = $booking->guest_name ?: $booking->client_name;
                    } else {
                        $displayName = $booking->client_name ?: $booking->guest_name;
                    }
                    $bookedBy = '';
                    if($booking->staff_id) {
                        $staffUser = \App\Models\User::find($booking->staff_id);
                        $bookedBy  = $staffUser ? $staffUser->name : 'Staff #'.$booking->staff_id;
                    } else {
                        $bookedBy  = auth()->user()->isStaff() ? 'by client' : 'Self';
                    }
                    $totalPaid = \App\Models\Payment::where('booking_id',$booking->id)
                                  ->where('status','successful')->sum('amount');

                    $method = '';
                    if($booking->status==='successful') {
                        $lastPayment = \App\Models\Payment::where('booking_id',$booking->id)
                                        ->where('status','successful')
                                        ->orderBy('id','desc')->first();
                        $method = $lastPayment ? $lastPayment->payment_method : '';
                    }
                    // Countdown
                    $countdownText = '';
                    if($booking->status==='pending' && !empty($booking->countdownSeconds)) {
                        $mins = floor($booking->countdownSeconds / 60);
                        $secs = $booking->countdownSeconds % 60;
                        $countdownText = "{$mins}m : {$secs}s left";
                    }
                    // Amount due
                    $balanceDue = $booking->total_amount - $totalPaid;
                    if($balanceDue < 0) $balanceDue = 0;
                @endphp
                <div class="border-b p-3 hover:bg-gray-50 text-sm">
                    <div class="font-bold text-blue-700">
                        Booking #{{ $booking->id }}
                        <span class="ml-2 capitalize">
                          ({{ $booking->status }})
                        </span>
                    </div>
                    <div class="mt-1">
                        <strong>Name:</strong> {{ $displayName }}
                    </div>
                    <div>
                        <strong>Booked By:</strong> {{ $bookedBy }}
                    </div>
                    <div>
                        <strong>Amount Paid:</strong> ₦{{ number_format($totalPaid,2) }}
                    </div>
                    <div>
                        <strong>Amount Due:</strong> ₦{{ number_format($balanceDue,2) }}
                    </div>
                    @if($booking->status==='successful')
                        <div><strong>Payment Method:</strong> {{ $method }}</div>
                    @endif
                    @if($countdownText)
                        <div class="text-red-600 mt-1">
                            {{ $countdownText }}
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-2 flex flex-wrap gap-2">
                        <a href="{{ route('bookings.show', $booking->id) }}"
                           class="bg-blue-500 text-white px-2 py-1 rounded text-xs">
                           View
                        </a>
                        @if($booking->status==='pending')
                            <form action="{{ route('bookings.destroy', $booking->id) }}"
                                  method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="bg-red-500 text-white px-2 py-1 rounded text-xs"
                                        onclick="return confirm('Delete this booking?')">
                                    Delete
                                </button>
                            </form>
                            <a href="{{ route('booking.payment', $booking->id) }}"
                               class="bg-green-600 text-white px-2 py-1 rounded text-xs">
                               Pay Now
                            </a>
                            @if(auth()->user()->isStaff())
                                <form action="{{ route('bookings.extend', $booking->id) }}"
                                      method="POST" class="inline-block">
                                    @csrf
                                    <input type="hidden" name="hours" value="2" />
                                    <button type="submit"
                                            class="bg-yellow-600 text-white px-2 py-1 rounded text-xs"
                                            onclick="return confirm('Extend by 2 hours?')">
                                        Extend
                                    </button>
                                </form>
                            @endif
                        @endif

                        @if($booking->status==='successful')
                            <a href="{{ route('bookings.edit', $booking->id) }}"
                               class="bg-purple-500 text-white px-2 py-1 rounded text-xs">
                               Change
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-4 text-center text-gray-500">
                    No bookings found.
                </div>
            @endforelse
        </div>
    </div> <!-- end responsive container -->

    <!-- Pagination -->
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
