<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Leave;
use App\Models\TimeTracker as Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
use App\Traits\HasTimeCalculations;

class TimeTracker extends Component
{
    use HasTimeCalculations;
    public $date, $task_description, $hours, $minutes;
    public $taskId = null;

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
            'hours' => ['required', 'integer', 'min:0', 'max:10'],
            'minutes' => ['required', 'integer', 'min:0', 'max:59']
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

    public function onLeave($date)
    {
        return Leave::where('user_id', auth()->id())
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    public function getTaskList()
    {
        return Task::where('user_id', auth()->id())
            ->orderByDesc('work_date')
            ->get()
            ->groupBy('work_date');
    }

    public function render()
    {
         $taskList = Task::where('user_id', auth()->id())
            ->orderByDesc('work_date')
            ->get()
            ->groupBy('work_date');

        return view('livewire.time-tracker',[
            'taskList' => $this->getTaskList(),
        ]);
    }
}
