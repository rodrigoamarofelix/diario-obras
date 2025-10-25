<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;

class NotificacaoComponent extends Component
{
    public $notificacoes = [];
    public $totalNaoLidas = 0;
    public $mostrarDropdown = false;
    public $carregando = false;

    protected $listeners = [
        'notificacaoCriada' => 'atualizarNotificacoes',
        'notificacaoLida' => 'atualizarNotificacoes',
        'notificacaoExcluida' => 'atualizarNotificacoes',
    ];

    public function mount()
    {
        $this->carregarNotificacoes();
    }

    public function carregarNotificacoes()
    {
        $this->carregando = true;

        $user = Auth::user();
        $this->notificacoes = $user->notificacoesNaoLidas()
            ->limit(5)
            ->get()
            ->map(function($notificacao) {
                return [
                    'id' => $notificacao->id,
                    'tipo' => $notificacao->tipo,
                    'titulo' => $notificacao->titulo,
                    'mensagem' => $notificacao->mensagem,
                    'icone' => $notificacao->icone,
                    'cor' => $notificacao->cor,
                    'dados' => $notificacao->dados,
                    'lida' => $notificacao->lida,
                    'created_at' => $notificacao->created_at,
                ];
            })
            ->toArray();
        $this->totalNaoLidas = $user->contarNotificacoesNaoLidas();

        $this->carregando = false;
    }

    public function toggleDropdown()
    {
        $this->mostrarDropdown = !$this->mostrarDropdown;

        if ($this->mostrarDropdown) {
            $this->carregarNotificacoes();
        }
    }

    public function marcarComoLida($notificacaoId)
    {
        $notificacao = Notificacao::find($notificacaoId);

        if ($notificacao && $notificacao->user_id === Auth::id()) {
            $notificacao->marcarComoLida();
            $this->atualizarNotificacoes();

            $this->dispatch('notificacaoLida', $notificacaoId);
        }
    }

    public function marcarTodasComoLidas()
    {
        $user = Auth::user();
        $user->marcarTodasNotificacoesComoLidas();

        $this->atualizarNotificacoes();
        $this->dispatch('todasNotificacoesLidas');
    }

    public function excluirNotificacao($notificacaoId)
    {
        $notificacao = Notificacao::find($notificacaoId);

        if ($notificacao && $notificacao->user_id === Auth::id()) {
            $notificacao->delete();
            $this->atualizarNotificacoes();

            $this->dispatch('notificacaoExcluida', $notificacaoId);
        }
    }

    public function atualizarNotificacoes()
    {
        $this->carregarNotificacoes();
    }

    public function getCorNotificacao($tipo)
    {
        $cores = [
            'info' => '#17a2b8',
            'success' => '#28a745',
            'warning' => '#ffc107',
            'error' => '#dc3545',
        ];

        return $cores[$tipo] ?? '#6c757d';
    }

    public function criarNotificacaoTeste()
    {
        $tipos = ['info', 'success', 'warning', 'error'];
        $tipo = $tipos[array_rand($tipos)];

        $titulos = [
            'info' => 'Nova Informação',
            'success' => 'Operação Concluída',
            'warning' => 'Atenção Necessária',
            'error' => 'Erro Detectado',
        ];

        $mensagens = [
            'info' => 'Uma nova informação foi adicionada ao sistema.',
            'success' => 'A operação foi concluída com sucesso!',
            'warning' => 'Atenção: Verifique os dados antes de continuar.',
            'error' => 'Ocorreu um erro durante a operação.',
        ];

        Notificacao::criar(
            Auth::id(),
            $tipo,
            $titulos[$tipo],
            $mensagens[$tipo],
            ['url' => route('dashboard')],
            'teste',
            'notificacao',
            null
        );

        $this->atualizarNotificacoes();
        $this->dispatch('notificacaoCriada');
    }

    public function render()
    {
        return view('livewire.notificacao-component');
    }
}
