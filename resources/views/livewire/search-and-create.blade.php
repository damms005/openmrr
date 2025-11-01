<div>
  <div class="flex gap-4">
    <div class="flex-1">
      {{ $this->form }}
    </div>

    <div>
      {{ $this->createStartupAction }}
    </div>
  </div>

  <x-filament-actions::modals />
</div>
