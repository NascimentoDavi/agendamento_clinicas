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
        Schema::create('faesa_clinica_psicologo_disponibilidade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('PSICOLOGO_ID')
                ->constrained('faesa_clinica_psicologo')
                ->onDelete('cascade');

            $table->tinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
