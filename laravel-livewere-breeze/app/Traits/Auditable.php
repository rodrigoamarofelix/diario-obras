<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Variável estática para controlar auditoria
     */
    protected static $auditingDisabled = false;

    /**
     * Boot do trait - registra os eventos do modelo
     */
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            if (Auth::check()) {
                $model->auditar('created', null, $model->getAttributes());
            }
        });

        static::updated(function ($model) {
            if (Auth::check() && !static::$auditingDisabled) {
                $observacoes = $model->gerarObservacoesAlteracao($model->getOriginal(), $model->getChanges());
                $model->auditar('updated', $model->getOriginal(), $model->getChanges(), $observacoes);
            }
        });

        static::deleted(function ($model) {
            if (Auth::check()) {
                $model->auditar('deleted', $model->getOriginal(), null);
            }
        });

        // Só registrar restored se o modelo usar soft delete
        if (method_exists(static::class, 'restore')) {
            static::restored(function ($model) {
                if (Auth::check()) {
                    $model->auditar('restored', null, $model->getAttributes());
                }
            });
        }
    }

    /**
     * Desabilitar auditoria temporariamente
     */
    public static function disableAuditing()
    {
        static::$auditingDisabled = true;
    }

    /**
     * Reabilitar auditoria
     */
    public static function enableAuditing()
    {
        static::$auditingDisabled = false;
    }

    /**
     * Registra uma ação na auditoria
     */
    public function auditar($acao, $dadosAnteriores = null, $dadosNovos = null, $observacoes = null)
    {
        Auditoria::create([
            'modelo' => class_basename($this),
            'modelo_id' => $this->getKey(),
            'acao' => $acao,
            'dados_anteriores' => $dadosAnteriores,
            'dados_novos' => $dadosNovos,
            'usuario_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'observacoes' => $observacoes,
        ]);
    }

    /**
     * Gera observações detalhadas das alterações
     */
    public function gerarObservacoesAlteracao($dadosAnteriores, $dadosNovos)
    {
        $observacoes = [];

        foreach ($dadosNovos as $campo => $valorNovo) {
            $valorAnterior = $dadosAnteriores[$campo] ?? null;

            // Pular campos que são timestamps
            if (in_array($campo, ['updated_at', 'created_at'])) {
                continue;
            }

            // Comparação mais robusta - converter para string para comparar
            $valorAnteriorStr = $this->normalizarValorParaComparacao($valorAnterior);
            $valorNovoStr = $this->normalizarValorParaComparacao($valorNovo);

            // Pular campos que não mudaram
            if ($valorAnteriorStr === $valorNovoStr) {
                continue;
            }

            $observacao = $this->gerarObservacaoCampo($campo, $valorAnterior, $valorNovo);
            if ($observacao) {
                $observacoes[] = $observacao;
            }
        }

        return !empty($observacoes) ? implode('; ', $observacoes) : null;
    }

    /**
     * Normaliza valor para comparação
     */
    protected function normalizarValorParaComparacao($valor)
    {
        if (is_null($valor)) {
            return '';
        }

        if (is_numeric($valor)) {
            return (string) $valor;
        }

        if (is_bool($valor)) {
            return $valor ? '1' : '0';
        }

        return (string) $valor;
    }

    /**
     * Gera observação para um campo específico
     */
    protected function gerarObservacaoCampo($campo, $valorAnterior, $valorNovo)
    {
        $nomeCampo = $this->getNomeCampoFormatado($campo);

        // Tratar valores nulos
        $valorAnteriorFormatado = $this->formatarValor($valorAnterior);
        $valorNovoFormatado = $this->formatarValor($valorNovo);

        return "{$nomeCampo} alterado de {$valorAnteriorFormatado} para {$valorNovoFormatado}";
    }

    /**
     * Retorna o nome formatado do campo
     */
    protected function getNomeCampoFormatado($campo)
    {
        $nomesCampos = [
            'status' => 'Status',
            'quantidade' => 'Quantidade',
            'valor_unitario' => 'Valor Unitário',
            'valor_total' => 'Valor Total',
            'data_medicao' => 'Data da Medição',
            'data_pagamento' => 'Data do Pagamento',
            'valor_pagamento' => 'Valor do Pagamento',
            'numero_medicao' => 'Número da Medição',
            'numero_pagamento' => 'Número do Pagamento',
            'observacoes' => 'Observações',
            'documento_redmine' => 'Documento Redmine',
            'catalogo_id' => 'Catálogo',
            'contrato_id' => 'Contrato',
            'lotacao_id' => 'Lotação',
            'medicao_id' => 'Medição',
        ];

        return $nomesCampos[$campo] ?? ucfirst(str_replace('_', ' ', $campo));
    }

    /**
     * Formata o valor para exibição
     */
    protected function formatarValor($valor)
    {
        if (is_null($valor)) {
            return 'nulo';
        }

        if (is_bool($valor)) {
            return $valor ? 'Sim' : 'Não';
        }

        if (is_numeric($valor)) {
            // Se for um valor monetário (contém ponto decimal)
            if (strpos($valor, '.') !== false) {
                return 'R$ ' . number_format($valor, 2, ',', '.');
            }
            return number_format($valor, 0, ',', '.');
        }

        if (is_string($valor) && strlen($valor) > 50) {
            return substr($valor, 0, 50) . '...';
        }

        return $valor;
    }

    /**
     * Relacionamento com auditorias
     */
    public function auditorias()
    {
        return $this->hasMany(Auditoria::class, 'modelo_id')
            ->where('modelo', class_basename($this))
            ->orderBy('created_at', 'desc');
    }
}
