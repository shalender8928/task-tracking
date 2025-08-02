<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\TimeTracker;
use Livewire\Livewire;

class TimeTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_time_log()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('time-tracker')
            ->set('date', now()->toDateString())
            ->set('task_description', 'Sample Task')
            ->set('hours', 2)
            ->set('minutes', 30)
            ->call('addOrUpdateTask');

        $this->assertDatabaseHas('time_trackers', [
            'user_id' => $user->id,
            'task_description' => 'Sample Task',
        ]);
    }

    public function test_user_cannot_log_more_than_10_hours_per_day()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        TimeTracker::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'hours' => 9,
            'minutes' => 30,
        ]);

        Livewire::test('time-tracker')
            ->set('date', now()->toDateString())
            ->set('task_description', 'Exceeding Task')
            ->set('hours', 1)
            ->set('minutes', 0)
            ->call('addOrUpdateTask')
            ->assertHasErrors(['hours']);
    }
}
