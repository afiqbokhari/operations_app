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
                            $dateStr = $current->toDateString();
                            $count = $bookingCounts[$dateStr] ?? 0;
                            $dayBookings = $monthBookings[$dateStr] ?? collect();
                            $dayEvents = $monthEvents[$dateStr] ?? collect();
                            $isCurrentMonth = $current->month === $monthStart->month;
                            $isToday = $current->isToday();
                            $ttId = 'tt-month-' . $dateStr;
                        @endphp
                        <td class="px-2 py-3 text-center border border-gray-200 dark:border-gray-700 
                            {{ $isToday ? 'bg-blue-50 dark:bg-blue-900' : '' }} 
                            {{ !$isCurrentMonth ? 'opacity-30' : '' }}">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $current->format('d') }}</div>
                            @if($isCurrentMonth && ($count > 0))
                                <div class="text-lg font-bold mt-1 cursor-pointer
                                    {{ $count >= 15 ? 'text-red-600 dark:text-red-400' : '' }}
                                    {{ $count >= 8 && $count < 15 ? 'text-yellow-600 dark:text-yellow-400' : '' }}
                                    {{ $count > 0 && $count < 8 ? 'text-green-600 dark:text-green-400' : '' }}"
                                    data-tooltip-target="{{ $ttId }}" data-tooltip-placement="bottom" data-tooltip-trigger="click">
                                    {{ $count }}
                                </div>
                                <div id="{{ $ttId }}" role="tooltip" class="absolute z-50 invisible inline-block px-3 py-2 text-xs font-medium text-white bg-gray-900 rounded-lg shadow-sm opacity-0 tooltip dark:bg-gray-700 max-w-xs text-left max-h-60 overflow-y-auto">
                                    @foreach($dayBookings as $b)
                                        @php
                                            $claimant = $b->participants->where('role', 'claimant')->first();
                                            $respondent = $b->participants->where('role', 'respondent')->first();
                                        @endphp
                                        <p class="font-bold">{{ $b->booking_id }}</p>
                                        <p>{{ $claimant?->contact?->name ?? '-' }} v {{ $respondent?->contact?->name ?? '-' }}</p>
                                        <p>{{ $b->room->room_code }} | {{ \Carbon\Carbon::parse($b->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($b->end_time)->format('H:i') }}</p>
                                        @if(!$loop->last)<hr class="my-1 border-gray-600">@endif
                                    @endforeach
                                    @if($dayBookings->isNotEmpty() && $dayEvents->isNotEmpty())<hr class="my-1 border-gray-600">@endif
                                    @foreach($dayEvents as $e)
                                        <p class="font-bold text-green-300">{{ $e->event_name }}</p>
                                        <p>{{ $e->start_time }} - {{ $e->end_time }}</p>
                                        @if(!$loop->last)<hr class="my-1 border-gray-600">@endif
                                    @endforeach
                                    <div class="tooltip-arrow" data-popper-arrow></div>
                                </div>
                            @elseif($isCurrentMonth)
                                <div class="text-lg font-bold mt-1 text-gray-300 dark:text-gray-600">-</div>
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
