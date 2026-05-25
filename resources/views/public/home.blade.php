@extends('layouts.public')

@section('title', $parametres->nom_evenement)

@section('content')
<div class="text-center mb-10">
    <h1 class="font-display text-4xl md:text-5xl mb-4">{{ $parametres->nom_evenement }}</h1>
    @if($parametres->message_accueil)
    <p class="text-mafia-muted max-w-2xl mx-auto">{{ $parametres->message_accueil }}</p>
    @endif
</div>

@if(!$parametres->votesAreOpen())
    @include('public.partials.votes-fermes-banner')
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($talents as $talent)
        <article class="card-mafia p-6 flex flex-col items-center text-center group">
            <div class="mb-4 text-mafia-red group-hover:scale-110 transition-transform">
                @include('partials.talent-icon', ['talent' => $talent])
            </div>
            <h2 class="font-display text-xl mb-1">{{ $talent->nom }}</h2>
            @if($talent->description)
            <p class="text-mafia-muted text-xs mb-2 italic">{{ $talent->description }}</p>
            @endif
            <p class="text-mafia-muted text-sm mb-4">{{ $talent->candidats_count }} candidat(s)</p>
            @if($parametres->votesAreOpen() && $talent->votes_actifs)
                <a href="{{ route('vote.show', $talent) }}" class="btn-mafia w-full min-h-[48px]">Voter</a>
            @else
                <span class="block w-full text-center text-mafia-muted text-sm py-2 border border-mafia-border rounded">Vote fermé</span>
            @endif
        </article>
    @empty
        <p class="col-span-full text-center text-mafia-muted">Aucun talent configuré pour le moment.</p>
    @endforelse
</div>

@if($parametres->reglement)
<div class="mt-12 card-mafia p-6 max-w-2xl mx-auto">
    <h2 class="font-display text-lg mb-3 text-mafia-gold">Règlement</h2>
    <div class="text-mafia-muted text-sm whitespace-pre-line">{{ $parametres->reglement }}</div>
</div>
@endif
@endsection
