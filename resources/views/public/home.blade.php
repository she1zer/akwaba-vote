@extends('layouts.public')

@section('title', $parametres->nom_evenement)

@section('content')
<div class="text-center mb-10">
    <h1 class="font-display text-4xl md:text-5xl mb-4">{{ $parametres->nom_evenement }}</h1>
    <p class="text-mafia-muted max-w-2xl mx-auto">{{ $parametres->message_accueil }}</p>
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
            <h2 class="font-display text-xl mb-2">{{ $talent->nom }}</h2>
            <p class="text-mafia-muted text-sm mb-4">{{ $talent->candidats_count }} candidat(s)</p>
            <a href="{{ route('vote.show', $talent) }}" class="btn-mafia w-full min-h-[48px]">Voter</a>
        </article>
    @empty
        <p class="col-span-full text-center text-mafia-muted">Aucun talent configuré pour le moment.</p>
    @endforelse
</div>
@endsection
