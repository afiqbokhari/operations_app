@extends('layouts.app')

@section('content')
<div x-data="{ open: false, editMode: false, menuId: null, form: { label: '', route_name: '', parent_id: '', order: 0, permission: '', icon: '', is_active: true } }">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Menu Management</h1>
        <button @click="open = true; editMode = false; form = { label: '', route_name: '', parent_id: '', order: 0, permission: '', icon: '', is_active: true }"
                class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">
            + Add Menu
        </button>
    </div>

    {{-- Current Menu Structure --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Menu Structure</h2>
        @foreach($menus as $menu)
        <div class="border dark:border-gray-700 rounded-lg p-3 mb-2">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-medium text-gray-900 dark:text-white">{{ $menu->label }}</span>
                    <span class="text-xs text-gray-500 ml-2">{{ $menu->route_name ?? 'no route' }}</span>
                    @if($menu->permission)
                        <span class="text-xs bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded ml-2">{{ $menu->permission }}</span>
                    @endif
                    @if(!$menu->is_active)
                        <span class="text-xs bg-red-100 text-red-800 px-1.5 py-0.5 rounded ml-2">Inactive</span>
                    @endif
                </div>
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button @click="open = true; editMode = true; menuId = {{ $menu->id }}; form = { label: '{{ $menu->label }}', route_name: '{{ $menu->route_name }}', parent_id: '{{ $menu->parent_id }}', order: {{ $menu->order }}, permission: '{{ $menu->permission }}', icon: '{{ $menu->icon }}', is_active: {{ $menu->is_active ? 'true' : 'false' }} }"
                            class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200">Edit</button>
                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" onsubmit="return confirm('Delete this menu?')" class="inline">
                        @csrf @method('DELETE')
                        <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200">Delete</button>
                    </form>
                </div>
            </div>
            @if($menu->children->isNotEmpty())
            <div class="ml-6 mt-2 space-y-1">
                @foreach($menu->children as $child)
                <div class="flex items-center justify-between text-sm border dark:border-gray-600 rounded p-2">
                    <div>
                        <span class="text-gray-700 dark:text-gray-300">↳ {{ $child->label }}</span>
                        <span class="text-xs text-gray-500 ml-2">{{ $child->route_name ?? 'no route' }}</span>
                    </div>
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button @click="open = true; editMode = true; menuId = {{ $child->id }}; form = { label: '{{ $child->label }}', route_name: '{{ $child->route_name }}', parent_id: '{{ $child->parent_id }}', order: {{ $child->order }}, permission: '{{ $child->permission }}', icon: '{{ $child->icon }}', is_active: {{ $child->is_active ? 'true' : 'false' }} }"
                                class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200">Edit</button>
                        <form action="{{ route('menus.destroy', $child) }}" method="POST" onsubmit="return confirm('Delete?')" class="inline">
                            @csrf @method('DELETE')
                            <button class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200">Del</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Modal --}}
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto p-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="open = false"></div>
        <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow w-full max-w-md max-h-full z-10">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editMode ? 'Edit Menu' : 'Add Menu'"></h3>
                <button @click="open = false" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 14 14"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/></svg>
                </button>
            </div>
            <form :action="editMode ? '/menus/' + menuId : '/menus'" method="POST" class="p-4 md:p-5">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <div class="grid gap-4 mb-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Label</label>
                        <input type="text" name="label" x-model="form.label" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Route Name</label>
                        <input type="text" name="route_name" x-model="form.route_name" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Parent</label>
                        <select name="parent_id" x-model="form.parent_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">None (Top Level)</option>
                            @foreach($allMenus as $m)
                                <option value="{{ $m->id }}">{{ $m->label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Order</label>
                            <input type="number" name="order" x-model="form.order" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Permission</label>
                            <select name="permission" x-model="form.permission" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">None (Always show)</option>
                                @foreach($permissions as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="open = false" class="text-gray-500 bg-white hover:bg-gray-100 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500">Cancel</button>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
