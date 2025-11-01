<div class="mx-auto max-w-2xl px-4 py-8">
  @if ($isSubmitted)
    <div class="rounded-lg border border-green-200 bg-green-50 p-6 text-center dark:border-green-800 dark:bg-green-900/20">
      <h2 class="mb-2 text-lg font-semibold text-green-700 dark:text-green-400">Thank you!</h2>
      <p class="text-green-600 dark:text-green-300">Your response has been submitted successfully.</p>
    </div>
  @else
    <div class="rounded-lg border border-gray-200 bg-white p-8 dark:border-gray-700 dark:bg-gray-800">
      <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">{{ $rfc->startup->name }}</h2>
      <p class="mb-6 text-gray-600 dark:text-gray-400">Please comment on your experience with {{ $rfc->startup->name }}.</p>

      <form wire:submit="submitResponse" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
          Submit
        </x-filament::button>
      </form>
    </div>
  @endif
</div>
