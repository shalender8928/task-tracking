<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\TimeTracker;

class MaxTaskHours implements ValidationRule
{
    protected $date;
    protected $taskId;
    protected $hours;
    protected $minutes;

    public function __construct($date, $taskId = null, $hours = 0, $minutes = 0)
    {
        $this->date = $date;
        $this->taskId = $taskId;
        $this->hours = $hours;
        $this->minutes = $minutes;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $totalMinutes = TimeTracker::where('user_id', auth()->id())
            ->where('work_date', $this->date)
            ->when($this->taskId, fn ($q) => $q->where('id', '!=', $this->taskId))
            ->get()
            ->sum(fn ($t) => $t->hours * 60 + $t->minutes);

        $newTotal = $totalMinutes + ($this->hours * 60 + $this->minutes);

        if ($newTotal > 600) { // More than 10 hours
            $fail('Total time cannot exceed 10 hours in a day.');
        }
    }
}
