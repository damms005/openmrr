<div class="space-y-8">
  <div>
    <div class="mb-1 flex items-center gap-3">
      <h2 class="text-3xl font-bold dark:text-white">{{ $founder->disaplayedHandle }}</h2>
      @if ($founder->x_handle)
        <a href="https://x.com/{{ $founder->x_handle }}" target="_blank" rel="noopener noreferrer"
          class="inline-flex items-center gap-1 rounded-full bg-black px-3 py-1 text-sm font-medium text-white transition-opacity hover:opacity-80 dark:bg-white dark:text-black">
          View on <span class="ml-0.5 text-xl">𝕏</span>
        </a>
      @endif
    </div>
    <p class="text-gray-600 dark:text-gray-400">{{ count($startups) }} verified {{ str('startup')->plural(count($startups)) }}</p>
  </div>

  @livewire(\App\Filament\Widgets\FounderRevenueStats::class, ['founder' => $founder])

  @if (count($startups) > 0)
    <div>
      <h3 class="mb-4 text-xl font-bold dark:text-white">Owned startups</h3>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ($startups as $startup)
          @livewire('startup-card', ['startup' => $startup], key('startup-' . $startup->id))
        @endforeach
      </div>
    </div>
  @else
    <div class="rounded-lg border bg-white p-8 text-center dark:border-gray-700 dark:bg-gray-800">
      <p class="text-gray-600 dark:text-gray-400">No startups yet.</p>
    </div>
  @endif
</div>
