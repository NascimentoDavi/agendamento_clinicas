<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaesaClinicaAgendamento extends Model
{
    protected $table = 'FAESA_CLINICA_AGENDAMENTO';

    protected $primaryKey = 'ID_AGENDAMENTO';

    public $timestamps = true;

    protected $fillable = [
        'ID_CLINICA',
        'ID_PACIENTE',
        'ID_USUARIO',
        'ID_SERVICO',
        'DT_AGEND',
        'HR_AGEND_INI',
        'HR_AGEND_FIN',
        'STATUS_AGEND',
        'ID_AGEND_REMARCADO',
        'RECORRENCIA',
        'VALOR_AGEND',
        'OBSERVACOES',
        'LOCAL',
        'MENSAGEM',
        'CREATED_AT',
        'UPDATED_AT',
        'STATUS_PAG',
        'ID_ALUNO',
        'ID_SALA',
    ];

    protected $casts = [
        'DT_AGEND' => 'date',
        'HR_AGEND_INI' => 'string',
        'HR_AGEND_FIN' => 'string',
        'RECORRENCIA' => 'string',
        'VALOR_AGEND' => 'decimal:2',
        'STATUS_AGEND' => 'string',
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
    ];

    /**
     * Define um relacionamento BelongsTo com a tabela FAESA_CLINICA
     * Um agendamento pertence a uma clinica.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function clinica(): BelongsTo
    {
        return $this->belongsTo(FaesaClinica::class, 'ID_CLINICA', 'ID_CLINICA');
    }

    /**
     * Define um relacionamento BelongsTo com a tabela FAESA_CLINICA_PACIENTE
     * Um agendamento pertence a um PACIENTE
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(FaesaClinicaPaciente::class, 'ID_PACIENTE');
    }


    /**
     * Define um relacionamento BelongsTo com a tabela FAESA_CLINICA_SERVICO
     * Um agendamento pertence a um SERVICO_CLINICA
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function servico(): BelongsTo
    {
        return $this->belongsTo(FaesaClinicaServico::class, 'ID_SERVICO', 'ID_SERVICO_CLINICA');
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(FaesaClinicaSala::class, 'ID_SALA', 'ID_SALA_CLINICA');
    }

    /**
     * Define um relacionamento BelongsTo com a tabela LY_ALUNO
     * Um ID_ALUNO pertence a um LY_ALUNO
     * 
     * @return Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function aluno(): BelongsTo
    {
        return $this->belongsTo(LyAluno::class, 'ID_ALUNO', 'ALUNO');
    }

    /**
     * Define o relacionamento BelongsTo para o agendamento original que foi remarcado.
     * Self-Referencing (um agendamento pode ter sido remarcado a partir de outro).
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agendamentoOriginal(): BelongsTo
    {
        // ID_AGEND_REMARCADO : Chave estrangeira local,
        // ID_AGENDAMENTO é a chave primária da tabela referenciada (AgendClinPac).
        return $this->belongsTo(FaesaClinicaAgendamento::class, 'ID_AGEND_REMARCADO', 'ID_AGENDAMENTO');
    }

   /**
     * Define o relacionamento HasMany para agendamentos que foram remarcados a partir deste.
     * Self-referencing inverso (um agendamento pode ter N remarcações).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function remarcacoes(): HasMany
    {
        return $this->hasMany(FaesaClinicaAgendamento::class, 'ID_AGEND_REMARCADO', 'ID_AGENDAMENTO');
    }

}
