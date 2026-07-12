<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class BreakoutRoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [];
        for ($i = 1; $i <= 10; $i++) {
            $rooms[] = ['room_code' => 'BR' . $i, 'room_name' => 'Breakout Room ' . $i, 'floor' => $i <= 5 ? 'G' : '1', 'capacity' => 4, 'type' => 'breakout_room', 'status' => 'active', 'is_breakout' => true];
        }
        $rooms[] = ['room_code' => 'BR20', 'room_name' => 'Breakout Room 20', 'floor' => '2', 'capacity' => 4, 'type' => 'breakout_room', 'status' => 'active', 'is_breakout' => true];
        $rooms[] = ['room_code' => 'BR21', 'room_name' => 'Breakout Room 21', 'floor' => '2', 'capacity' => 4, 'type' => 'breakout_room', 'status' => 'active', 'is_breakout' => true];

        foreach ($rooms as $room) {
            Room::create($room);
        }
    }
}
