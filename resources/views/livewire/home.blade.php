<div class="space-y-8">
  <div class="my-6">
    @livewire('search-and-create')
  </div>

  @livewire('revenue-table')

  <div class="mt-12 space-y-8 border-t border-gray-200 pt-8 dark:border-gray-700">
    <section>
      <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Business Categories</h2>
      <div class="flex flex-wrap gap-3">
        @forelse ($topCategories as $category)
          <a href="{{ route('category.show', $category->slug) }}"
            class="inline-flex items-center rounded-full bg-blue-100 px-4 py-2 text-sm font-medium text-blue-800 transition hover:bg-blue-200 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800">
            {{ $category->label }}
          </a>
        @empty
          <p class="text-gray-600 dark:text-gray-400">No categories available.</p>
        @endforelse
      </div>
    </section>

  </div>
</div>
