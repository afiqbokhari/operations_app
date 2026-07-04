@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @include('schedule.partials.header', ['inputType' => 'date'])

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
        </span>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 text-center text-gray-500 dark:text-gray-400">
            No bookings for this date.
        </div>
    @else
        <div class="relative overflow-x-auto shadow-md rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Room</th>
                        <th class="px-4 py-3">Session</th>
                        <th class="px-4 py-3">Case</th>
                        <th class="px-4 py-3 hidden md:table-cell">Claimant</th>
                        <th class="px-4 py-3 hidden md:table-cell">Respondent</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Arbitrator(s)</th>
                        <th class="px-4 py-3 hidden lg:table-cell">Features</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $booking->room->room_code }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 rounded text-xs font-medium
                                {{ $booking->session_type === 'full_day' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                {{ $booking->session_type === 'half_am' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300' : '' }}
                                {{ $booking->session_type === 'overtime' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300' : '' }}">
                                {{ match($booking->session_type) { 'full_day' => 'Full Day', 'half_am' => 'AM', 'half_pm' => 'PM', 'overtime' => 'OT', default => $booking->session_type } }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}-{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $booking->case->reference_number ?? $booking->booking_id }}</td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @php $claimant = $booking->participants->where('role', 'claimant')->first(); @endphp
                            {{ $claimant?->contact?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            @php $respondent = $booking->participants->where('role', 'respondent')->first(); @endphp
                            {{ $respondent?->contact?->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @php $arbitrators = $booking->participants->whereIn('role', ['presiding_arbitrator', 'co_arbitrator']); @endphp
                            @foreach($arbitrators as $arb)
                                {{ $arb->contact->name ?? '-' }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($booking->features as $feature)
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $feature->name }}</span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
