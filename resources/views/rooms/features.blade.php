@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('rooms.index') }}" class="text-blue-600 dark:text-blue-500 hover:underline">&larr; Back to Rooms</a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $room->room_name }} ({{ $room->room_code }})</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Manage features for this room</p>

        <form action="{{ route('rooms.features.update', $room) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-3 mb-6">
                @foreach($features as $feature)
                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                        {{ in_array($feature->id, $roomFeatures) ? 'checked' : '' }}
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 mr-3">
                    <span class="text-gray-700 dark:text-gray-300">{{ $feature->name }}</span>
                </label>
                @endforeach
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Save Features</button>
        </form>
    </div>
</div>
@endsection
