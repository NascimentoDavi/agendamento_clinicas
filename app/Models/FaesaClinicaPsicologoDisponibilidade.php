<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaesaClinicaPsicologoDisponibilidade extends Model
{
    protected $table = 'FAESA_CLINICA_PSICOLOGO_DISPONIBILIDADE';

    protected $fillable = [
        'PSICOLOGO_ID',
        'dia_semana',
        'hora_inicio',
        'hora_fim',
    ];

    public function psicologo()
    {
        return $this->belongsTo(FaesaClinicaPsicologo::class, 'PSICOLOGO_ID');
    }
}
