@extends('layouts.app')

@section('content')
<div x-data="{ open: false, editMode: false, featureId: null, form: { name: '', is_active: true } }">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Features</h1>
        <button @click="open = true; editMode = false; form = { name: '', is_active: true }" 
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
            + Add Feature
        </button>
    </div>

    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($features as $feature)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $feature->name }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded {{ $feature->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                            {{ $feature->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <button @click="open = true; editMode = true; featureId = {{ $feature->id }}; form = { name: '{{ $feature->name }}', is_active: {{ $feature->is_active ? 'true' : 'false' }} }" class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800">
                                Edit
                            </button>
                            <form action="{{ route('features.destroy', $feature) }}" method="POST" onsubmit="return confirm('Delete this feature?')" class="inline">
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
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Feature' : 'Add Feature'"></h3>
                <button @click="open = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form :action="editMode ? '/features/' + featureId : '/features'" method="POST" class="p-4 md:p-5">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="mb-4">
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                    <input type="text" name="name" x-model="form.name" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div class="flex items-center mb-4">
                    <input type="checkbox" name="is_active" x-model="form.is_active" value="1" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <label class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Active</label>
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
