<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class BreakoutRoomSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 8; $i++) {
            Room::create([
                'room_code' => 'BR' . $i,
                'room_name' => 'Breakout Room ' . $i,
                'floor' => $i <= 4 ? 'G' : '1',
                'capacity' => 4,
                'type' => 'breakout_room',
                'status' => 'active',
                'is_breakout' => true,
            ]);
        }
    }
}
