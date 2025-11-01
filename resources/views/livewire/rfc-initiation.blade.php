<div class="min-h-screen bg-linear-to-b from-white to-gray-50 dark:from-gray-950 dark:to-gray-900 p-4 md:p-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                Request for Comments
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Gather feedback from your customers for {{ $startup->name }}
            </p>
        </div>

        @if ($errorMessage)
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <p class="text-red-800 dark:text-red-200">{{ $errorMessage }}</p>
            </div>
        @endif

        @if ($loadingCustomers)
            <div class="flex justify-center items-center h-64">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        @else
            {{ $this->table }}
        @endif
    </div>
</div>
