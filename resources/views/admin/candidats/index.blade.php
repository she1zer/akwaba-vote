@extends('layouts.admin')

@section('page-title', 'Candidats')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="font-display text-xl">Candidats ({{ $candidats->count() }})</h1>
    <a href="{{ route('admin.candidats.create') }}" class="btn-mafia text-sm">+ Ajouter</a>
</div>

@if(session('status'))
<div class="mb-4 p-3 card-mafia border-green-700/30 text-green-400 text-sm">{{ session('status') }}</div>
@endif

<div class="card-mafia overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-mafia-soft text-mafia-muted">
                <tr>
                    <th class="p-3 text-left">Photo</th>
                    <th class="p-3 text-left">Nom</th>
                    <th class="p-3 text-left">Talent</th>
                    <th class="p-3 text-right">Votes</th>
                    <th class="p-3 text-center">Statut</th>
                    <th class="p-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($candidats as $c)
                <tr class="border-t border-mafia-border {{ $c->is_active ? '' : 'opacity-50' }}">
                    <td class="p-3">
                        @if($c->thumbUrl())
                            <img src="{{ $c->thumbUrl() }}" alt="" class="w-10 h-10 rounded-full object-cover border border-mafia-border">
                        @else
                            <div class="w-10 h-10 rounded-full bg-mafia-soft flex items-center justify-center text-mafia-muted text-xs font-bold border border-mafia-border">
                                {{ $c->initials() }}
                            </div>
                        @endif
                    </td>
                    <td class="p-3">
                        <div class="font-medium">{{ $c->nom_complet }}</div>
                        @if($c->slogan)
                        <div class="text-xs text-mafia-muted italic">{{ $c->slogan }}</div>
                        @endif
                    </td>
                    <td class="p-3 text-mafia-muted">{{ $c->talent?->nom }}</td>
                    <td class="p-3 text-right font-semibold text-mafia-red-bright">{{ $c->votes_count }}</td>
                    <td class="p-3 text-center">
                        <form method="POST" action="{{ route('admin.candidats.toggle', $c) }}">
                            @csrf
                            <button type="submit" class="text-xs px-2 py-0.5 rounded border
                                {{ $c->is_active ? 'border-green-700 text-green-400 hover:bg-red-900/10 hover:text-red-400 hover:border-red-700' : 'border-mafia-border text-mafia-muted hover:border-green-700 hover:text-green-400' }}">
                                {{ $c->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>
                    <td class="p-3">
                        <div class="flex gap-2 justify-end">
                            <a href="{{ route('admin.candidats.edit', $c) }}" class="text-mafia-muted hover:text-mafia-red-bright text-xs">Modifier</a>
                            <form method="POST" action="{{ route('admin.candidats.destroy', $c) }}"
                                  onsubmit="return confirm('Supprimer {{ addslashes($c->nom_complet) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-mafia-muted hover:text-red-500 text-xs">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="p-6 text-center text-mafia-muted">Aucun candidat</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
