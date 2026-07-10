@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Dashboard</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Today's Hearings</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $todayBookings->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Today's Events</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $todayEvents->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Rooms in Use</p>
            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $roomsInUse }} / {{ $totalRooms }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
            <p class="text-3xl font-bold text-orange-600 dark:text-orange-400">{{ $monthBookings + $monthEvents }}</p>
        </div>
    </div>

    {{-- Today's Schedule --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Hearings --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Today's Hearings ({{ $today->format('d/m/Y') }})
            </h2>
            @if($todayBookings->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm">No hearings today.</p>
            @else
                <div class="space-y-2">
                    @foreach($todayBookings as $booking)
                        @php $claimant = $booking->participants->where('role', 'claimant')->first(); @endphp
                        <a href="{{ route('bookings.edit', $booking) }}" class="block p-3 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $booking->room->room_code }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-xs font-medium
                                    {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800' : '' }}">
                                    {{ match($booking->session_type) { 'full_day' => 'Full', 'half_am' => 'AM', 'half_pm' => 'PM', default => $booking->session_type } }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $claimant?->contact?->name ?? $booking->booking_id }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Events --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">
                Today's Events
            </h2>
            @if($todayEvents->isEmpty())
                <p class="text-gray-500 dark:text-gray-400 text-sm">No events today.</p>
            @else
                <div class="space-y-2">
                    @foreach($todayEvents as $event)
                        <a href="{{ route('events.edit', $event) }}" class="block p-3 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                            <div class="flex justify-between items-start">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $event->event_name }}</span>
                                    <span class="text-xs text-gray-500 ml-2">{{ $event->room->room_code }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event->start_time }} - {{ $event->end_time }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
