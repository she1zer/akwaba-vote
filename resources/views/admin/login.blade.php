<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — AKWABA STIC 25</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-mafia-black px-4">
    <form method="POST" action="{{ route('admin.login.submit') }}" class="card-mafia p-8 w-full max-w-md">
        @csrf
        <h1 class="font-display text-2xl text-center text-mafia-red mb-6">Connexion Admin</h1>
        @if($errors->any())
            <p class="text-mafia-red-bright text-sm mb-4">{{ $errors->first() }}</p>
        @endif
        @if(session('status'))
            <p class="text-mafia-gold text-sm mb-4">{{ session('status') }}</p>
        @endif
        <label class="block mb-4">
            <span class="text-sm text-mafia-muted">Email</span>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-mafia-text">
        </label>
        <label class="block mb-6">
            <span class="text-sm text-mafia-muted">Mot de passe</span>
            <input type="password" name="password" required class="mt-1 w-full bg-mafia-soft border border-mafia-border rounded px-3 py-2 text-mafia-text">
        </label>
        <label class="flex items-center gap-2 mb-6 text-sm text-mafia-muted">
            <input type="checkbox" name="remember"> Se souvenir de moi
        </label>
        <button type="submit" class="btn-mafia w-full">Se connecter</button>
    </form>
</body>
</html>
