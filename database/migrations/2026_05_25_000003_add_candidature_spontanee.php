<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajouter les colonnes pour les candidatures spontanées
        Schema::table('candidats', function (Blueprint $table) {
            // Statut : 'valide' (admin), 'en_attente' (proposé par voteur), 'rejete'
            $table->enum('statut', ['valide', 'en_attente', 'rejete'])->default('valide')->after('is_active');
            // Qui a proposé ce candidat
            $table->string('propose_par_ip', 45)->nullable()->after('statut');
            $table->string('propose_par_session')->nullable()->after('propose_par_ip');
            $table->timestamp('propose_le')->nullable()->after('propose_par_session');
            $table->text('note_admin')->nullable()->after('propose_le'); // note du modérateur
        });

        // Activer ou non la fonctionnalité de proposition par talent
        Schema::table('talents', function (Blueprint $table) {
            $table->boolean('allow_candidature_spontanee')->default(false)->after('max_votes_par_ip');
        });
    }

    public function down(): void
    {
        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn(['statut', 'propose_par_ip', 'propose_par_session', 'propose_le', 'note_admin']);
        });
        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['allow_candidature_spontanee']);
        });
    }
};
