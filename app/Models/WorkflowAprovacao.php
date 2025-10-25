<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Carbon\Carbon;

class WorkflowAprovacao extends Model
{
    use HasFactory;

    protected $table = 'workflow_aprovacoes';

    protected $fillable = [
        'model_type',
        'model_id',
        'tipo',
        'status',
        'solicitante_id',
        'aprovador_id',
        'aprovado_por',
        'aprovado_em',
        'comentarios',
        'justificativa_rejeicao',
        'nivel_aprovacao',
        'nivel_maximo',
        'valor',
        'dados_extras',
        'prazo_aprovacao',
        'urgente',
    ];

    protected $casts = [
        'dados_extras' => 'array',
        'aprovado_em' => 'datetime',
        'prazo_aprovacao' => 'datetime',
        'valor' => 'decimal:2',
        'urgente' => 'boolean',
    ];

    /**
     * Relacionamento polimórfico com o modelo relacionado
     */
    public function model(): MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }

    /**
     * Usuário que solicitou a aprovação
     */
    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    /**
     * Usuário responsável por aprovar
     */
    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovador_id');
    }

    /**
     * Usuário que aprovou
     */
    public function aprovadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    /**
     * Scopes para filtros
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmAnalise($query)
    {
        return $query->where('status', 'em_analise');
    }

    public function scopeAprovados($query)
    {
        return $query->where('status', 'aprovado');
    }

    public function scopeRejeitados($query)
    {
        return $query->where('status', 'rejeitado');
    }

    public function scopeUrgentes($query)
    {
        return $query->where('urgente', true);
    }

    public function scopeVencidos($query)
    {
        return $query->where('prazo_aprovacao', '<', now())
                    ->whereIn('status', ['pendente', 'em_analise']);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorAprovador($query, $aprovadorId)
    {
        return $query->where('aprovador_id', $aprovadorId);
    }

    /**
     * Accessors
     */
    public function getStatusFormatadoAttribute()
    {
        $statuses = [
            'pendente' => 'Pendente',
            'em_analise' => 'Em Análise',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
            'suspenso' => 'Suspenso',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusCorAttribute()
    {
        $cores = [
            'pendente' => 'warning',
            'em_analise' => 'info',
            'aprovado' => 'success',
            'rejeitado' => 'danger',
            'suspenso' => 'secondary',
        ];

        return $cores[$this->status] ?? 'secondary';
    }

    public function getValorFormatadoAttribute()
    {
        return $this->valor ? 'R$ ' . number_format($this->valor, 2, ',', '.') : null;
    }

    public function getTempoDecorridoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getTempoRestanteAttribute()
    {
        if (!$this->prazo_aprovacao) {
            return null;
        }

        if ($this->prazo_aprovacao->isPast()) {
            return 'Vencido';
        }

        return $this->prazo_aprovacao->diffForHumans();
    }

    /**
     * Métodos de negócio
     */
    public function podeSerAprovadoPor($userId)
    {
        return $this->aprovador_id === $userId &&
               in_array($this->status, ['pendente', 'em_analise']);
    }

    public function podeSerRejeitadoPor($userId)
    {
        return $this->aprovador_id === $userId &&
               in_array($this->status, ['pendente', 'em_analise']);
    }

    public function podeSerVisualizadoPor($userId)
    {
        $user = User::find($userId);

        // Master e Admin podem ver tudo
        if (in_array($user->profile, ['master', 'admin'])) {
            return true;
        }

        // Usuário pode ver próprias solicitações
        if ($this->solicitante_id === $userId) {
            return true;
        }

        // Aprovador pode ver itens que deve aprovar
        if ($this->aprovador_id === $userId) {
            return true;
        }

        return false;
    }

    public function estaVencido()
    {
        return $this->prazo_aprovacao &&
               $this->prazo_aprovacao->isPast() &&
               in_array($this->status, ['pendente', 'em_analise']);
    }

    public function marcarComoEmAnalise()
    {
        $this->update(['status' => 'em_analise']);
    }

    public function aprovar($aprovadorId, $comentarios = null)
    {
        $this->update([
            'status' => 'aprovado',
            'aprovado_por' => $aprovadorId,
            'aprovado_em' => now(),
            'comentarios' => $comentarios,
        ]);

        // Notificar próximo nível se necessário
        $this->notificarProximoNivel();
    }

    public function rejeitar($aprovadorId, $justificativa)
    {
        $this->update([
            'status' => 'rejeitado',
            'aprovado_por' => $aprovadorId,
            'aprovado_em' => now(),
            'justificativa_rejeicao' => $justificativa,
        ]);
    }

    public function suspender($aprovadorId, $comentarios)
    {
        $this->update([
            'status' => 'suspenso',
            'aprovado_por' => $aprovadorId,
            'aprovado_em' => now(),
            'comentarios' => $comentarios,
        ]);
    }

    private function notificarProximoNivel()
    {
        // Implementar lógica para próximo nível de aprovação
        // Por enquanto, apenas log
        \Log::info("Aprovação {$this->id} aprovada no nível {$this->nivel_aprovacao}");
    }

    /**
     * Métodos estáticos para criação de workflows
     */
    public static function criarParaMedicao($medicaoId, $solicitanteId, $valor = null)
    {
        $medicao = Medicao::find($medicaoId);
        $aprovadorId = self::determinarAprovador('medicao', $valor);

        return self::create([
            'model_type' => Medicao::class,
            'model_id' => $medicaoId,
            'tipo' => 'medicao',
            'status' => 'pendente',
            'solicitante_id' => $solicitanteId,
            'aprovador_id' => $aprovadorId,
            'valor' => $valor ?? $medicao->valor_total,
            'nivel_aprovacao' => 1,
            'nivel_maximo' => self::determinarNivelMaximo('medicao', $valor),
            'prazo_aprovacao' => now()->addDays(3),
            'urgente' => $valor && $valor > 50000,
        ]);
    }

    public static function criarParaPagamento($pagamentoId, $solicitanteId, $valor = null)
    {
        $pagamento = Pagamento::find($pagamentoId);
        $aprovadorId = self::determinarAprovador('pagamento', $valor);

        return self::create([
            'model_type' => Pagamento::class,
            'model_id' => $pagamentoId,
            'tipo' => 'pagamento',
            'status' => 'pendente',
            'solicitante_id' => $solicitanteId,
            'aprovador_id' => $aprovadorId,
            'valor' => $valor ?? $pagamento->valor_pagamento,
            'nivel_aprovacao' => 1,
            'nivel_maximo' => self::determinarNivelMaximo('pagamento', $valor),
            'prazo_aprovacao' => now()->addDays(2),
            'urgente' => $valor && $valor > 100000,
        ]);
    }

    private static function determinarAprovador($tipo, $valor)
    {
        // Lógica para determinar quem deve aprovar baseado no valor e tipo
        if ($valor <= 5000) {
            return User::where('profile', 'admin')->first()?->id;
        } elseif ($valor <= 50000) {
            return User::where('profile', 'master')->first()?->id;
        } else {
            return User::where('profile', 'master')->first()?->id;
        }
    }

    private static function determinarNivelMaximo($tipo, $valor)
    {
        // Lógica para determinar quantos níveis de aprovação são necessários
        if ($valor <= 5000) {
            return 1;
        } elseif ($valor <= 50000) {
            return 2;
        } else {
            return 3;
        }
    }
}