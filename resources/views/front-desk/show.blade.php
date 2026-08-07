@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    @if(session('success'))
        <div class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">{{ session('error') }}</div>
    @endif

    <div class="mb-6">
        <a href="{{ route('front-desk.mail.index') }}" class="text-blue-600 dark:text-blue-500 hover:underline">← Back to Mail Log</a>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Item Details</h1>
        <div class="flex gap-2 flex-wrap">
            @can('front_desk.edit')
            @if(!$frontDeskItem->passed_to)
            <form action="{{ route('front-desk.mail.pass', $frontDeskItem) }}" method="POST" class="inline-flex items-center gap-2">
                @csrf
                <select name="passed_to" required
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">-- Select Legal --</option>
                    @foreach($legalUsers as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-4 py-2">
                    ✓ Pass to Legal
                </button>
            </form>
            @elseif(!$frontDeskItem->collected_by)
            <form action="{{ route('front-desk.mail.undo-pass', $frontDeskItem) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="text-gray-700 bg-gray-200 hover:bg-gray-300 font-medium rounded-lg text-sm px-4 py-2">
                    ↩ Undo Pass
                </button>
            </form>
            @endif
            <a href="{{ route('front-desk.mail.edit', $frontDeskItem) }}"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 no-underline">
                Edit Item
            </a>
            @endcan
            @can('front_desk.delete')
            <form action="{{ route('front-desk.mail.destroy', $frontDeskItem) }}" method="POST"
                onsubmit="return confirm('Delete this item?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-red-600 dark:hover:bg-red-700">
                    Delete
                </button>
            </form>
            @endcan
        </div>
    </div>

    {{-- Status banner --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-5 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
        <div class="flex items-center gap-3">
            @if($frontDeskItem->collected_by)
                <span class="px-3 py-1 text-sm font-medium rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">Collected</span>
            @elseif($frontDeskItem->passed_to)
                <span class="px-3 py-1 text-sm font-medium rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">Passed to Legal</span>
            @else
                <span class="px-3 py-1 text-sm font-medium rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Pending</span>
            @endif
            @if($frontDeskItem->batch_number)
                <span class="px-3 py-1 text-sm font-medium rounded bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">Batch {{ $frontDeskItem->batch_number }}</span>
            @endif
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
            Logged {{ $frontDeskItem->created_at?->format('d/m/y H:i') }} by {{ $frontDeskItem->loggedBy?->name ?? 'Unknown' }}
        </div>
    </div>

    {{-- Main info --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Delivery Information</h2>
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Date Received</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->date_received->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Batch</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $frontDeskItem->batch_number ? 'Batch ' . $frontDeskItem->batch_number : '-' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Received Via</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    <span class="px-2 py-0.5 rounded text-xs font-medium
                        {{ $frontDeskItem->received_via === 'Hand Delivery' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                        {{ $frontDeskItem->received_via === 'Courier' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                        {{ $frontDeskItem->received_via === 'Post' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}">
                        {{ $frontDeskItem->received_via }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Received From</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->contact?->name ?? $frontDeskItem->received_from ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Address To</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->address_to }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Passed To</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->passedTo?->name ?? '-' }}</p>
            </div>
            @if($frontDeskItem->passed_by)
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Passed By</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->passedBy?->name ?? '-' }} ({{ $frontDeskItem->passed_at?->format('d/m/Y H:i') }})</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Passed To</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->passedTo?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Letter Date</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->letter_date?->format('d/m/Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Matter / Case Reference</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->matter?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Case Reference</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->case_reference ?? '-' }}</p>
            </div>
        </div>

        <div class="mt-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Document Type(s)</p>
            <div class="flex flex-wrap gap-1">
                @forelse($frontDeskItem->doc_type as $doc)
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $doc }}</span>
                @empty
                    <span class="text-sm text-gray-400">-</span>
                @endforelse
            </div>
        </div>

        @if($frontDeskItem->details)
        <div class="mt-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Details</p>
            <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $frontDeskItem->details }}</p>
        </div>
        @endif

        @if($frontDeskItem->remarks)
        <div class="mt-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Remarks</p>
            <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $frontDeskItem->remarks }}</p>
        </div>
        @endif
    </div>

    {{-- Collection info --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Collection Information</h2>
        <div class="grid gap-6 md:grid-cols-3">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->collected_by ? 'Collected' : 'Not Collected' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Collected By</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->collectedBy?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Collected At</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->collected_at?->format('d/m/Y H:i') ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
