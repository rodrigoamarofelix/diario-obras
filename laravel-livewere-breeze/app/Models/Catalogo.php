<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Catalogo extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'catalogos';

    protected $fillable = [
        'nome',
        'descricao',
        'codigo',
        'valor_unitario',
        'unidade_medida',
        'status',
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'status' => 'string',
    ];

    /**
     * Scope para filtrar por status ativo
     */
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para filtrar por status inativo
     */
    public function scopeInativo($query)
    {
        return $query->where('status', 'inativo');
    }

    /**
     * Retorna o nome do status em português
     */
    public function getStatusNameAttribute(): string
    {
        return match($this->status) {
            'ativo' => 'Ativo',
            'inativo' => 'Inativo',
            default => 'Ativo'
        };
    }

    /**
     * Retorna o valor unitário formatado
     */
    public function getValorUnitarioFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    /**
     * Relacionamento com medições
     */
    public function medicoes()
    {
        return $this->hasMany(Medicao::class);
    }
}


