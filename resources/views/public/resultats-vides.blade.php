@extends('layouts.public')

@section('title', 'Résultats')

@section('content')
<div class="text-center card-mafia p-12 max-w-lg mx-auto">
    <svg class="mx-auto mb-6" width="64" height="64" viewBox="0 0 64 64" aria-label="Aucun résultat" role="img">
        <rect x="8" y="40" width="12" height="16" fill="#2a2a2a"/>
        <rect x="26" y="28" width="12" height="28" fill="#2a2a2a"/>
        <rect x="44" y="16" width="12" height="40" fill="#2a2a2a"/>
    </svg>
    <h1 class="font-display text-2xl mb-2">Résultats non disponibles</h1>
    <p class="text-mafia-muted">Aucun vote n'a encore été enregistré.</p>
    <a href="{{ route('home') }}" class="btn-mafia mt-6 inline-block">Commencer à voter</a>
</div>
@endsection
