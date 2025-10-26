<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class ContratoResponsavel extends Model
{
    use Auditable;

    protected $table = 'contrato_responsaveis';

    protected $fillable = [
        'contrato_id',
        'gestor_id',
        'fiscal_id',
        'data_inicio',
        'data_fim',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Contrato
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relacionamento com Pessoa (Gestor)
     */
    public function gestor(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'gestor_id');
    }

    /**
     * Relacionamento com Pessoa (Fiscal)
     */
    public function fiscal(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'fiscal_id');
    }

    /**
     * Scope para responsáveis ativos (sem data_fim)
     */
    public function scopeAtivo($query)
    {
        return $query->whereNull('data_fim');
    }

    /**
     * Scope para responsáveis históricos (com data_fim)
     */
    public function scopeHistorico($query)
    {
        return $query->whereNotNull('data_fim');
    }

    /**
     * Accessor para verificar se está ativo
     */
    public function getEstaAtivoAttribute(): bool
    {
        return is_null($this->data_fim);
    }

    /**
     * Accessor para período formatado
     */
    public function getPeriodoFormatadoAttribute(): string
    {
        $dataInicio = is_string($this->data_inicio) ? \Carbon\Carbon::parse($this->data_inicio) : $this->data_inicio;
        $dataInicioFormatada = is_object($dataInicio) ? $dataInicio->format('d/m/Y') : ($dataInicio ?? 'N/A');

        if ($this->esta_ativo) {
            return "De {$dataInicioFormatada} até hoje";
        }

        $dataFim = is_string($this->data_fim) ? \Carbon\Carbon::parse($this->data_fim) : $this->data_fim;
        $dataFimFormatada = is_object($dataFim) ? $dataFim->format('d/m/Y') : ($dataFim ?? 'N/A');

        return "De {$dataInicioFormatada} até {$dataFimFormatada}";
    }
}
