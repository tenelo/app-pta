<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effet_id')->constrained('effets')->onDelete('cascade');
            $table->string('numero_produit');
            $table->text('libelle_produit');
            $table->text('description')->nullable();
            $table->string('indicateur_principal')->nullable();
            $table->string('realisation_2022')->nullable();
            $table->string('cible_2023')->nullable();
            $table->decimal('budget_total_prevu', 15, 2)->nullable();
            $table->decimal('budget_total_execute', 15, 2)->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produits');
    }
};