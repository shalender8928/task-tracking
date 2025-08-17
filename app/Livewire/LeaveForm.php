<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Leave;
use App\Models\TimeTracker;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 
use Livewire\WithPagination;

class LeaveForm extends Component
{
    use WithPagination;
    public $start_date, $end_date;
    public $leaveList = [];
    public $leaveId = null;
    public int $perPage = 2;

    public function mount()
    {
        $this->resetForm();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function submit()
    {
        $this->validate([
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        // Check if any time logs exist within the date range
        $hasLogs = TimeTracker::where('user_id', auth()->id())
            ->whereBetween('work_date', [$this->start_date, $this->end_date])
            ->exists();

        if ($hasLogs) {
            $this->addError('start_date', 'You cannot apply leave for dates where time logs already exist.');
            return;
        }

        Leave::updateOrCreate(
            ['id' => $this->leaveId],
            [
                'user_id'    => auth()->id(),
                'start_date' => $this->start_date,
                'end_date'   => $this->end_date,
            ]
        );

        session()->flash('message', $this->leaveId ? 'Leave updated successfully!' : 'Leave applied successfully!');
        $this->resetForm();
    }

    public function edit($id)
    {
        $leave = Leave::where('user_id', auth()->id())->findOrFail($id);

        $this->leaveId = $leave->id;
        $this->start_date = $leave->start_date->format('Y-m-d');
        $this->end_date = $leave->end_date->format('Y-m-d');
    }

    #[On('deleteLeave')]
    public function delete($id)
    {
        // logger("Trying to delete leave ID: " . $id); 
        $leave = Leave::where('user_id', auth()->id())->findOrFail($id);
        $leave->delete();

        session()->flash('message', 'Leave deleted successfully!');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->leaveId = null;
        $this->start_date = null;
        $this->end_date = null;
    }

    public function getleaveList()
    {
        try {
            return Leave::where('user_id', auth()->id())
            ->orderBy('start_date')
            ->paginate($this->perPage);

        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Failed to load Leaves');
            return collect();
        }
    }

    public function render()
    {
        return view('livewire.leave-form', [
            'leaves' => $this->getleaveList()
        ]);
    }
}
