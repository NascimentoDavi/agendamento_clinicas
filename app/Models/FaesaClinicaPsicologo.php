<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaesaClinicaPsicologo extends Model
{
    protected $table = 'FAESA_CLINICA_PSICOLOGO';

    protected $fillable = [
        'NOME_COMPL',
        'CPF',
        'MATRICULA'
    ];

    public function disponibilidades()
    {
        return $this->hasMany(FaesaClinicaPsicologoDisponibilidade::class, 'PSICOLOGO_ID');
    }
}
