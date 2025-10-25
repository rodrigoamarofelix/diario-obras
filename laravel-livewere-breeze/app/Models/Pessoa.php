<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;

class Pessoa extends Model
{
    use SoftDeletes, Auditable;

    protected $fillable = [
        'nome',
        'cpf',
        'lotacao_id',
        'status',
        'status_validacao',
        'observacoes_validacao',
        'data_validacao',
        'data_ultima_tentativa',
        'tentativas_validacao',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
            'data_validacao' => 'datetime',
            'data_ultima_tentativa' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Lotacao
     */
    public function lotacao(): BelongsTo
    {
        return $this->belongsTo(Lotacao::class);
    }

    /**
     * Scope para pessoas ativas
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para pessoas inativas
     */
    public function scopeInativo($query)
    {
        return $query->where('status', 'inativo');
    }

    /**
     * Accessor para formatar CPF
     */
    public function getCpfFormatadoAttribute(): string
    {
        $cpf = preg_replace('/\D/', '', $this->cpf);
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
    }

    /**
     * Mutator para CPF (remove formatação e garante 11 dígitos)
     */
    public function setCpfAttribute($value)
    {
        // Remove todos os caracteres não numéricos
        $cpf = preg_replace('/\D/', '', $value);

        // Garante que tenha exatamente 11 dígitos
        if (strlen($cpf) > 11) {
            $cpf = substr($cpf, 0, 11);
        }

        $this->attributes['cpf'] = $cpf;
    }

    /**
     * Accessor para status formatado
     */
    public function getStatusFormatadoAttribute(): string
    {
        return $this->status === 'ativo' ? 'Ativo' : 'Inativo';
    }

    /**
     * Verifica se esta pessoa foi reativada (tem data de criação diferente da atualização)
     */
    public function getFoiReativadaAttribute()
    {
        return $this->created_at->format('Y-m-d H:i:s') !== $this->updated_at->format('Y-m-d H:i:s');
    }

    /**
     * Scope para buscar pessoas ativas (não excluídas)
     */
    public function scopeAtivas($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope para buscar pessoas excluídas
     */
    public function scopeExcluidas($query)
    {
        return $query->onlyTrashed();
    }

    /**
     * Scope para pessoas com validação pendente
     */
    public function scopePendenteValidacao($query)
    {
        return $query->where('status_validacao', 'pendente');
    }

    /**
     * Scope para pessoas validadas
     */
    public function scopeValidadas($query)
    {
        return $query->where('status_validacao', 'validado');
    }

    /**
     * Scope para pessoas rejeitadas
     */
    public function scopeRejeitadas($query)
    {
        return $query->where('status_validacao', 'rejeitado');
    }

    /**
     * Accessor para status de validação formatado
     */
    public function getStatusValidacaoFormatadoAttribute(): string
    {
        return match($this->status_validacao) {
            'pendente' => 'Pendente',
            'validado' => 'Validado',
            'rejeitado' => 'Rejeitado',
            default => 'Desconhecido'
        };
    }

    /**
     * Verifica se a pessoa pode ser editada
     */
    public function podeSerEditada(): bool
    {
        return $this->status_validacao === 'pendente' || $this->status_validacao === 'validado';
    }

    /**
     * Marca como validada
     */
    public function marcarComoValidada(): void
    {
        $this->update([
            'status_validacao' => 'validado',
            'status' => 'ativo', // Status da pessoa fica ativo quando validado
            'data_validacao' => now(),
            'observacoes_validacao' => 'CPF validado com sucesso na Receita Federal'
        ]);
    }

    /**
     * Marca como rejeitada
     */
    public function marcarComoRejeitada(string $motivo): void
    {
        $this->update([
            'status_validacao' => 'rejeitado',
            'status' => 'inativo', // Status da pessoa fica inativo quando rejeitado
            'data_validacao' => now(),
            'observacoes_validacao' => $motivo
        ]);
    }

    /**
     * Incrementa tentativas de validação
     */
    public function incrementarTentativas(): void
    {
        $this->increment('tentativas_validacao');
        $this->update(['data_ultima_tentativa' => now()]);
    }
}
