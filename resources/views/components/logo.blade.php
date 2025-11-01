@props(['size' => 'h-6 w-6'])

<svg {{ $attributes->merge(['class' => $size . ' text-green-600']) }} fill="currentColor" viewBox="0 0 24 24">
  <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z" />
  <path d="M10 17l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" fill="white" />
</svg>
