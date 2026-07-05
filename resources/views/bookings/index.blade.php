@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bookings</h1>
        <a href="/bookings/create" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
            + New Booking
        </a>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking ID or Case Ref..."
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 flex-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Status</option>
                <option value="tentative" {{ request('status') === 'tentative' ? 'selected' : '' }}>Tentative</option>
                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Search</button>
            <a href="{{ route('bookings.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Clear</a>
        </form>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Booking ID</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3">Session</th>
                    <th class="px-4 py-3 hidden md:table-cell">Case Ref</th>
                    <th class="px-4 py-3 hidden lg:table-cell">Claimant</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $booking->booking_id }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</td>
                    <td class="px-4 py-3">{{ $booking->room->room_code }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                            {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                            {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}
                            {{ $booking->session_type === 'overtime' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : '' }}">
                            {{ match($booking->session_type) { 'full_day' => 'Full', 'half_am' => 'AM', 'half_pm' => 'PM', 'overtime' => 'OT', default => $booking->session_type } }}
                        </span>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">{{ $booking->case->reference_number ?? '-' }}</td>
                    <td class="px-4 py-3 hidden lg:table-cell">
                        @php $claimant = $booking->participants->where('role', 'claimant')->first(); @endphp
                        {{ $claimant?->contact?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded
                            {{ $booking->booking_status === 'confirmed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $booking->booking_status === 'tentative' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                            {{ $booking->booking_status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}
                            {{ $booking->booking_status === 'completed' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}">
                            {{ ucfirst($booking->booking_status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <button class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800">View</button>
                            <a href="{{ route('bookings.edit', $booking) }}" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border-t border-b border-green-200 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-800 no-underline">Edit</a>
                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('Cancel this booking?');" class="inline">@csrf @method('DELETE') <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800">Cancel</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No bookings found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
