@props(['commentCount' => 0])

@php
  $hasComments = $commentCount > 0;
@endphp

<span class="relative inline-flex translate-y-1.5" title="Comments from verified RFCs">
  <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" @class([
      'size-6',
      'text-green-300 dark:text-green-300' => $hasComments,
      'text-gray-200 dark:text-gray-700' => !$hasComments,
  ])>
    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
  </svg>
  <span @class([
      'absolute inset-0 flex items-center justify-center text-[0.5rem] font-bold',
      'text-green-800 dark:text-green-500' => $hasComments,
      'text-gray-500 dark:text-gray-600' => !$hasComments,
  ])>
    {{ $commentCount > 0 ? \Illuminate\Support\Number::abbreviate($commentCount) : '0' }}
  </span>
</span>
