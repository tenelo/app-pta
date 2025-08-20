<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fichiers_joints', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type');
            $table->unsignedBigInteger('attachable_id');
            $table->string('nom_fichier');
            $table->string('nom_original');
            $table->string('type_mime');
            $table->integer('taille');
            $table->string('chemin_fichier');
            $table->text('description')->nullable();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs');
            $table->timestamps();
            
            $table->index(['attachable_type', 'attachable_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('fichiers_joints');
    }
};