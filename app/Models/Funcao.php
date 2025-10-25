<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcao extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'funcoes';

    protected $fillable = [
        'nome',
        'descricao',
        'categoria',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relacionamentos
    public function pessoas()
    {
        return $this->hasMany(Pessoa::class);
    }

    // Scopes
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInativas($query)
    {
        return $query->where('ativo', false);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Accessors
    public function getCategoriaFormatadaAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->categoria));
    }

    // Métodos auxiliares
    public function getStatusFormatadoAttribute()
    {
        return $this->ativo ? 'Ativa' : 'Inativa';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->ativo ? 'success' : 'secondary';
    }
}
