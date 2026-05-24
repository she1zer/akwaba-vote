@extends('layouts.admin')

@section('page-title', 'Statistiques')

@section('content')

{{-- Chiffres clés --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Total votes</p>
        <p class="text-3xl font-display text-mafia-red-bright">{{ $totalVotes }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Votes suspects</p>
        <p class="text-3xl font-display text-yellow-400">{{ $fraudeStats['votes_flagges'] }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">IPs suspectes (10min)</p>
        <p class="text-3xl font-display text-orange-400">{{ $fraudeStats['ips_suspectes'] }}</p>
    </div>
    <div class="card-mafia p-5">
        <p class="text-mafia-muted text-xs uppercase tracking-wider mb-1">Votes invalides</p>
        <p class="text-3xl font-display text-mafia-muted">{{ $fraudeStats['total_invalides'] }}</p>
    </div>
</div>

{{-- Exports --}}
<div class="flex flex-wrap gap-3 mb-8">
    <a href="{{ route('admin.export.csv') }}" class="btn-mafia text-sm">⬇ CSV résultats</a>
    <a href="{{ route('admin.export.csv.bruts') }}" class="btn-mafia text-sm bg-transparent border-mafia-border hover:border-mafia-gold">⬇ CSV votes bruts</a>
    <a href="{{ route('admin.export.pdf') }}" class="btn-mafia text-sm bg-transparent border-mafia-border hover:border-mafia-gold">⬇ PDF rapport</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Activité par heure --}}
    <div class="card-mafia p-6">
        <h2 class="font-display text-lg mb-4">Activité (24h)</h2>
        <div class="flex items-end gap-1 h-32">
            @php
                $maxH = max(1, max($votesParHeure ?: [0]));
            @endphp
            @for($h = 0; $h < 24; $h++)
            @php $v = $votesParHeure[$h] ?? 0; $pct = round(($v / $maxH) * 100); @endphp
            <div class="flex-1 flex flex-col items-center gap-1 group relative">
                <div class="w-full bg-mafia-red/80 rounded-sm transition-all"
                     style="height: {{ $pct }}%"
                     title="{{ $h }}h : {{ $v }} votes"></div>
                @if($h % 4 === 0)
                <span class="text-mafia-muted text-[10px]">{{ $h }}h</span>
                @endif
            </div>
            @endfor
        </div>
    </div>

    {{-- Top candidats --}}
    <div class="card-mafia overflow-hidden">
        <h2 class="font-display text-lg p-4 border-b border-mafia-border">Top 10 candidats</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-mafia-soft text-mafia-muted">
                    <tr>
                        <th class="p-3 text-left">#</th>
                        <th class="p-3 text-left">Candidat</th>
                        <th class="p-3 text-left">Talent</th>
                        <th class="p-3 text-right">Votes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topCandidats as $i => $c)
                    <tr class="border-t border-mafia-border">
                        <td class="p-3 text-mafia-muted">{{ $i + 1 }}</td>
                        <td class="p-3">{{ $c->nom_complet }}</td>
                        <td class="p-3 text-mafia-muted">{{ $c->talent?->nom }}</td>
                        <td class="p-3 text-right font-semibold text-mafia-red-bright">{{ $c->votes_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Votes suspects --}}
@if($votesFlagges->count())
<div class="card-mafia overflow-hidden">
    <h2 class="font-display text-lg p-4 border-b border-mafia-border text-yellow-400">
        ⚠ Votes suspects ({{ $votesFlagges->count() }} derniers)
    </h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted">
                <tr>
                    <th class="p-3 text-left">Candidat</th>
                    <th class="p-3 text-left">IP</th>
                    <th class="p-3 text-left">Score</th>
                    <th class="p-3 text-left">Raison</th>
                    <th class="p-3 text-left">Date</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($votesFlagges as $v)
                <tr class="border-t border-mafia-border bg-yellow-900/10">
                    <td class="p-3">{{ $v->candidat?->nom_complet }}</td>
                    <td class="p-3 font-mono text-xs text-mafia-muted">{{ $v->ip_address }}</td>
                    <td class="p-3">
                        <span class="text-xs px-2 py-0.5 rounded bg-red-900/30 text-red-400">
                            {{ $v->score_confiance }}
                        </span>
                    </td>
                    <td class="p-3 text-mafia-muted text-xs">{{ $v->flag_reason }}</td>
                    <td class="p-3 text-mafia-muted">{{ $v->created_at?->format('d/m H:i') }}</td>
                    <td class="p-3">
                        <form method="POST" action="{{ route('admin.votes.flag', $v) }}">
                            @csrf
                            <button type="submit" class="text-xs text-mafia-muted hover:text-mafia-red-bright">
                                Valider
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
