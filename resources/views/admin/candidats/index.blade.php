@extends('layouts.admin')

@section('page-title', 'Gestion des Candidats')

@section('content')
<div class="flex justify-between mb-6">
    <h2 class="font-display text-xl">Candidats</h2>
    <a href="{{ route('admin.candidats.create') }}" class="btn-mafia text-sm">+ Nouveau candidat</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($candidats as $candidat)
    <div class="card-mafia p-4">
        <div class="aspect-square w-24 mx-auto mb-3 rounded overflow-hidden border border-mafia-border">
            @include('partials.candidat-avatar', ['candidat' => $candidat])
        </div>
        <p class="font-semibold text-center">{{ $candidat->nom_complet }}</p>
        <p class="text-mafia-muted text-sm text-center mb-3">{{ $candidat->talent->nom }}</p>
        <div class="flex gap-2 justify-center">
            <a href="{{ route('admin.candidats.edit', $candidat) }}" class="btn-outline-mafia text-xs">Modifier</a>
            <form method="POST" action="{{ route('admin.candidats.destroy', $candidat) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn-outline-mafia text-xs text-mafia-red">Suppr.</button></form>
        </div>
    </div>
    @endforeach
</div>
@endsection
