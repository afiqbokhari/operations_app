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
                        @php $dayBookings = $bookings[$room->id][$day->toDateString()] ?? collect(); @endphp
                        <td class="px-2 py-3 text-center {{ $day->isToday() ? 'bg-blue-50 dark:bg-blue-900' : '' }}">
                            @if($dayBookings->isNotEmpty())
                                <div class="space-y-1">
                                    @foreach($dayBookings as $booking)
                                        <div class="text-xs px-1.5 py-0.5 rounded font-medium
                                            {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                            {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                            {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}">
                                            {{ match($booking->session_type) { 'full_day' => 'Full', 'half_am' => 'AM', 'half_pm' => 'PM', 'overtime' => 'OT', default => $booking->session_type } }}
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
@endsection
