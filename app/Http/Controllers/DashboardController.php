<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Room;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $todayBookings = Booking::with(['room', 'case', 'participants.contact'])
            ->where('booking_date', $today)
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('start_time')
            ->get();

        $todayEvents = Event::with('room')
            ->where('status', 'approved')
            ->where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->orderBy('start_time')
            ->get();

        $roomsInUse = $todayBookings->pluck('room_id')->merge($todayEvents->pluck('room_id'))->unique()->count();

        $monthBookings = Booking::whereBetween('booking_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->where('booking_status', '!=', 'cancelled')
            ->count();

        $monthEvents = Event::where('status', 'approved')
            ->whereBetween('start_date', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
            ->count();

        $totalRooms = Room::where('status', 'active')->count();

        return view('dashboard', compact(
            'todayBookings', 'todayEvents', 'roomsInUse',
            'monthBookings', 'monthEvents', 'totalRooms', 'today'
        ));
    }
}
