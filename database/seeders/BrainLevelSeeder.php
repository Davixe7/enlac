<?php

namespace Database\Seeders;

use App\Models\BrainLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrainLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BrainLevel::create([
            'name' => 'Médula y cordón espinal',
            'color' => '#EE8B0080',
            'grade' => 'I',
            'S' => 5,
            'P' => 1,
            'L' => 2,
        ]);

        BrainLevel::create([
            'name' => 'Protuberancia anular',
            'color' => '#DD004A80',
            'grade' => 'II',
            'S' => 1,
            'P' => 2.5,
            'L' => 5,
        ]);

        BrainLevel::create([
            'name' => 'Cerebro medio',
            'color' => '#8DAF1280',
            'grade' => 'III',
            'S' => 3.5,
            'P' => 7,
            'L' => 14,
        ]);

        BrainLevel::create([
            'name' => 'Corteza inicial',
            'color' => '#00659180',
            'grade' => 'IV',
            'S' => 6,
            'P' => 12,
            'L' => 24,
        ]);

        BrainLevel::create([
            'name' => 'Corteza temprana',
            'color' => '#98007780',
            'grade' => 'V',
            'S' => 9,
            'P' => 18,
            'L' => 36,
        ]);

        BrainLevel::create([
            'name' => 'Corteza primitiva',
            'color' => '#D4242980',
            'grade' => 'VI',
            'S' => 18,
            'P' => 36,
            'L' => 72,
        ]);

        BrainLevel::create([
            'name' => 'Corteza sofisticada',
            'color' => '#36087680',
            'grade' => 'VII',
            'S' => 36,
            'P' => 72,
            'L' => 144,
        ]);
    }
}
