<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained('actions')->onDelete('cascade');
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->year('annee');
            $table->string('numero_activite');
            $table->text('libelle_activite');
            
            // Classification des réformes
            $table->boolean('reforme_identifiee')->default(false);
            $table->boolean('reforme_2023')->default(false);
            $table->boolean('reforme_cle')->default(false);
            $table->boolean('realisation_majeure')->default(false);
            
            // Indicateurs et réalisations
            $table->string('indicateur')->nullable();
            $table->string('realisation_2022')->nullable();
            
            // Objectifs trimestriels
            $table->string('objectif_trim1')->nullable();
            $table->string('objectif_trim2')->nullable();
            $table->string('objectif_trim3')->nullable();
            $table->string('objectif_trim4')->nullable();
            $table->string('objectif_annuel')->nullable();
            
            // Localisation et responsabilité
            $table->text('zones_execution')->nullable();
            $table->string('structure_responsable')->nullable();
            
            // Données budgétaires
            $table->decimal('budget_alloue_2022', 15, 2)->nullable();
            $table->decimal('cout_investissement_2023', 15, 2)->nullable();
            $table->decimal('cout_biens_services_2023', 15, 2)->nullable();
            $table->decimal('cout_transfert_2023', 15, 2)->nullable();
            $table->decimal('cout_personnel_2023', 15, 2)->nullable();
            $table->decimal('cout_total_2023', 15, 2)->nullable();
            $table->decimal('cout_prevu_2024', 15, 2)->nullable();
            $table->decimal('cout_prevu_2025', 15, 2)->nullable();
            
            // Références
            $table->string('reference_pnd')->nullable();
            $table->string('programme_dppd')->nullable();
            
            // Suivi et validation
            $table->enum('statut', ['brouillon', 'soumis', 'valide', 'rejete'])->default('brouillon');
            $table->text('commentaires')->nullable();
            $table->text('difficultes')->nullable();
            $table->text('recommandations')->nullable();
            $table->foreignId('validateur_id')->nullable()->constrained('utilisateurs');
            $table->timestamp('date_validation')->nullable();
            $table->date('date_saisie');
            
            $table->timestamps();
            
            $table->index(['annee', 'statut']);
            $table->index(['action_id', 'annee']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activites');
    }
};