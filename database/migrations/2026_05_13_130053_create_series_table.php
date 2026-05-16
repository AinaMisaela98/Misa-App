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
    Schema::create('series', function (Blueprint $table) {
        $table->id();
        $table->foreignId('classe_id')->constrained('classes')->cascadeOnDelete();
        $table->string('nom_serie');
        $table->timestamps();
    });
  }
};
