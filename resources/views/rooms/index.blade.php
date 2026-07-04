@extends('layouts.app')

@section('content')
<div x-data="{ open: false, editMode: false, roomId: null, form: { room_code: '', room_name: '', floor: '', capacity: '', type: 'hearing_room', status: 'active', notes: '', is_breakout: false } }">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Rooms</h1>
        <button @click="open = true; editMode = false; form = { room_code: '', room_name: '', floor: '', capacity: '', type: 'hearing_room', status: 'active', notes: '' }"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
            + Add Room
        </button>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Code</th>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3 hidden md:table-cell">Floor</th>
                    <th class="px-6 py-3 hidden md:table-cell">Capacity</th>
                    <th class="px-6 py-3 hidden lg:table-cell">Type</th>
                    <th class="px-6 py-3 hidden lg:table-cell">Features</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $room->room_code }}</td>
                    <td class="px-6 py-4">{{ $room->room_name }}</td>
                    <td class="px-6 py-4 hidden md:table-cell">{{ $room->floor }}</td>
                    <td class="px-6 py-4 hidden md:table-cell">{{ $room->capacity }}</td>
                    <td class="px-6 py-4 hidden lg:table-cell">{{ str_replace('_', ' ', ucfirst($room->type)) }}</td>
                    <td class="px-6 py-4 hidden lg:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @foreach($room->features as $feature)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $feature->name }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded {{ $room->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('rooms.features', $room) }}" class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border border-green-200 rounded-s-lg hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-800">
                                Features
                            </a>
                            <button @click="open = true; editMode = true; roomId = {{ $room->id }}; form = { room_code: '{{ $room->room_code }}', room_name: '{{ $room->room_name }}', floor: '{{ $room->floor }}', capacity: '{{ $room->capacity }}', type: '{{ $room->type }}', status: '{{ $room->status }}', notes: '{{ $room->notes }}', is_breakout: {{ $room->is_breakout ? 'true' : 'false' }} }" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border-t border-b border-blue-200 hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800">
                                Edit
                            </button>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirm('Delete this room?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow w-full max-w-md max-h-full z-10">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Room' : 'Add Room'"></h3>
                <button @click="open = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form :action="editMode ? '/rooms/' + roomId : '/rooms'" method="POST" class="p-4 md:p-5">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="grid gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room Code</label>
                        <input type="text" name="room_code" x-model="form.room_code" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room Name</label>
                        <input type="text" name="room_name" x-model="form.room_name" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Floor</label>
                            <input type="text" name="floor" x-model="form.floor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Capacity</label>
                            <input type="number" name="capacity" x-model="form.capacity" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Type</label>
                        <select name="type" x-model="form.type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="hearing_room">Hearing Room</option>
                            <option value="breakout_room">Breakout Room</option>
                            <option value="mediation_room">Mediation Room</option>
                            <option value="conference_room">Conference Room</option>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                        <select name="status" x-model="form.status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="active">Active</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="is_breakout" x-model="form.is_breakout" value="1" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <label class="ml-2 text-sm font-medium text-gray-900 dark:text-white">Available as Breakout Room</label>
                        </select>
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Notes</label>
                        <textarea name="notes" x-model="form.notes" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="open = false" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Cancel</button>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
