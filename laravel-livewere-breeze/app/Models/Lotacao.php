<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Lotacao extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'lotacoes';

    protected $fillable = [
        'nome',
        'descricao',
        'status',
    ];

    protected $casts = [
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
}
