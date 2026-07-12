@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Edit Event: {{ $event->event_name }}</h1>

    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">{{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form action="{{ route('events.update', $event) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Name</label>
                <input type="text" name="event_name" value="{{ old('event_name', $event->event_name) }}" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Event Type</label>
                    <select name="event_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select type</option>
                        <option value="meeting" {{ old('event_type', $event->event_type) == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="training" {{ old('event_type', $event->event_type) == 'training' ? 'selected' : '' }}>Training</option>
                        <option value="competition" {{ old('event_type', $event->event_type) == 'competition' ? 'selected' : '' }}>Competition</option>
                        <option value="seminar" {{ old('event_type', $event->event_type) == 'seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="government_meeting" {{ old('event_type', $event->event_type) == 'government_meeting' ? 'selected' : '' }}>Government Meeting</option>
                        <option value="other" {{ old('event_type', $event->event_type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reference Number</label>
                    <input type="text" name="reference_number" value="{{ old('reference_number', $event->reference_number) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room</label>
                <select name="room_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('room_id', $event->room_id) == $room->id ? 'selected' : '' }}>{{ $room->room_code }} - {{ $room->room_name }} ({{ $room->capacity }} pax)</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Time</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $event->start_time) }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Time</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $event->end_time) }}" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Organizer</label>
                    <input type="text" name="organizer" value="{{ old('organizer', $event->organizer) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Attendees</label>
                    <input type="number" name="attendees_count" value="{{ old('attendees_count', $event->attendees_count) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            {{-- Features --}}
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Features</label>
                <div class="flex flex-wrap gap-3">
                    @foreach(\App\Models\Feature::where('is_active', true)->orderBy('name')->get() as $feature)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}"
                            {{ $event->features->contains($feature->id) ? 'checked' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Breakout Rooms --}}
            <div x-data="{ breakouts: {{ $event->breakoutRooms->count() }} }">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Breakout Rooms</label>
                @foreach($event->breakoutRooms as $i => $br)
                <div class="flex items-center gap-2 mb-2">
                    <select name="breakout_rooms[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select breakout room</option>
                        @foreach(\App\Models\Room::where('status', 'active')->where('is_breakout', true)->where('id', '!=', $event->room_id)->orderedByType()->get() as $r)
                            <option value="{{ $r->id }}" {{ $br->room_id == $r->id ? 'selected' : '' }}>{{ $r->room_code }} - {{ $r->room_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" @click="breakouts--; $el.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm">✕</button>
                </div>
                @endforeach
                <template x-for="i in breakouts - {{ $event->breakoutRooms->count() }}" :key="i">
                    <div class="flex items-center gap-2 mb-2">
                        <select name="breakout_rooms[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select breakout room</option>
                            @foreach(\App\Models\Room::where('status', 'active')->where('is_breakout', true)->where('id', '!=', $event->room_id)->orderedByType()->get() as $r)
                                <option value="{{ $r->id }}">{{ $r->room_code }} - {{ $r->room_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="breakouts--; $el.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm">✕</button>
                    </div>
                </template>
                <button type="button" @click="breakouts++" class="mt-2 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">+ Add Breakout Room</button>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="setup_needed" value="1" {{ old('setup_needed', $event->setup_needed) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Setup needed</span>
                </label>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" name="catering_needed" value="1" {{ old('catering_needed', $event->catering_needed) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Catering needed</span>
                </label>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                <textarea name="notes" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $event->notes) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('events.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Update Event</button>
            </div>
        </form>
    </div>
</div>
@endsection