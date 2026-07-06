<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $view = $request->get('view', 'daily');

        $rooms = Room::where('status', 'active')->orderBy('room_code')->get();

        if ($view === 'daily') {
            $bookings = Booking::with(['room', 'case', 'participants.contact', 'features'])
                ->where('booking_date', $date)
                ->where('booking_status', '!=', 'cancelled')
                ->orderBy('room_id')
                ->orderBy('start_time')
                ->get();

            $events = Event::with('room')
                ->where('status', 'approved')
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)
                ->orderBy('room_id')
                ->get();

            return view('schedule.daily', compact('bookings', 'events', 'rooms', 'date', 'view'));
        }

        if ($view === 'weekly') {
            $startOfWeek = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);
            $endOfWeek = Carbon::parse($date)->endOfWeek(Carbon::SUNDAY);

            $bookingsRaw = Booking::with(['room', 'case'])
                ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
                ->where('booking_status', '!=', 'cancelled')
                ->get();
            $bookings = $bookingsRaw->groupBy('room_id')
                ->map(fn($items) => $items->groupBy(fn($b) => $b->booking_date->toDateString()));

            $eventsRaw = Event::with('room')
                ->where('status', 'approved')
                ->whereBetween('start_date', [$startOfWeek, $endOfWeek])
                ->get();
            $events = $eventsRaw->groupBy('room_id')
                ->map(fn($items) => $items->groupBy(fn($e) => $e->start_date->toDateString()));

            $weekDays = [];
            for ($i = 0; $i < 7; $i++) {
                $weekDays[] = $startOfWeek->copy()->addDays($i);
            }

            return view('schedule.weekly', compact('bookings', 'events', 'rooms', 'weekDays', 'date', 'view', 'startOfWeek'));
        }

        if ($view === 'monthly') {
            $monthStart = Carbon::parse($date)->startOfMonth();
            $monthEnd = Carbon::parse($date)->endOfMonth();

            $bookingCounts = Booking::whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('booking_status', '!=', 'cancelled')
                ->selectRaw('booking_date, COUNT(DISTINCT room_id) as room_count')
                ->groupBy('booking_date')
                ->pluck('room_count', 'booking_date');

            $eventCounts = Event::where('status', 'approved')
                ->whereBetween('start_date', [$monthStart, $monthEnd])
                ->get()
                ->groupBy(fn($e) => $e->start_date->toDateString())
                ->map(fn($items) => $items->count());

            foreach ($eventCounts as $d => $c) {
                $bookingCounts[$d] = ($bookingCounts[$d] ?? 0) + $c;
            }

            $monthBookings = Booking::with(['room', 'case', 'participants.contact'])
                ->whereBetween('booking_date', [$monthStart, $monthEnd])
                ->where('booking_status', '!=', 'cancelled')
                ->get()
                ->groupBy(fn($b) => $b->booking_date->toDateString());

            $monthEvents = Event::with('room')
                ->where('status', 'approved')
                ->whereBetween('start_date', [$monthStart, $monthEnd])
                ->get()
                ->groupBy(fn($e) => $e->start_date->toDateString());

            return view('schedule.monthly', compact('bookingCounts', 'monthBookings', 'monthEvents', 'date', 'view', 'monthStart', 'monthEnd'));
        }

        return redirect()->route('schedule.index', ['view' => 'daily']);
    }
}
