@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">New Booking</h1>

    {{-- Date + Size/Pax --}}
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('bookings.create') }}" class="space-y-4">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Date</label>
                <input type="date" name="date" value="{{ $date }}" required onchange="this.form.submit()"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Room Size</label>
                <div class="flex flex-wrap gap-2">
                    <button type="submit" name="size" value="small" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $size === 'small' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300' }}">Small (10 pax)</button>
                    <button type="submit" name="size" value="medium" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $size === 'medium' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300' }}">Medium (14 pax)</button>
                    <button type="submit" name="size" value="large" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $size === 'large' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300' }}">Large (22 pax)</button>
                    <button type="submit" name="size" value="seminar" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $size === 'seminar' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300' }}">Seminar (50 pax)</button>
                    <button type="submit" name="size" value="auditorium" class="px-3 py-1.5 text-xs font-medium rounded-lg border {{ $size === 'auditorium' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 hover:bg-gray-100 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-300' }}">Auditorium (182 pax)</button>
                </div>
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Or Custom Pax</label>
                <input type="number" name="pax" value="{{ $pax }}" min="1" placeholder="Enter number then press Enter..."
                       onchange="this.form.submit()"
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </form>
    </div>

    {{-- Room Availability (Collapsible) --}}
    @if($rooms->isNotEmpty())
    <div x-data="{ showRooms: {{ isset($selectedRoom) ? 'false' : 'true' }} }" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center cursor-pointer" @click="showRooms = !showRooms">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                @if($size)
                    {{ $sizeLabel }} Rooms for {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                @else
                    Rooms (≥ {{ $displayPax }} pax) for {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
                @endif
            </h2>
            <span class="text-gray-500 text-sm" x-text="showRooms ? '▲ Hide' : '▼ Show'"></span>
        </div>
        <div x-show="showRooms" class="relative overflow-x-auto rounded-lg mt-4">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Room</th>
                        <th class="px-4 py-3">Capacity</th>
                        <th class="px-4 py-3">Floor</th>
                        <th class="px-4 py-3">AM</th>
                        <th class="px-4 py-3">PM</th>
                        <th class="px-4 py-3">Full Day</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        @php
                            $roomBookings = $existingBookings[$room->id] ?? collect();
                            $amBooked = $roomBookings->contains(function($b) { return in_array($b->session_type, ['full_day', 'half_am']); });
                            $pmBooked = $roomBookings->contains(function($b) { return in_array($b->session_type, ['full_day', 'half_pm']); });
                            $isSelected = isset($selectedRoom) && $selectedRoom->id === $room->id;
                            $params = ['date' => $date, 'room_id' => $room->id];
                            if ($size) $params['size'] = $size;
                            if ($pax) $params['pax'] = $pax;
                        @endphp
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 {{ $isSelected ? 'ring-2 ring-blue-500' : '' }}">
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $room->room_code }}</td>
                        <td class="px-4 py-3">{{ $room->capacity }}</td>
                        <td class="px-4 py-3">{{ $room->floor }}</td>
                        <td class="px-4 py-3">
                            @if($amBooked)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Booked</span>
                            @else
                                <a href="{{ route('bookings.create', array_merge($params, ['session' => 'half_am'])) }}" class="px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Pick AM</a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($pmBooked)
                                <span class="px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">Booked</span>
                            @else
                                <a href="{{ route('bookings.create', array_merge($params, ['session' => 'half_pm'])) }}" class="px-2 py-1 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700">Pick PM</a>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if(!$amBooked && !$pmBooked)
                                <a href="{{ route('bookings.create', array_merge($params, ['session' => 'full_day'])) }}" class="px-2 py-1 text-xs font-medium text-white bg-purple-600 rounded hover:bg-purple-700">Pick Full</a>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @elseif(($pax || $size) && $rooms->isEmpty())
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-8 text-center text-gray-500 dark:text-gray-400">
        No matching rooms available.
    </div>
    @endif

    {{-- Booking Form --}}
    @if(isset($selectedRoom))
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Booking: {{ $selectedRoom->room_name }} ({{ $selectedRoom->room_code }}) — 
            {{ match($session) { 'full_day' => 'Full Day (9:00-17:00)', 'half_am' => 'Half AM (9:00-13:00)', 'half_pm' => 'Half PM (14:00-17:00)', default => $session } }}
        </h2>

        <form action="{{ route('bookings.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
            <input type="hidden" name="session_type" value="{{ $session }}">
            <input type="hidden" name="booking_date" value="{{ $date }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Booking ID</label>
                    <input type="text" name="booking_id" required
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Case Reference</label>
                    <input type="text" name="case_reference" list="caseList" placeholder="Start typing..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <datalist id="caseList">
                        @foreach(\App\Models\Cases::orderBy('reference_number')->get() as $case)
                            <option value="{{ $case->reference_number }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Claimant</label>
                    <input type="text" name="claimant" list="contactList" placeholder="Search contact..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Claimant Solicitor</label>
                    <input type="text" name="claimant_solicitor" list="contactList" placeholder="Search contact..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Respondent</label>
                    <input type="text" name="respondent" list="contactList" placeholder="Search contact..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Respondent Solicitor</label>
                    <input type="text" name="respondent_solicitor" list="contactList" placeholder="Search contact..."
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            <datalist id="contactList">
                @foreach($contacts as $contact)
                    <option value="{{ $contact->name }}">
                @endforeach
            </datalist>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Arbitrators (comma separated)</label>
                <input type="text" name="arbitrators" list="contactList" placeholder="Search and add..."
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Number of Attendees</label>
                    <input type="number" name="number_of_attendees" value="{{ $displayPax ?? $pax }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                    <select name="booking_status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="confirmed">Confirmed</option>
                        <option value="tentative">Tentative</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Features</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($features as $feature)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div x-data="{ breakouts: [] }">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Breakout Rooms</label>
                <template x-for="(b, index) in breakouts" :key="index">
                    <div class="flex items-center gap-2 mb-2">
                        <select :name="'breakout_rooms[]'" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select breakout room</option>
                            @foreach($allRooms as $r)
                                <option value="{{ $r->id }}">{{ $r->room_code }} - {{ $r->room_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="breakouts.splice(index, 1)" class="text-red-600 hover:text-red-800 text-sm">✕</button>
                    </div>
                </template>
                <button type="button" @click="breakouts.push({})" class="mt-2 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">+ Add Breakout Room</button>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Special Requirements</label>
                <textarea name="special_requirements" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Internal Notes</label>
                <textarea name="internal_notes" rows="2" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('bookings.create', ['date' => $date]) }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Clear</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Save Booking</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
