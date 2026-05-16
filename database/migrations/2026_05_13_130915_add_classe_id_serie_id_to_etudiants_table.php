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
        $table->foreignId('classe_id')->nullable()->after('sexe')->constrained('classes')->nullOnDelete();
        $table->foreignId('serie_id')->nullable()->after('classe_id')->constrained('series')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('etudiants', function (Blueprint $table) {
        $table->dropConstrainedForeignId('serie_id');
        $table->dropConstrainedForeignId('classe_id');
    });
}
};
