<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Feature;
use App\Models\RoomFeature;

class RoomFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $featureIds = Feature::pluck('id', 'name')->toArray();
        $rooms = Room::pluck('id', 'room_code')->toArray();

        // All rooms get Projector and Smart Screen
        $allRooms = Room::all();
        foreach ($allRooms as $room) {
            RoomFeature::create(['room_id' => $room->id, 'feature_id' => $featureIds['Projector']]);
            RoomFeature::create(['room_id' => $room->id, 'feature_id' => $featureIds['Smart Screen']]);
        }

        // HR1, HR6, HR7, SR1: Recording + VC
        $specialRooms = ['HR1', 'HR6', 'HR7', 'SR1'];
        foreach ($specialRooms as $code) {
            RoomFeature::create(['room_id' => $rooms[$code], 'feature_id' => $featureIds['Recording']]);
            RoomFeature::create(['room_id' => $rooms[$code], 'feature_id' => $featureIds['VC']]);
        }

        // Auditorium: PA System, Stage Lighting, Podium/Microphone
        RoomFeature::create(['room_id' => $rooms['AUD'], 'feature_id' => $featureIds['Recording']]);
        RoomFeature::create(['room_id' => $rooms['AUD'], 'feature_id' => $featureIds['VC']]);
        RoomFeature::create(['room_id' => $rooms['AUD'], 'feature_id' => $featureIds['PA System']]);
        RoomFeature::create(['room_id' => $rooms['AUD'], 'feature_id' => $featureIds['Stage Lighting']]);
        RoomFeature::create(['room_id' => $rooms['AUD'], 'feature_id' => $featureIds['Podium/Microphone']]);

        // Seminar Rooms: PA System
        RoomFeature::create(['room_id' => $rooms['SR1'], 'feature_id' => $featureIds['PA System']]);
        RoomFeature::create(['room_id' => $rooms['SR2'], 'feature_id' => $featureIds['PA System']]);
    }
}
