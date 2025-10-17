<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Group;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Candidate::all()->each(function($c){
            $g = Group::create(['name' => $c->first_name, 'is_individual' => 1]);
            $g->candidates()->attach($c);
        });
    }
}
