<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::with(['room', 'bookedBy', 'reviewedBy'])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->search, fn($q, $s) => $q->where('event_name', 'like', "%{$s}%"))
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return view('events.index', compact('events'));
    }

    public function create()
    {
        $rooms = Room::where('status', 'active')->orderedByType()->get();
        return view('events.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'organizer' => 'nullable|string|max:255',
            'attendees_count' => 'nullable|integer|min:1',
            'setup_needed' => 'boolean',
            'catering_needed' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        // Check conflicts with both bookings and events
        $conflict = $this->checkConflict(
            $validated['room_id'],
            $validated['start_date'],
            $validated['end_date'],
            $validated['start_time'],
            $validated['end_time']
        );

        if ($conflict) {
            return back()->withInput()->with('error', 'Room is already booked for this time period.');
        }

        Event::create([
            'event_name' => $validated['event_name'],
            'room_id' => $validated['room_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'organizer' => $validated['organizer'],
            'attendees_count' => $validated['attendees_count'],
            'setup_needed' => $validated['setup_needed'] ?? false,
            'catering_needed' => $validated['catering_needed'] ?? false,
            'notes' => $validated['notes'],
            'status' => 'pending',
            'booked_by' => auth()->id(),
        ]);

        return redirect()->route('events.index')->with('success', 'Event request submitted for approval.');
    }

    public function approve(Event $event)
    {
        $event->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Event approved.');
    }

    public function reject(Request $request, Event $event)
    {
        $event->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reject_reason' => $request->reason,
        ]);

        return back()->with('success', 'Event rejected.');
    }

    public function edit(Event $event)
    {
        $rooms = Room::where('status', 'active')->orderedByType()->get();
        return view('events.edit', compact('event', 'rooms'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'organizer' => 'nullable|string|max:255',
            'attendees_count' => 'nullable|integer|min:1',
            'setup_needed' => 'boolean',
            'catering_needed' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $conflict = $this->checkConflict(
            $validated['room_id'], $validated['start_date'], $validated['end_date'],
            $validated['start_time'], $validated['end_time'], $event->id
        );

        if ($conflict) {
            return back()->withInput()->with('error', 'Room is already booked for this time period.');
        }

        $event->update([
            'event_name' => $validated['event_name'],
            'room_id' => $validated['room_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'organizer' => $validated['organizer'],
            'attendees_count' => $validated['attendees_count'],
            'setup_needed' => $validated['setup_needed'] ?? false,
            'catering_needed' => $validated['catering_needed'] ?? false,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('events.index')->with('success', 'Event updated.');
    }

    public function cancel(Event $event)
    {
        $event->update(['status' => 'rejected', 'reject_reason' => 'Cancelled by user']);
        return redirect()->route('events.index')->with('success', 'Event cancelled.');
    }

    private function checkConflict($roomId, $startDate, $endDate, $startTime, $endTime, $excludeEventId = null)
    {
        // Check bookings table
        $bookingConflict = \App\Models\Booking::where('room_id', $roomId)
            ->where('booking_status', '!=', 'cancelled')
            ->where('booking_date', '>=', $startDate)
            ->where('booking_date', '<=', $endDate)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        if ($bookingConflict) return true;

        // Check events table (approved only)
        $eventConflict = Event::where('room_id', $roomId)
            ->when($excludeEventId, fn($q) => $q->where('id', '!=', $excludeEventId))
            ->where('status', 'approved')
            ->where(function ($q) use ($startDate, $endDate, $startTime, $endTime) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
            })
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime)
            ->exists();

        return $eventConflict;
    }
}
