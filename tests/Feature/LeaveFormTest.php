<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\TimeTracker;
use App\Models\Leave;
use Livewire\Livewire;

use PHPUnit\Framework\Attributes\Test;

class LeaveFormTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_user_can_apply_leave_successfully()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('leave-form')
            ->set('start_date', now()->toDateString())
            ->set('end_date', now()->addDays(2)->toDateString())
            ->call('submit');

        $this->assertDatabaseHas('leaves', [
            'user_id' => $user->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
        ]);
    }

    public function test_user_cannot_apply_leave_if_time_log_exists()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        TimeTracker::create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'task_description' => 'Existing Task',
            'hours' => 2,
            'minutes' => 30,
        ]);

        Livewire::test('leave-form')
            ->set('start_date', now()->toDateString())
            ->set('end_date', now()->addDays(1)->toDateString())
            ->call('submit')
            ->assertHasErrors(['start_date']);

        $this->assertDatabaseMissing('leaves', [
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_apply_leave_with_invalid_dates()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test('leave-form')
            ->set('start_date', now()->addDays(3)->toDateString())
            ->set('end_date', now()->toDateString()) // Invalid range
            ->call('submit')
            ->assertHasErrors(['start_date']);

        $this->assertCount(0, Leave::all());
    }
}
