<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FotoObra extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'projeto_id',
        'atividade_id',
        'equipe_id',
        'user_id',
        'titulo',
        'descricao',
        'caminho_arquivo',
        'nome_arquivo',
        'mime_type',
        'tamanho_arquivo',
        'hash_arquivo',
        'latitude',
        'longitude',
        'altitude',
        'precisao',
        'camera_marca',
        'camera_modelo',
        'lente',
        'aperture',
        'shutter_speed',
        'iso',
        'focal_length',
        'tags',
        'categoria',
        'publica',
        'aprovada',
        'data_captura',
        'data_upload'
    ];

    protected $casts = [
        'tags' => 'array',
        'publica' => 'boolean',
        'aprovada' => 'boolean',
        'data_captura' => 'datetime',
        'data_upload' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'altitude' => 'decimal:2',
        'precisao' => 'decimal:2',
        'aperture' => 'decimal:2',
        'shutter_speed' => 'decimal:4',
        'focal_length' => 'decimal:2',
    ];

    // Relacionamentos
    public function projeto(): BelongsTo
    {
        return $this->belongsTo(Projeto::class);
    }

    public function atividade(): BelongsTo
    {
        return $this->belongsTo(AtividadeObra::class);
    }

    public function equipe(): BelongsTo
    {
        return $this->belongsTo(EquipeObra::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeAprovadas($query)
    {
        return $query->where('aprovada', true);
    }

    public function scopePublicas($query)
    {
        return $query->where('publica', true);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopeComGeolocalizacao($query)
    {
        return $query->whereNotNull('latitude')->whereNotNull('longitude');
    }

    // Accessors
    public function getTamanhoFormatadoAttribute()
    {
        $bytes = $this->tamanho_arquivo;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getCoordenadasAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return null;
    }

    public function getUrlArquivoAttribute()
    {
        return asset('storage/' . $this->caminho_arquivo);
    }

    // Métodos auxiliares
    public function temGeolocalizacao()
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    public function adicionarTag($tag)
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            $this->save();
        }
    }

    public function removerTag($tag)
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, function($t) use ($tag) {
            return $t !== $tag;
        });
        $this->tags = array_values($tags);
        $this->save();
    }
}
