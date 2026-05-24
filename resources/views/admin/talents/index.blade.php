@extends('layouts.admin')

@section('page-title', 'Talents')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-display text-xl">Talents ({{ $talents->count() }})</h1>
    <a href="{{ route('admin.talents.create') }}" class="btn-mafia text-sm">+ Ajouter</a>
</div>

@if(session('status'))
<div class="mb-4 p-3 card-mafia border-green-700/30 text-green-400 text-sm">{{ session('status') }}</div>
@endif

<div class="card-mafia overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted">
                <tr>
                    <th class="p-3 text-left">Ordre</th>
                    <th class="p-3 text-left">Couleur</th>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-right">Candidats</th>
                    <th class="p-3 text-right">Votes</th>
                    <th class="p-3 text-center">Statut</th>
                    <th class="p-3 text-center">Reset</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($talents as $t)
                <tr class="border-t border-mafia-border">
                    <td class="p-3">
                        <div class="flex gap-1">
                            <form method="POST" action="{{ route('admin.talents.reorder', [$t, 'up']) }}">@csrf
                                <button class="text-mafia-muted hover:text-white text-xs px-1">▲</button>
                            </form>
                            <form method="POST" action="{{ route('admin.talents.reorder', [$t, 'down']) }}">@csrf
                                <button class="text-mafia-muted hover:text-white text-xs px-1">▼</button>
                            </form>
                            <span class="text-mafia-muted text-xs ml-1">{{ $t->ordre }}</span>
                        </div>
                    </td>
                    <td class="p-3">
                        <div class="w-5 h-5 rounded border border-mafia-border"
                             style="background: {{ $t->couleur_hex ?? '#cc0000' }}"
                             title="{{ $t->couleur_hex ?? '#cc0000' }}"></div>
                    </td>
                    <td class="p-3 font-medium">{{ $t->nom }}
                        @if($t->description)
                        <div class="text-xs text-mafia-muted">{{ \Illuminate\Support\Str::limit($t->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="p-3 text-right text-mafia-muted">{{ $t->candidats_count }}</td>
                    <td class="p-3 text-right font-semibold text-mafia-red-bright">{{ $t->votes_count }}</td>
                    <td class="p-3 text-center">
                        <span class="text-xs px-2 py-0.5 rounded border
                            {{ $t->votes_actifs ? 'border-green-700 text-green-400' : 'border-mafia-border text-mafia-muted' }}">
                            {{ $t->votes_actifs ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        <form method="POST" action="{{ route('admin.talents.reset-votes', $t) }}"
                              onsubmit="return confirm('Réinitialiser TOUS les votes de {{ addslashes($t->nom) }} ? Cette action est irréversible.')">
                            @csrf
                            <button type="submit" class="text-xs text-red-500 hover:text-red-300">Reset</button>
                        </form>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.talents.edit', $t) }}" class="text-mafia-muted hover:text-mafia-red-bright text-xs">Modifier</a>
                            <form method="POST" action="{{ route('admin.talents.destroy', $t) }}"
                                  onsubmit="return confirm('Supprimer {{ addslashes($t->nom) }} et tous ses candidats ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-mafia-muted hover:text-red-500 text-xs">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="p-6 text-center text-mafia-muted">Aucun talent</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
