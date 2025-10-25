<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Medicao extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'medicoes';

    protected $fillable = [
        'catalogo_id',
        'contrato_id',
        'lotacao_id',
        'numero_medicao',
        'data_medicao',
        'quantidade',
        'valor_unitario',
        'valor_total',
        'observacoes',
        'status',
        'usuario_id',
    ];

    protected $casts = [
        'data_medicao' => 'date',
        'quantidade' => 'decimal:3',
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2',
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
        return $query->whereBetween('data_medicao', [$inicio, $fim]);
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
            default => 'Pendente'
        };
    }

    /**
     * Retorna o valor total formatado
     */
    public function getValorTotalFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    /**
     * Retorna o valor unitário formatado
     */
    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    /**
     * Relacionamento com catálogo
     */
    public function catalogo()
    {
        return $this->belongsTo(Catalogo::class);
    }

    /**
     * Relacionamento com contrato
     */
    public function contrato()
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relacionamento com lotação
     */
    public function lotacao()
    {
        return $this->belongsTo(Lotacao::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com pagamentos
     */
    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class);
    }

    /**
     * Calcula o valor total automaticamente
     */
    public function calcularValorTotal()
    {
        $this->valor_total = $this->quantidade * $this->valor_unitario;
    }

    /**
     * Gera o próximo número de medição no formato MED00NUMERO_SEQUENCIAL
     */
    public static function gerarProximoNumero()
    {
        // Busca o último número de medição criado
        $ultimaMedicao = self::withTrashed()
            ->where('numero_medicao', 'LIKE', 'MED00%')
            ->orderBy('numero_medicao', 'desc')
            ->first();

        if (!$ultimaMedicao) {
            // Se não há medições, começa com MED001
            return 'MED001';
        }

        // Extrai o número sequencial do último número
        $ultimoNumero = preg_replace('/^MED00/', '', $ultimaMedicao->numero_medicao);
        $proximoNumero = (int)$ultimoNumero + 1;

        // Formata o próximo número com zeros à esquerda
        return 'MED00' . str_pad($proximoNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica se a medição já foi paga
     */
    public function foiPaga(): bool
    {
        return $this->pagamentos()
            ->where('status', 'pago')
            ->exists();
    }

    /**
     * Scope para filtrar medições que não foram pagas
     */
    public function scopeNaoPagas($query)
    {
        return $query->whereDoesntHave('pagamentos', function($q) {
            $q->where('status', 'pago');
        });
    }

    /**
     * Sobrescreve o método para tratar campos de relacionamento na auditoria
     */
    protected function gerarObservacaoCampo($campo, $valorAnterior, $valorNovo)
    {
        // Tratar campos de relacionamento
        switch ($campo) {
            case 'catalogo_id':
                $catalogoAnterior = $valorAnterior ? Catalogo::find($valorAnterior) : null;
                $catalogoNovo = $valorNovo ? Catalogo::find($valorNovo) : null;
                $nomeAnterior = $catalogoAnterior ? $catalogoAnterior->nome : 'nulo';
                $nomeNovo = $catalogoNovo ? $catalogoNovo->nome : 'nulo';
                return "Catálogo alterado de {$nomeAnterior} para {$nomeNovo}";

            case 'contrato_id':
                $contratoAnterior = $valorAnterior ? Contrato::find($valorAnterior) : null;
                $contratoNovo = $valorNovo ? Contrato::find($valorNovo) : null;
                $numeroAnterior = $contratoAnterior ? $contratoAnterior->numero : 'nulo';
                $numeroNovo = $contratoNovo ? $contratoNovo->numero : 'nulo';
                return "Contrato alterado de {$numeroAnterior} para {$numeroNovo}";

            case 'lotacao_id':
                $lotacaoAnterior = $valorAnterior ? Lotacao::find($valorAnterior) : null;
                $lotacaoNovo = $valorNovo ? Lotacao::find($valorNovo) : null;
                $nomeAnterior = $lotacaoAnterior ? $lotacaoAnterior->nome : 'nulo';
                $nomeNovo = $lotacaoNovo ? $lotacaoNovo->nome : 'nulo';
                return "Lotação alterada de {$nomeAnterior} para {$nomeNovo}";

            case 'status':
                $statusAnterior = $this->getStatusNameFromValue($valorAnterior);
                $statusNovo = $this->getStatusNameFromValue($valorNovo);
                return "Status alterado de {$statusAnterior} para {$statusNovo}";

            case 'quantidade':
                $qtdAnterior = $this->formatarValor($valorAnterior);
                $qtdNovo = $this->formatarValor($valorNovo);
                return "Quantidade alterada de {$qtdAnterior} para {$qtdNovo}";

            case 'valor_unitario':
                $valorAnteriorFormatado = $this->formatarValor($valorAnterior);
                $valorNovoFormatado = $this->formatarValor($valorNovo);
                return "Valor Unitário alterado de {$valorAnteriorFormatado} para {$valorNovoFormatado}";

            case 'valor_total':
                $valorAnteriorFormatado = $this->formatarValor($valorAnterior);
                $valorNovoFormatado = $this->formatarValor($valorNovo);
                return "Valor Total alterado de {$valorAnteriorFormatado} para {$valorNovoFormatado}";

            case 'data_medicao':
                $dataAnterior = $valorAnterior ? \Carbon\Carbon::parse($valorAnterior)->format('d/m/Y') : 'nulo';
                $dataNovo = $valorNovo ? \Carbon\Carbon::parse($valorNovo)->format('d/m/Y') : 'nulo';
                return "Data da Medição alterada de {$dataAnterior} para {$dataNovo}";

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
            default => $status ?? 'nulo'
        };
    }
}
