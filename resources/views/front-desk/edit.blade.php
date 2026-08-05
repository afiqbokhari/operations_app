@extends('layouts.app')

@section('content')
@php $viewing = request()->query('view') == 1; @endphp
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ $viewing ? 'Item Details' : 'Edit Mail / Package' }}</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('front-desk.mail.update', $frontDeskItem) }}">
            @csrf
            @method('PUT')

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                {{-- Date Received --}}
                <div>
                    <label for="date_received" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date Received *</label>
                    <input type="date" name="date_received" id="date_received"
                        value="{{ old('date_received', $frontDeskItem->date_received?->format('Y-m-d')) }}" required
                        {{ $viewing ? 'disabled' : '' }}
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewing ? 'opacity-60' : '' }}">
                    @error('date_received')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Batch Number --}}
                <div>
                    <label for="batch_number" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Batch</label>
                    @if($viewing)
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $frontDeskItem->batch_number ? 'Batch ' . $frontDeskItem->batch_number : '-' }}
                        </p>
                    @else
                        <select name="batch_number" id="batch_number"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">-- Select Batch --</option>
                            @foreach([1 => '8:30AM-10AM', 2 => '10AM-12PM', 3 => '12PM-2:30PM', 4 => '2:30PM-4PM', 5 => '4PM-5:30PM'] as $num => $time)
                                <option value="{{ $num }}" {{ old('batch_number', $frontDeskItem->batch_number) == $num ? 'selected' : '' }}>
                                    Batch {{ $num }} ({{ $time }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('batch_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Received Via --}}
                <div>
                    <label for="received_via" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Received Via *</label>
                    <select name="received_via" id="received_via" required {{ $viewing ? 'disabled' : '' }}
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewing ? 'opacity-60' : '' }}">
                        @php $via = old('received_via', $frontDeskItem->received_via); @endphp
                        <option value="Hand Delivery" {{ $via === 'Hand Delivery' ? 'selected' : '' }}>Hand Delivery</option>
                        <option value="Courier" {{ $via === 'Courier' ? 'selected' : '' }}>Courier</option>
                        <option value="Post" {{ $via === 'Post' ? 'selected' : '' }}>Post</option>
                    </select>
                    @error('received_via')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Received From (autofill) --}}
                <div>
                    <label for="received_from" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Received From *</label>
                    @if($viewing)
                        <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->contact?->name ?? $frontDeskItem->received_from ?? '-' }}</p>
                    @else
                        <div x-data="{ 
                            open: false, 
                            search: '', 
                            selected: '{{ addslashes(old('contact_name', $frontDeskItem->contact?->name ?? $frontDeskItem->received_from ?? '')) }}',
                            contacts: {{ Js::from($contacts->map(fn($c) => ['name' => $c->name, 'company' => $c->company])) }},
                            filteredContacts: []
                        }" class="relative">
                            <input type="text" 
                                name="contact_name"
                                id="received_from" 
                                x-model="selected"
                                @input.debounce.200ms="
                                    search = selected.toLowerCase();
                                    if (search.length >= 2) {
                                        filteredContacts = contacts.filter(c => 
                                            c.name.toLowerCase().includes(search) || 
                                            (c.company && c.company.toLowerCase().includes(search))
                                        );
                                        open = true;
                                    } else {
                                        open = false;
                                    }
                                "
                                @focus="
                                    if (selected.length >= 2) {
                                        filteredContacts = contacts.filter(c => 
                                            c.name.toLowerCase().includes(selected.toLowerCase()) || 
                                            (c.company && c.company.toLowerCase().includes(selected.toLowerCase()))
                                        );
                                        open = true;
                                    }
                                "
                                @keydown.escape="open = false"
                                required
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            
                            <div x-show="open" 
                                x-cloak 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-y-95"
                                x-transition:enter-end="opacity-100 transform scale-y-100"
                                class="absolute top-full left-0 mt-1 w-full bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-48 overflow-y-auto">
                                
                                <template x-for="contact in filteredContacts" :key="contact.name">
                                    <div @click="selected = contact.name; open = false"
                                        class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-600 cursor-pointer border-b dark:border-gray-600 last:border-0 transition-colors duration-150">
                                        <span x-text="contact.name"></span>
                                        <span x-show="contact.company" class="text-xs text-gray-400 ml-1" x-text="'(' + contact.company + ')'"></span>
                                    </div>
                                </template>
                                
                                <div x-show="filteredContacts.length === 0" 
                                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No contacts found matching "<span x-text="search"></span>"
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('contact_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Address To --}}
                <div>
                    <label for="address_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address To *</label>
                    <input type="text" name="address_to" id="address_to" value="{{ old('address_to', $frontDeskItem->address_to) }}" required
                        {{ $viewing ? 'disabled' : '' }}
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewing ? 'opacity-60' : '' }}">
                    @error('address_to')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Passed To --}}
                <div>
                    <label for="passed_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Passed To</label>
                    @if($viewing)
                        <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->passedTo?->name ?? '-' }}</p>
                    @else
                        <select name="passed_to" id="passed_to"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @foreach($legalUsers as $user)
                                <option value="{{ $user->id }}" {{ old('passed_to', $frontDeskItem->passed_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    @error('passed_to')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Letter Date --}}
                <div>
                    <label for="letter_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Letter Date</label>
                    <input type="date" name="letter_date" id="letter_date"
                        value="{{ old('letter_date', $frontDeskItem->letter_date?->format('Y-m-d')) }}"
                        {{ $viewing ? 'disabled' : '' }}
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewing ? 'opacity-60' : '' }}">
                    @error('letter_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Matter (autofill) --}}
                <div>
                    <label for="matter_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matter / Case Reference</label>
                    @if($viewing)
                        <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->matter?->name ?? '-' }}</p>
                    @else
                        <div x-data="{ 
                            open: false, 
                            search: '', 
                            selectedId: '{{ addslashes(old('matter_id', $frontDeskItem->matter_id)) }}', 
                            selectedName: '{{ addslashes(old('matter_name', $frontDeskItem->matter?->name ?? '')) }}',
                            matters: {{ Js::from($matters->map(fn($m) => ['id' => $m->id, 'name' => $m->name])) }},
                            filteredMatters: []
                        }" class="relative">
                            <input type="hidden" name="matter_id" :value="selectedId">
                            <input type="hidden" name="matter_name" :value="selectedName">
                            <input type="text" 
                                x-model="selectedName"
                                @input.debounce.200ms="
                                    search = selectedName.toLowerCase();
                                    if (search.length >= 2) {
                                        filteredMatters = matters.filter(m => 
                                            m.name.toLowerCase().includes(search)
                                        );
                                        open = true;
                                    } else {
                                        open = false;
                                    }
                                "
                                @focus="
                                    if (selectedName.length >= 2) {
                                        filteredMatters = matters.filter(m => 
                                            m.name.toLowerCase().includes(selectedName.toLowerCase())
                                        );
                                        open = true;
                                    }
                                "
                                @keydown.escape="open = false"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            
                            <div x-show="open" 
                                x-cloak 
                                @click.away="open = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-y-95"
                                x-transition:enter-end="opacity-100 transform scale-y-100"
                                class="absolute top-full left-0 mt-1 w-full bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-48 overflow-y-auto">
                                
                                <template x-for="matter in filteredMatters" :key="matter.id">
                                    <div @click="selectedId = matter.id; selectedName = matter.name; open = false"
                                        class="px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-gray-600 cursor-pointer border-b dark:border-gray-600 last:border-0 transition-colors duration-150"
                                        x-text="matter.name">
                                    </div>
                                </template>
                                
                                <div x-show="filteredMatters.length === 0" 
                                    class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400 text-center">
                                    No matters found matching "<span x-text="search"></span>"
                                </div>
                            </div>
                        </div>
                    @endif
                    @error('matter_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Case Reference --}}
                <div>
                    <label for="case_reference" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Case Reference</label>
                    @if($viewing)
                        <p class="font-medium text-gray-900 dark:text-white">{{ $frontDeskItem->case_reference ?? '-' }}</p>
                    @else
                        <input type="text" name="case_reference" id="case_reference" 
                            value="{{ old('case_reference', $frontDeskItem->case_reference) }}" 
                            maxlength="20"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @endif
                    @error('case_reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Document Types --}}
            <div class="mb-6">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Document Type(s) *</label>

                @if($viewing)
                    <div class="flex flex-wrap gap-1">
                        @forelse($frontDeskItem->doc_type as $doc)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">{{ $doc }}</span>
                        @empty
                            <span class="text-sm text-gray-400">-</span>
                        @endforelse
                    </div>
                @else
                <div x-data="{
                    types: {{ json_encode(old('doc_type', $frontDeskItem->doc_type ?? ['Letter'])) }},
                    otherType: ''
                }">
                    <div class="flex flex-wrap gap-2 mb-3">
                        <template x-for="type in ['Letter', 'Invoice', 'Contract', 'Report', 'Package']" :key="type">
                            <label :class="[
                                'flex items-center px-3 py-1.5 text-sm font-medium rounded cursor-pointer transition-all duration-200',
                                types.includes(type)
                                    ? 'bg-blue-600 text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600'
                                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600'
                            ]"
                                @click="if(types.includes(type)) { types = types.filter(t => t !== type); } else { types.push(type); }">
                                <span x-text="type"></span>
                            </label>
                        </template>
                    </div>

                    <div class="mb-3">
                        <label for="doc-type-other-input"
                            class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Other document type:</label>
                        <input type="text" id="doc-type-other-input" x-model="otherType"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>

                    @error('doc_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                    <template x-for="type in types" :key="type">
                        <input type="hidden" name="doc_type[]" :value="type">
                    </template>
                    <template x-if="otherType && otherType.trim() !== ''">
                        <input type="hidden" name="doc_type[]" :value="otherType">
                    </template>
                </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="mb-6">
                <label for="details" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Details</label>
                @if($viewing)
                    <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $frontDeskItem->details ?? '-' }}</p>
                @else
                    <textarea name="details" id="details" rows="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('details', $frontDeskItem->details) }}</textarea>
                @endif
                @error('details')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Remarks --}}
            <div class="mb-6">
                <label for="remarks" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks</label>
                @if($viewing)
                    <p class="text-gray-900 dark:text-white whitespace-pre-line">{{ $frontDeskItem->remarks ?? '-' }}</p>
                @else
                    <textarea name="remarks" id="remarks" rows="2"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('remarks', $frontDeskItem->remarks) }}</textarea>
                @endif
                @error('remarks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('front-desk.mail.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                    {{ $viewing ? 'Back to Mail Log' : 'Cancel' }}
                </a>
                @if($viewing)
                    @can('front_desk.edit')
                    <a href="{{ route('front-desk.mail.edit', $frontDeskItem) }}"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 no-underline">
                        Edit Item
                    </a>
                    @endcan
                @else
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                        Update Item
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
