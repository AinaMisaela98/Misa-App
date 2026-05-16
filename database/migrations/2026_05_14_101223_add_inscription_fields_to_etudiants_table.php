<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('etudiants', function (Blueprint $table) {
        if (!Schema::hasColumn('etudiants', 'date_naissance')) {
            $table->date('date_naissance')->nullable()->after('sexe');
        }

        if (!Schema::hasColumn('etudiants', 'lieu_naissance')) {
            $table->string('lieu_naissance')->nullable()->after('date_naissance');
        }

        if (!Schema::hasColumn('etudiants', 'classe')) {
            $table->string('classe')->nullable();
        }

        if (!Schema::hasColumn('etudiants', 'section')) {
            $table->string('section')->nullable();
        }

        if (!Schema::hasColumn('etudiants', 'annee_scolaire')) {
            $table->string('annee_scolaire')->nullable();
        }

        if (!Schema::hasColumn('etudiants', 'site')) {
            $table->string('site')->nullable();
        }
    });
}

public function down(): void
{
    Schema::table('etudiants', function (Blueprint $table) {
        $table->dropColumn([
            'date_naissance',
            'lieu_naissance',
            'classe',
            'section',
            'annee_scolaire',
            'site',
        ]);
    });
 }
};
