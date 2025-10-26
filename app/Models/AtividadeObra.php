<?php

namespace App\Models;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AtividadeObra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'projeto_id',
        'data_atividade',
        'titulo',
        'descricao',
        'tipo',
        'status',
        'hora_inicio',
        'hora_fim',
        'tempo_gasto_minutos',
        'observacoes',
        'problemas_encontrados',
        'solucoes_aplicadas',
        'responsavel_id',
        'created_by'
    ];

    protected $casts = [
        'data_atividade' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'responsavel_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function equipe(): HasMany
    {
        return $this->hasMany(EquipeObra::class, 'atividade_id');
    }

    public function materiais(): HasMany
    {
        return $this->hasMany(MaterialObra::class, 'atividade_id');
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoObra::class, 'atividade_id');
    }

    // Scopes
    public function scopeHoje($query)
    {
        return $query->whereDate('data_atividade', today());
    }

    public function scopeEstaSemana($query)
    {
        return $query->whereBetween('data_atividade', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', 'concluido');
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', 'em_andamento');
    }
}
