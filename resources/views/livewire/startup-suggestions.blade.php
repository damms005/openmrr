<div class="space-y-4">
  <h3 class="text-lg font-semibold text-gray-900 dark:text-white">More startups</h3>
  
  <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    @foreach ($suggestions as $startup)
      @livewire('startup-card', ['startup' => $startup], key('suggestion-' . $startup->id))
    @endforeach
  </div>
</div>
