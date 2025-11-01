<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ isset($title) ? $title . ' | ' : '' }}OpenMRR</title>

  <style>
    [x-cloak] {
      display: none !important;
    }
  </style>

  @filamentStyles
  @vite('resources/css/app.css')

</head>

<body class="bg-gray-50 antialiased dark:bg-gray-900">
  <div class="flex min-h-screen flex-col">
    <header class="border-b bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
      <div class="relative mx-auto max-w-7xl px-4 py-6 text-center">
        <div class="absolute right-0 top-6">
          <x-github-star />
        </div>
        <h1 class="text-4xl font-bold dark:text-white">
          <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
            <x-logo size="h-10 w-10" />
            OpenMRR
          </a>
        </h1>
        <p class="mt-1 text-2xl text-gray-600 dark:text-gray-400">An openly verifiable database of startup revenue</p>
      </div>
    </header>

    <main class="flex-1">
      @php
        $sidebarAdvertisers = \App\Models\Advertiser::active()->get();
      @endphp

      <div class="mx-auto max-w-7xl px-4 py-8">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
          <x-advert-placeholder side="left" :advertisers="$sidebarAdvertisers" />

          <div class="lg:col-span-8">

            {{ $slot }}

            @livewire('notifications')

            @filamentScripts
            @vite('resources/js/app.js')
          </div>

          <x-advert-placeholder side="right" :advertisers="$sidebarAdvertisers" />
        </div>
      </div>
    </main>

    <footer class="mt-12 bg-white py-6">
      <div class="mx-auto my-6 max-w-2xl">
        <div class="relative mx-auto max-w-7xl px-4 py-6 text-center">
          <h1 class="text-4xl font-bold dark:text-white"> <a href="{{ route('home') }}">OpenMRR</a></h1>
          <p class="mt-1 text-2xl text-gray-600 dark:text-gray-400">An openly verifiable database of startup revenue</p>
        </div>
      </div>
      <div class="mx-auto my-6 max-w-2xl">
        @livewire('search-and-create')
      </div>

      <div class="mx-auto max-w-7xl px-4 text-center text-sm text-gray-600 dark:text-gray-400">
        <a href="https://github.com/damms005/openmrr" target="_blank"
          class="mt-1 inline-flex items-center text-gray-600 transition-colors hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
          Star on GitHub <svg class="ml-2 h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
              d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z"
              clip-rule="evenodd" />
          </svg>
        </a>
      </div>
    </footer>
  </div>
</body>

</html>
