<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Notificacao extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensagem',
        'icone',
        'cor',
        'dados',
        'lida',
        'lida_em',
        'acao',
        'modelo',
        'modelo_id',
    ];

    protected $casts = [
        'dados' => 'array',
        'lida' => 'boolean',
        'lida_em' => 'datetime',
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    public function scopeLidas($query)
    {
        return $query->where('lida', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($dias));
    }

    // Métodos
    public function marcarComoLida()
    {
        $this->update([
            'lida' => true,
            'lida_em' => now(),
        ]);
    }

    public function marcarComoNaoLida()
    {
        $this->update([
            'lida' => false,
            'lida_em' => null,
        ]);
    }

    public function getTempoAtrasAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getUrlAttribute()
    {
        if (!$this->dados || !isset($this->dados['url'])) {
            return null;
        }

        return $this->dados['url'];
    }

    // Métodos estáticos para criar notificações
    public static function criar($userId, $tipo, $titulo, $mensagem, $dados = [], $acao = null, $modelo = null, $modeloId = null)
    {
        $icones = [
            'info' => 'fas fa-info-circle',
            'success' => 'fas fa-check-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'error' => 'fas fa-times-circle',
        ];

        $cores = [
            'info' => 'info',
            'success' => 'success',
            'warning' => 'warning',
            'error' => 'danger',
        ];

        return self::create([
            'user_id' => $userId,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'mensagem' => $mensagem,
            'icone' => $icones[$tipo] ?? 'fas fa-bell',
            'cor' => $cores[$tipo] ?? 'info',
            'dados' => $dados,
            'acao' => $acao,
            'modelo' => $modelo,
            'modelo_id' => $modeloId,
        ]);
    }

    public static function notificarUsuarios($userIds, $tipo, $titulo, $mensagem, $dados = [], $acao = null, $modelo = null, $modeloId = null)
    {
        $notificacoes = [];
        foreach ($userIds as $userId) {
            $notificacoes[] = self::criar($userId, $tipo, $titulo, $mensagem, $dados, $acao, $modelo, $modeloId);
        }
        return $notificacoes;
    }

    public static function notificarTodos($tipo, $titulo, $mensagem, $dados = [], $acao = null, $modelo = null, $modeloId = null)
    {
        $userIds = User::pluck('id')->toArray();
        return self::notificarUsuarios($userIds, $tipo, $titulo, $mensagem, $dados, $acao, $modelo, $modeloId);
    }
}
