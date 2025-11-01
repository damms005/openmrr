<div class="rounded-lg bg-white p-4 dark:bg-gray-800">
    <div class="mb-2 flex items-start justify-between">
        <div>
            <p class="font-medium text-gray-900 dark:text-white">{{ $getRecord()->customer_name }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $getRecord()->created_at->format('M d, Y') }}</p>
        </div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" class="h-5 w-5 text-green-600 dark:text-green-400">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
    </div>
    <p class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ $getRecord()->response }}</p>
    <div class="mt-3">
        {{ ($this->copyCommentLinkAction)(['rfc' => $getRecord()->id]) }}
    </div>
</div>
