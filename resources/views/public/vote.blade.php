@extends('layouts.public')

@section('title', 'Voter — '.$talent->nom)

@section('content')
<div class="mb-8">
    <a href="{{ route('home') }}" class="text-mafia-muted hover:text-mafia-red text-sm">&larr; Tous les talents</a>
    <h1 class="font-display text-3xl mt-2">{{ $talent->nom }}</h1>
    <p class="text-mafia-muted">Choisissez votre candidat favori</p>
</div>

@if($errors->any())
    <div class="mb-6 p-4 border border-mafia-red text-mafia-red-bright rounded">{{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    @foreach($talent->candidats as $candidat)
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
                <h2 class="font-display text-lg mb-4">{{ $candidat->nom_complet }}</h2>
                <form method="POST" action="{{ route('vote.store', $talent) }}" class="vote-form">
                    @csrf
                    <input type="hidden" name="candidat_id" value="{{ $candidat->id }}">
                    <button type="submit" class="btn-mafia w-full min-h-[48px] pulse-vote relative">
                        <svg class="inline mr-2" width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2l2.4 7.4H22l-6 4.6 2.3 7-6.3-4.6L5.7 21l2.3-7-6-4.6h7.6z"/></svg>
                        Voter
                    </button>
                </form>
            </div>
        </article>
    @endforeach
</div>

<div id="vote-success-overlay" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center">
    <svg width="120" height="120" viewBox="0 0 100 100" class="pulse-vote" aria-label="Vote confirmé" role="img">
        <polygon points="50,5 61,38 95,38 68,58 79,91 50,72 21,91 32,58 5,38 39,38" fill="#b8960c" stroke="#cc0000" stroke-width="2"/>
    </svg>
</div>
@endsection
