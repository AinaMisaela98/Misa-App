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
    Schema::table('classes', function (Blueprint $table) {
        if (!Schema::hasColumn('classes', 'annee_scolaire')) {
            $table->string('annee_scolaire')->nullable()->after('nom_classe');
        }
    });

    Schema::table('series', function (Blueprint $table) {
        if (!Schema::hasColumn('series', 'annee_scolaire')) {
            $table->string('annee_scolaire')->nullable()->after('nom_serie');
        }
    });
}

public function down(): void
{
    Schema::table('classes', function (Blueprint $table) {
        $table->dropColumn('annee_scolaire');
    });

    Schema::table('series', function (Blueprint $table) {
        $table->dropColumn('annee_scolaire');
    });
  }
};
