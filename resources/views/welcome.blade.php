<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>OpenMRR - Coming Soon</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  @vite('resources/css/app.css')
</head>

<body class="bg-white font-sans antialiased">
  <div class="flex min-h-screen flex-col">
    <header class="px-6 py-8 lg:px-12 lg:py-12">
      <div class="flex items-center">
        <div class="flex h-8 w-9 items-center justify-center">
          <x-logo />
        </div>
        <span class="ml-3 text-lg font-semibold text-neutral-900">OpenMRR</span>
      </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-6 lg:px-12">
      <div class="mx-auto max-w-4xl text-center">
        <div class="mb-16">
          <div class="mb-8 flex flex-col items-center space-y-4">
            <div class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-600">
              <div class="mr-2 h-1.5 w-1.5 rounded-full bg-neutral-400"></div>
              Coming Soon
            </div>

            <div class="inline-flex items-center overflow-hidden rounded text-xs font-medium">
              <div class="flex items-center bg-neutral-600 px-2.5 py-1 text-white">
                <svg class="mr-1.5 h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                Laravel Cloud
              </div>
              <div class="bg-green-600 px-2.5 py-1 text-white">
                Verified
              </div>
            </div>
          </div>

          <h1 class="mb-6 text-4xl font-light tracking-tight text-neutral-900 lg:text-6xl xl:text-7xl">
            Openly verifiable<br>startup revenue
          </h1>

          <p class="mx-auto mb-16 max-w-2xl text-lg leading-relaxed text-neutral-500 lg:text-xl">
            A transparent database bringing accountability to startup financials through open-source technology and verifiable data standards.
          </p>
        </div>

        <div class="mb-20 grid grid-cols-1 gap-8 md:grid-cols-3">
          <div class="text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-50">
              <svg class="h-6 w-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
              </svg>
            </div>
            <h3 class="mb-2 font-medium text-neutral-900">Verifiable</h3>
            <p class="text-sm text-neutral-500">Cryptographically secured data integrity</p>
          </div>

          <div class="text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-50">
              <svg class="h-6 w-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5" />
              </svg>
            </div>
            <h3 class="mb-2 font-medium text-neutral-900">Open Source</h3>
            <p class="text-sm text-neutral-500">Transparent algorithms and methodology</p>
          </div>

          <div class="text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-neutral-50">
              <svg class="h-6 w-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
              </svg>
            </div>
            <h3 class="mb-2 font-medium text-neutral-900">Analytics</h3>
            <p class="text-sm text-neutral-500">Professional-grade insights and reporting</p>
          </div>
        </div>
      </div>
    </main>

    <footer class="px-6 py-8 lg:px-12 lg:py-12">
      <div class="flex items-center justify-center">
        <div class="flex items-center space-x-6">
          <a href="{{ route('home') }}" class="text-sm text-neutral-500 transition-colors hover:text-neutral-700">Demo</a>
          <a href="https://github.com/damms005/openmrr" class="text-sm text-neutral-500 transition-colors hover:text-neutral-700">GitHub</a>
        </div>
      </div>
    </footer>
  </div>
</body>

</html>
