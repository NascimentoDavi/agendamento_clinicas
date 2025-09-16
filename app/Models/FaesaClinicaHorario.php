<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaesaClinicaHorario extends Model
{
    protected $table = 'FAESA_CLINICA_HORARIO';

    protected $primaryKey = 'ID_HORARIO';

    public $timestamps = true;

    protected $fillable = [
        'ID_HORARIO',
        'USUARIO',
        'DATA_HORARIO_INICIAL',
        'DATA_HORARIO_FINAL',
        'HR_HORARIO_INICIAL',
        'HR_HORARIO_FINAL',
        'OBSERVACAO',
        'BLOQUEADO',
        'DESCRICAO_HORARIO',
    ];

    protected $casts = [
        'DATA_HORARIO_INICIAL' => 'date',
        'DATA_HORARIO_FINAL' => 'date',
        'HR_HORARIO_INICIAL' => 'string',
        'HR_HORARIO_FINAL' => 'string',
    ];
}
