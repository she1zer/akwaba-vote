<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Admin') — AKWABA STIC 25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-mafia-black flex">
    @php
        $nbEnAttente = \App\Models\Candidat::where('statut', 'en_attente')->count();
    @endphp

    <aside class="w-64 bg-black border-r border-mafia-border flex-shrink-0 hidden md:flex flex-col">
        <div class="p-6 border-b border-mafia-border">
            <p class="font-display text-xl text-mafia-red">Admin</p>
            <p class="text-mafia-muted text-sm">AKWABA STIC 25</p>
        </div>
        <nav class="flex-1 p-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.dashboard') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">Dashboard</a>
            <a href="{{ route('admin.talents.index') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.talents.*') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">Talents</a>
            <a href="{{ route('admin.candidats.index') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.candidats.*') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">Candidats</a>

            {{-- Modération avec badge si candidatures en attente --}}
            <a href="{{ route('admin.moderation') }}" class="flex items-center justify-between px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.moderation*') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">
                <span>🛡 Modération</span>
                @if($nbEnAttente > 0)
                <span class="text-xs bg-mafia-red text-white rounded-full px-2 py-0.5 font-bold">{{ $nbEnAttente }}</span>
                @endif
            </a>

            <a href="{{ route('admin.statistiques') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.statistiques*') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">📊 Statistiques</a>
            <a href="{{ route('admin.parametres.edit') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.parametres.*') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">Paramètres</a>
            <a href="{{ route('admin.qrcode') }}" class="block px-3 py-2 rounded hover:bg-mafia-card {{ request()->routeIs('admin.qrcode') ? 'bg-mafia-card text-mafia-red-bright' : '' }}">QR Code</a>
            <div class="pt-2 border-t border-mafia-border mt-2">
                <p class="text-mafia-muted text-xs px-3 mb-1 uppercase tracking-wider">Exports</p>
                <a href="{{ route('admin.export.csv') }}" class="block px-3 py-2 rounded hover:bg-mafia-card text-mafia-muted hover:text-mafia-text">CSV résultats</a>
                <a href="{{ route('admin.export.csv.bruts') }}" class="block px-3 py-2 rounded hover:bg-mafia-card text-mafia-muted hover:text-mafia-text">CSV votes bruts</a>
                <a href="{{ route('admin.export.pdf') }}" class="block px-3 py-2 rounded hover:bg-mafia-card text-mafia-muted hover:text-mafia-text">PDF rapport</a>
            </div>
        </nav>
        <form method="POST" action="{{ route('admin.logout') }}" class="p-4 border-t border-mafia-border">
            @csrf
            <button type="submit" class="text-mafia-muted hover:text-mafia-red text-sm w-full text-left">Déconnexion</button>
        </form>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-mafia-soft border-b border-mafia-border px-6 py-4 flex flex-wrap gap-3 items-center justify-between no-print">
            <h1 class="font-display text-xl">@yield('page-title', 'Dashboard')</h1>
            <div class="flex flex-wrap gap-2 items-center">
                @if($nbEnAttente > 0)
                <a href="{{ route('admin.moderation') }}" class="flex items-center gap-2 text-sm text-mafia-gold border border-mafia-gold/40 rounded px-3 py-1.5 hover:bg-mafia-gold/10 transition">
                    ⏳ {{ $nbEnAttente }} candidature(s) à modérer
                </a>
                @endif
                <a href="{{ route('admin.qrcode') }}" class="btn-outline-mafia text-sm">📱 QR Code</a>
                <a href="{{ route('resultats') }}" target="_blank" rel="noopener" class="btn-mafia text-sm relative">
                    📊 Voir les Résultats
                    @if($live ?? false)
                    <span class="absolute -top-2 -right-2 flex items-center gap-1 bg-black border border-mafia-red px-2 py-0.5 text-xs rounded-full">
                        <span class="w-2 h-2 bg-mafia-red-bright rounded-full live-dot" aria-hidden="true"></span>
                        LIVE
                    </span>
                    @endif
                </a>
            </div>
        </header>

        <main class="flex-1 p-6 overflow-auto">
            @if(session('status'))
                <div class="mb-4 p-3 bg-mafia-card border border-mafia-gold/40 rounded text-mafia-gold">{{ session('status') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="mb-4 p-3 bg-mafia-card border border-mafia-red rounded text-mafia-red-bright">
                    <ul class="list-disc pl-5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
