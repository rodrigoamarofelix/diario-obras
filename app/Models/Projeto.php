<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Projeto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome',
        'descricao',
        'endereco',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'cliente',
        'contrato',
        'valor_total',
        'data_inicio',
        'data_fim_prevista',
        'data_fim_real',
        'status',
        'prioridade',
        'observacoes',
        'responsavel_id',
        'created_by'
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'data_fim_prevista' => 'date',
        'data_fim_real' => 'date',
        'valor_total' => 'decimal:2',
    ];

    // Relacionamentos
    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function atividades(): HasMany
    {
        return $this->hasMany(AtividadeObra::class);
    }

    public function equipe(): HasMany
    {
        return $this->hasMany(EquipeObra::class);
    }

    public function materiais(): HasMany
    {
        return $this->hasMany(MaterialObra::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoObra::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'projeto_empresa')
                    ->withPivot(['tipo_participacao', 'observacoes', 'ativo'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeAtivos($query)
    {
        return $query->whereIn('status', ['planejamento', 'em_andamento']);
    }

    public function scopeConcluidos($query)
    {
        return $query->where('status', 'concluido');
    }

    // Accessors
    public function getProgressoAttribute()
    {
        $totalAtividades = $this->atividades()->count();
        $atividadesConcluidas = $this->atividades()->where('status', 'concluido')->count();

        if ($totalAtividades == 0) return 0;

        return round(($atividadesConcluidas / $totalAtividades) * 100, 2);
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->data_fim_prevista) return null;

        $hoje = now();
        $dataFim = $this->data_fim_prevista;

        return $hoje->diffInDays($dataFim, false);
    }
}
