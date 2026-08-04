@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">{{ session('success') }}</div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Role Permissions</h1>
    </div>

    @foreach($roles as $role)
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ ucfirst($role->name) }}</h2>
            <form action="{{ route('permissions.update', $role) }}" method="POST">
                @csrf
                @method('PUT')
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700">Save {{ ucfirst($role->name) }}</button>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($routeGroups as $group)
                <div class="border dark:border-gray-600 rounded-lg p-3">
                    <h3 class="font-medium text-gray-700 dark:text-gray-300 mb-2">{{ ucfirst($group) }}</h3>
                    @foreach($actions as $action)
                        <label class="flex items-center mb-1 cursor-pointer">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $group }}.{{ $action }}"
                                   {{ $role->hasPermissionTo($group . '.' . $action) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($action) }}</span>
                        </label>
                    @endforeach
                </div>
            @endforeach
        </div>
            </form>
    </div>
    @endforeach
</div>
@endsection
