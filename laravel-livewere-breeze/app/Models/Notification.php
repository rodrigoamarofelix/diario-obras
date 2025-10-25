<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read' => 'boolean',
            'read_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento com Usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para notificações não lidas
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope para notificações lidas
     */
    public function scopeRead($query)
    {
        return $query->where('read', true);
    }

    /**
     * Scope para notificações por tipo
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope para notificações por usuário
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Marcar como lida
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Marcar como não lida
     */
    public function markAsUnread()
    {
        $this->update([
            'read' => false,
            'read_at' => null,
        ]);
    }

    /**
     * Retorna o ícone baseado no tipo
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'success' => 'fas fa-check-circle',
            'error' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle',
            default => 'fas fa-bell',
        };
    }

    /**
     * Retorna a cor baseada no tipo
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'success' => 'text-green-600',
            'error' => 'text-red-600',
            'warning' => 'text-yellow-600',
            'info' => 'text-blue-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Retorna a cor de fundo baseada no tipo
     */
    public function getBackgroundColorAttribute(): string
    {
        return match($this->type) {
            'success' => 'bg-green-50',
            'error' => 'bg-red-50',
            'warning' => 'bg-yellow-50',
            'info' => 'bg-blue-50',
            default => 'bg-gray-50',
        };
    }
}
