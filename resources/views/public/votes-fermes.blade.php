@extends('layouts.public')

@section('title', 'Votes fermés')

@section('content')
<div class="max-w-lg mx-auto text-center card-mafia p-10">
    <svg class="mx-auto mb-6" width="80" height="80" viewBox="0 0 24 24" aria-label="Cadenas votes fermés" role="img">
        <rect x="3" y="11" width="18" height="11" rx="2" fill="none" stroke="#cc0000" stroke-width="2"/>
        <path d="M7 11V7a5 5 0 0 1 10 0v4" fill="none" stroke="#cc0000" stroke-width="2"/>
    </svg>
    <h1 class="font-display text-3xl mb-4">Votes fermés</h1>
    <p class="text-mafia-muted">{{ $parametres->message_accueil ?? 'Revenez plus tard pour voter.' }}</p>
    <a href="{{ route('home') }}" class="btn-outline-mafia mt-8 inline-block">Retour à l\'accueil</a>
</div>
@endsection
