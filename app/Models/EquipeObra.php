<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipeObra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'projeto_id',
        'atividade_id',
        'funcionario_id',
        'pessoa_id',
        'data_trabalho',
        'hora_entrada',
        'tipo_almoco',
        'hora_saida_almoco',
        'hora_retorno_almoco',
        'hora_saida',
        'horas_trabalhadas',
        'funcao',
        'atividades_realizadas',
        'observacoes',
        'presente',
        'created_by'
    ];

    protected $casts = [
        'data_trabalho' => 'date',
        'hora_entrada' => 'datetime:H:i',
        'hora_saida_almoco' => 'datetime:H:i',
        'hora_retorno_almoco' => 'datetime:H:i',
        'hora_saida' => 'datetime:H:i',
        'presente' => 'boolean',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(AtividadeObra::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoObra::class);
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
