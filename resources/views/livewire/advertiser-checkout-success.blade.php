<div class="min-h-screen bg-white dark:bg-slate-950">
    <div class="mx-auto max-w-2xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-slate-200 bg-slate-50 p-8 dark:border-slate-800 dark:bg-slate-900">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                    Advertise Your Link
                </h1>
                <p class="mt-2 text-slate-600 dark:text-slate-400">
                    Payment successful! Now complete your advertiser details below.
                </p>
            </div>

            <form wire:submit.prevent="createAdvertiser" class="space-y-6">
                {{ $this->form }}

                <div class="flex gap-3 pt-4">
                    <a
                        href="{{ route('home') }}"
                        class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-center font-medium text-slate-900 hover:bg-slate-100 dark:border-slate-700 dark:text-white dark:hover:bg-slate-800"
                    >
                        Skip
                    </a>
                    <button
                        type="submit"
                        class="flex-1 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600"
                    >
                        Create Advertiser Slot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
