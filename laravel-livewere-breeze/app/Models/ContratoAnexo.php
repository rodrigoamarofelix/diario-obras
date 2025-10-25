<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Auditable;
use Illuminate\Support\Facades\Storage;

class ContratoAnexo extends Model
{
    use Auditable;

    protected $fillable = [
        'contrato_id',
        'nome_original',
        'nome_arquivo',
        'caminho',
        'tipo_mime',
        'tamanho',
        'descricao',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'tamanho' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Contrato
     */
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }

    /**
     * Relacionamento com Usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor para tamanho formatado
     */
    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Accessor para extensão do arquivo
     */
    public function getExtensaoAttribute(): string
    {
        return pathinfo($this->nome_original, PATHINFO_EXTENSION);
    }

    /**
     * Accessor para ícone baseado no tipo MIME
     */
    public function getIconeAttribute(): string
    {
        $mime = $this->tipo_mime;

        if (str_starts_with($mime, 'image/')) {
            return 'fas fa-image';
        } elseif (str_starts_with($mime, 'application/pdf')) {
            return 'fas fa-file-pdf';
        } elseif (str_starts_with($mime, 'application/msword') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.wordprocessingml')) {
            return 'fas fa-file-word';
        } elseif (str_starts_with($mime, 'application/vnd.ms-excel') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.spreadsheetml')) {
            return 'fas fa-file-excel';
        } elseif (str_starts_with($mime, 'application/vnd.ms-powerpoint') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.presentationml')) {
            return 'fas fa-file-powerpoint';
        } elseif (str_starts_with($mime, 'text/')) {
            return 'fas fa-file-alt';
        } elseif (str_starts_with($mime, 'application/zip') || str_starts_with($mime, 'application/x-rar')) {
            return 'fas fa-file-archive';
        } else {
            return 'fas fa-file';
        }
    }

    /**
     * Accessor para cor do ícone baseado no tipo MIME
     */
    public function getCorIconeAttribute(): string
    {
        $mime = $this->tipo_mime;

        if (str_starts_with($mime, 'image/')) {
            return 'text-success';
        } elseif (str_starts_with($mime, 'application/pdf')) {
            return 'text-danger';
        } elseif (str_starts_with($mime, 'application/msword') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.wordprocessingml')) {
            return 'text-primary';
        } elseif (str_starts_with($mime, 'application/vnd.ms-excel') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.spreadsheetml')) {
            return 'text-success';
        } elseif (str_starts_with($mime, 'application/vnd.ms-powerpoint') || str_starts_with($mime, 'application/vnd.openxmlformats-officedocument.presentationml')) {
            return 'text-warning';
        } elseif (str_starts_with($mime, 'text/')) {
            return 'text-info';
        } elseif (str_starts_with($mime, 'application/zip') || str_starts_with($mime, 'application/x-rar')) {
            return 'text-secondary';
        } else {
            return 'text-muted';
        }
    }

    /**
     * Verifica se o arquivo existe no sistema
     */
    public function arquivoExiste(): bool
    {
        return Storage::exists($this->caminho);
    }

    /**
     * Obtém o caminho completo do arquivo
     */
    public function getCaminhoCompletoAttribute(): string
    {
        return storage_path('app/' . $this->caminho);
    }
}