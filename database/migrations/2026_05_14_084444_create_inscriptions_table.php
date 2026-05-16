<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();

            $table->date('date_inscription')->nullable();
            $table->string('numero_matricule')->nullable();
            $table->string('nom');
            $table->string('prenoms')->nullable();
            $table->string('genre')->nullable();
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance')->nullable();
            $table->string('signe_particulier')->nullable();
            $table->string('allergique')->nullable();
            $table->string('ecole_origine')->nullable();
            $table->string('telephone')->nullable();
            $table->string('adresse')->nullable();
            $table->string('email')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('ville')->nullable();
            $table->string('taille')->nullable();
            $table->string('poids')->nullable();
            $table->string('photo')->nullable();

            $table->string('pere_nom')->nullable();
            $table->string('pere_telephone')->nullable();
            $table->string('pere_profession')->nullable();
            $table->string('mere_nom')->nullable();
            $table->string('mere_telephone')->nullable();
            $table->string('mere_profession')->nullable();

            $table->string('tuteur_nom')->nullable();
            $table->string('tuteur_telephone')->nullable();
            $table->string('tuteur_lien')->nullable();
            $table->string('tuteur_adresse')->nullable();

            $table->string('niveau')->nullable();
            $table->string('formation')->nullable();
            $table->decimal('frais_inscription', 12, 2)->nullable();
            $table->decimal('frais_formation', 12, 2)->nullable();
            $table->string('mode_paiement')->nullable();

            $table->json('activites')->nullable();
            $table->text('observation')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
