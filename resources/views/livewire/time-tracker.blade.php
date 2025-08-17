<div>
    <div  class="grid grid-cols-12 gap-3">
        <div class="col-span-6 p-4 rounded-xl bg-white bg-clip-border text-slate-700 shadow-md dark:bg-gray-800">
            {{-- " mx-auto flex w-full flex-col rounded-xl bg-white bg-clip-border text-slate-700 shadow-md --}}
            <form wire:submit.prevent="addOrUpdateTask">
                <div class="flex flex-col p-6">
                    <h4
                    class="text-2xl mb-1 font-semibold text-slate-700 dark:text-white">
                    Time Log Form
                    </h4>
                    
                    <div class="w-full mt-4">
                        <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                            Date:
                        </label>
                        <input
                            type="date"
                            class="w-full h-10 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
                            placeholder="Enter Date"
                            wire:model="date" max="{{ now()->toDateString() }}" required
                            />
                        @error('date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="w-full mt-4">
                        <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                            Task Description:
                        </label>
                        <textarea id="message" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter Task Description here..." wire:model="task_description" maxlength="255" required></textarea>
                        @error('task_description') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex justify-between mt-4 gap-4">
                        <div class="w-full">
                            <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                                Hours
                            </label>
                            <input
                                type="number"
                                class="w-full h-10 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Enter Hours" wire:model="hours" min="0" max="10" pattern="[0-9]*" required onkeydown="if(event.key === 'e' || event.key === 'E' || event.key === '+' || event.key === '-') event.preventDefault();" />
                            @error('hours') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="w-full">
                            <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                                Minutes
                            </label>
                            <input
                                type="number"
                                class="w-full h-10 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Enter Minutes" wire:model="minutes" min="0" max="59" required onkeydown="if(event.key === 'e' || event.key === 'E' || event.key === '+' || event.key === '-') event.preventDefault();" />
                            @error('minutes') <span class="text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                </div>

                <div class="p-6 pt-0">
                    <div class="flex gap-4">
            
                        <button
                            class="w-full mx-auto select-none rounded bg-slate-800 py-2 px-4 text-center text-sm font-semibold text-white shadow-md shadow-slate-900/10 transition-all hover:shadow-lg hover:shadow-slate-900/20 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none dark:text-black dark:bg-slate-400"
                            type="submit">
                            {{ $taskId ? 'Update Task' : 'Add Task' }}
                        </button>

                        @if ($taskId)
                            <button
                            class="w-full mx-auto select-none rounded border border-gray-500 py-2 px-4 text-center text-sm font-semibold text-gray-500 transition-all hover:bg-gray-500 hover:text-white hover:shadow-md hover:shadow-gray-500/20 active:bg-gray-700 active:text-white active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                            type="button" wire:click="resetForm">
                            Cancel
                        </button>
                        @endif

                    </div>
                    @if (session()->has('message'))
                        <div class="text-green-600">{{ session('message') }}</div>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-span-6 rounded-xl bg-white bg-clip-border text-slate-700 shadow-md p-4 dark:bg-gray-800">
            <div class="w-full text-gray-700 bg-white dark:bg-gray-800">
                <div class="flex justify-between items-center py-2 relative dark:text-white">
                    <h4>All Tasks</h4>
                    <select wire:model.live="perPage"  wire:loading.attr="disabled" class="border p-1 rounded dark:text-white dark:bg-gray-700 disabled:bg-gray-500">
                        <option value="2">2 per page</option>
                        <option value="5">5 per page</option>
                        <option value="10">10 per page</option>
                    </select>
                    <span class="absolute right-0 bottom-0 w-full" wire:loading>
                        <div class="flex justify-center items-center p-3">
                            Updating...
                        </div>
                    </span>
                </div>
                @forelse ($taskList as $date => $tasks)
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                            <tr>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                        Description
                                    </p>
                                </th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                        Time
                                    </p>
                                </th>
                                <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                    <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                        Actions
                                    </p>
                                </th>
                            </tr>
                        </thead>
                        <div class="flex justify-between align-center p-2 bg-gray-300 dark:bg-gray-500">
                            <h5 class="font-bold underline dark:text-white">#{{ \Carbon\Carbon::parse($date)->format('d M Y') }} </h5>
                            <span class="text-sm italic">
                                <small>
                                    <span class="mb-2 text-sm font-semibold @if($tasks['totalHours'] < 10) text-green-600 dark:text-green-400 @else text-red-600 dark:text-red-800 @endif">
                                        Total Time: {{ $tasks['totalHours'] }}h {{ $tasks['remainingMinutes'] }}m
                                    </span>
                                </small>
                            </span>
                        </div>
                        
                        <tbody>
                            @foreach ($tasks['tasks'] as $task)
                                <tr>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 dark:text-white">
                                            {{  Str::limit($task->task_description,10,preserveWords: true) }}
                                        </p>
                                    </td>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 dark:text-white">
                                            {{ $task->hours }}h {{ $task->minutes }}m
                                        </p>
                                    </td>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <div class="flex content-between gap-3">
                                            <a href="javascript:void(0)" class="block font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900" wire:click="edit({{ $task->id }})">
                                                <button type="button" class="w-full mx-auto select-none rounded bg-slate-800 py-2 px-4 text-center text-sm font-semibold text-white shadow-md shadow-slate-900/10 transition-all hover:shadow-lg hover:shadow-slate-900/20 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none dark:text-black dark:bg-slate-400">
                                                    Edit
                                                </button>
                                            </a>

                                            <button class="select-none rounded border border-red-500 py-1 px-1 text-center text-sm font-semibold text-red-500 transition-all hover:bg-red-500 hover:text-white hover:shadow-md hover:shadow-red-500/20 active:bg-red-700 active:text-white active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" onclick="if (confirm('Are you sure?')) { Livewire.dispatch('deleteTask', [{{ $task->id }}]) }">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @empty
                    <p>No tasks found.</p>
                @endforelse
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $taskList->links() }}
                </div>
            </div>
        </div> 
    </div>
</div>