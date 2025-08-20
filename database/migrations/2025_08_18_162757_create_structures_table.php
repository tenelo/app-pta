<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->string('nom_structure');
            $table->string('code_structure')->unique();
            $table->string('type_structure')->default('Ministère');
            $table->text('description')->nullable();
            $table->string('responsable')->nullable();
            $table->string('email')->nullable();
            $table->string('telephone')->nullable();
            $table->enum('statut', ['actif', 'inactif'])->default('actif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('structures');
    }
};

