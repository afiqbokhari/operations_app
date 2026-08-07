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
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Legal Mail / Packages</h1>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-4 mb-6">
        <form method="GET" action="{{ route('front-desk.mail.legal.index') }}" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 flex-1 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <select name="status"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All</option>
                <option value="passed" {{ request('status')==='passed' ? 'selected' : '' }}>Pending Collection</option>
                <option value="collected" {{ request('status')==='collected' ? 'selected' : '' }}>Collected</option>
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="submit"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Filter</button>
            <a href="{{ route('front-desk.mail.legal.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Clear</a>
        </form>
    </div>

    {{-- Batch Collect Button --}}
    <div class="mb-4" x-show="selectedCount > 0" x-cloak>
        <form action="{{ route('front-desk.mail.legal.batch-collect') }}" method="POST"
            onsubmit="return confirm('Mark selected item(s) as collected?');"
            class="inline-flex items-center gap-2">
            @csrf
            <template x-for="id in selectedItems" :key="id">
                <input type="hidden" name="items[]" :value="id">
            </template>
            <span class="text-sm text-gray-600 dark:text-gray-400" x-text="selectedCount + ' item(s) selected'"></span>
            <button type="submit"
                class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700">
                ✓ Batch Collect
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="relative overflow-x-auto shadow-md rounded-lg">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-3 py-3 w-10">
                        <input type="checkbox" x-model="allSelected"
                            @change="if(allSelected) { selectedItems = {{ $items->pluck('id') }}; selectedCount = selectedItems.length; } else { selectedItems = []; selectedCount = 0; }"
                            class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    </th>
                    <th class="px-3 py-3">Date</th>
                    <th class="px-3 py-3">From</th>
                    <th class="px-3 py-3">To</th>
                    <th class="px-3 py-3 hidden md:table-cell">Doc Type</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Matter</th>
                    <th class="px-3 py-3 hidden lg:table-cell">Passed By</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3 w-10">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                    <td class="px-3 py-3">
                        @if(!$item->collected_by)
                        <input type="checkbox" value="{{ $item->id }}"
                            x-model="selectedItems"
                            @change="selectedCount = selectedItems.length; allSelected = selectedCount === {{ $items->total() }}"
                            class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        @endif
                    </td>
                    <td class="px-3 py-3 text-xs whitespace-nowrap">{{ $item->date_received->format('d/m/y') }}</td>
                    <td class="px-3 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap">{{ $item->contact?->name ?? '-' }}</td>
                    <td class="px-3 py-3 whitespace-nowrap">{{ $item->address_to }}</td>
                    <td class="px-3 py-3 hidden md:table-cell">
                        <div class="flex flex-wrap gap-1">
                            @foreach($item->doc_type as $doc)
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $doc }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs">{{ $item->matter?->name ?? '-' }}</td>
                    <td class="px-3 py-3 hidden lg:table-cell text-xs whitespace-nowrap">{{ $item->passedBy?->name ?? '-' }}<br><span class="text-gray-400">{{ $item->passed_at?->format('d/m/y H:i') }}</span></td>
                    <td class="px-3 py-3">
                        @if($item->collected_by)
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                <span class="text-xs text-green-700 dark:text-green-400">Collected</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                                <span class="text-xs text-yellow-700 dark:text-yellow-400">Pending</span>
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        @if(!$item->collected_by)
                        <form action="{{ route('front-desk.mail.legal.collect', $item) }}" method="POST"
                            onsubmit="return confirm('Mark as collected?');" class="inline">
                            @csrf
                            <button title="Collect" class="p-1.5 text-green-600 hover:bg-green-100 rounded dark:text-green-400 dark:hover:bg-green-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </button>
                        </form>
                        @else
                            <span class="text-xs text-gray-400">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr class="bg-white dark:bg-gray-800">
                    <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No items passed to legal yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection