@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">New Event Request</h1>

    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form action="{{ route('events.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Name</label>
                <input type="text" name="event_name" value="{{ old('event_name') }}" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room</label>
                <select name="room_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Select room</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>{{ $room->room_code }} - {{ $room->room_name }} ({{ $room->capacity }} pax)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time') }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time') }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Organiser</label>
                    <input type="text" name="organizer" value="{{ old('organizer') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attendees</label>
                    <input type="number" name="attendees_count" value="{{ old('attendees_count') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="setup_needed" value="1" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Setup needed</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="catering_needed" value="1" class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Catering needed</span>
                </label>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                <textarea name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('events.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
