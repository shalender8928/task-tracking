<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TimeTracker as Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
use App\Traits\{HasTimeCalculations, SanitizesNumericInputs};
use Livewire\WithPagination;

class TimeTracker extends Component
{
    use HasTimeCalculations, SanitizesNumericInputs, WithPagination;
    public $date, $task_description, $hours, $minutes;
    public $taskId = null;
    public $numericFields = ['hours', 'minutes']; //will senitize these arguments using the SanitizesNumericInputs Traits.
    public int $perPage = 2;

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->date = now()->toDateString();
        $this->resetForm();
    }

    public function addOrUpdateTask()
    {
        $this->validate([
            'date' => 'required|date|before_or_equal:today',
            'task_description' => 'required|max:255',
            'hours' => ['required', 'numeric', 'min:0', 'max:10'],
            'minutes' => ['required', 'numeric', 'min:0', 'max:59']
        ]);

        if (empty($this->hours) && empty($this->minutes)) {
            $this->addError('minutes', 'Task duration cannot be zero.');
            return;
        }

        $totalMinutes = $this->totalMinutesForDate(auth()->id(), $this->date, $this->taskId);

        $newTotal = $totalMinutes + ($this->hours * 60 + $this->minutes);

        if ($newTotal > 600) {
            $this->addError('hours', 'Total time exceed 10 hours');
            return;
        }

        if ($this->onLeave($this->date)) {
            $this->addError('date', 'Cannot log work on a leave day.');
            return;
        }

        Task::updateOrCreate(
            ['id' => $this->taskId],
            [
                'user_id' => auth()->id(),
                'work_date' => $this->date,
                'task_description' => $this->task_description,
                'hours' => $this->hours,
                'minutes' => $this->minutes,
            ]
        );

        session()->flash('message', 'Time Log submitted successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);

        $this->taskId = $task->id;
        $this->task_description = $task->task_description;
        $this->hours = $task->hours;
        $this->minutes = $task->minutes;
        $this->date = $task->work_date->format('Y-m-d');
    }

    #[On('deleteTask')]
    public function delete($id)
    {
        $task = Task::where('user_id', auth()->id())->findOrFail($id);
        $task->delete();

        session()->flash('message', 'Task deleted successfully!');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->taskId = null;
        $this->task_description = '';
        $this->hours = '';
        $this->minutes = '';
        $this->date = now()->toDateString();
    }

    public function getPaginatedWorkDates()
    {
        // Get paginated DISTINCT dates (grouped)
        return Task::where('user_id', auth()->id())
            ->select('work_date')
            ->groupBy('work_date')
            ->orderByDesc('work_date')
            ->paginate($this->perPage);
    }

    public function getTaskList()
    {
        try {
            $paginatedDates = $this->getPaginatedWorkDates();

            // Get all tasks for these dates
            $tasksByDate = Task::where('user_id', auth()->id())
                ->whereIn('work_date', $paginatedDates->pluck('work_date'))
                ->orderByDesc('work_date')
                ->get()
                ->groupBy('work_date');

            // Calculate totals per date
            $groupedTasks = $tasksByDate->map(function ($tasks, $date) {
                $totalHours = $tasks->sum('hours');
                $totalMinutes = $tasks->sum('minutes');

                // Convert extra minutes to hours
                $totalHours += intdiv($totalMinutes, 60);
                $remainingMinutes = $totalMinutes % 60;

                return [
                    'tasks' => $tasks,
                    'totalHours' => $totalHours,
                    'remainingMinutes' => $remainingMinutes,
                    'date' => $date,
                ];
            });

            // Reattach pagination to the grouped results
            $paginatedDates->setCollection($groupedTasks);

            return $paginatedDates;
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to load tasks');
            return collect();
        }
    }

    public function render()
    {
        return view('livewire.time-tracker',[
            'taskList' => $this->getTaskList()
        ]);
    }
}
