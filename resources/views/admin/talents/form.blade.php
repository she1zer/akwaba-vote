@extends('layouts.admin')

@section('page-title', $talent->exists ? 'Modifier talent' : 'Nouveau talent')

@section('content')
<div class="max-w-xl">
    <form method="POST"
          action="{{ $talent->exists ? route('admin.talents.update', $talent) : route('admin.talents.store') }}"
          class="card-mafia p-6 space-y-5">
        @csrf
        @if($talent->exists) @method('PUT') @endif

        <div>
            <label class="block text-sm text-mafia-muted mb-1">Nom du talent *</label>
            <input type="text" name="nom" value="{{ old('nom', $talent->nom) }}"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm" required>
            @error('nom')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm text-mafia-muted mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm"
                      placeholder="Courte description de cette catégorie…">{{ old('description', $talent->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-mafia-muted mb-1">Icône SVG</label>
            <textarea name="icone_svg" rows="3"
                      class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm font-mono text-xs"
                      placeholder="<svg>...</svg>">{{ old('icone_svg', $talent->icone_svg) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Couleur</label>
                <div class="flex gap-2 items-center">
                    <input type="color" name="couleur_hex" id="couleur_pick"
                           value="{{ old('couleur_hex', $talent->couleur_hex ?? '#cc0000') }}"
                           class="w-10 h-9 rounded border border-mafia-border bg-mafia-soft cursor-pointer">
                    <input type="text" id="couleur_txt"
                           value="{{ old('couleur_hex', $talent->couleur_hex ?? '#cc0000') }}"
                           class="flex-1 bg-mafia-soft border border-mafia-border rounded px-2 py-2 text-sm font-mono"
                           oninput="document.getElementById('couleur_pick').value=this.value">
                </div>
            </div>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Ordre</label>
                <input type="number" name="ordre" value="{{ old('ordre', $talent->ordre ?? 0) }}" min="0"
                       class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm text-mafia-muted mb-1">Max votes par IP</label>
            <input type="number" name="max_votes_par_ip"
                   value="{{ old('max_votes_par_ip', $talent->max_votes_par_ip ?? 1) }}"
                   min="1" max="10"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
            <p class="text-xs text-mafia-muted mt-1">Nombre de votes autorisés par adresse IP pour ce talent</p>
        </div>

        {{-- Options booléennes --}}
        <div class="space-y-3 pt-2 border-t border-mafia-border">
            <div class="flex items-center gap-2">
                <input type="hidden" name="votes_actifs" value="0">
                <input type="checkbox" name="votes_actifs" value="1" id="votes_actifs"
                       {{ old('votes_actifs', $talent->votes_actifs ?? true) ? 'checked' : '' }}
                       class="accent-mafia-red w-4 h-4">
                <label for="votes_actifs" class="text-sm">Votes actifs pour ce talent</label>
            </div>

            <div class="flex items-center gap-2">
                <input type="hidden" name="allow_candidature_spontanee" value="0">
                <input type="checkbox" name="allow_candidature_spontanee" value="1" id="allow_candidature_spontanee"
                       {{ old('allow_candidature_spontanee', $talent->allow_candidature_spontanee ?? false) ? 'checked' : '' }}
                       class="accent-mafia-red w-4 h-4">
                <label for="allow_candidature_spontanee" class="text-sm">
                    Autoriser les voteurs à proposer un candidat
                    <span class="block text-xs text-mafia-muted">Les propositions devront être validées en modération avant d'apparaître</span>
                </label>
            </div>
        </div>

        <div class="flex gap-3 pt-2 border-t border-mafia-border">
            <button type="submit" class="btn-mafia">
                {{ $talent->exists ? 'Mettre à jour' : 'Créer' }}
            </button>
            <a href="{{ route('admin.talents.index') }}" class="btn-mafia bg-transparent border-mafia-border text-mafia-muted hover:border-mafia-red">
                Annuler
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('couleur_pick').addEventListener('input', function() {
    document.getElementById('couleur_txt').value = this.value;
});
</script>
@endsection
