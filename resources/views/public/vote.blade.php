@extends('layouts.public')

@section('title', 'Voter — '.$talent->nom)

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-mafia-muted hover:text-mafia-red text-sm">&larr; Tous les talents</a>
    <h1 class="font-display text-3xl mt-2">{{ $talent->nom }}</h1>
    @if($talent->description)
    <p class="text-mafia-muted">{{ $talent->description }}</p>
    @else
    <p class="text-mafia-muted">Choisissez votre candidat favori</p>
    @endif
</div>

{{-- Messages --}}
@if($errors->has('vote'))
    <div class="mb-6 p-4 border border-mafia-red text-mafia-red-bright rounded">{{ $errors->first('vote') }}</div>
@endif

@if(session('candidature_succes'))
    <div class="mb-6 p-4 border border-mafia-gold/60 bg-mafia-gold/10 text-mafia-gold rounded flex items-start gap-3">
        <span class="text-xl">⏳</span>
        <div>
            <p class="font-semibold">Proposition envoyée !</p>
            <p class="text-sm mt-1">{{ session('candidature_succes') }}</p>
        </div>
    </div>
@endif

{{-- Grille candidats --}}
@if($talent->candidatsActifs->count())
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
    @foreach($talent->candidatsActifs as $candidat)
        <article class="card-mafia overflow-hidden">
            <div class="relative p-4">
                <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 300 320" preserveAspectRatio="none" aria-hidden="true">
                    <rect x="8" y="8" width="284" height="304" fill="none" stroke="#cc0000" stroke-width="2" opacity="0.6"/>
                    <path d="M8 8 L40 8 M260 8 L292 8 M8 312 L40 312 M260 312 L292 312" stroke="#b8960c" stroke-width="3"/>
                </svg>
                <div class="aspect-square w-full max-w-[220px] mx-auto overflow-hidden rounded border-2 border-mafia-border">
                    @include('partials.candidat-avatar', ['candidat' => $candidat])
                </div>
            </div>
            <div class="p-4 text-center border-t border-mafia-border">
                <h2 class="font-display text-lg mb-1">{{ $candidat->nom_complet }}</h2>
                @if($candidat->slogan)
                <p class="text-mafia-muted text-xs italic mb-3">« {{ $candidat->slogan }} »</p>
                @else
                <div class="mb-3"></div>
                @endif
                <form method="POST" action="{{ route('vote.store', $talent) }}" class="vote-form">
                    @csrf
                    <input type="hidden" name="candidat_id" value="{{ $candidat->id }}">
                    <button type="submit" class="btn-mafia w-full min-h-[48px] pulse-vote relative">
                        <svg class="inline mr-2" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill="currentColor" d="M12 2l2.4 7.4H22l-6 4.6 2.3 7-6.3-4.6L5.7 21l2.3-7-6-4.6h7.6z"/>
                        </svg>
                        Voter
                    </button>
                </form>
            </div>
        </article>
    @endforeach
</div>
@else
<div class="text-center text-mafia-muted py-12 mb-8">
    <p class="text-lg">Aucun candidat disponible pour le moment.</p>
    <p class="text-sm mt-2">Soyez le premier à proposer un candidat !</p>
</div>
@endif

{{-- Section proposition de candidat --}}
@if($talent->allow_candidature_spontanee)
<div class="card-mafia p-6 max-w-xl mx-auto border-mafia-gold/30">
    <h2 class="font-display text-xl mb-1 text-mafia-gold">💡 Proposer un candidat</h2>
    <p class="text-mafia-muted text-sm mb-5">
        Vous ne trouvez pas votre candidat dans la liste ? Proposez un nom — il sera ajouté après validation par l'équipe organisatrice.
    </p>

    @if($errors->has('nom_complet'))
        <div class="mb-4 p-3 border border-mafia-red text-mafia-red-bright rounded text-sm">
            {{ $errors->first('nom_complet') }}
        </div>
    @endif

    <form method="POST" action="{{ route('candidature.store', $talent) }}" class="space-y-4">
        @csrf
        <div>
            <label for="nom_complet" class="block text-sm text-mafia-muted mb-1">
                Nom complet du candidat <span class="text-mafia-red">*</span>
            </label>
            <input type="text"
                   id="nom_complet"
                   name="nom_complet"
                   value="{{ old('nom_complet') }}"
                   maxlength="100"
                   placeholder="Ex: Jean Kouadio"
                   required
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm focus:border-mafia-gold focus:outline-none transition">
        </div>
        <div>
            <label for="slogan" class="block text-sm text-mafia-muted mb-1">
                Slogan <span class="text-mafia-muted text-xs">(optionnel, max 200 car.)</span>
            </label>
            <input type="text"
                   id="slogan"
                   name="slogan"
                   value="{{ old('slogan') }}"
                   maxlength="200"
                   placeholder="Son slogan de campagne…"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm focus:border-mafia-gold focus:outline-none transition">
        </div>
        <button type="submit" class="btn-mafia w-full bg-transparent border-mafia-gold text-mafia-gold hover:bg-mafia-gold/10 min-h-[48px]">
            Envoyer la proposition
        </button>
        <p class="text-mafia-muted text-xs text-center">
            Votre proposition sera examinée avant d'apparaître dans la liste de vote.
        </p>
    </form>
</div>
@endif

<div id="vote-success-overlay" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center">
    <svg width="120" height="120" viewBox="0 0 100 100" class="pulse-vote" aria-label="Vote confirmé" role="img">
        <polygon points="50,5 61,38 95,38 68,58 79,91 50,72 21,91 32,58 5,38 39,38" fill="#b8960c" stroke="#cc0000" stroke-width="2"/>
    </svg>
</div>
@endsection
