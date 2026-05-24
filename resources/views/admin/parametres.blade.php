@extends('layouts.admin')

@section('page-title', 'Paramètres événement')

@section('content')
<form method="POST" action="{{ route('admin.parametres.update') }}" class="card-mafia p-6 max-w-xl space-y-4">
    @csrf @method('PUT')
    <label class="block"><span class="text-sm text-mafia-muted">Nom de l'événement</span><input name="nom_evenement" value="{{ old('nom_evenement', $parametres->nom_evenement) }}" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="block"><span class="text-sm text-mafia-muted">Message d'accueil</span><textarea name="message_accueil" rows="3" class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2">{{ old('message_accueil', $parametres->message_accueil) }}</textarea></label>
    <label class="block"><span class="text-sm text-mafia-muted">Début des votes</span><input type="datetime-local" name="date_debut_vote" value="{{ old('date_debut_vote', $parametres->date_debut_vote?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="block"><span class="text-sm text-mafia-muted">Fin des votes</span><input type="datetime-local" name="date_fin_vote" value="{{ old('date_fin_vote', $parametres->date_fin_vote?->format('Y-m-d\TH:i')) }}" class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2"></label>
    <label class="flex items-center gap-2"><input type="checkbox" name="votes_ouverts" value="1" {{ old('votes_ouverts', $parametres->votes_ouverts) ? 'checked' : '' }}> Votes ouverts globalement</label>
    <button type="submit" class="btn-mafia">Enregistrer</button>
</form>
@endsection
