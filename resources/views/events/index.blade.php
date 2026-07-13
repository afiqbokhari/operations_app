@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Events</h1>
        <a href="{{ route('events.create') }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
            + New Event
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('events.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search events..."
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 flex-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <select name="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To"
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Filter</button>
            <a href="{{ route('events.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500">Clear</a>
        </form>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 hidden lg:table-cell">Created</th>
                    <th class="px-4 py-3">Event</th>
                    <th class="px-4 py-3">Room</th>
                    <th class="px-4 py-3 hidden md:table-cell">Date</th>
                    <th class="px-4 py-3 hidden md:table-cell">Organiser</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-3 hidden lg:table-cell text-xs">{{ $event->created_at->format('d/m/y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $event->event_name }}</td>
                    <td class="px-4 py-3">{{ $event->room->room_code }}</td>
                    <td class="px-4 py-3 hidden md:table-cell text-xs">
                        {{ $event->start_date->format('d/m/Y') }} - {{ $event->end_date->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">{{ $event->organizer ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded
                            {{ $event->status === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $event->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                            {{ $event->status === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('events.edit', $event) }}" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800">Edit</a>
                            <form action="{{ route('events.cancel', $event) }}" method="POST" class="inline" onsubmit="return confirm('Cancel this event?');">
                                @csrf
                                <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800">Cancel</button>
                            </form>
                        </div>
                        @if($event->status === 'pending' && auth()->user()->role === 'admin')
                        <div class="inline-flex rounded-md shadow-sm ml-2" role="group">
                            <form action="{{ route('events.approve', $event) }}" method="POST" class="inline">
                                @csrf
                                <button class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border border-green-200 rounded-s-lg hover:bg-green-200">Approve</button>
                            </form>
                            <button onclick="document.getElementById('reject-{{ $event->id }}').classList.toggle('hidden')" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200">Reject</button>
                        </div>
                        <form id="reject-{{ $event->id }}" action="{{ route('events.reject', $event) }}" method="POST" class="hidden mt-2">
                            @csrf
                            <input type="text" name="reason" placeholder="Reason..." class="text-xs border rounded px-2 py-1 w-full mb-1">
                            <button class="text-xs text-white bg-red-600 px-2 py-1 rounded">Confirm Reject</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No events found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $events->links() }}</div>
</div>
@endsection