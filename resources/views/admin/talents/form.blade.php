@extends('layouts.admin')

@section('page-title', $talent->exists ? 'Modifier talent' : 'Nouveau talent')

@section('content')
<form method="POST" action="{{ $talent->exists ? route('admin.talents.update', $talent) : route('admin.talents.store') }}" class="card-mafia p-6 max-w-xl space-y-4">
    @csrf
    @if($talent->exists) @method('PUT') @endif
    <label class="block"><span class="text-sm text-mafia-muted">Nom</span><input name="nom" value="{{ old('nom', $talent->nom) }}" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="block"><span class="text-sm text-mafia-muted">Ordre</span><input type="number" name="ordre" value="{{ old('ordre', $talent->ordre ?? 0) }}" class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="flex items-center gap-2"><input type="checkbox" name="votes_actifs" value="1" {{ old('votes_actifs', $talent->votes_actifs ?? true) ? 'checked' : '' }}> Votes actifs pour ce talent</label>
    <label class="block"><span class="text-sm text-mafia-muted">Icône SVG (optionnel)</span><textarea name="icone_svg" rows="4" class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 font-mono text-xs">{{ old('icone_svg', $talent->icone_svg) }}</textarea></label>
    <button type="submit" class="btn-mafia">Enregistrer</button>
</form>
@endsection
