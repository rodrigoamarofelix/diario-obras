<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Auditable;

class Contrato extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'numero',
        'descricao',
        'data_inicio',
        'data_fim',
        'gestor_id',
        'fiscal_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
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
     * Scope para contratos ativos
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para contratos inativos
     */
    public function scopeInativo($query)
    {
        return $query->where('status', 'inativo');
    }

    /**
     * Scope para contratos vencidos
     */
    public function scopeVencido($query)
    {
        return $query->where('status', 'vencido');
    }

    /**
     * Scope para contratos suspensos
     */
    public function scopeSuspenso($query)
    {
        return $query->where('status', 'suspenso');
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        return match($this->status) {
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            'vencido' => 'Vencido',
            'suspenso' => 'Suspenso',
            default => 'Ativo'
        };
    }

    /**
     * Accessor para verificar se está vencido
     */
    public function getEstaVencidoAttribute(): bool
    {
        return $this->data_fim < now()->toDateString();
    }

    /**
     * Relacionamento com medições
     */
    public function medicoes(): HasMany
    {
        return $this->hasMany(Medicao::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com pagamentos
     */
    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class)->orderBy('created_at', 'desc');
    }

    /**
     * Relacionamento com histórico de responsáveis
     */
    public function responsaveis(): HasMany
    {
        return $this->hasMany(ContratoResponsavel::class);
    }

    /**
     * Relacionamento com responsável atual
     */
    public function responsavelAtual(): HasMany
    {
        return $this->hasMany(ContratoResponsavel::class)->ativo();
    }

    /**
     * Relacionamento com anexos
     */
    public function anexos(): HasMany
    {
        return $this->hasMany(ContratoAnexo::class)->orderBy('created_at', 'desc');
    }

    /**
     * Accessor para gestor atual
     */
    public function getGestorAtualAttribute()
    {
        $responsavelAtual = $this->responsaveis()->ativo()->first();
        return $responsavelAtual ? $responsavelAtual->gestor : $this->gestor;
    }

    /**
     * Accessor para fiscal atual
     */
    public function getFiscalAtualAttribute()
    {
        $responsavelAtual = $this->responsaveis()->ativo()->first();
        return $responsavelAtual ? $responsavelAtual->fiscal : $this->fiscal;
    }

    /**
     * Accessor para dias restantes
     */
    public function getDiasRestantesAttribute(): int
    {
        return now()->diffInDays($this->data_fim, false);
    }
}
