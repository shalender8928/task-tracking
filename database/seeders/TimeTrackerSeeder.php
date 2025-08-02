<?php

namespace Database\Seeders;

use App\Models\TimeTracker;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TimeTrackerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        TimeTracker::factory()->count(5)->create([
            'user_id' => $user->id,
        ]);
    }
}
