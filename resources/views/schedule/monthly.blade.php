@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule</h1>

        <div class="flex items-center space-x-4">
            <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                <a href="{{ route('schedule.index', ['view' => 'daily', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm text-gray-600 dark:text-gray-300">Daily</a>
                <a href="{{ route('schedule.index', ['view' => 'weekly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm text-gray-600 dark:text-gray-300">Weekly</a>
                <a href="{{ route('schedule.index', ['view' => 'monthly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow">Monthly</a>
            </div>

            <form action="{{ route('schedule.index') }}" method="GET" class="flex items-center space-x-2">
                <input type="hidden" name="view" value="monthly">
                <input type="month" name="date" value="{{ \Carbon\Carbon::parse($date)->format('Y-m') }}"
                       class="border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Go</button>
            </form>
        </div>
    </div>

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ \Carbon\Carbon::parse($date)->format('F Y') }}
        </span>
        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Number of rooms booked per day</span>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mon</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Tue</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Wed</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Thu</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Fri</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sat</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sun</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $start = $monthStart->copy()->startOfWeek(Carbon\Carbon::MONDAY);
                    $end = $monthEnd->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
                @endphp

                @while($start <= $end)
                <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $current = $start->copy()->addDays($i);
                            $count = $bookingCounts[$current->toDateString()] ?? 0;
                            $isCurrentMonth = $current->month === $monthStart->month;
                            $isToday = $current->isToday();
                        @endphp
                        <td class="px-2 py-3 text-center {{ $isToday ? 'bg-blue-50 dark:bg-blue-900' : '' }} {{ !$isCurrentMonth ? 'opacity-30' : '' }}">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $current->format('d') }}</div>
                            @if($isCurrentMonth)
                                <div class="text-lg font-bold mt-1
                                    {{ $count >= 15 ? 'text-red-600 dark:text-red-400' : '' }}
                                    {{ $count >= 8 && $count < 15 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                    {{ $count > 0 && $count < 8 ? 'text-green-600 dark:text-green-400' : '' }}
                                    {{ $count == 0 ? 'text-gray-300 dark:text-gray-600' : '' }}">
                                    {{ $count ?: '-' }}
                                </div>
                            @endif
                        </td>
                    @endfor
                </tr>
                @php $start->addWeek(); @endphp
                @endwhile
            </tbody>
        </table>
    </div>
</div>
@endsection
