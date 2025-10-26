<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialObra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'projeto_id',
        'atividade_id',
        'nome_material',
        'descricao',
        'unidade_medida',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'tipo_movimento',
        'data_movimento',
        'fornecedor',
        'nota_fiscal',
        'observacoes',
        'responsavel_id',
        'created_by'
    ];

    protected $casts = [
        'data_movimento' => 'date',
        'quantidade' => 'decimal:3',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(AtividadeObra::class, 'atividade_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
