<div>
    <div  class="grid grid-cols-12 gap-3">
        <div class="col-span-6 p-4 rounded-xl bg-white bg-clip-border text-slate-700 shadow-md dark:bg-gray-800">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="submit">

                <div class="flex flex-col p-6">
                    <h4
                    class="text-2xl mb-1 font-semibold text-slate-700 dark:text-white">
                    Leave Application Form
                    </h4>

                    <div class="w-full mt-4">
                        <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                            Start Date:
                        </label>
                        <input class="w-full h-10 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter Start Date" type="date" wire:model="start_date" required>
                        @error('start_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="w-full mt-4">
                        <label class="block mb-1 text-sm text-slate-700 dark:text-white">
                            End Date:
                        </label>
                        <input class="w-full h-10 bg-transparent placeholder:text-slate-400 text-slate-700 text-sm border border-slate-200 rounded px-3 py-2 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Enter End Date" type="date" wire:model="end_date" required>
                        @error('end_date') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="p-6 pt-0">
                    <div class="flex gap-4">
                        <button
                            class="w-full mx-auto select-none rounded bg-slate-800 py-2 px-4 text-center text-sm font-semibold text-white shadow-md shadow-slate-900/10 transition-all hover:shadow-lg hover:shadow-slate-900/20 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none dark:text-black dark:bg-slate-400"
                            type="submit">
                            {{ $leaveId ? 'Update Leave' : 'Apply Leave' }}
                        </button>

                        @if ($leaveId)
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
            <div class="flex justify-between items-center py-2 relative dark:text-white">
                <h4>Leave History</h4>
                <select wire:model.live="perPage"  wire:loading.attr="disabled" class="border p-1 rounded dark:text-white dark:bg-gray-700">
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
            <div class="w-full text-gray-700 bg-white dark:bg-gray-800">
                @if ($leaves)
                    <table class="w-full text-left table-auto min-w-max">
                        <thead>
                        <tr>
                            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                    Start Date
                                </p>
                            </th>
                            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                    End Date
                                </p>
                            </th>
                            <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                <p class="block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70 dark:text-white">
                                    Actions
                                </p>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($leaves as $leave)
                                <tr>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 dark:text-white">
                                            {{ $leave->start_date->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 dark:text-white">
                                            {{ $leave->end_date->format('d M Y') }}
                                        </p>
                                    </td>
                                    <td class="p-4 border-b border-blue-gray-50">
                                        <div class="flex content-between gap-3">

                                            <a href="javascript:void(0)" class="block font-sans text-sm antialiased font-medium leading-normal text-blue-gray-900" wire:click="edit({{ $leave->id }})">
                                                <button type="button" class="w-full mx-auto select-none rounded bg-slate-800 py-2 px-4 text-center text-sm font-semibold text-white shadow-md shadow-slate-900/10 transition-all hover:shadow-lg hover:shadow-slate-900/20 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none dark:text-black dark:bg-slate-400">
                                                    Edit
                                                </button>
                                            </a>
                                            
                                            <button class="select-none rounded border border-red-500 py-1 px-1 text-center text-sm font-semibold text-red-500 transition-all hover:bg-red-500 hover:text-white hover:shadow-md hover:shadow-red-500/20 active:bg-red-700 active:text-white active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" onclick="if (confirm('Are you sure?')) { Livewire.dispatch('deleteLeave', [{{ $leave->id }}]) }">Delete</button>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No leaves found.</p>
                @endif
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $leaves->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

