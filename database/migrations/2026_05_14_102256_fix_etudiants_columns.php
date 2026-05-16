<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etudiants', function (Blueprint $table) {
            if (!Schema::hasColumn('etudiants', 'numero')) {
                $table->string('numero')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'prenoms')) {
                $table->string('prenoms')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'sexe')) {
                $table->string('sexe')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'date_naissance')) {
                $table->date('date_naissance')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'lieu_naissance')) {
                $table->string('lieu_naissance')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'contact')) {
                $table->string('contact')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'adresse')) {
                $table->string('adresse')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('etudiants', 'photo')) {
                $table->string('photo')->nullable();
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
            foreach ([
                'numero',
                'prenoms',
                'sexe',
                'date_naissance',
                'lieu_naissance',
                'contact',
                'adresse',
                'email',
                'photo',
                'classe',
                'section',
                'annee_scolaire',
                'site',
            ] as $column) {
                if (Schema::hasColumn('etudiants', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
