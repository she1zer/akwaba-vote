@extends('layouts.admin', ['live' => $live])

@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="card-mafia p-6"><p class="text-mafia-muted text-sm">Talents</p><p class="text-3xl font-display text-mafia-red-bright">{{ $stats['talents'] }}</p></div>
    <div class="card-mafia p-6"><p class="text-mafia-muted text-sm">Candidats</p><p class="text-3xl font-display text-mafia-red-bright">{{ $stats['candidats'] }}</p></div>
    <div class="card-mafia p-6"><p class="text-mafia-muted text-sm">Votes</p><p class="text-3xl font-display text-mafia-red-bright">{{ $stats['votes'] }}</p></div>
</div>

<div class="flex flex-wrap gap-3 mb-8">
    <form method="POST" action="{{ route('admin.votes.toggle') }}">@csrf<button class="btn-mafia text-sm">{{ $parametres->votes_ouverts ? 'Fermer les votes' : 'Ouvrir les votes' }}</button></form>
</div>

<div class="card-mafia overflow-hidden">
    <h2 class="p-4 border-b border-mafia-border font-display">Votes récents</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted"><tr><th class="p-3 text-left">Candidat</th><th class="p-3 text-left">Talent</th><th class="p-3 text-left">Date</th></tr></thead>
            <tbody>
                @forelse($recentVotes as $v)
                <tr class="border-t border-mafia-border"><td class="p-3">{{ $v->candidat->nom_complet }}</td><td class="p-3">{{ $v->talent->nom }}</td><td class="p-3 text-mafia-muted">{{ $v->created_at?->diffForHumans() }}</td></tr>
                @empty
                <tr><td colspan="3" class="p-6 text-center text-mafia-muted">Aucun vote</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
