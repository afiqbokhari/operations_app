@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Search Results for "{{ $query }}"</h1>

    @if($bookings->isEmpty() && $events->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 text-center text-gray-500 dark:text-gray-400">
            No results found.
        </div>
    @endif

    @if($bookings->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Hearings ({{ $bookings->count() }})</h2>
    <div class="relative overflow-x-auto shadow-md rounded-lg mb-6">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Booking ID</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3 hidden md:table-cell">Case</th>
                    <th class="px-4 py-3 hidden md:table-cell">Claimant</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $b)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer" onclick="window.location='{{ route('bookings.edit', $b) }}'">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $b->booking_id }}</td>
                    <td class="px-4 py-3 text-xs">{{ $b->booking_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $b->room->room_code }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">{{ $b->case->reference_number ?? '-' }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        @php $cl = $b->participants->where('role','claimant')->first(); @endphp
                        {{ $cl?->contact?->name ?? '-' }}
                    </td>
                    <td class="px-4 py-3">{{ ucfirst($b->booking_status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($events->isNotEmpty())
    <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-3">Events ({{ $events->count() }})</h2>
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3">Event</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3 hidden md:table-cell">Date</th>
                    <th class="px-4 py-3 hidden md:table-cell">Organizer</th>
                    <th class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $e)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer" onclick="window.location='{{ route('events.edit', $e) }}'">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $e->event_name }}</td>
                    <td class="px-4 py-3">{{ $e->room->room_code }}</td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs">{{ $e->start_date->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">{{ $e->organizer ?? '-' }}</td>
                    <td class="px-4 py-3">{{ ucfirst($e->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
