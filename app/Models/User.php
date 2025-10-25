<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Auditable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, Auditable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile',
        'approval_status',
        'approved_at',
        'approved_by',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_backup_codes',
        'two_factor_enabled_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_backup_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_backup_codes' => 'array',
            'two_factor_enabled_at' => 'datetime',
        ];
    }

    /**
     * Verifica se o usuário é master
     */
    public function isMaster(): bool
    {
        return $this->profile === 'master';
    }

    /**
     * Verifica se o usuário é admin
     */
    public function isAdmin(): bool
    {
        return $this->profile === 'admin';
    }

    /**
     * Verifica se o usuário é user comum
     */
    public function isUser(): bool
    {
        return $this->profile === 'user';
    }

    /**
     * Verifica se o usuário pode gerenciar outros usuários
     */
    public function canManageUsers(): bool
    {
        return $this->isMaster() || $this->isAdmin();
    }

    /**
     * Verifica se o usuário pode excluir outros usuários
     */
    public function canDeleteUsers(): bool
    {
        return $this->isMaster();
    }

    /**
     * Retorna o nome do perfil em português
     */
    public function getProfileNameAttribute(): string
    {
        return match($this->profile) {
            'master' => 'Master',
            'admin' => 'Administrador',
            'user' => 'Usuário',
            default => 'Usuário'
        };
    }

    /**
     * Verifica se o usuário está pendente de aprovação
     */
    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    /**
     * Verifica se o usuário foi aprovado
     */
    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    /**
     * Verifica se o usuário foi rejeitado
     */
    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    /**
     * Retorna o nome do status de aprovação em português
     */
    public function getApprovalStatusNameAttribute(): string
    {
        return match($this->approval_status) {
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            default => 'Pendente'
        };
    }

    /**
     * Relacionamento com o usuário que aprovou
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relacionamento com notificações
     */
    public function notificacoes()
    {
        return $this->hasMany(Notificacao::class)->orderBy('created_at', 'desc');
    }

    /**
     * Notificações não lidas
     */
    public function notificacoesNaoLidas()
    {
        return $this->hasMany(Notificacao::class)->naoLidas()->orderBy('created_at', 'desc');
    }

    /**
     * Contar notificações não lidas
     */
    public function contarNotificacoesNaoLidas()
    {
        return $this->notificacoesNaoLidas()->count();
    }

    /**
     * Marcar todas as notificações como lidas
     */
    public function marcarTodasNotificacoesComoLidas()
    {
        $this->notificacoesNaoLidas()->update([
            'lida' => true,
            'lida_em' => now(),
        ]);
    }

    /**
     * Scope para usuários pendentes
     */
    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    /**
     * Scope para usuários aprovados
     */
    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    /**
     * Verifica se o usuário tem 2FA ativado
     */
    public function hasTwoFactorEnabled(): bool
    {
        // Se o campo não existe ou é null, retorna false
        if (!isset($this->two_factor_enabled) || is_null($this->two_factor_enabled)) {
            return false;
        }

        return $this->two_factor_enabled === true || $this->two_factor_enabled === 1 || $this->two_factor_enabled === '1';
    }

    /**
     * Ativa o 2FA para o usuário
     */
    public function enableTwoFactor(string $secret, array $backupCodes): void
    {
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => $secret,
            'two_factor_backup_codes' => $backupCodes,
            'two_factor_enabled_at' => now(),
        ]);
    }

    /**
     * Desativa o 2FA para o usuário
     */
    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_enabled_at' => null,
        ]);

        // Limpar os atributos do modelo para evitar problemas de cache
        $this->two_factor_enabled = false;
        $this->two_factor_secret = null;
        $this->two_factor_backup_codes = null;
        $this->two_factor_enabled_at = null;
    }

    /**
     * Verifica se um código de backup é válido
     */
    public function verifyBackupCode(string $code): bool
    {
        if (!$this->two_factor_backup_codes) {
            return false;
        }

        $index = array_search($code, $this->two_factor_backup_codes);
        if ($index !== false) {
            $backupCodes = $this->two_factor_backup_codes;
            unset($backupCodes[$index]);
            $this->update(['two_factor_backup_codes' => array_values($backupCodes)]);
            return true;
        }

        return false;
    }

    /**
     * Retorna os códigos de backup (apenas para exibição)
     */
    public function getBackupCodes(): array
    {
        // Se não há códigos de backup, retorna array vazio
        if (empty($this->two_factor_backup_codes)) {
            return [];
        }

        // Se é string JSON, decodifica
        if (is_string($this->two_factor_backup_codes)) {
            $decoded = json_decode($this->two_factor_backup_codes, true);
            return is_array($decoded) ? $decoded : [];
        }

        // Se já é array, retorna diretamente
        if (is_array($this->two_factor_backup_codes)) {
            return $this->two_factor_backup_codes;
        }

        return [];
    }

    /**
     * Inicializa campos do 2FA com valores padrão
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (is_null($user->two_factor_enabled)) {
                $user->two_factor_enabled = false;
            }
        });
    }
}
