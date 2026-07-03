<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            ['room_code' => 'HR1',  'room_name' => 'Hearing Room 1',  'floor' => 'G',         'capacity' => 22,  'type' => 'hearing_room'],
            ['room_code' => 'HR2',  'room_name' => 'Hearing Room 2',  'floor' => 'G',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR3',  'room_name' => 'Hearing Room 3',  'floor' => 'G',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR4',  'room_name' => 'Hearing Room 4',  'floor' => 'G',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR5',  'room_name' => 'Hearing Room 5',  'floor' => 'G',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR6',  'room_name' => 'Hearing Room 6',  'floor' => 'G',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR7',  'room_name' => 'Hearing Room 7',  'floor' => 'G',         'capacity' => 22,  'type' => 'hearing_room'],
            ['room_code' => 'HR8',  'room_name' => 'Hearing Room 8',  'floor' => 'G',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR9',  'room_name' => 'Hearing Room 9',  'floor' => '1',         'capacity' => 22,  'type' => 'hearing_room'],
            ['room_code' => 'HR10', 'room_name' => 'Hearing Room 10', 'floor' => '1',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR11', 'room_name' => 'Hearing Room 11', 'floor' => '1',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR12', 'room_name' => 'Hearing Room 12', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR13', 'room_name' => 'Hearing Room 13', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR14', 'room_name' => 'Hearing Room 14', 'floor' => '1',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR15', 'room_name' => 'Hearing Room 15', 'floor' => '1',         'capacity' => 10,  'type' => 'hearing_room'],
            ['room_code' => 'HR16', 'room_name' => 'Hearing Room 16', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR17', 'room_name' => 'Hearing Room 17', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR18', 'room_name' => 'Hearing Room 18', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'HR19', 'room_name' => 'Hearing Room 19', 'floor' => '1',         'capacity' => 14,  'type' => 'hearing_room'],
            ['room_code' => 'AUD',  'room_name' => 'Auditorium',      'floor' => '3',         'capacity' => 182, 'type' => 'conference_room'],
            ['room_code' => 'SR1',  'room_name' => 'Seminar Room 1',  'floor' => 'Pavillion', 'capacity' => 50,  'type' => 'conference_room'],
            ['room_code' => 'SR2',  'room_name' => 'Seminar Room 2',  'floor' => 'Pavillion', 'capacity' => 50,  'type' => 'conference_room'],
        ];

        foreach ($rooms as $room) {
            Room::create(array_merge($room, ['status' => 'active']));
        }
    }
}
