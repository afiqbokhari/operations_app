<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Feature;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('features')->orderBy('room_code')->get();
        return view('rooms.index', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:20|unique:rooms,room_code',
            'room_name' => 'required|string|max:100',
            'floor' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:hearing_room,breakout_room,mediation_room,conference_room',
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string',
            'is_breakout' => 'boolean',
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')->with('success', 'Room added successfully.');
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_code' => 'required|string|max:20|unique:rooms,room_code,' . $room->id,
            'room_name' => 'required|string|max:100',
            'floor' => 'nullable|string|max:50',
            'capacity' => 'required|integer|min:1',
            'type' => 'required|in:hearing_room,breakout_room,mediation_room,conference_room',
            'status' => 'required|in:active,maintenance,inactive',
            'notes' => 'nullable|string',
            'is_breakout' => 'boolean',
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')->with('success', 'Room updated successfully.');
    }

    public function destroy(Room $room)
    {
        if ($room->bookings()->exists()) {
            return redirect()->route('rooms.index')->with('error', 'Cannot delete room with existing bookings.');
        }

        $room->delete();

        return redirect()->route('rooms.index')->with('success', 'Room deleted successfully.');
    }

    public function features(Room $room)
    {
        $features = Feature::orderBy('name')->get();
        $roomFeatures = $room->features->pluck('id')->toArray();
        return view('rooms.features', compact('room', 'features', 'roomFeatures'));
    }

    public function updateFeatures(Request $request, Room $room)
    {
        $room->features()->sync($request->input('features', []));

        return redirect()->route('rooms.index')->with('success', 'Features updated for ' . $room->room_name . '.');
    }
}
