@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule</h1>

        <div class="flex items-center space-x-4">
            <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                <a href="{{ route('schedule.index', ['view' => 'daily', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm text-gray-600 dark:text-gray-300">Daily</a>
                <a href="{{ route('schedule.index', ['view' => 'weekly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow">Weekly</a>
                <a href="{{ route('schedule.index', ['view' => 'monthly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm text-gray-600 dark:text-gray-300">Monthly</a>
            </div>

            <form action="{{ route('schedule.index') }}" method="GET" class="flex items-center space-x-2">
                <input type="hidden" name="view" value="weekly">
                <input type="date" name="date" value="{{ $date }}"
                       class="border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Go</button>
            </form>
        </div>
    </div>

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ $startOfWeek->format('d M Y') }} - {{ $startOfWeek->copy()->endOfWeek()->format('d M Y') }}
        </span>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase w-20">Room</th>
                        @foreach($weekDays as $day)
                            <th class="px-2 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                                {{ $day->format('D') }}<br>{{ $day->format('d') }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($rooms as $room)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $room->room_code }}</td>
                        @foreach($weekDays as $day)
                            @php
                                $dayBookings = $bookings[$room->id][$day->toDateString()] ?? collect();
                            @endphp
                            <td class="px-2 py-3 text-center {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                                @if($dayBookings->isNotEmpty())
                                    <div class="space-y-1">
                                        @foreach($dayBookings as $booking)
                                            <div class="text-xs px-1 py-0.5 rounded cursor-pointer
                                                {{ $booking->session_type === 'full_day' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : '' }}
                                                {{ $booking->session_type === 'half_am' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300' : '' }}
                                                {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300' : '' }}"
                                                title="{{ $booking->booking_id }} - {{ $booking->case->reference_number ?? '' }}">
                                                {{ $booking->session_type === 'full_day' ? 'Full' : ($booking->session_type === 'half_am' ? 'AM' : ($booking->session_type === 'half_pm' ? 'PM' : 'OT')) }}
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-600">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
