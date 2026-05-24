@extends('layouts.admin')

@section('page-title', $candidat->exists ? 'Modifier candidat' : 'Nouveau candidat')

@section('content')
<div class="max-w-2xl">
    <form method="POST"
          action="{{ $candidat->exists ? route('admin.candidats.update', $candidat) : route('admin.candidats.store') }}"
          enctype="multipart/form-data"
          class="card-mafia p-6 space-y-5">
        @csrf
        @if($candidat->exists)
            @method('PUT')
        @endif

        {{-- Talent --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Talent *</label>
            <select name="talent_id" class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm" required>
                <option value="">— Choisir un talent —</option>
                @foreach($talents as $t)
                <option value="{{ $t->id }}" {{ old('talent_id', $candidat->talent_id) == $t->id ? 'selected' : '' }}>
                    {{ $t->nom }}
                </option>
                @endforeach
            </select>
            @error('talent_id')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Nom --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Nom complet *</label>
            <input type="text" name="nom_complet" value="{{ old('nom_complet', $candidat->nom_complet) }}"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm" required>
            @error('nom_complet')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Slogan --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Slogan <span class="text-xs">(max 200 car.)</span></label>
            <input type="text" name="slogan" value="{{ old('slogan', $candidat->slogan) }}"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm"
                   placeholder="Son slogan de campagne…">
            @error('slogan')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Bio --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Bio <span class="text-xs">(max 1000 car.)</span></label>
            <textarea name="bio" rows="3"
                      class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm"
                      placeholder="Quelques mots de présentation…">{{ old('bio', $candidat->bio) }}</textarea>
            @error('bio')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Genre + Ordre --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Genre</label>
                <select name="genre" class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
                    <option value="">—</option>
                    <option value="M" {{ old('genre', $candidat->genre) === 'M' ? 'selected' : '' }}>Masculin</option>
                    <option value="F" {{ old('genre', $candidat->genre) === 'F' ? 'selected' : '' }}>Féminin</option>
                    <option value="autre" {{ old('genre', $candidat->genre) === 'autre' ? 'selected' : '' }}>Autre</option>
                </select>
            </div>
            <div>
                <label class="block text-sm text-mafia-muted mb-1">Ordre d'affichage</label>
                <input type="number" name="ordre" value="{{ old('ordre', $candidat->ordre ?? 0) }}" min="0"
                       class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
            </div>
        </div>

        {{-- Contact email --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Email contact <span class="text-xs">(non affiché)</span></label>
            <input type="email" name="contact_email" value="{{ old('contact_email', $candidat->contact_email) }}"
                   class="w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-sm">
        </div>

        {{-- Photo --}}
        <div>
            <label class="block text-sm text-mafia-muted mb-1">Photo</label>
            @if($candidat->photoUrl())
                <div class="mb-2 flex items-center gap-3">
                    <img src="{{ $candidat->photoUrl() }}" alt="" class="w-16 h-16 object-cover rounded border border-mafia-border">
                    <span class="text-xs text-mafia-muted">Photo actuelle</span>
                </div>
            @endif
            <input type="file" name="photo" accept="image/*"
                   class="w-full text-sm text-mafia-muted file:mr-3 file:py-1 file:px-3 file:rounded file:border file:border-mafia-border file:bg-mafia-soft file:text-sm file:text-mafia-muted">
            <p class="text-xs text-mafia-muted mt-1">JPG, PNG, WEBP — max 4 Mo</p>
            @error('photo')<p class="text-xs text-mafia-red mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Actif --}}
        <div class="flex items-center gap-2">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   {{ old('is_active', $candidat->is_active ?? true) ? 'checked' : '' }}
                   class="accent-mafia-red">
            <label for="is_active" class="text-sm">Candidat visible dans le vote</label>
        </div>

        {{-- Boutons --}}
        <div class="flex gap-3 pt-2 border-t border-mafia-border">
            <button type="submit" class="btn-mafia">
                {{ $candidat->exists ? 'Mettre à jour' : 'Ajouter' }}
            </button>
            <a href="{{ route('admin.candidats.index') }}" class="btn-mafia bg-transparent border-mafia-border text-mafia-muted hover:border-mafia-red">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
