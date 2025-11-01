@props(['side' => 'left', 'advertisers' => []])

@php
  $isRightSide = $side === 'right';
  $maxAdsPerSide = 4;
  $maxTotalAds = 15;

  if ($isRightSide) {
      $displayedAds = collect($advertisers)->slice($maxAdsPerSide, $maxAdsPerSide);
  } else {
      $displayedAds = collect($advertisers)->take($maxAdsPerSide);
  }

  $totalUsedSlots = collect($advertisers)->count();
  $remainingSlots = max(0, $maxTotalAds - $totalUsedSlots);
@endphp

<aside class="hidden lg:col-span-2 lg:block">
  <div class="sticky top-6 space-y-7">
    @foreach ($displayedAds as $advertiser)
      <a href="{{ $advertiser->link_url }}" target="_blank" rel="noopener noreferrer" class="group block">
        <div
          class="overflow-hidden rounded-lg border bg-white p-4 shadow-sm transition-all duration-200 hover:border-gray-300 hover:shadow-md dark:border-gray-700 dark:bg-gray-800 dark:hover:border-gray-600">
          <div class="flex flex-col items-center gap-3 text-center">
            @if ($advertiser->image_url)
              <img src="{{ $advertiser->image_url }}" alt="{{ $advertiser->title }}" class="h-12 w-12 rounded-full border border-gray-200 object-cover dark:border-gray-700">
            @endif
            <div class="flex-1">
              <div class="text-sm font-semibold text-gray-900 transition-colors group-hover:text-blue-600 dark:text-white dark:group-hover:text-blue-400">{{ $advertiser->title }}</div>
              @if ($advertiser->description)
                <div class="mt-2 line-clamp-2 text-xs text-gray-600 dark:text-gray-400">{{ $advertiser->description }}</div>
              @endif
            </div>
          </div>
        </div>
      </a>
    @endforeach

    @if ($isRightSide || $displayedAds->count() < $maxAdsPerSide)
      @php
        $isRightSideFull = $isRightSide && $displayedAds->count() >= $maxAdsPerSide;
      @endphp

      <a href="{{ config('app.advertiser_checkout_url', '#') }}" target="_blank" rel="noopener noreferrer" class="group block">
        <div
          class="{{ $isRightSideFull ? 'min-h-16 p-2' : 'min-h-32 p-4' }} flex items-center justify-center rounded-lg border-2 border-dashed bg-gray-50 text-center text-sm text-gray-600 transition-all duration-200 hover:border-gray-400 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:bg-gray-700">
          <div class="{{ $isRightSideFull ? 'gap-y-1' : 'gap-y-2' }} flex flex-col items-center">
            <div class="{{ $isRightSideFull ? 'text-xs' : '' }} font-semibold text-gray-700 transition-colors group-hover:text-blue-600 dark:text-gray-300 dark:group-hover:text-blue-400">
              Advertise here
            </div>
            @if (!$isRightSideFull)
              <div class="text-xs text-gray-500 dark:text-gray-500">
                @if ($totalUsedSlots > 0)
                  {{ $remainingSlots }} of {{ $maxTotalAds }} slots left
                @else
                  {{ $maxTotalAds }} slots available
                @endif
              </div>
            @endif
          </div>
        </div>
      </a>
    @endif
  </div>
</aside>
