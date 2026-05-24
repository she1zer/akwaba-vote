@extends('layouts.admin')

@section('page-title', 'Paramètres')

@section('content')
<div class="max-w-2xl">
    @if(session('status'))
    <div class="mb-4 p-3 card-mafia border-green-700/30 text-green-400 text-sm">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.parametres.update') }}" class="card-mafia p-6 space-y-6">
        @csrf @method('PUT')

        <fieldset class="space-y-4">
            <legend class="font-display text-mafia-red-bright text-sm uppercase tracking-wider mb-3">Événement</legend>

            <div>
                <label class="block text-sm text-mafia-muted mb-1">Nom de l'événement</label>
                <input type="text" name="nom_evenement" value="{{ old('nom_evenement', $parametres->nom_evenement) }}"
                       class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Message d'accueil</label>
                <textarea name="message_accueil" rows="3"
                          class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">{{ old('message_accueil', $parametres->message_accueil) }}</textarea>
            </div>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Règlement / informations</label>
                <textarea name="reglement" rows="4"
                          class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm"
                          placeholder="Règles du vote, conditions de participation…">{{ old('reglement', $parametres->reglement) }}</textarea>
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="font-display text-mafia-red-bright text-sm uppercase tracking-wider mb-3">Période de vote</legend>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-mafia-muted mb-1">Début</label>
                    <input type="datetime-local" name="date_debut_vote"
                           value="{{ old('date_debut_vote', $parametres->date_debut_vote?->format('Y-m-d\TH:i')) }}"
                           class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-mafia-muted mb-1">Fin</label>
                    <input type="datetime-local" name="date_fin_vote"
                           value="{{ old('date_fin_vote', $parametres->date_fin_vote?->format('Y-m-d\TH:i')) }}"
                           class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="hidden" name="votes_ouverts" value="0">
                <input type="checkbox" name="votes_ouverts" value="1" id="votes_ouverts"
                       {{ old('votes_ouverts', $parametres->votes_ouverts) ? 'checked' : '' }}
                       class="accent-mafia-red">
                <label for="votes_ouverts" class="text-sm">Votes ouverts globalement</label>
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="font-display text-mafia-red-bright text-sm uppercase tracking-wider mb-3">Affichage</legend>

            <div class="space-y-2">
                <div class="flex items-center gap-2">
                    <input type="hidden" name="afficher_resultats_live" value="0">
                    <input type="checkbox" name="afficher_resultats_live" value="1" id="arl"
                           {{ old('afficher_resultats_live', $parametres->afficher_resultats_live) ? 'checked' : '' }}
                           class="accent-mafia-red">
                    <label for="arl" class="text-sm">Afficher les résultats en temps réel</label>
                </div>
                <div class="flex items-center gap-2">
                    <input type="hidden" name="afficher_nb_votes" value="0">
                    <input type="checkbox" name="afficher_nb_votes" value="1" id="anv"
                           {{ old('afficher_nb_votes', $parametres->afficher_nb_votes) ? 'checked' : '' }}
                           class="accent-mafia-red">
                    <label for="anv" class="text-sm">Afficher le nombre de votes</label>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-mafia-muted mb-1">Couleur primaire</label>
                    <div class="flex gap-2">
                        <input type="color" name="couleur_primaire" value="{{ old('couleur_primaire', $parametres->couleur_primaire ?? '#cc0000') }}"
                               class="w-10 h-9 rounded border border-mafia-border bg-mafia-soft cursor-pointer">
                        <input type="text" value="{{ old('couleur_primaire', $parametres->couleur_primaire ?? '#cc0000') }}"
                               class="flex-1 bg-mafia-soft border border-mafia-border rounded px-2 py-2 text-sm font-mono" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm text-mafia-muted mb-1">Couleur secondaire</label>
                    <div class="flex gap-2">
                        <input type="color" name="couleur_secondaire" value="{{ old('couleur_secondaire', $parametres->couleur_secondaire ?? '#b8960c') }}"
                               class="w-10 h-9 rounded border border-mafia-border bg-mafia-soft cursor-pointer">
                        <input type="text" value="{{ old('couleur_secondaire', $parametres->couleur_secondaire ?? '#b8960c') }}"
                               class="flex-1 bg-mafia-soft border border-mafia-border rounded px-2 py-2 text-sm font-mono" readonly>
                    </div>
                </div>
            </div>
        </fieldset>

        <fieldset class="space-y-4">
            <legend class="font-display text-mafia-red-bright text-sm uppercase tracking-wider mb-3">Réseaux sociaux</legend>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Lien Facebook</label>
                <input type="url" name="lien_facebook" value="{{ old('lien_facebook', $parametres->lien_facebook) }}"
                       placeholder="https://facebook.com/…"
                       class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Lien Instagram</label>
                <input type="url" name="lien_instagram" value="{{ old('lien_instagram', $parametres->lien_instagram) }}"
                       placeholder="https://instagram.com/…"
                       class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
            </div>
        </fieldset>

        <div class="pt-2 border-t border-mafia-border">
            <button type="submit" class="btn-mafia">Enregistrer</button>
        </div>
    </form>
</div>
@endsection
