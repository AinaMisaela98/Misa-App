<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            if (!Schema::hasColumn('etudiants', 'prenoms')) {
                $table->string('prenoms')->nullable()->after('nom');
            }

            if (!Schema::hasColumn('etudiants', 'sexe')) {
                $table->enum('sexe', ['Masculin', 'Féminin'])->nullable()->after('prenoms');
            }

            if (!Schema::hasColumn('etudiants', 'classe')) {
                $table->string('classe')->nullable()->after('sexe');
            }
        });
    }

    public function down(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            if (Schema::hasColumn('etudiants', 'classe')) {
                $table->dropColumn('classe');
            }

            if (Schema::hasColumn('etudiants', 'sexe')) {
                $table->dropColumn('sexe');
            }

            if (Schema::hasColumn('etudiants', 'prenoms')) {
                $table->dropColumn('prenoms');
            }
        });
    }
};
