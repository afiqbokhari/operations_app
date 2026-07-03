@extends('layouts.app')

@section('content')
<div x-data="{ open: false, editMode: false, featureId: null, form: { name: '', is_active: true } }" class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Features</h1>
        <button @click="open = true; editMode = false; form = { name: '', is_active: true }" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Feature</button>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($features as $feature)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $feature->name }}</td>
                    <td class="px-6 py-4 text-sm">
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium {{ $feature->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' }}">
                            {{ $feature->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <button @click="open = true; editMode = true; featureId = {{ $feature->id }}; form = { name: '{{ $feature->name }}', is_active: {{ $feature->is_active ? 'true' : 'false' }} }" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2">Edit</button>
                        <form action="{{ route('features.destroy', $feature) }}" method="POST" class="inline" onsubmit="return confirm('Delete this feature?')">
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
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 z-10">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Feature' : 'Add Feature'"></h3>
            </div>
            <form :action="editMode ? '/features/' + featureId : '/features'" method="POST" class="px-6 py-4">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                        <input type="text" name="name" x-model="form.name" required class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-md shadow-sm">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" x-model="form.is_active" value="1" class="rounded border-gray-300 dark:border-gray-600">
                        <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
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
