@extends('layouts.admin', ['live' => $live])

@section('page-title', 'Dashboard')

@section('content')
{{-- Stats cards --}}
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Talents</p>
        <p class="text-3xl font-display text-mafia-red-bright">{{ $stats['talents'] }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Candidats</p>
        <p class="text-3xl font-display text-mafia-red-bright">{{ $stats['candidats'] }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Votes valides</p>
        <p class="text-3xl font-display text-mafia-red-bright">{{ $stats['votes'] }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Votes suspects</p>
        <p class="text-3xl font-display text-yellow-400">{{ $stats['votes_flagges'] }}</p>
    </div>
    <a href="{{ route('admin.moderation') }}" class="card-mafia p-5 hover:border-mafia-gold/50 transition {{ $stats['en_attente'] > 0 ? 'border-mafia-gold/40' : '' }}">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">À modérer</p>
        <p class="text-3xl font-display {{ $stats['en_attente'] > 0 ? 'text-mafia-gold' : 'text-mafia-muted' }}">{{ $stats['en_attente'] }}</p>
    </a>
</div>

{{-- Actions rapides --}}
<div class="flex flex-wrap gap-3 mb-8">
    <form method="POST" action="{{ route('admin.votes.toggle') }}">
        @csrf
        <button class="btn-mafia text-sm">
            {{ $parametres->votes_ouverts ? '🔒 Fermer les votes' : '🔓 Ouvrir les votes' }}
        </button>
    </form>
    @if($stats['en_attente'] > 0)
    <a href="{{ route('admin.moderation') }}" class="btn-mafia text-sm bg-transparent border-mafia-gold text-mafia-gold hover:bg-mafia-gold/10">
        🛡 Modérer ({{ $stats['en_attente'] }})
    </a>
    @endif
    <a href="{{ route('admin.statistiques') }}" class="btn-mafia text-sm bg-transparent border-mafia-border hover:border-mafia-gold">
        📊 Statistiques
    </a>
    <a href="{{ route('admin.export.csv') }}" class="btn-mafia text-sm bg-transparent border-mafia-border hover:border-mafia-gold">
        ⬇ CSV résultats
    </a>
</div>

{{-- Statut votes --}}
@if($parametres->votes_ouverts)
<div class="mb-6 p-3 rounded border border-green-600/30 bg-green-900/10 text-green-400 text-sm flex items-center gap-2">
    <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
    Votes ouverts
    @if($parametres->tempsRestant())
        — se ferment dans {{ $parametres->tempsRestant() }}
    @endif
</div>
@else
<div class="mb-6 p-3 rounded border border-mafia-red/30 bg-mafia-red/10 text-mafia-red-bright text-sm">
    🔒 Votes fermés
</div>
@endif

{{-- Votes récents --}}
<div class="card-mafia overflow-hidden">
    <div class="flex items-center justify-between p-4 border-b border-mafia-border">
        <h2 class="font-display">Votes récents</h2>
        @if($live)
        <span class="flex items-center gap-1 text-xs text-green-400">
            <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span>
            Live
        </span>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted">
                <tr>
                    <th class="p-3 text-left">Candidat</th>
                    <th class="p-3 text-left">Talent</th>
                    <th class="p-3 text-left">IP</th>
                    <th class="p-3 text-left">Confiance</th>
                    <th class="p-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentVotes as $v)
                <tr class="border-t border-mafia-border {{ $v->is_flagged ? 'bg-yellow-900/20' : '' }}">
                    <td class="p-3">
                        {{ $v->candidat?->nom_complet }}
                        @if($v->is_flagged)
                            <span class="ml-1 text-yellow-400 text-xs" title="{{ $v->flag_reason }}">⚠</span>
                        @endif
                    </td>
                    <td class="p-3 text-mafia-muted">{{ $v->talent?->nom }}</td>
                    <td class="p-3 text-mafia-muted font-mono text-xs">{{ $v->ip_address }}</td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-0.5 rounded
                            {{ ($v->score_confiance ?? 100) >= 70 ? 'bg-green-900/30 text-green-400' :
                               (($v->score_confiance ?? 100) >= 40 ? 'bg-yellow-900/30 text-yellow-400' : 'bg-red-900/30 text-red-400') }}">
                            {{ $v->score_confiance ?? '—' }}
                        </span>
                    </td>
                    <td class="p-3 text-mafia-muted">{{ $v->created_at?->diffForHumans() }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="p-6 text-center text-mafia-muted">Aucun vote</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
