@extends('layouts.admin')

@section('page-title', 'Gestion des Talents')

@section('content')
<div class="flex justify-between mb-6">
    <h2 class="font-display text-xl">Talents</h2>
    <a href="{{ route('admin.talents.create') }}" class="btn-mafia text-sm">+ Nouveau talent</a>
</div>
<div class="space-y-3">
    @foreach($talents as $talent)
    <div class="card-mafia p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            @include('partials.talent-icon', ['talent' => $talent])
            <div>
                <p class="font-semibold">{{ $talent->nom }}</p>
                <p class="text-mafia-muted text-sm">{{ $talent->candidats_count }} candidats — {{ $talent->votes_actifs ? 'Votes actifs' : 'Votes désactivés' }}</p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('admin.talents.edit', $talent) }}" class="btn-outline-mafia text-sm">Modifier</a>
            <form method="POST" action="{{ route('admin.talents.reset-votes', $talent) }}" onsubmit="return confirm('Réinitialiser les votes ?')">@csrf<button class="btn-outline-mafia text-sm text-mafia-gold">Reset votes</button></form>
            <form method="POST" action="{{ route('admin.talents.destroy', $talent) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-outline-mafia text-sm text-mafia-red">Supprimer</button></form>
        </div>
    </div>
    @endforeach
</div>
@endsection
