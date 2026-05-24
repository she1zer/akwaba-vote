@extends('layouts.admin')

@section('page-title', $candidat->exists ? 'Modifier candidat' : 'Nouveau candidat')

@section('content')
<form method="POST" action="{{ $candidat->exists ? route('admin.candidats.update', $candidat) : route('admin.candidats.store') }}" enctype="multipart/form-data" class="card-mafia p-6 max-w-xl space-y-4">
    @csrf
    @if($candidat->exists) @method('PUT') @endif
    <label class="block"><span class="text-sm text-mafia-muted">Nom complet</span><input name="nom_complet" value="{{ old('nom_complet', $candidat->nom_complet) }}" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="block"><span class="text-sm text-mafia-muted">Talent</span>
        <select name="talent_id" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2">
            @foreach($talents as $t)<option value="{{ $t->id }}" @selected(old('talent_id', $candidat->talent_id) == $t->id)>{{ $t->nom }}</option>@endforeach
        </select>
    </label>
    <label class="block"><span class="text-sm text-mafia-muted">Photo (jpg/png/webp, max 2Mo){{ $candidat->exists ? ' — laisser vide pour conserver' : '' }}</span><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" {{ $candidat->exists ? '' : 'required' }} class="mt-1 w-full text-sm"></label>
    @if($candidat->exists && $candidat->photoUrl())
        <img src="{{ $candidat->photoUrl() }}" alt="Aperçu" class="w-32 h-32 object-cover rounded border border-mafia-border">
    @endif
    <button type="submit" class="btn-mafia">Enregistrer</button>
</form>
@endsection
