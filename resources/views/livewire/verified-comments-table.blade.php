<div class="rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/10">
    <div class="mb-4 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Verified Comments
        </h3>
        @php
            $commentCount = $startup->rfcs()->whereNotNull('response')->count();
        @endphp
        <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-900 dark:bg-green-900 dark:text-green-100">
            {{ $commentCount }} {{ Illuminate\Support\Str::plural('comment', $commentCount) }}
        </span>
    </div>

    @if (!$showComments)
        <div class="flex justify-center">
            {{ $this->viewCommentsAction }}
        </div>
    @else
        {{ $this->table }}
    @endif

    <x-filament-actions::modals />
</div>
