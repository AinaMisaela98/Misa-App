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
        $table->foreignId('niveau_id')
            ->nullable()
            ->after('id')
            ->constrained('niveaux')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('classes', function (Blueprint $table) {
        $table->dropConstrainedForeignId('niveau_id');
    });
}
};
