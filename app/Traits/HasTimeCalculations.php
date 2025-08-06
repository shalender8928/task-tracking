<?php

namespace App\Traits;

use App\Models\{Leave, TimeTracker};

trait HasTimeCalculations
{
    public function totalMinutesForDate($userId, $date, $excludeTaskId = null)
    {
        // dd($userId, $date, $excludeTaskId);
        return TimeTracker::forUser($userId)
            ->forDate($date)
            ->when($excludeTaskId, fn ($q) => $q->where('id', '!=', $excludeTaskId))
            ->get()
            ->sum(fn ($t) => $t->hours * 60 + $t->minutes);
    }

    public function onLeave($date)
    {
        return Leave::where('user_id', auth()->id())
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }
}
