<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('telephone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
            $table->string('telephone')->nullable(false)->change();
        });
    }
};
