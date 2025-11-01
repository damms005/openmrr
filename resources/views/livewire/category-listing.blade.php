<div class="space-y-8">
  <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $category->label }}</h1>
    @if ($category->description)
      <p class="mt-2 text-gray-600 dark:text-gray-400">{{ $category->description }}</p>
    @endif
  </div>

  @if ($startups->count() > 0)
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
      @foreach ($startups as $startup)
        <div class="rounded-lg border bg-white p-4 transition hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:shadow-lg">
          <div class="mb-3 flex items-center gap-3">
            @if ($startup->avatar_url)
              <img src="{{ $startup->avatar_url }}" alt="{{ $startup->name }}" class="h-8 w-8 shrink-0 rounded-lg object-cover">
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
      @endforeach
    </div>

    <div class="mt-8">
      {{ $startups->links() }}
    </div>
  @else
    <div class="rounded-lg border border-gray-300 bg-gray-50 p-8 text-center dark:border-gray-700 dark:bg-gray-800">
      <p class="text-gray-600 dark:text-gray-400">No startups found in this category yet.</p>
    </div>
  @endif

  <div class="mt-12 border-t border-gray-200 pt-8 dark:border-gray-700">
    <section>
      <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">All Categories</h2>
      <div class="flex flex-wrap gap-2">
        @forelse ($allCategories as $categoryItem)
          <a href="{{ route('category.show', $categoryItem->slug) }}" wire:navigate
            class="{{ $categoryItem->id === $category->id ? 'ring-2 ring-blue-500' : '' }} inline-flex rounded-full bg-gray-200 px-3 py-1 text-xs font-medium text-gray-800 transition hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            {{ $categoryItem->label }}
          </a>
        @empty
          <p class="text-gray-600 dark:text-gray-400">No categories available.</p>
        @endforelse
      </div>
    </section>
  </div>
</div>
