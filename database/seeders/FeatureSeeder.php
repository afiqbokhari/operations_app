<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            'Projector',
            'Recording',
            'VC',
            'Smart Screen',
            'PA System',
            'Stage Lighting',
            'Podium/Microphone',
            'Portable VC',
        ];

        foreach ($features as $feature) {
            Feature::create(['name' => $feature, 'is_active' => true]);
        }
    }
}
