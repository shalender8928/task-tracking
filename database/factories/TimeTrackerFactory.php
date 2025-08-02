<?php

namespace Database\Factories;

use App\Models\TimeTracker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeTracker>
 */
class TimeTrackerFactory extends Factory
{

    protected $model = TimeTracker::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        
        return [
            'user_id' => 1,
            'work_date' => now()->subDays(rand(0, 5))->format('Y-m-d'),
            'task_description' => $this->faker->sentence,
            'hours' => rand(1, 3),
            'minutes' => rand(0, 59),
        ];
    }
}
