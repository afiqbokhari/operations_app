@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Log New Mail / Package</h1>

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <form method="POST" action="{{ route('front-desk.mail.store') }}">
            @csrf

            <div class="grid gap-6 mb-6 md:grid-cols-2">
                {{-- Date Received --}}
                <div>
                    <label for="date_received" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date
                        Received *</label>
                    <input type="date" name="date_received" id="date_received"
                        value="{{ old('date_received', date('Y-m-d')) }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('date_received')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Received Via --}}
                <div>
                    <label for="received_via"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Received Via *</label>
                    <select name="received_via" id="received_via" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="Hand Delivery" {{ old('received_via')==='Hand Delivery' ? 'selected' : '' }}>Hand
                            Delivery</option>
                        <option value="Courier" {{ old('received_via')==='Courier' ? 'selected' : '' }}>Courier</option>
                        <option value="Post" {{ old('received_via')==='Post' ? 'selected' : '' }}>Post</option>
                    </select>
                    @error('received_via')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Received From (autofill + manual) --}}
                <div>
                    <label for="received_from"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Received From *</label>
                    <div x-data="{ open: false, search: '', selected: '' }" class="relative">
                        <input type="text" name="received_from" id="received_from" x-model="selected"
                            @input="search = $event.target.value; open = search.length >= 2"
                            value="{{ old('received_from') }}" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div x-show="open" x-cloak @click.away="open = false"
                            class="absolute top-full left-0 mt-1 w-full bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-40 overflow-y-auto">
                            @foreach($contacts as $contact)
                            <div @click="selected = '{{ $contact->name }}'; search = ''; open = false"
                                class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b dark:border-gray-600 last:border-0">
                                {{ $contact->name }}
                                @if($contact->company)
                                <span class="text-xs text-gray-400">({{ $contact->company }})</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @error('received_from')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Address To --}}
                <div>
                    <label for="address_to" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address
                        To *</label>
                    <input type="text" name="address_to" id="address_to" value="{{ old('address_to') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('address_to')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Letter Date --}}
                <div>
                    <label for="letter_date" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Letter
                        Date</label>
                    <input type="date" name="letter_date" id="letter_date" value="{{ old('letter_date') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @error('letter_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                {{-- Matter (autofill) --}}
                <div>
                    <label for="matter_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Matter /
                        Case Reference</label>
                    <div x-data="{ open: false, search: '', selectedId: '{{ old('matter_id') }}', selectedName: '' }"
                        class="relative">
                        <input type="hidden" name="matter_id" :value="selectedId">
                        <input type="text" x-model="selectedName"
                            @input="search = $event.target.value; open = search.length >= 2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div x-show="open" x-cloak @click.away="open = false"
                            class="absolute top-full left-0 mt-1 w-full bg-white dark:bg-gray-700 border dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-40 overflow-y-auto">
                            @foreach($matters as $matter)
                            <div @click="selectedId = '{{ $matter->id }}'; selectedName = '{{ $matter->name }}'; open = false"
                                class="px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer border-b dark:border-gray-600 last:border-0">
                                {{ $matter->name }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @error('matter_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Document Types (toggle chips + free-text Other) --}}
            <div class="mb-6" x-data="{
                types: {{ json_encode(old('doc_type', ['Letter'])) }},
                otherType: ''
            }">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Document Type(s) *</label>

                <!-- Toggle chips for predefined types -->
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

                <!-- Free-text Other input -->
                <div class="mb-3">
                    <label for="doc-type-other-input"
                        class="block mb-1 text-sm font-medium text-gray-900 dark:text-white">Other document
                        type:</label>
                    <input type="text" id="doc-type-other-input" x-model="otherType"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>

                @error('doc_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                <!-- Hidden inputs for form submission -->
                <template x-for="type in types" :key="type">
                    <input type="hidden" name="doc_type[]" :value="type">
                </template>
                <template x-if="otherType && otherType.trim() !== ''">
                    <input type="hidden" name="doc_type[]" :value="otherType">
                </template>
            </div>

            {{-- Details --}}
            <div class="mb-6">
                <label for="details"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Details</label>
                <textarea name="details" id="details" rows="3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('details') }}</textarea>
                @error('details')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            {{-- Remarks --}}
            <div class="mb-6">
                <label for="remarks"
                    class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Remarks</label>
                <textarea name="remarks" id="remarks" rows="2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('remarks') }}</textarea>
                @error('remarks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('front-desk.mail.index') }}"
                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">
                    Log Item
                </button>
            </div>
        </form>
    </div>
</div>
@endsection