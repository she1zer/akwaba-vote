@php
$defaultIcon = '<svg viewBox="0 0 64 64" width="48" height="48" aria-label="Icône talent" role="img"><circle cx="32" cy="32" r="28" fill="none" stroke="#cc0000" stroke-width="2"/><path d="M32 12 L38 28 L54 28 L42 38 L46 54 L32 44 L18 54 L22 38 L10 28 L26 28 Z" fill="#b8960c" opacity="0.8"/></svg>';
@endphp
{!! $talent->icone_svg ?? $defaultIcon !!}
