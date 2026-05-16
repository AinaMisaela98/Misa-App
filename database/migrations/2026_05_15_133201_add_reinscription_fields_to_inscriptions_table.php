<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('inscriptions', 'classe_id')) {
                $table->unsignedBigInteger('classe_id')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'serie_id')) {
                $table->unsignedBigInteger('serie_id')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'annee_scolaire')) {
                $table->string('annee_scolaire')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'type')) {
                $table->string('type')->default('inscription');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            if (Schema::hasColumn('inscriptions', 'classe_id')) {
                $table->dropColumn('classe_id');
            }

            if (Schema::hasColumn('inscriptions', 'serie_id')) {
                $table->dropColumn('serie_id');
            }

            if (Schema::hasColumn('inscriptions', 'annee_scolaire')) {
                $table->dropColumn('annee_scolaire');
            }

            if (Schema::hasColumn('inscriptions', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
