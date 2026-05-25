<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vider dans l'ordre (clés étrangères respectées)
        if (Schema::hasTable('reactions'))       DB::table('reactions')->delete();
        if (Schema::hasTable('stats_horaires'))  DB::table('stats_horaires')->delete();
        if (Schema::hasTable('votes'))           DB::table('votes')->delete();
        if (Schema::hasTable('candidats'))       DB::table('candidats')->delete();
        if (Schema::hasTable('talents'))         DB::table('talents')->delete();
        if (Schema::hasTable('logs_admin'))      DB::table('logs_admin')->delete();

        // Remettre les auto-increments à 1
        $tables = ['reactions', 'stats_horaires', 'votes', 'candidats', 'talents', 'logs_admin'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
            }
        }
    }

    public function down(): void
    {
        // Irréversible
    }
};
