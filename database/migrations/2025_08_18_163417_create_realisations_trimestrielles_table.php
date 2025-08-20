<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('realisations_trimestrielles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activite_id')->constrained('activites')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->year('annee');
            $table->integer('trimestre');

            // Réalisations quantitatives
            $table->string('realisation_quantitative')->nullable();
            $table->decimal('taux_realisation', 5, 2)->nullable();

            // Réalisations financières
            $table->decimal('budget_execute', 15, 2)->nullable();
            $table->decimal('taux_execution_budgetaire', 5, 2)->nullable();

            // Informations qualitatives
            $table->text('resultats_obtenus')->nullable();
            $table->text('difficultes_rencontrees')->nullable();
            $table->text('mesures_correctives')->nullable();
            $table->text('recommandations')->nullable();

            // Statut de validation
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete'])->default('brouillon');
            $table->text('commentaires_validation')->nullable();
            $table->foreignId('validateur_id')->nullable()->constrained('utilisateurs');
            $table->timestamp('date_validation')->nullable();
            $table->date('date_saisie');

            $table->timestamps();

            $table->unique(['activite_id', 'annee', 'trimestre']);
            $table->index(['annee', 'trimestre']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('realisations_trimestrielles');
    }
};
