<div class="mx-auto max-w-2xl px-4 py-8">
  <div class="rounded-lg border border-gray-200 bg-white p-8 dark:border-gray-700 dark:bg-gray-800">
    <div class="mb-6">
      <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Comment for {{ $rfc->startup->name }}</h2>
    </div>

    <div class="space-y-4">
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
        <p class="text-gray-900 dark:text-white">{{ $rfc->customer_name }}</p>
        <p class="text-gray-600 dark:text-gray-400">{{ $rfc->customer_email }}</p>
      </div>

      <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50">
        <p class="whitespace-pre-wrap text-gray-900 dark:text-gray-200">{{ $rfc->response }}</p>
      </div>

      <p class="text-gray-600 dark:text-gray-400">{{ $rfc->created_at->format('F d, Y') }}</p>

      <div class="mt-6 flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" class="h-5 w-5">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Verified comment</span>
      </div>
    </div>
  </div>
</div>
