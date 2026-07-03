@extends('layouts.app')

@section('content')
<div x-data="{ open: false, editMode: false, roomId: null, form: { room_code: '', room_name: '', floor: '', capacity: '', type: 'hearing_room', status: 'active', notes: '' } }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rooms</h1>
        <button @click="open = true; editMode = false; form = { room_code: '', room_name: '', floor: '', capacity: '', type: 'hearing_room', status: 'active', notes: '' }" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Room</button>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Floor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Features</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($rooms as $room)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $room->room_code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $room->room_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $room->floor }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $room->capacity }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', ucfirst($room->type)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        @foreach($room->features as $feature)
                            <span class="inline-block bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-300 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $feature->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium {{ $room->status === 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300' }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                        <a href="{{ route('rooms.features', $room) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-2">Features</a>
                        <button @click="open = true; editMode = true; roomId = {{ $room->id }}; form = { room_code: '{{ $room->room_code }}', room_name: '{{ $room->room_name }}', floor: '{{ $room->floor }}', capacity: '{{ $room->capacity }}', type: '{{ $room->type }}', status: '{{ $room->status }}', notes: '{{ $room->notes }}' }" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2">Edit</button>
                        <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="inline" onsubmit="return confirm('Delete this room?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" style="display: none;">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg mx-4 z-10">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Room' : 'Add Room'"></h3>
            </div>
            <form :action="editMode ? '/rooms/' + roomId : '/rooms'" method="POST" class="px-6 py-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Code</label>
                        <input type="text" name="room_code" x-model="form.room_code" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Room Name</label>
                        <input type="text" name="room_name" x-model="form.room_name" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Floor</label>
                        <input type="text" name="floor" x-model="form.floor" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Capacity</label>
                        <input type="number" name="capacity" x-model="form.capacity" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                        <select name="type" x-model="form.type" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                            <option value="hearing_room">Hearing Room</option>
                            <option value="breakout_room">Breakout Room</option>
                            <option value="mediation_room">Mediation Room</option>
                            <option value="conference_room">Conference Room</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" x-model="form.status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" x-model="form.notes" rows="2" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" @click="open = false" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
