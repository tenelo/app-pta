<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email', 191)->unique(); // Longueur fixée à 191
            $table->string('telephone')->nullable();
            $table->string('matricule', 191)->unique()->nullable(); // Longueur fixée à 191
            $table->string('fonction')->nullable();
            $table->foreignId('structure_id')->constrained('structures');
            $table->enum('role', ['admin', 'directeur', 'operateur', 'consultation'])->default('operateur');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('utilisateurs');
    }
};