<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Cases;
use App\Models\Contact;
use App\Models\Feature;
use App\Models\BookingParticipant;
use App\Models\BookingFeature;
use App\Models\BookingBreakoutRoom;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with(['room', 'case', 'participants.contact'])
            ->when($request->search, function ($query, $search) {
                $query->where('booking_id', 'like', "%{$search}%")
                    ->orWhereHas('case', function ($q) use ($search) {
                        $q->where('reference_number', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('booking_status', $status);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->where('booking_date', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->where('booking_date', '<=', $date);
            })
            ->orderBy('booking_date', 'desc')
            ->orderBy('start_time')
            ->paginate(20);

        return view('bookings.index', compact('bookings'));
    }

    public function create(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $pax = $request->get('pax');
        $size = $request->get('size');
        $roomId = $request->get('room_id');
        $session = $request->get('session');

        $rooms = collect();
        $existingBookings = collect();
        $selectedRoom = null;
        $sizeLabel = '';
        $displayPax = null;

        $capacityMap = [
            'small' => [10],
            'medium' => [14],
            'large' => [22],
            'seminar' => [50],
            'auditorium' => [182],
        ];

        if ($size && isset($capacityMap[$size])) {
            $rooms = Room::where('status', 'active')
                ->whereIn('capacity', $capacityMap[$size])
                ->orderedByType()
                ->get();
            $displayPax = $capacityMap[$size][0];
            $sizeLabel = ucfirst($size);
        } elseif ($pax) {
            $rooms = Room::where('status', 'active')
                ->where('capacity', '>=', $pax)
                ->orderedByType()
                ->get();
            $displayPax = $pax;
        }

        if ($rooms->isNotEmpty()) {
            $existingBookings = Booking::where('booking_date', $date)
                ->where('booking_status', '!=', 'cancelled')
                ->get()
                ->groupBy('room_id');
        }

        if ($roomId && $session) {
            $selectedRoom = Room::find($roomId);
            $features = $selectedRoom->features()->orderBy('name')->get();
            $allRooms = Room::where('status', 'active')->where('is_breakout', true)->where('id', '!=', $roomId)->orderedByType()->get();
            $contacts = Contact::orderBy('name')->get();

            return view('bookings.create', compact(
                'date', 'pax', 'size', 'sizeLabel', 'displayPax',
                'rooms', 'existingBookings', 'selectedRoom', 'session',
                'features', 'allRooms', 'contacts'
            ));
        }

        return view('bookings.create', compact(
            'date', 'pax', 'size', 'sizeLabel', 'displayPax',
            'rooms', 'existingBookings'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|string|unique:bookings,booking_id',
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date',
            'session_type' => 'required|in:full_day,half_am,half_pm',
            'case_reference' => 'nullable|string',
            'claimant' => 'nullable|string',
            'claimant_solicitor' => 'nullable|string',
            'respondent' => 'nullable|string',
            'respondent_solicitor' => 'nullable|string',
            'arbitrators' => 'nullable|string',
            'number_of_attendees' => 'nullable|integer',
            'booking_status' => 'required|in:tentative,confirmed',
            'features' => 'nullable|array',
            'breakout_rooms' => 'nullable|array',
            'special_requirements' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $times = match($validated['session_type']) {
            'full_day' => ['09:00:00', '17:00:00'],
            'half_am' => ['09:00:00', '13:00:00'],
            'half_pm' => ['14:00:00', '17:00:00'],
            default => ['09:00:00', '17:00:00'],
        };

        $conflict = Booking::where('room_id', $validated['room_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('booking_status', '!=', 'cancelled')
            ->where(function ($q) use ($times) {
                $q->where('start_time', '<', $times[1])
                  ->where('end_time', '>', $times[0]);
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'This room is already booked for the selected time.');
        }

        $case = null;
        if ($validated['case_reference']) {
            $case = Cases::firstOrCreate(
                ['reference_number' => $validated['case_reference']],
                ['status' => 'active']
            );
        }

        $findOrCreateContact = function ($name) {
            if (!$name) return null;
            return Contact::firstOrCreate(['name' => $name], ['type' => 'individual']);
        };

        $booking = Booking::create([
            'booking_id' => $validated['booking_id'],
            'case_id' => $case?->id,
            'room_id' => $validated['room_id'],
            'booking_date' => $validated['booking_date'],
            'session_type' => $validated['session_type'],
            'start_time' => $times[0],
            'end_time' => $times[1],
            'booking_type' => 'external',
            'number_of_attendees' => $validated['number_of_attendees'],
            'booking_status' => $validated['booking_status'],
            'billing_status' => 'pending',
            'special_requirements' => $validated['special_requirements'],
            'internal_notes' => $validated['internal_notes'],
            'booked_by' => auth()->id(),
        ]);

        if ($contact = $findOrCreateContact($validated['claimant'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'claimant', 'display_order' => 0]);
        }
        if ($contact = $findOrCreateContact($validated['claimant_solicitor'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'claimant_solicitor', 'display_order' => 1]);
        }
        if ($contact = $findOrCreateContact($validated['respondent'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'respondent', 'display_order' => 2]);
        }
        if ($contact = $findOrCreateContact($validated['respondent_solicitor'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'respondent_solicitor', 'display_order' => 3]);
        }

        if ($validated['arbitrators']) {
            $arbNames = explode(',', $validated['arbitrators']);
            foreach ($arbNames as $i => $name) {
                $name = trim($name);
                if ($name) {
                    $contact = $findOrCreateContact($name);
                    BookingParticipant::create([
                        'booking_id' => $booking->id,
                        'contact_id' => $contact->id,
                        'role' => $i === 0 ? 'presiding_arbitrator' : 'co_arbitrator',
                        'display_order' => $i + 4,
                    ]);
                }
            }
        }

        if ($validated['features']) {
            foreach ($validated['features'] as $featureId) {
                BookingFeature::create(['booking_id' => $booking->id, 'feature_id' => $featureId]);
            }
        }

        if (!empty($validated['breakout_rooms'])) {
            foreach ($validated['breakout_rooms'] as $breakoutRoomId) {
                if ($breakoutRoomId) {
                    BookingBreakoutRoom::create(['booking_id' => $booking->id, 'room_id' => $breakoutRoomId]);
                }
            }
        }

        return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
    }

    public function edit(Booking $booking)
    {
        $booking->load(['room', 'case', 'participants.contact', 'features', 'breakoutRooms.room']);

        $viewMode = request()->has('view');
        $date = $booking->booking_date->format('Y-m-d');
        $selectedRoom = $booking->room;
        $session = $booking->session_type;
        $features = $selectedRoom->features()->orderBy('name')->get();
        $allRooms = Room::where('status', 'active')->where('is_breakout', true)->where('id', '!=', $selectedRoom->id)->orderedByType()->get();
        $contacts = Contact::orderBy('name')->get();

        $logs = \App\Models\ActivityLog::where('entity_type', 'Booking')
            ->where('entity_id', $booking->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('bookings.edit', compact(
            'booking', 'date', 'selectedRoom', 'session',
            'features', 'allRooms', 'contacts', 'logs', 'viewMode'
        ));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'booking_id' => 'required|string',
            'room_id' => 'required|exists:rooms,id',
            'booking_date' => 'required|date',
            'session_type' => 'required|in:full_day,half_am,half_pm',
            'case_reference' => 'nullable|string',
            'claimant' => 'nullable|string',
            'claimant_solicitor' => 'nullable|string',
            'respondent' => 'nullable|string',
            'respondent_solicitor' => 'nullable|string',
            'arbitrators' => 'nullable|string',
            'number_of_attendees' => 'nullable|integer',
            'booking_status' => 'required|in:tentative,confirmed,completed,cancelled',
            'features' => 'nullable|array',
            'breakout_rooms' => 'nullable|array',
            'special_requirements' => 'nullable|string',
            'internal_notes' => 'nullable|string',
        ]);

        $times = match($validated['session_type']) {
            'full_day' => ['09:00:00', '17:00:00'],
            'half_am' => ['09:00:00', '13:00:00'],
            'half_pm' => ['14:00:00', '17:00:00'],
            default => ['09:00:00', '17:00:00'],
        };

        $conflict = Booking::where('room_id', $validated['room_id'])
            ->where('booking_date', $validated['booking_date'])
            ->where('booking_status', '!=', 'cancelled')
            ->where('id', '!=', $booking->id)
            ->where(function ($q) use ($times) {
                $q->where('start_time', '<', $times[1])
                  ->where('end_time', '>', $times[0]);
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'This room is already booked for the selected time.');
        }

        $case = null;
        if ($validated['case_reference']) {
            $case = Cases::firstOrCreate(
                ['reference_number' => $validated['case_reference']],
                ['status' => 'active']
            );
        }

        $findOrCreateContact = function ($name) {
            if (!$name) return null;
            return Contact::firstOrCreate(['name' => $name], ['type' => 'individual']);
        };

        $booking->update([
            'booking_id' => $validated['booking_id'],
            'case_id' => $case?->id,
            'room_id' => $validated['room_id'],
            'booking_date' => $validated['booking_date'],
            'session_type' => $validated['session_type'],
            'start_time' => $times[0],
            'end_time' => $times[1],
            'number_of_attendees' => $validated['number_of_attendees'],
            'booking_status' => $validated['booking_status'],
            'special_requirements' => $validated['special_requirements'],
            'internal_notes' => $validated['internal_notes'],
        ]);

        $booking->participants()->delete();

        if ($contact = $findOrCreateContact($validated['claimant'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'claimant', 'display_order' => 0]);
        }
        if ($contact = $findOrCreateContact($validated['claimant_solicitor'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'claimant_solicitor', 'display_order' => 1]);
        }
        if ($contact = $findOrCreateContact($validated['respondent'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'respondent', 'display_order' => 2]);
        }
        if ($contact = $findOrCreateContact($validated['respondent_solicitor'])) {
            BookingParticipant::create(['booking_id' => $booking->id, 'contact_id' => $contact->id, 'role' => 'respondent_solicitor', 'display_order' => 3]);
        }

        if ($validated['arbitrators']) {
            $arbNames = explode(',', $validated['arbitrators']);
            foreach ($arbNames as $i => $name) {
                $name = trim($name);
                if ($name) {
                    $contact = $findOrCreateContact($name);
                    BookingParticipant::create([
                        'booking_id' => $booking->id,
                        'contact_id' => $contact->id,
                        'role' => $i === 0 ? 'presiding_arbitrator' : 'co_arbitrator',
                        'display_order' => $i + 4,
                    ]);
                }
            }
        }

        $booking->features()->sync($validated['features'] ?? []);
        $booking->breakoutRooms()->delete();

        if (!empty($validated['breakout_rooms'])) {
            foreach ($validated['breakout_rooms'] as $breakoutRoomId) {
                if ($breakoutRoomId) {
                    BookingBreakoutRoom::create(['booking_id' => $booking->id, 'room_id' => $breakoutRoomId]);
                }
            }
        }

        return redirect()->route('bookings.edit', $booking)->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->update(['booking_status' => 'cancelled']);
        return redirect()->route('bookings.index')->with('success', 'Booking cancelled.');
    }
}
