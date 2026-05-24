<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — AKWABA STIC 25</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-mafia-black px-4">
    <div class="text-center card-mafia p-12">
        <p class="font-display text-6xl text-mafia-red mb-4">404</p>
        <h1 class="font-display text-2xl mb-2">Page introuvable</h1>
        <p class="text-mafia-muted mb-6">Cette route n'existe pas dans la famille.</p>
        <a href="{{ url('/') }}" class="btn-mafia">Retour à l'accueil</a>
    </div>
</body>
</html>
