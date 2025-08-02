<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-center items-center size-full bg-blue-300 text-2xl hover:bg-blue-500 hover:text-white hover:shadow-md hover:shadow-blue-500/20">
                    <a href="{{ route('timetrackers.index') }}"> Task Time Logging</a>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <div class="flex justify-center items-center size-full bg-indigo-300 text-2xl hover:bg-indigo-500 hover:text-white hover:shadow-md hover:shadow-indigo-500/20" >
                    <a href="{{ route('leave.form') }}"> Leave Request</a>
                </div>
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>
        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>
    </div>
</x-layouts.app>
