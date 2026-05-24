@extends('layouts.public')

@section('title', 'Résultats — '.$parametres->nom_evenement)

@section('content')
@if($voteSuccess)
<div class="mb-6 p-4 card-mafia text-center border-mafia-gold/50 flex flex-col items-center gap-2">
    <svg width="48" height="48" viewBox="0 0 100 100" class="pulse-vote" aria-label="Vote enregistré" role="img">
        <path d="M50 5 L62 35 L95 38 L70 58 L78 92 L50 75 L22 92 L30 58 L5 38 L38 35 Z" fill="#b8960c" stroke="#cc0000"/>
    </svg>
    <p class="text-mafia-gold font-semibold">Merci ! Votre vote a été enregistré.</p>
    <p class="text-mafia-muted text-sm">Montrez votre soutien avec une réaction 👇</p>
</div>
@endif

<h1 class="font-display text-3xl mb-6 text-center">Résultats en direct</h1>

@if($parametres->date_fin_vote && $parametres->votesAreOpen())
<p class="text-center text-mafia-muted text-sm mb-6">
    ⏳ Votes se ferment dans <strong class="text-mafia-gold">{{ $parametres->tempsRestant() }}</strong>
</p>
@endif

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

        {{-- Barres --}}
        <div class="card-mafia p-6 mb-6">
            @if($parametres->afficher_nb_votes)
            <p class="text-xs text-mafia-muted mb-3 text-right">{{ $block['total_votes'] }} vote(s) au total</p>
            @endif
            @foreach($candidats as $c)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span>{{ $c['nom'] }}</span>
                    <span class="text-mafia-muted">
                        @if($parametres->afficher_nb_votes){{ $c['votes'] }} vote(s) — @endif{{ $c['percent'] }}%
                    </span>
                </div>
                <div class="h-3 rounded bg-mafia-soft overflow-hidden">
                    <div class="h-full bg-mafia-red bar-grow transition-all" style="width: {{ $c['percent'] }}%"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Réactions --}}
        @if($voteSuccess)
        <div class="card-mafia p-4 text-center">
            <p class="text-sm text-mafia-muted mb-3">Réagissez pour votre candidat favori !</p>
            <div class="flex justify-center gap-6 flex-wrap">
                @foreach($candidats->take(3) as $c)
                <div class="flex flex-col items-center gap-2">
                    <span class="text-sm text-mafia-muted">{{ \Illuminate\Support\Str::limit($c['nom'], 15) }}</span>
                    <div class="flex gap-2" data-candidat="{{ $c['id'] }}">
                        @foreach(['coeur' => '❤️', 'feu' => '🔥', 'star' => '⭐', 'clap' => '👏'] as $type => $emoji)
                        <button onclick="sendReaction({{ $c['id'] }}, '{{ $type }}')"
                                class="reaction-btn text-xl hover:scale-125 transition-transform p-1"
                                data-type="{{ $type }}" title="{{ $type }}">
                            {{ $emoji }}
                            <span class="text-xs text-mafia-muted block" id="reaction-{{ $c['id'] }}-{{ $type }}">
                                {{ $c['reactions'][$type] ?? 0 }}
                            </span>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>
@endforeach
</div>

{{-- Partage --}}
<div class="mt-8 text-center">
    <p class="text-mafia-muted text-sm mb-3">Partagez la plateforme de vote</p>
    <div class="flex justify-center gap-3 flex-wrap">
        @if($parametres->lien_facebook)
        <a href="{{ $parametres->lien_facebook }}" target="_blank" rel="noopener"
           class="btn-mafia text-sm bg-transparent border-blue-700 text-blue-400 hover:bg-blue-900/10">
            Facebook
        </a>
        @endif
        @if($parametres->lien_instagram)
        <a href="{{ $parametres->lien_instagram }}" target="_blank" rel="noopener"
           class="btn-mafia text-sm bg-transparent border-pink-700 text-pink-400 hover:bg-pink-900/10">
            Instagram
        </a>
        @endif
        <button onclick="copyLink()" class="btn-mafia text-sm bg-transparent border-mafia-border text-mafia-muted">
            🔗 Copier le lien
        </button>
    </div>
</div>

<script>
function sendReaction(candidatId, type) {
    fetch('/candidat/' + candidatId + '/reaction', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ type })
    })
    .then(r => r.json())
    .then(data => {
        if (data.counts) {
            Object.entries(data.counts).forEach(([t, n]) => {
                const el = document.getElementById('reaction-' + candidatId + '-' + t);
                if (el) el.textContent = n;
            });
        }
    });
}

function copyLink() {
    navigator.clipboard.writeText(window.location.origin + '/resultats')
        .then(() => alert('Lien copié !'));
}
</script>
@endsection
