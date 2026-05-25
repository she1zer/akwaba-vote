@extends('layouts.public')

@section('title', 'AKWABA STIC 25')

@section('content')
<div class="text-center py-12">
    <h1 class="font-display text-5xl mb-4">AKWABA STIC 25</h1>
    <p class="text-mafia-muted mb-8">Plateforme de vote officielle</p>
    <a href="{{ route('home') }}" class="btn-mafia text-lg px-8">Accéder au vote</a>
</div>
@endsection
