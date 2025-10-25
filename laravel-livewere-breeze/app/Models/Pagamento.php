<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Pagamento extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'pagamentos';

    protected $fillable = [
        'medicao_id',
        'contrato_id',
        'numero_pagamento',
        'data_pagamento',
        'valor_pagamento',
        'observacoes',
        'documento_redmine',
        'status',
        'usuario_id',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'valor_pagamento' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Scope para filtrar por status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopePeriodo($query, $inicio, $fim)
    {
        return $query->whereBetween('data_pagamento', [$inicio, $fim]);
    }

    /**
     * Retorna o nome do status em português
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
            'pago' => 'Pago',
            default => 'Pendente'
        };
    }

    /**
     * Retorna o valor do pagamento formatado
     */
    public function getValorPagamentoFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_pagamento, 2, ',', '.');
    }

    /**
     * Relacionamento com medição
     */
    public function medicao()
    {
        return $this->belongsTo(Medicao::class);
    }

    /**
     * Relacionamento com contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para verificar se tem documento do Redmine
     */
    public function getTemDocumentoRedmineAttribute(): bool
    {
        return !empty($this->documento_redmine);
    }

    /**
     * Accessor para link do documento do Redmine
     */
    public function getLinkDocumentoRedmineAttribute(): string
    {
        if (empty($this->documento_redmine)) {
            return '';
        }

        // Se já é uma URL completa, retorna como está
        if (filter_var($this->documento_redmine, FILTER_VALIDATE_URL)) {
            return $this->documento_redmine;
        }

        // Se é apenas um número ou ID, assume que é um ticket do Redmine
        if (is_numeric($this->documento_redmine)) {
            return "https://redmine.exemplo.com/issues/{$this->documento_redmine}";
        }

        return $this->documento_redmine;
    }

    /**
     * Gera o próximo número de pagamento no formato PAG00NUMERO_SEQUENCIAL
     */
    public static function gerarProximoNumero()
    {
        // Busca o último número de pagamento criado
        $ultimoPagamento = self::withTrashed()
            ->where('numero_pagamento', 'LIKE', 'PAG%')
            ->orderBy('numero_pagamento', 'desc')
            ->first();

        if (!$ultimoPagamento) {
            // Se não há pagamentos, começa com PAG001
            return 'PAG001';
        }

        // Extrai o número sequencial do último número
        $ultimoNumero = preg_replace('/^PAG/', '', $ultimoPagamento->numero_pagamento);
        $proximoNumero = (int)$ultimoNumero + 1;

        // Formata o próximo número com zeros à esquerda
        return 'PAG' . str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Sobrescreve o método para tratar campos de relacionamento na auditoria
     */
    protected function gerarObservacaoCampo($campo, $valorAnterior, $valorNovo)
    {
        // Tratar campos de relacionamento
        switch ($campo) {
            case 'medicao_id':
                $medicaoAnterior = $valorAnterior ? Medicao::find($valorAnterior) : null;
                $medicaoNovo = $valorNovo ? Medicao::find($valorNovo) : null;
                $numeroAnterior = $medicaoAnterior ? $medicaoAnterior->numero_medicao : 'nulo';
                $numeroNovo = $medicaoNovo ? $medicaoNovo->numero_medicao : 'nulo';
                return "Medição alterada de {$numeroAnterior} para {$numeroNovo}";

            case 'status':
                $statusAnterior = $this->getStatusNameFromValue($valorAnterior);
                $statusNovo = $this->getStatusNameFromValue($valorNovo);
                return "Status alterado de {$statusAnterior} para {$statusNovo}";

            case 'valor_pagamento':
                $valorAnteriorFormatado = $this->formatarValor($valorAnterior);
                $valorNovoFormatado = $this->formatarValor($valorNovo);
                return "Valor do Pagamento alterado de {$valorAnteriorFormatado} para {$valorNovoFormatado}";

            case 'data_pagamento':
                $dataAnterior = $valorAnterior ? \Carbon\Carbon::parse($valorAnterior)->format('d/m/Y') : 'nulo';
                $dataNovo = $valorNovo ? \Carbon\Carbon::parse($valorNovo)->format('d/m/Y') : 'nulo';
                return "Data do Pagamento alterada de {$dataAnterior} para {$dataNovo}";

            case 'numero_pagamento':
                return "Número do Pagamento alterado de {$valorAnterior} para {$valorNovo}";

            case 'documento_redmine':
                $docAnterior = $valorAnterior ?: 'nulo';
                $docNovo = $valorNovo ?: 'nulo';
                return "Documento Redmine alterado de {$docAnterior} para {$docNovo}";

            case 'observacoes':
                $obsAnterior = $valorAnterior ?: 'nulo';
                $obsNovo = $valorNovo ?: 'nulo';
                return "Observações alteradas de {$obsAnterior} para {$obsNovo}";

            default:
                $nomeCampo = $this->getNomeCampoFormatado($campo);
                $valorAnteriorFormatado = $this->formatarValor($valorAnterior);
                $valorNovoFormatado = $this->formatarValor($valorNovo);
                return "{$nomeCampo} alterado de {$valorAnteriorFormatado} para {$valorNovoFormatado}";
        }
    }

    /**
     * Retorna o nome do status baseado no valor
     */
    private function getStatusNameFromValue($status)
    {
        return match($status) {
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
            'pago' => 'Pago',
            default => $status ?? 'nulo'
        };
    }
}
