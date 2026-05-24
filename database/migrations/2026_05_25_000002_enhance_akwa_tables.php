<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Amélioration table candidats
        Schema::table('candidats', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('nom_complet');
            $table->string('slogan', 200)->nullable()->after('bio');
            $table->enum('genre', ['M', 'F', 'autre'])->nullable()->after('slogan');
            $table->string('contact_email')->nullable()->after('genre');
            $table->unsignedInteger('ordre')->default(0)->after('photo_thumb');
            $table->boolean('is_active')->default(true)->after('ordre');
            $table->string('slug')->nullable()->after('nom_complet');
        });

        // Amélioration table talents
        Schema::table('talents', function (Blueprint $table) {
            $table->text('description')->nullable()->after('nom');
            $table->string('couleur_hex', 7)->default('#cc0000')->after('description');
            $table->unsignedInteger('max_votes_par_ip')->default(1)->after('couleur_hex');
        });

        // Amélioration table votes — device fingerprint et score de confiance
        Schema::table('votes', function (Blueprint $table) {
            $table->string('device_fingerprint', 64)->nullable()->after('user_agent');
            $table->unsignedTinyInteger('score_confiance')->default(100)->after('device_fingerprint'); // 0-100
            $table->string('pays', 2)->nullable()->after('score_confiance');
            $table->boolean('is_flagged')->default(false)->after('is_valid');
            $table->string('flag_reason')->nullable()->after('is_flagged');
        });

        // Amélioration table parametres
        Schema::table('parametres', function (Blueprint $table) {
            $table->boolean('afficher_resultats_live')->default(true)->after('logo');
            $table->boolean('afficher_nb_votes')->default(true)->after('afficher_resultats_live');
            $table->string('couleur_primaire', 7)->default('#cc0000')->after('afficher_nb_votes');
            $table->string('couleur_secondaire', 7)->default('#b8960c')->after('couleur_primaire');
            $table->string('lien_facebook')->nullable()->after('couleur_secondaire');
            $table->string('lien_instagram')->nullable()->after('lien_facebook');
            $table->text('reglement')->nullable()->after('lien_instagram');
        });

        // Nouvelle table: réactions après vote
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
            $table->string('session_id')->index();
            $table->string('ip_address', 45)->index();
            $table->enum('type', ['coeur', 'feu', 'star', 'clap'])->default('coeur');
            $table->timestamp('created_at')->useCurrent();
        });

        // Nouvelle table: statistiques horaires (cache précalculé)
        Schema::create('stats_horaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
            $table->unsignedSmallInteger('heure'); // 0-23
            $table->date('date_stat');
            $table->unsignedInteger('nb_votes')->default(0);
            $table->unique(['talent_id', 'candidat_id', 'date_stat', 'heure']);
            $table->index(['date_stat', 'heure']);
        });

        // Nouvelle table: sessions de vote (accès public avec token)
        Schema::create('sessions_vote', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->string('label')->nullable(); // ex: "Salle A", "Table 5"
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('nb_votes_emis')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_vote');
        Schema::dropIfExists('stats_horaires');
        Schema::dropIfExists('reactions');

        Schema::table('parametres', function (Blueprint $table) {
            $table->dropColumn(['afficher_resultats_live', 'afficher_nb_votes', 'couleur_primaire',
                'couleur_secondaire', 'lien_facebook', 'lien_instagram', 'reglement']);
        });

        Schema::table('votes', function (Blueprint $table) {
            $table->dropColumn(['device_fingerprint', 'score_confiance', 'pays', 'is_flagged', 'flag_reason']);
        });

        Schema::table('talents', function (Blueprint $table) {
            $table->dropColumn(['description', 'couleur_hex', 'max_votes_par_ip']);
        });

        Schema::table('candidats', function (Blueprint $table) {
            $table->dropColumn(['bio', 'slogan', 'genre', 'contact_email', 'ordre', 'is_active', 'slug']);
        });
    }
};
