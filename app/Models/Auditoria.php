<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $fillable = [
        'modelo',
        'modelo_id',
        'acao',
        'dados_anteriores',
        'dados_novos',
        'usuario_id',
        'ip_address',
        'user_agent',
        'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'dados_anteriores' => 'array',
            'dados_novos' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopeModelo($query, $modelo)
    {
        return $query->where('modelo', $modelo);
    }

    /**
     * Scope para filtrar por ação
     */
    public function scopeAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    /**
     * Scope para filtrar por usuário
     */
    public function scopeUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('created_at', [$inicio, $fim]);
    }

    /**
     * Accessor para ação formatada
     */
    public function getAcaoFormatadaAttribute(): string
    {
        $acoes = [
            'created' => 'Criado',
            'updated' => 'Atualizado',
            'deleted' => 'Excluído',
            'restored' => 'Restaurado',
            'manager_changed' => 'Responsável Alterado',
            'supervisor_changed' => 'Fiscal Alterado',
        ];

        return $acoes[$this->acao] ?? ucfirst($this->acao);
    }

    /**
     * Accessor para modelo formatado
     */
    public function getModeloFormatadoAttribute(): string
    {
        $modelos = [
            'Pessoa' => 'Pessoa',
            'Contrato' => 'Contrato',
            'ContratoResponsavel' => 'Responsável de Contrato',
            'Lotacao' => 'Lotação',
            'User' => 'Usuário',
        ];

        return $modelos[$this->modelo] ?? $this->modelo;
    }

    /**
     * Accessor para dados formatados
     */
    public function getDadosFormatadosAttribute(): array
    {
        $dados = [];

        if ($this->dados_anteriores) {
            $dados['anteriores'] = $this->dados_anteriores;
        }

        if ($this->dados_novos) {
            $dados['novos'] = $this->dados_novos;
        }

        return $dados;
    }
}