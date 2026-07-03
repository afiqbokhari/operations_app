@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Rooms</h1>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Room</button>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Features</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($rooms as $room)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $room->room_code }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $room->room_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $room->floor }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $room->capacity }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ str_replace('_', ' ', ucfirst($room->type)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">
                        @foreach($room->features as $feature)
                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $feature->name }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium
                            {{ $room->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($room->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button class="text-blue-600 hover:text-blue-900 mr-2">Edit</button>
                        <button class="text-red-600 hover:text-red-900">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
