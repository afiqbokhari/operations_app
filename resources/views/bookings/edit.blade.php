@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    <div class="mb-6">
        <a href="javascript:history.back()" class="text-blue-600 dark:text-blue-500 hover:underline">← Back</a>
    </div>

    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
        {{ $viewMode ? 'View Booking' : 'Edit Booking' }}: {{ $booking->booking_id }}
    </h1>

    @if(session('error'))
        <div class="flex items-center p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            {{ $selectedRoom->room_name }} ({{ $selectedRoom->room_code }}) — 
            {{ match($session) { 'full_day' => 'Full Day (9:00-17:00)', 'half_am' => 'Half AM (9:00-13:00)', 'half_pm' => 'Half PM (14:00-17:00)', default => $session } }}
            on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
        </h2>

        <form action="{{ route('bookings.update', $booking) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="room_id" value="{{ $selectedRoom->id }}">
            <input type="hidden" name="session_type" value="{{ $session }}">
            <input type="hidden" name="booking_date" value="{{ $date }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Booking ID</label>
                    <input type="text" name="booking_id" value="{{ $booking->booking_id }}" required {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Case Reference</label>
                    <input type="text" name="case_reference" value="{{ $booking->case->reference_number ?? '' }}" list="caseList" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                    @if(!$viewMode)
                    <datalist id="caseList">
                        @foreach(\App\Models\Cases::orderBy('reference_number')->get() as $case)
                            <option value="{{ $case->reference_number }}">
                        @endforeach
                    </datalist>
                    @endif
                </div>
            </div>

            @php
                $claimant = $booking->participants->where('role', 'claimant')->first();
                $claimantSol = $booking->participants->where('role', 'claimant_solicitor')->first();
                $respondent = $booking->participants->where('role', 'respondent')->first();
                $respondentSol = $booking->participants->where('role', 'respondent_solicitor')->first();
                $arbitrators = $booking->participants->whereIn('role', ['presiding_arbitrator', 'co_arbitrator'])->pluck('contact.name')->implode(', ');
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Claimant</label>
                    <input type="text" name="claimant" value="{{ $claimant->contact->name ?? '' }}" list="contactList" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Claimant Solicitor</label>
                    <input type="text" name="claimant_solicitor" value="{{ $claimantSol->contact->name ?? '' }}" list="contactList" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Respondent</label>
                    <input type="text" name="respondent" value="{{ $respondent->contact->name ?? '' }}" list="contactList" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Respondent Solicitor</label>
                    <input type="text" name="respondent_solicitor" value="{{ $respondentSol->contact->name ?? '' }}" list="contactList" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
            </div>

            @if(!$viewMode)
            <datalist id="contactList">
                @foreach($contacts as $contact)
                    <option value="{{ $contact->name }}">
                @endforeach
            </datalist>
            @endif

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Arbitrators (comma separated)</label>
                <input type="text" name="arbitrators" value="{{ $arbitrators }}" list="contactList" {{ $viewMode ? 'disabled' : '' }}
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Number of Attendees</label>
                    <input type="number" name="number_of_attendees" value="{{ $booking->number_of_attendees }}" {{ $viewMode ? 'disabled' : '' }}
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                </div>
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                    <select name="booking_status" {{ $viewMode ? 'disabled' : '' }}
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">
                        <option value="tentative" {{ $booking->booking_status === 'tentative' ? 'selected' : '' }}>Tentative</option>
                        <option value="confirmed" {{ $booking->booking_status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ $booking->booking_status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $booking->booking_status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Features</label>
                <div class="flex flex-wrap gap-3">
                    @foreach($features as $feature)
                    <label class="flex items-center space-x-2 {{ $viewMode ? 'opacity-70' : '' }}">
                        <input type="checkbox" name="features[]" value="{{ $feature->id }}" 
                            {{ $booking->features->contains($feature->id) ? 'checked' : '' }}
                            {{ $viewMode ? 'disabled' : '' }}
                            class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $feature->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            @if(!$viewMode)
            <div x-data="{ breakouts: {{ $booking->breakoutRooms->count() }} }">
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Breakout Rooms</label>
                @foreach($booking->breakoutRooms as $i => $br)
                <div class="flex items-center gap-2 mb-2">
                    <select name="breakout_rooms[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">Select breakout room</option>
                        @foreach($allRooms as $r)
                            <option value="{{ $r->id }}" {{ $br->room_id == $r->id ? 'selected' : '' }}>{{ $r->room_code }} - {{ $r->room_name }}</option>
                        @endforeach
                    </select>
                    <button type="button" @click="breakouts--; $el.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm">✕</button>
                </div>
                @endforeach
                <template x-for="i in breakouts - {{ $booking->breakoutRooms->count() }}" :key="i">
                    <div class="flex items-center gap-2 mb-2">
                        <select name="breakout_rooms[]" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="">Select breakout room</option>
                            @foreach($allRooms as $r)
                                <option value="{{ $r->id }}">{{ $r->room_code }} - {{ $r->room_name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="breakouts--; $el.parentElement.remove()" class="text-red-600 hover:text-red-800 text-sm">✕</button>
                    </div>
                </template>
                <button type="button" @click="breakouts++" class="mt-2 px-3 py-1.5 text-xs font-medium text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-300 dark:hover:bg-blue-800">+ Add Breakout Room</button>
            </div>
            @else
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Breakout Rooms</label>
                @if($booking->breakoutRooms->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach($booking->breakoutRooms as $br)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-1 rounded dark:bg-blue-900 dark:text-blue-300">{{ $br->room->room_code }} - {{ $br->room->room_name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">None</p>
                @endif
            </div>
            @endif

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Special Requirements</label>
                <textarea name="special_requirements" rows="2" {{ $viewMode ? 'disabled' : '' }}
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">{{ $booking->special_requirements }}</textarea>
            </div>

            <div>
                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Internal Notes</label>
                <textarea name="internal_notes" rows="2" {{ $viewMode ? 'disabled' : '' }}
                          class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $viewMode ? 'opacity-70 cursor-not-allowed' : '' }}">{{ $booking->internal_notes }}</textarea>
            </div>

            <div class="flex justify-end space-x-3">
                @if($viewMode)
                    <a href="{{ route('bookings.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Close</a>
                    <a href="{{ route('bookings.edit', $booking) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Edit</a>
                @else
                    <a href="{{ route('bookings.index') }}" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600">Cancel</a>
                    <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700">Update Booking</button>
                @endif
            </div>
        </form>

        @if($logs->isNotEmpty())
        <div class="mt-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity History</h2>
            <div class="relative overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2">Time</th>
                            <th class="px-4 py-2">User</th>
                            <th class="px-4 py-2">Action</th>
                            <th class="px-4 py-2 hidden md:table-cell">Changes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 text-xs">{{ $log->created_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-2">{{ $log->user?->name ?? 'System' }}</td>
                            <td class="px-4 py-2">{{ ucfirst($log->action) }}</td>
                            <td class="px-4 py-2 hidden md:table-cell text-xs">@if($log->changes) @foreach($log->changes as $change) {!! $change !!}<br> @endforeach @else {{ $log->description }} @endif</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
