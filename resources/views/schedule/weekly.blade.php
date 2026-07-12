@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @include('schedule.partials.header', ['inputType' => 'date'])

    {{-- Room Filter --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 mb-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Filter Rooms</h3>
            <div class="flex gap-2 items-center">
                <button onclick="checkAll()"
                    class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">Select All</button>
                <span class="text-gray-300">|</span>
                <button onclick="uncheckAll()"
                    class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium">Clear</button>
                <span class="text-gray-300">|</span>
                <label class="flex items-center gap-1 text-xs cursor-pointer">
                    <input type="checkbox" id="showUsedOnly" onchange="filterUsedRooms()" class="w-3.5 h-3.5 rounded">
                    <span class="text-gray-600 dark:text-gray-300">In use this week only</span>
                </label>
            </div>
        </div>
        <div class="flex flex-wrap gap-1.5">
            @foreach($rooms as $room)
            <label class="cursor-pointer">
                <input type="checkbox" class="room-filter hidden peer" value="{{ $room->id }}" checked
                    onchange="filterRooms()">
                <span
                    class="inline-block px-2.5 py-1 text-xs rounded-md border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 peer-checked:bg-blue-100 peer-checked:text-blue-800 peer-checked:border-blue-300 dark:peer-checked:bg-blue-900 dark:peer-checked:text-blue-300 dark:peer-checked:border-blue-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ $room->room_code }}
                </span>
            </label>
            @endforeach
        </div>
    </div>

    <script>
        // Load saved filter on page load
        document.addEventListener('DOMContentLoaded', function () {
            const saved = JSON.parse(localStorage.getItem('roomFilter') || 'null');
            document.querySelectorAll('.room-filter').forEach(cb => {
                if (saved) {
                    cb.checked = saved.includes(cb.value) || !(saved.length);
                }
            });
            filterRooms();
            filterUsedRooms();
        });

        function filterRooms() {
            const checked = [];
            document.querySelectorAll('.room-filter').forEach(cb => {
                const row = document.getElementById('room-row-' + cb.value);
                if (row) row.style.display = cb.checked ? '' : 'none';
                if (cb.checked) checked.push(cb.value);
            });
            localStorage.setItem('roomFilter', JSON.stringify(checked));
            filterUsedRooms();
        }

        function filterUsedRooms() {
            const showUsedOnly = document.getElementById('showUsedOnly').checked;
            document.querySelectorAll('.room-filter').forEach(cb => {
                const row = document.getElementById('room-row-' + cb.value);
                if (!row || !cb.checked) return;
                if (showUsedOnly) {
                    const hasContent = row.querySelectorAll('td .space-y-2 > a, td .space-y-1 > a').length > 0;
                    row.style.display = hasContent ? '' : 'none';
                }
            });
        }

        function checkAll() {
            document.querySelectorAll('.room-filter').forEach(cb => { cb.checked = true; });
            filterRooms();
        }
        function uncheckAll() {
            document.querySelectorAll('.room-filter').forEach(cb => { cb.checked = false; });
            filterRooms();
        }
    </script>

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ $startOfWeek->format('d/m/Y') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d/m/Y') }}
            <a href="{{ route('schedule.print-week', ['date' => $startOfWeek->toDateString()]) }}"
                class="text-sm text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 border px-3 py-1 rounded hover:border-blue-300"
                target="_blank">🖨 Print Week</a>
        </span>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full table-fixed text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 w-20">Room</th>
                    @foreach($weekDays as $day)
                    <th
                        class="px-2 py-3 text-center w-[calc((100%-5rem)/7)] {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                        {{ $day->format('D') }}<br>{{ $day->format('d') }}
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr id="room-row-{{ $room->id }}"
                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 h-32">
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white align-top">{{ $room->room_code }}
                    </td>
                    @foreach($weekDays as $day)
                    @php
                    $dayBookings = $bookings[$room->id][$day->toDateString()] ?? collect();
                    $dayEvents = $events[$room->id][$day->toDateString()] ?? collect();
                    @endphp
                    <td
                        class="px-2 py-2 text-center text-xs align-top {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                        @if($dayBookings->isNotEmpty() || $dayEvents->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($dayBookings as $booking)
                            @php
                            $claimant = $booking->participants->where('role', 'claimant')->first();
                            $respondent = $booking->participants->where('role', 'respondent')->first();
                            $ttId = 'tt-booking-' . $booking->id;
                            @endphp
                            <a href="{{ route('bookings.edit', $booking) }}" data-tooltip-target="{{ $ttId }}"
                                data-tooltip-placement="bottom"
                                class="block px-2 py-1.5 rounded font-medium no-underline text-xs leading-tight
                                    {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                    {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                    {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}">
                                <div class="font-semibold">{{ $claimant?->contact?->name ?? 'TBA' }}</div>
                                <div class="text-[10px] opacity-75">v</div>
                                <div class="font-semibold">{{ $respondent?->contact?->name ?? 'TBA' }}</div>
                            </a>
                            <div id="{{ $ttId }}" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700 max-w-xs">
                                <p class="font-bold">{{ $booking->booking_id }}</p>
                                <p>{{ $claimant?->contact?->name ?? '-' }} v {{ $respondent?->contact?->name ?? '-' }}
                                </p>
                                <p>Arbitrator: {{ $arbitrators ?? '-' }}</p>
                                <p>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{
                                    \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</p>
                                @if($booking->features->isNotEmpty())
                                <p>Features: {{ $booking->features->pluck('name')->implode(', ') }}</p>
                                @endif
                                <div class="tooltip-arrow" data-popper-arrow></div>
                            </div>
                            @endforeach
                            @foreach($dayEvents as $event)
                            @php $etId = 'tt-event-' . $event->id; @endphp
                            <a href="{{ route('events.edit', $event) }}" data-tooltip-target="{{ $etId }}"
                                data-tooltip-placement="bottom"
                                class="block px-2 py-1.5 rounded font-medium no-underline bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 text-xs leading-tight">
                                <div class="font-semibold">{{ $event->event_name }}</div>
                            </a>
                            <div id="{{ $etId }}" role="tooltip"
                                class="absolute z-10 invisible inline-block px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700 max-w-xs">
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