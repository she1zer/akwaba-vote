<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── candidats ───────────────────────────────────────────────────────
        Schema::table('candidats', function (Blueprint $table) {
            if (! Schema::hasColumn('candidats', 'bio'))
                $table->text('bio')->nullable()->after('nom_complet');
            if (! Schema::hasColumn('candidats', 'slogan'))
                $table->string('slogan', 200)->nullable()->after('bio');
            if (! Schema::hasColumn('candidats', 'genre'))
                $table->enum('genre', ['M', 'F', 'autre'])->nullable()->after('slogan');
            if (! Schema::hasColumn('candidats', 'contact_email'))
                $table->string('contact_email')->nullable()->after('genre');
            if (! Schema::hasColumn('candidats', 'ordre'))
                $table->unsignedInteger('ordre')->default(0)->after('photo_thumb');
            if (! Schema::hasColumn('candidats', 'is_active'))
                $table->boolean('is_active')->default(true)->after('ordre');
            if (! Schema::hasColumn('candidats', 'slug'))
                $table->string('slug')->nullable()->after('nom_complet');
            // Candidature spontanée
            if (! Schema::hasColumn('candidats', 'statut'))
                $table->enum('statut', ['valide', 'en_attente', 'rejete'])->default('valide')->after('is_active');
            if (! Schema::hasColumn('candidats', 'propose_par_ip'))
                $table->string('propose_par_ip', 45)->nullable()->after('statut');
            if (! Schema::hasColumn('candidats', 'propose_par_session'))
                $table->string('propose_par_session')->nullable()->after('propose_par_ip');
            if (! Schema::hasColumn('candidats', 'propose_le'))
                $table->timestamp('propose_le')->nullable()->after('propose_par_session');
            if (! Schema::hasColumn('candidats', 'note_admin'))
                $table->text('note_admin')->nullable()->after('propose_le');
        });

        // ── talents ─────────────────────────────────────────────────────────
        Schema::table('talents', function (Blueprint $table) {
            if (! Schema::hasColumn('talents', 'description'))
                $table->text('description')->nullable()->after('nom');
            if (! Schema::hasColumn('talents', 'couleur_hex'))
                $table->string('couleur_hex', 7)->default('#cc0000')->after('description');
            if (! Schema::hasColumn('talents', 'max_votes_par_ip'))
                $table->unsignedInteger('max_votes_par_ip')->default(1)->after('couleur_hex');
            if (! Schema::hasColumn('talents', 'allow_candidature_spontanee'))
                $table->boolean('allow_candidature_spontanee')->default(false)->after('max_votes_par_ip');
        });

        // ── votes ────────────────────────────────────────────────────────────
        Schema::table('votes', function (Blueprint $table) {
            if (! Schema::hasColumn('votes', 'device_fingerprint'))
                $table->string('device_fingerprint', 64)->nullable()->after('user_agent');
            if (! Schema::hasColumn('votes', 'score_confiance'))
                $table->unsignedTinyInteger('score_confiance')->default(100)->after('device_fingerprint');
            if (! Schema::hasColumn('votes', 'pays'))
                $table->string('pays', 2)->nullable()->after('score_confiance');
            if (! Schema::hasColumn('votes', 'is_flagged'))
                $table->boolean('is_flagged')->default(false)->after('is_valid');
            if (! Schema::hasColumn('votes', 'flag_reason'))
                $table->string('flag_reason')->nullable()->after('is_flagged');
        });

        // ── parametres ───────────────────────────────────────────────────────
        Schema::table('parametres', function (Blueprint $table) {
            if (! Schema::hasColumn('parametres', 'afficher_resultats_live'))
                $table->boolean('afficher_resultats_live')->default(true)->after('logo');
            if (! Schema::hasColumn('parametres', 'afficher_nb_votes'))
                $table->boolean('afficher_nb_votes')->default(true)->after('afficher_resultats_live');
            if (! Schema::hasColumn('parametres', 'couleur_primaire'))
                $table->string('couleur_primaire', 7)->default('#cc0000')->after('afficher_nb_votes');
            if (! Schema::hasColumn('parametres', 'couleur_secondaire'))
                $table->string('couleur_secondaire', 7)->default('#b8960c')->after('couleur_primaire');
            if (! Schema::hasColumn('parametres', 'lien_facebook'))
                $table->string('lien_facebook')->nullable()->after('couleur_secondaire');
            if (! Schema::hasColumn('parametres', 'lien_instagram'))
                $table->string('lien_instagram')->nullable()->after('lien_facebook');
            if (! Schema::hasColumn('parametres', 'reglement'))
                $table->text('reglement')->nullable()->after('lien_instagram');
        });

        // ── nouvelles tables ─────────────────────────────────────────────────
        if (! Schema::hasTable('reactions')) {
            Schema::create('reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
                $table->string('session_id')->index();
                $table->string('ip_address', 45)->index();
                $table->enum('type', ['coeur', 'feu', 'star', 'clap'])->default('coeur');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (! Schema::hasTable('stats_horaires')) {
            Schema::create('stats_horaires', function (Blueprint $table) {
                $table->id();
                $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
                $table->foreignId('candidat_id')->constrained('candidats')->cascadeOnDelete();
                $table->unsignedSmallInteger('heure');
                $table->date('date_stat');
                $table->unsignedInteger('nb_votes')->default(0);
                $table->unique(['talent_id', 'candidat_id', 'date_stat', 'heure']);
                $table->index(['date_stat', 'heure']);
            });
        }

        if (! Schema::hasTable('sessions_vote')) {
            Schema::create('sessions_vote', function (Blueprint $table) {
                $table->id();
                $table->string('token', 64)->unique();
                $table->string('label')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('nb_votes_emis')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions_vote');
        Schema::dropIfExists('stats_horaires');
        Schema::dropIfExists('reactions');
    }
};
