<div class="rounded-lg border bg-white p-4 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:shadow-lg">
  <div class="mb-3 flex items-center gap-3">
    @if ($startup->avatar_url)
      <img src="{{ $startup->avatar_url }}" alt="logo" class="h-8 w-8 shrink-0 rounded-lg object-cover">
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
      <div class="bg-linear-to-br flex h-8 w-8 shrink-0 items-center justify-center rounded-lg from-blue-500 to-purple-600">
        <span class="text-xs font-bold text-white">{{ $initials }}</span>
      </div>
    @endif

    <a href="{{ route('startup.show', $startup->slug) }}" class="block text-lg font-bold text-blue-600 hover:underline dark:text-blue-400">
      {{ $startup->name }}
    </a>

  </div>

  @if ($startup->description)
    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ Str::limit($startup->description, 100) }}</p>
  @endif

  <div class="mt-4 space-y-1 text-sm">
    <x-verified-comments-badge :commentCount="$commentCount" />
    <p>
      <strong class="dark:text-white">${{ number_format($startup->monthly_recurring_revenue, 0) }}</strong>
      MRR
    </p>
    <p class="text-gray-600 dark:text-gray-400">
      <strong class="dark:text-gray-300">{{ $startup->subscriber_count }}</strong>
      subscribers
    </p>
  </div>
</div>
