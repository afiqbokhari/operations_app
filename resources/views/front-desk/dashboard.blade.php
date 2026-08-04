@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Front Desk Dashboard</h1>
        <div class="flex gap-2">
            <a href="{{ route('front-desk.mail.index') }}"
                class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                View Mail Log
            </a>
            <a href="{{ route('front-desk.mail.create') }}"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                + Log New Item
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
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
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">This Month</p>
            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $monthCount }}</p>
        </div>
    </div>

    {{-- Recent Items --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Items</h2>
            <a href="{{ route('front-desk.mail.index') }}"
                class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View all →</a>
        </div>
        @if($recentItems->isEmpty())
            <p class="text-gray-500 dark:text-gray-400 text-sm">No items logged yet.</p>
        @else
            <div class="space-y-2">
                @foreach($recentItems as $item)
                    <a href="{{ route('front-desk.mail.show', $item) }}"
                        class="block p-3 border dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->contact?->name ?? $item->received_from ?? '-' }} → {{ $item->address_to }}</span>
                                <span class="text-xs text-gray-500 ml-2">{{ $item->date_received->format('d/m/y') }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $item->collected_by ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' }}">
                                {{ $item->collected_by ? 'Collected' : 'Pending' }}
                            </span>
                        </div>
                        @if($item->doc_type)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ implode(', ', $item->doc_type) }}</p>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
