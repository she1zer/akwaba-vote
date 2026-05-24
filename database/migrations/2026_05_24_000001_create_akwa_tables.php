<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres', function (Blueprint $table) {
            $table->id();
            $table->string('nom_evenement')->default('AKWABA STIC 25');
            $table->text('message_accueil')->nullable();
            $table->dateTime('date_debut_vote')->nullable();
            $table->dateTime('date_fin_vote')->nullable();
            $table->boolean('votes_ouverts')->default(true);
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('talents', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('icone_svg')->nullable();
            $table->boolean('votes_actifs')->default(true);
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('candidats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->string('nom_complet');
            $table->string('photo')->nullable();
            $table->string('photo_thumb')->nullable();
            $table->timestamps();
        });

        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('talent_id')->constrained('talents')->cascadeOnDelete();
            $table->foreignId('candidat_id')->constrained()->cascadeOnDelete();
            $table->string('session_id')->index();
            $table->string('ip_address', 45)->index();
            $table->string('user_agent')->nullable();
            $table->boolean('is_valid')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['talent_id', 'candidat_id']);
        });

        Schema::create('logs_admin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admins')->cascadeOnDelete();
            $table->string('action');
            $table->text('detail')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_admin');
        Schema::dropIfExists('votes');
        Schema::dropIfExists('candidats');
        Schema::dropIfExists('talents');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('parametres');
    }
};
