<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AKWABA STIC 25')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    @php $p = $parametres ?? \App\Models\Parametre::current(); @endphp
    <header class="border-b border-mafia-border bg-mafia-soft/90 backdrop-blur sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4 flex flex-wrap items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-display text-2xl text-mafia-text hover:text-mafia-red-bright transition">
                {{ $p->nom_evenement }}
            </a>
            <div class="flex items-center gap-4">
                @if($p->votesAreOpen())
                    <span class="flex items-center gap-2 text-sm text-green-400" aria-label="Votes ouverts">
                        <svg width="12" height="12" aria-hidden="true"><circle cx="6" cy="6" r="5" fill="#22c55e"/></svg>
                        Votes ouverts
                    </span>
                @else
                    <span class="flex items-center gap-2 text-sm text-mafia-red-bright" aria-label="Votes fermés">
                        <svg width="12" height="12" aria-hidden="true"><circle cx="6" cy="6" r="5" fill="#cc0000"/></svg>
                        Votes fermés
                    </span>
                @endif
                <a href="{{ route('resultats') }}" class="btn-outline-mafia text-sm">Résultats</a>
            </div>
        </div>
        @if($p->date_fin_vote && $p->votesAreOpen())
            <div class="text-center pb-3 text-mafia-muted text-sm">
                Fin des votes dans : <span id="vote-countdown" class="text-mafia-red-bright font-semibold" data-end="{{ $p->date_fin_vote->toIso8601String() }}">--</span>
            </div>
        @endif
    </header>

    <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-8">
        @if(session('status'))
            <div class="mb-6 p-4 border border-mafia-gold/50 bg-mafia-card rounded text-mafia-gold">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <footer class="border-t border-mafia-border py-6 text-center text-mafia-muted text-sm">
        &copy; {{ date('Y') }} {{ $p->nom_evenement }} — Vote officiel
    </footer>
</body>
</html>
