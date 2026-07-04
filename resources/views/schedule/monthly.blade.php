@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">

    @include('schedule.partials.header', ['inputType' => 'month'])

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ \Carbon\Carbon::parse($date)->format('F Y') }}
        </span>
        <span class="text-sm text-gray-500 dark:text-gray-400 ml-2">Rooms booked per day</span>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-center">Mon</th>
                    <th class="px-4 py-3 text-center">Tue</th>
                    <th class="px-4 py-3 text-center">Wed</th>
                    <th class="px-4 py-3 text-center">Thu</th>
                    <th class="px-4 py-3 text-center">Fri</th>
                    <th class="px-4 py-3 text-center">Sat</th>
                    <th class="px-4 py-3 text-center">Sun</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $start = $monthStart->copy()->startOfWeek(Carbon\Carbon::MONDAY);
                    $end = $monthEnd->copy()->endOfWeek(Carbon\Carbon::SUNDAY);
                @endphp

                @while($start <= $end)
                <tr class="bg-white dark:bg-gray-800">
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $current = $start->copy()->addDays($i);
                            $count = $bookingCounts[$current->toDateString()] ?? 0;
                            $isCurrentMonth = $current->month === $monthStart->month;
                            $isToday = $current->isToday();
                        @endphp
                        <td class="px-2 py-3 text-center border border-gray-200 dark:border-gray-700 
                            {{ $isToday ? 'bg-blue-50 dark:bg-blue-900' : '' }} 
                            {{ !$isCurrentMonth ? 'opacity-30' : '' }}">
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
