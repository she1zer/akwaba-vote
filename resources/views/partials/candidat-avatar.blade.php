@if($candidat->photoUrl())
    <img src="{{ $candidat->photoUrl() }}" alt="Photo de {{ $candidat->nom_complet }}" class="w-full h-full object-cover rounded">
@else
    <svg viewBox="0 0 100 100" class="w-full h-full" aria-label="Avatar {{ $candidat->initials() }}" role="img">
        <rect width="100" height="100" fill="#1a1a1a"/>
        <circle cx="50" cy="50" r="45" fill="none" stroke="#cc0000" stroke-width="2"/>
        <text x="50" y="58" text-anchor="middle" fill="#b8960c" font-size="32" font-family="Playfair Display, serif">{{ $candidat->initials() }}</text>
    </svg>
@endif
