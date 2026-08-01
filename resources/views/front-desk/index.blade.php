@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ allSelected: false, selectedItems: [], selectedCount: 0 }">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">{{ session('error') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Mail / Packages</h1>
        <a href="{{ route('front-desk.mail.create') }}"
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
            + Log New Item
        </a>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Today's Items</p>
            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $todayCount }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pending Pickup</p>
            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingPickups }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Aging (7+ days)</p>
            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $agingItems }}</p>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('front-desk.mail.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search sender, addressee, batch..."
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 flex-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <select name="status"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Status</option>
                <option value="pending" {{ request('status')==='pending' ? 'selected' : '' }}>Pending Pickup</option>
                <option value="collected" {{ request('status')==='collected' ? 'selected' : '' }}>Collected</option>
                <option value="aging" {{ request('status')==='aging' ? 'selected' : '' }}>Aging (7+ days)</option>
            </select>
            <select name="received_via"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Methods</option>
                <option value="Hand Delivery" {{ request('received_via')==='Hand Delivery' ? 'selected' : '' }}>Hand Delivery</option>
                <option value="Courier" {{ request('received_via')==='Courier' ? 'selected' : '' }}>Courier</option>
                <option value="Post" {{ request('received_via')==='Post' ? 'selected' : '' }}>Post</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="From"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="To"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            <a href="{{ route('front-desk.mail.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Clear</a>
        </form>
    </div>

    {{-- Batch Pickup Button --}}
    <div class="mb-4" x-show="selectedCount > 0" x-cloak>
        <form action="{{ route('front-desk.mail.batch-collect') }}" method="POST"
            onsubmit="return confirm('Mark {{ $pendingPickups }} selected item(s) as collected?');"
            class="inline-flex items-center gap-2">
            @csrf
            <template x-for="id in selectedItems" :key="id">
                <input type="hidden" name="items[]" :value="id">
            </template>
            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedCount + ' item(s) selected'"></span>
            <button type="submit"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700">
                ✓ Batch Pickup
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 w-10">
                        <input type="checkbox" x-model="allSelected"
                            @change="if(allSelected) { selectedItems = {{ $items->pluck('id') }}; selectedCount = selectedItems.length; } else { selectedItems = []; selectedCount = 0; }"
                            class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th class="px-4 py-3 hidden lg:table-cell">Date</th>
                    <th class="px-4 py-3">From</th>
                    <th class="px-4 py-3">To</th>
                    <th class="px-4 py-3 hidden md:table-cell">Doc Type</th>
                    <th class="px-4 py-3 hidden md:table-cell">Via</th>
                    <th class="px-4 py-3 hidden lg:table-cell">Matter</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-4 py-3">
                        @if(!$item->collected_by)
                        <input type="checkbox" value="{{ $item->id }}"
                            x-model="selectedItems"
                            @change="selectedCount = selectedItems.length; allSelected = selectedCount === {{ $items->total() }}"
                            class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        @endif
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell text-xs">{{ $item->date_received->format('d/m/y') }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $item->received_from }}</td>
                    <td class="px-4 py-3">{{ $item->address_to }}</td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @foreach($item->doc_type as $doc)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $doc }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3 hidden md:table-cell">
                        <span class="px-2 py-0.5 rounded text-xs font-medium
                            {{ $item->received_via === 'Hand Delivery' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                            {{ $item->received_via === 'Courier' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                            {{ $item->received_via === 'Post' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}">
                            {{ $item->received_via }}
                        </span>
                    </td>
                    <td class="px-4 py-3 hidden lg:table-cell text-xs">{{ $item->matter?->name ?? '-' }}</td>
                    <td class="px-4 py-3">
                        @if($item->collected_by)
                            <span class="px-2 py-1 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                Collected
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-medium rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                Pending
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="inline-flex rounded-md shadow-sm" role="group">
                            <a href="{{ route('front-desk.mail.edit', $item) }}?view=1"
                                class="px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 border border-blue-200 rounded-s-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:border-blue-700 dark:hover:bg-blue-800 no-underline">View</a>
                            <a href="{{ route('front-desk.mail.edit', $item) }}"
                                class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-100 border-t border-b border-green-200 hover:bg-green-200 dark:bg-green-900 dark:text-green-300 dark:border-green-700 dark:hover:bg-green-800 no-underline">Edit</a>
                            <form action="{{ route('front-desk.mail.destroy', $item) }}" method="POST"
                                onsubmit="return confirm('Delete this item?');" class="inline">@csrf @method('DELETE')
                                <button class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 border border-red-200 rounded-e-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-300 dark:border-red-700 dark:hover:bg-red-800">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No mail/package items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection