<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('inscriptions', 'classe')) {
                $table->string('classe')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'niveau')) {
                $table->string('niveau')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'section')) {
                $table->string('section')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'classe_id')) {
                $table->unsignedBigInteger('classe_id')->nullable();
            }

            if (!Schema::hasColumn('inscriptions', 'serie_id')) {
                $table->unsignedBigInteger('serie_id')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            foreach (['classe', 'niveau', 'section', 'classe_id', 'serie_id'] as $column) {
                if (Schema::hasColumn('inscriptions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
