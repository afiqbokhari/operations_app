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
        $type = $request->get('type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');

        $bookings = collect();
        $events = collect();

        if ($query || $dateFrom || $dateTo || $status) {
            if ($type !== 'event') {
                $bookings = Booking::with(['room', 'case', 'participants.contact'])
                    ->when($query, function ($q) use ($query) {
                        $q->where(function ($q) use ($query) {
                            $q->where('booking_id', 'like', "%{$query}%")
                            ->orWhereHas('case', fn($c) => $c->where('reference_number', 'like', "%{$query}%"))
                            ->orWhereHas('participants.contact', fn($c) => $c->where('name', 'like', "%{$query}%"));
                        });
                    })
                    ->when($dateFrom, fn($q) => $q->where('booking_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('booking_date', '<=', $dateTo))
                    ->when($status, fn($q) => $q->where('booking_status', $status))
                    ->where('booking_status', '!=', 'cancelled')
                    ->orderBy('booking_date', 'desc')
                    ->limit(30)
                    ->get();
            }

            if ($type !== 'hearing') {
                $events = Event::with('room')
                    ->when($query, function ($q) use ($query) {
                        $q->where(function ($q) use ($query) {
                            $q->where('event_name', 'like', "%{$query}%")
                            ->orWhere('organizer', 'like', "%{$query}%")
                            ->orWhere('reference_number', 'like', "%{$query}%");
                        });
                    })
                    ->when($dateFrom, fn($q) => $q->where('start_date', '>=', $dateFrom))
                    ->when($dateTo, fn($q) => $q->where('start_date', '<=', $dateTo))
                    ->when($status, fn($q) => $q->where('status', $status === 'confirmed' ? 'approved' : $status))
                    ->orderBy('start_date', 'desc')
                    ->limit(30)
                    ->get();
            }
        }

        return view('search.index', compact('query', 'bookings', 'events', 'type', 'dateFrom', 'dateTo', 'status'));
    }

    public function api(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status');

        // Allow empty query when filters are present
        if (!$query && !$type && !$dateFrom && !$dateTo && !$status) return response()->json([]);

        $results = [];

        if ($type !== 'event') {
            $bookings = Booking::with(['room', 'case'])
                ->where(function ($q) use ($query) {
                    $q->where('booking_id', 'like', "%{$query}%")
                    ->orWhereHas('case', fn($c) => $c->where('reference_number', 'like', "%{$query}%"))
                    ->orWhereHas('participants.contact', fn($c) => $c->where('name', 'like', "%{$query}%"));
                })
                ->when($dateFrom, fn($q) => $q->where('booking_date', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('booking_date', '<=', $dateTo))
                ->when($status, fn($q) => $q->where('booking_status', $status))
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
        }

        if ($type !== 'hearing') {
            $events = Event::with('room')
                ->where(function ($q) use ($query) {
                    $q->where('event_name', 'like', "%{$query}%")
                    ->orWhere('organizer', 'like', "%{$query}%");
                })
                ->when($dateFrom, fn($q) => $q->where('start_date', '>=', $dateFrom))
                ->when($dateTo, fn($q) => $q->where('start_date', '<=', $dateTo))
                ->when($status, fn($q) => $q->where('status', $status === 'confirmed' ? 'approved' : $status))
                ->orderBy('start_date', 'desc')->limit(5)->get();

            foreach ($events as $e) {
                $results[] = [
                    'type' => 'Event',
                    'title' => $e->event_name,
                    'subtitle' => $e->room->room_code . ' | ' . $e->start_date->format('d/m/Y'),
                    'url' => route('events.edit', $e),
                ];
            }
        }

        return response()->json(array_slice($results, 0, 8));
    }
}
