@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('rooms.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">&larr; Back to Rooms</a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $room->room_name }} ({{ $room->room_code }})</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-6">Manage features for this room</p>

        <form action="{{ route('rooms.features.update', $room) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-3">
                @foreach($features as $feature)
                <label class="flex items-center p-3 border dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                        {{ in_array($feature->id, $roomFeatures) ? 'checked' : '' }}
                        class="rounded border-gray-300 dark:border-gray-600 mr-3">
                    <span class="text-gray-700 dark:text-gray-300">{{ $feature->name }}</span>
                </label>
                @endforeach
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Save Features</button>
            </div>
        </form>
    </div>
</div>
@endsection
