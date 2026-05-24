@extends('layouts.public')

@section('title', 'Résultats — '.$parametres->nom_evenement)

@section('content')
@if($voteSuccess)
<div class="mb-6 p-4 card-mafia text-center border-mafia-gold/50 flex flex-col items-center gap-2">
    <svg width="48" height="48" viewBox="0 0 100 100" class="pulse-vote" aria-label="Vote enregistré" role="img">
        <path d="M50 5 L62 35 L95 38 L70 58 L78 92 L50 75 L22 92 L30 58 L5 38 L38 35 Z" fill="#b8960c" stroke="#cc0000"/>
    </svg>
    <p class="text-mafia-gold font-semibold">Merci ! Votre vote a été enregistré.</p>
</div>
@endif

<h1 class="font-display text-3xl mb-6 text-center">Résultats en direct</h1>

<nav class="flex flex-wrap gap-2 justify-center mb-8" aria-label="Filtrer par talent">
    @foreach($talents as $t)
        <a href="{{ route('resultats', ['talent' => $t->id]) }}"
           class="px-4 py-2 min-h-[48px] flex items-center rounded border transition {{ $selectedTalent == $t->id ? 'border-mafia-red bg-mafia-red/20 text-mafia-red-bright' : 'border-mafia-border hover:border-mafia-red' }}">
            {{ $t->nom }}
        </a>
    @endforeach
</nav>

<div id="results-container" data-talent="{{ $selectedTalent }}">
@foreach($results as $block)
    <section class="mb-12">
        <h2 class="font-display text-2xl mb-6 text-mafia-red-bright">{{ $block['nom'] }}</h2>

        @php $candidats = collect($block['candidats']); $top3 = $candidats->take(3); @endphp
        @if($top3->count())
        <div class="flex justify-center items-end gap-4 mb-10" aria-label="Podium top 3">
            @foreach([1 => 0, 0 => 1, 2 => 2] as $pos => $idx)
                @if($top3->has($idx))
                @php $c = $top3[$idx]; $heights = [120, 160, 90]; @endphp
                <div class="text-center flex flex-col items-center" style="order: {{ $pos }}">
                    <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-mafia-gold mb-2">
                        @if($c['photo'])
                            <img src="{{ $c['photo'] }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full bg-mafia-card flex items-center justify-center text-mafia-gold font-bold">{{ $c['initials'] }}</div>
                        @endif
                    </div>
                    <svg width="80" height="{{ $heights[$idx] }}" aria-label="Place {{ $idx + 1 }}">
                        <rect width="80" height="{{ $heights[$idx] }}" fill="#1a1a1a" stroke="#cc0000" class="bar-grow"/>
                        <text x="40" y="{{ $heights[$idx] - 10 }}" text-anchor="middle" fill="#f0f0f0" font-size="12">{{ $c['votes'] }}</text>
                    </svg>
                    <p class="text-sm mt-2 max-w-[100px] truncate">{{ $c['nom'] }}</p>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        <div class="card-mafia p-6">
            <svg viewBox="0 0 800 300" class="w-full h-auto" role="img" aria-label="Diagramme des votes pour {{ $block['nom'] }}">
                @php $maxVotes = max(1, $candidats->max('votes') ?? 1); $barW = min(80, 700 / max(1, $candidats->count())); @endphp
                @foreach($candidats as $i => $c)
                    @php
                        $h = ($c['votes'] / $maxVotes) * 200;
                        $x = 40 + $i * ($barW + 20);
                    @endphp
                    <g>
                        @if($c['photo'])
                            <image href="{{ $c['photo'] }}" x="{{ $x + $barW/2 - 20 }}" y="10" width="40" height="40" clip-path="circle(50%)"/>
                        @else
                            <circle cx="{{ $x + $barW/2 }}" cy="30" r="20" fill="#1a1a1a" stroke="#b8960c"/>
                            <text x="{{ $x + $barW/2 }}" y="35" text-anchor="middle" fill="#b8960c" font-size="12">{{ $c['initials'] }}</text>
                        @endif
                        <rect x="{{ $x }}" y="{{ 280 - $h }}" width="{{ $barW }}" height="{{ $h }}" fill="#cc0000" class="bar-grow" style="animation-delay: {{ $i * 0.1 }}s">
                            <title>{{ $c['nom'] }}: {{ $c['votes'] }} votes ({{ $c['percent'] }}%)</title>
                        </rect>
                        <text x="{{ $x + $barW/2 }}" y="295" text-anchor="middle" fill="#888" font-size="10">{{ \Illuminate\Support\Str::limit($c['nom'], 12) }}</text>
                        <text x="{{ $x + $barW/2 }}" y="{{ 270 - $h }}" text-anchor="middle" fill="#f0f0f0" font-size="11">{{ $c['votes'] }} ({{ $c['percent'] }}%)</text>
                    </g>
                @endforeach
            </svg>
        </div>
    </section>
@endforeach
</div>
@endsection
