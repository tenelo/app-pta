<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('effets', function (Blueprint $table) {
            $table->id();
            $table->year('annee');
            $table->string('numero_effet');
            $table->text('libelle_effet');
            $table->text('description')->nullable();
            $table->foreignId('structure_id')->constrained('structures');
            $table->decimal('budget_total_prevu', 15, 2)->nullable();
            $table->decimal('budget_total_execute', 15, 2)->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
            
            $table->unique(['annee', 'numero_effet', 'structure_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('effets');
    }
};