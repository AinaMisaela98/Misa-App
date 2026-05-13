<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmes', function (Blueprint $table) {

            $table->id();

            // 🔥 relation avec classes
            $table->foreignId('classe_id')
                ->constrained()
                ->onDelete('cascade');

            // 🔥 nom du programme
            $table->string('nom');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programmes');
    }
};