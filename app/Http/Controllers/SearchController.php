<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q');
        $bookings = collect();
        $events = collect();

        if ($query) {
            $bookings = Booking::with(['room', 'case', 'participants.contact'])
                ->where(function ($q) use ($query) {
                    $q->where('booking_id', 'like', "%{$query}%")
                      ->orWhereHas('case', fn($c) => $c->where('reference_number', 'like', "%{$query}%"))
                      ->orWhereHas('participants.contact', fn($c) => $c->where('name', 'like', "%{$query}%"));
                })
                ->where('booking_status', '!=', 'cancelled')
                ->orderBy('booking_date', 'desc')
                ->limit(20)
                ->get();

            $events = Event::with('room')
                ->where(function ($q) use ($query) {
                    $q->where('event_name', 'like', "%{$query}%")
                      ->orWhere('organizer', 'like', "%{$query}%")
                      ->orWhere('reference_number', 'like', "%{$query}%");
                })
                ->where('status', 'approved')
                ->orderBy('start_date', 'desc')
                ->limit(20)
                ->get();
        }

        return view('search.index', compact('query', 'bookings', 'events'));
    }

    public function api(Request $request)
    {
        $query = $request->get('q');
        if (!$query || strlen($query) < 2) return response()->json([]);

        $results = [];

        $bookings = Booking::with(['room', 'case'])
            ->where('booking_id', 'like', "%{$query}%")
            ->orWhereHas('case', fn($c) => $c->where('reference_number', 'like', "%{$query}%"))
            ->orWhereHas('participants.contact', fn($c) => $c->where('name', 'like', "%{$query}%"))
            ->where('booking_status', '!=', 'cancelled')
            ->orderBy('booking_date', 'desc')->limit(5)->get();

        foreach ($bookings as $b) {
            $results[] = [
                'type' => 'Hearing',
                'title' => $b->booking_id . ' - ' . ($b->case->reference_number ?? 'No Case'),
                'subtitle' => $b->room->room_code . ' | ' . $b->booking_date->format('d/m/Y'),
                'url' => route('bookings.edit', $b),
            ];
        }

        $events = Event::with('room')
            ->where('event_name', 'like', "%{$query}%")
            ->orWhere('organizer', 'like', "%{$query}%")
            ->where('status', 'approved')->orderBy('start_date', 'desc')->limit(5)->get();

        foreach ($events as $e) {
            $results[] = [
                'type' => 'Event',
                'title' => $e->event_name,
                'subtitle' => $e->room->room_code . ' | ' . $e->start_date->format('d/m/Y'),
                'url' => route('events.edit', $e),
            ];
        }

        return response()->json(array_slice($results, 0, 8));
    }
}
