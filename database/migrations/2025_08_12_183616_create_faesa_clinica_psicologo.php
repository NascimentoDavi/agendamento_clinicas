<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faesa_clinica_psicologo', function (Blueprint $table) {
            $table->id();
            $table->string('NOME_COMPL');
            $table->string('CPF');
            $table->string('MATRICULA');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faesa_clinica_psicologo');
    }
};
