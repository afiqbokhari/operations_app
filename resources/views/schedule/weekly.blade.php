@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @include('schedule.partials.header', ['inputType' => 'date'])

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ $startOfWeek->format('d M Y') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d M Y') }}
        </span>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 w-20">Room</th>
                    @foreach($weekDays as $day)
                        <th class="px-2 py-3 text-center {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                            {{ $day->format('D') }}<br>{{ $day->format('d') }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $room->room_code }}</td>
                    @foreach($weekDays as $day)
                        @php
                            $dayBookings = $bookings[$room->id][$day->toDateString()] ?? collect();
                            $dayEvents = $events[$room->id][$day->toDateString()] ?? collect();
                        @endphp
                        <td class="px-1 py-2 text-center text-xs {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                            @if($dayBookings->isNotEmpty() || $dayEvents->isNotEmpty())
                                <div class="space-y-1">
                                    @foreach($dayBookings as $booking)
                                        @php
                                            $claimant = $booking->participants->where('role', 'claimant')->first();
                                            $respondent = $booking->participants->where('role', 'respondent')->first();
                                            $arbitrators = $booking->participants->whereIn('role', ['presiding_arbitrator', 'co_arbitrator'])->pluck('contact.name')->implode(', ');
                                            $ttId = 'tt-booking-' . $booking->id;
                                        @endphp
                                        <a href="{{ route('bookings.edit', $booking) }}" 
                                           data-tooltip-target="{{ $ttId }}" data-tooltip-placement="bottom"
                                           class="block px-1 py-0.5 rounded font-medium no-underline
                                            {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                            {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                            {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}">
                                            {{ \Illuminate\Support\Str::limit($booking->case->reference_number ?? $booking->booking_id, 8) }}
                                        </a>
                                        <div id="{{ $ttId }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700 max-w-xs">
                                            <p class="font-bold">{{ $booking->booking_id }}</p>
                                            <p>{{ $claimant?->contact?->name ?? '-' }} v {{ $respondent?->contact?->name ?? '-' }}</p>
                                            <p>Arbitrator: {{ $arbitrators ?: '-' }}</p>
                                            <p>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                                            @if($booking->features->isNotEmpty())
                                            <p>Features: {{ $booking->features->pluck('name')->implode(', ') }}</p>
                                            @endif
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    @endforeach
                                    @foreach($dayEvents as $event)
                                        @php $etId = 'tt-event-' . $event->id; @endphp
                                        <a href="{{ route('events.edit', $event) }}" 
                                           data-tooltip-target="{{ $etId }}" data-tooltip-placement="bottom"
                                           class="block px-1 py-0.5 rounded font-medium no-underline bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            {{ \Illuminate\Support\Str::limit($event->event_name, 10) }}
                                        </a>
                                        <div id="{{ $etId }}" role="tooltip" class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700 max-w-xs">
                                            <p class="font-bold">{{ $event->event_name }}</p>
                                            
                                            <p>{{ $event->start_date->format('d M') }} - {{ $event->end_date->format('d M') }}</p>
                                            <p>{{ $event->start_time }} - {{ $event->end_time }}</p>
                                            <p>Organizer: {{ $event->organizer ?? '-' }}</p>
                                            <div class="tooltip-arrow" data-popper-arrow></div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">-</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
