@extends('layouts.app')

@section('content')
<div x-data="{ view: '{{ $view }}', date: '{{ $date }}' }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Schedule</h1>

        <div class="flex items-center space-x-4">
            <div class="flex bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                <a href="{{ route('schedule.index', ['view' => 'daily', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm {{ $view === 'daily' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">Daily</a>
                <a href="{{ route('schedule.index', ['view' => 'weekly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm {{ $view === 'weekly' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">Weekly</a>
                <a href="{{ route('schedule.index', ['view' => 'monthly', 'date' => $date]) }}"
                   class="px-3 py-1 rounded text-sm {{ $view === 'monthly' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow' : 'text-gray-600 dark:text-gray-300' }}">Monthly</a>
            </div>

            <form action="{{ route('schedule.index') }}" method="GET" class="flex items-center space-x-2">
                <input type="hidden" name="view" value="daily">
                <input type="date" name="date" value="{{ $date }}"
                       class="border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm text-sm">
                <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">Go</button>
            </form>
        </div>
    </div>

    <div class="mb-4">
        <span class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
        </span>
    </div>

    @if($bookings->isEmpty())
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8 text-center text-gray-500 dark:text-gray-400">
            No bookings for this date.
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Room</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Session</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Case</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Claimant</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Respondent</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Arbitrator(s)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Features</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($bookings as $booking)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $booking->room->room_code }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium
                                {{ $booking->session_type === 'full_day' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-300' : '' }}
                                {{ $booking->session_type === 'half_am' ? 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300' : '' }}
                                {{ $booking->session_type === 'half_pm' ? 'bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-300' : '' }}
                                {{ $booking->session_type === 'overtime' ? 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-300' : '' }}">
                                {{ $booking->session_type === 'full_day' ? 'Full Day' : '' }}
                                {{ $booking->session_type === 'half_am' ? 'AM' : '' }}
                                {{ $booking->session_type === 'half_pm' ? 'PM' : '' }}
                                {{ $booking->session_type === 'overtime' ? 'OT' : '' }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ $booking->case->reference_number ?? $booking->booking_id }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            @php $claimant = $booking->participants->where('role', 'claimant')->first(); @endphp
                            {{ $claimant?->contact?->name ?? '-' }}
                            @if($claimant?->contact?->organization)
                                <br><span class="text-xs text-gray-500 dark:text-gray-400">{{ $claimant->contact->organization }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            @php $respondent = $booking->participants->where('role', 'respondent')->first(); @endphp
                            {{ $respondent?->contact?->name ?? '-' }}
                            @if($respondent?->contact?->organization)
                                <br><span class="text-xs text-gray-500 dark:text-gray-400">{{ $respondent->contact->organization }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            @php $arbitrators = $booking->participants->whereIn('role', ['presiding_arbitrator', 'co_arbitrator']); @endphp
                            @foreach($arbitrators as $arb)
                                {{ $arb->contact->name ?? '-' }}@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            @foreach($booking->features as $bf)
                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs px-1.5 py-0.5 rounded mr-1">{{ $bf->feature->name }}</span>
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
