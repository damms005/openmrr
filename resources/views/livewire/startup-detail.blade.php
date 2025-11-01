<div class="space-y-8">
  <div class="mb-8">
    <div class="flex items-start justify-between gap-8">
      <div class="flex-1">
        <div class="flex items-center gap-4">
          @if ($startup->avatar_url)
            <img src="{{ $startup->avatar_url }}" alt="{{ $startup->name }}" class="h-16 w-16 shrink-0 rounded-lg object-cover">
          @else
            @php
              $initials = strtoupper(
                  substr(
                      collect(explode(' ', $startup->name))
                          ->map(fn($word) => $word[0] ?? '')
                          ->implode(''),
                      0,
                      2,
                  ),
              );
            @endphp
            <div class="bg-linear-to-br flex h-16 w-16 shrink-0 items-center justify-center rounded-lg from-blue-500 to-purple-600">
              <span class="text-lg font-bold text-white">{{ $initials }}</span>
            </div>
          @endif
          <div>
            <div class="flex items-center gap-3">
              <h2 class="text-3xl font-bold dark:text-white">{{ $startup->name }}</h2>
              @if ($startup->rank)
                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-900 dark:bg-blue-900 dark:text-blue-100">
                  #{{ $startup->rank }}
                </span>
              @endif
            </div>
          </div>
        </div>
        <div class="mt-2 text-gray-600 dark:text-gray-400">
          <div>
            {{ $startup->description }}
          </div>
          @if ($startup->founder?->x_handle)
            <a href="{{ route('founder.show', $startup->founder->x_handle) }}" class="font-mono text-blue-600 underline hover:underline dark:text-blue-400">
              {{ $startup->founder->disaplayedHandle }}
            </a>
          @endif
        </div>
      </div>
      <div class="shrink-0">
        {{ $this->startupInfolist }}
      </div>
    </div>
  </div>

  @livewire(\App\Filament\Widgets\StartupRevenueStats::class, ['startup' => $startup])

  @livewire(\App\Filament\Widgets\StartupRevenueChart::class, ['startup' => $startup])

  @livewire(\App\Livewire\VerifiedCommentsTable::class, ['startup' => $startup])

  @livewire('startup-suggestions', ['currentStartup' => $startup])

  <div class="rounded-lg border bg-gray-50 p-4 text-center text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
    <p>All revenue data is verified with API keys.</p>
    @if ($startup->last_synced_at)
      <p class="mt-1">Last synced: {{ $startup->last_synced_at->format('m/d/Y, g:i:s A') }}</p>
    @endif
  </div>
</div>
