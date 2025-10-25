<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\LotacaoController;
use App\Http\Controllers\UserApprovalController;
use App\Http\Controllers\PessoaController;
use App\Http\Controllers\ContratoController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\MedicaoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

// Rota raiz - redireciona para login se não estiver autenticado
Route::get('/', function () {
    try {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('login');
    } catch (\Exception $e) {
        // Fallback para a view de login diretamente
        return view('auth.login');
    }
});

Route::get('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Rotas para gerenciamento de usuários
Route::middleware(['auth'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('users/{user}/edit-profile', [UserController::class, 'editProfile'])->name('users.edit-profile');
    Route::put('users/{user}/profile', [UserController::class, 'updateProfile'])->name('users.update-profile');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');

    // Rotas para aprovação de usuários (apenas master)
    Route::get('user-approvals', [UserApprovalController::class, 'index'])->name('user-approvals.index');
    Route::patch('user-approvals/{user}/approve', [UserApprovalController::class, 'approve'])->name('user-approvals.approve');
    Route::patch('user-approvals/{user}/reject', [UserApprovalController::class, 'reject'])->name('user-approvals.reject');

    // Rota para atualização de senha tradicional
    Route::post('profile/update-password', function (Illuminate\Http\Request $request) {
        try {
            $request->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ]);

            $user = Auth::user();
            $user->update([
                'password' => Hash::make($request->password),
            ]);

            // Garantir que a mensagem seja persistida
            $request->session()->flash('success', 'Senha atualizada com sucesso!');

            return redirect()->route('dashboard')->with('success', 'Senha atualizada com sucesso!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao atualizar senha: ' . $e->getMessage());
        }
    })->name('profile.update-password');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    // CRUD de Lotações
    Route::resource('lotacao', LotacaoController::class);

    // Rotas adicionais para soft delete de lotações
    Route::patch('lotacao/{id}/restore', [LotacaoController::class, 'restore'])->name('lotacao.restore');
    Route::delete('lotacao/{id}/force-delete', [LotacaoController::class, 'forceDelete'])->name('lotacao.force-delete');

    // CRUD de Contratos
    Route::resource('contrato', ContratoController::class);
    Route::patch('contrato/{id}/restore', [ContratoController::class, 'restore'])->name('contrato.restore');

    // Rotas para anexos de contratos
    Route::post('contrato/{id}/anexos/upload', [ContratoController::class, 'uploadAnexos'])->name('contrato.anexos.upload');
    Route::get('contrato/anexo/{id}/download', [ContratoController::class, 'downloadAnexo'])->name('contrato.anexo.download');
    Route::delete('contrato/anexo/{id}/excluir', [ContratoController::class, 'excluirAnexo'])->name('contrato.anexo.excluir');

    // Rota de teste para upload
    Route::get('test-upload', function() {
        return view('test-upload');
    });

    Route::post('test-upload', function(\Illuminate\Http\Request $request) {
        if ($request->hasFile('test_file')) {
            $file = $request->file('test_file');
            return response()->json([
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'size_mb' => round($file->getSize() / 1024 / 1024, 2),
                'mime' => $file->getMimeType(),
                'is_valid' => $file->isValid(),
                'max_size' => '10MB = ' . (10 * 1024 * 1024) . ' bytes'
            ]);
        }
        return response()->json(['error' => 'Nenhum arquivo enviado']);
    })->name('test.upload');

    // CRUD de Pessoas
    Route::resource('pessoa', PessoaController::class);
    Route::patch('pessoa/{id}/restore', [PessoaController::class, 'restore'])->name('pessoa.restore');
    Route::post('pessoa/consultar-cpf', [PessoaController::class, 'consultarCpf'])->name('pessoa.consultar-cpf');
    Route::post('pessoa/{id}/revalidar', [PessoaController::class, 'revalidar'])->name('pessoa.revalidar');

    // Rotas de Auditoria
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::get('auditoria/pessoas', [AuditoriaController::class, 'pessoas'])->name('auditoria.pessoas');
    Route::get('auditoria/responsaveis', [AuditoriaController::class, 'responsaveis'])->name('auditoria.responsaveis');
    Route::get('auditoria/contratos', [AuditoriaController::class, 'contratos'])->name('auditoria.contratos');
    Route::get('auditoria/lotacoes', [AuditoriaController::class, 'lotacoes'])->name('auditoria.lotacoes');
    Route::get('auditoria/usuarios', [AuditoriaController::class, 'usuarios'])->name('auditoria.usuarios');
    Route::get('auditoria/teste', [AuditoriaController::class, 'testarObservacoes'])->name('auditoria.teste');
    Route::get('auditoria/{id}', [AuditoriaController::class, 'show'])->name('auditoria.show');

    // CRUD de Catálogos
    Route::resource('catalogo', CatalogoController::class);
    Route::patch('catalogo/{id}/restore', [CatalogoController::class, 'restore'])->name('catalogo.restore');

    // CRUD de Medições
    Route::resource('medicao', MedicaoController::class);
    Route::patch('medicao/{id}/restore', [MedicaoController::class, 'restore'])->name('medicao.restore');
    Route::get('medicao/catalogo/{id}/valor', [MedicaoController::class, 'getCatalogoValor'])->name('medicao.catalogo.valor');

    // CRUD de Pagamentos
    Route::resource('pagamento', PagamentoController::class);
    Route::patch('pagamento/{id}/restore', [PagamentoController::class, 'restore'])->name('pagamento.restore');
    Route::get('pagamento/medicao/{id}/data', [PagamentoController::class, 'getMedicaoData'])->name('pagamento.medicao.data');

    // Sistema de Relatórios
    Route::get('reports', [App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::post('reports/generate', [App\Http\Controllers\ReportsController::class, 'generate'])->name('reports.generate');

    // Sistema de Notificações
    Route::prefix('notificacoes')->name('notificacoes.')->group(function () {
        Route::get('/', [App\Http\Controllers\NotificacaoController::class, 'index'])->name('index');
        Route::get('/nao-lidas', [App\Http\Controllers\NotificacaoController::class, 'naoLidas'])->name('nao-lidas');
        Route::patch('/{notificacao}/marcar-lida', [App\Http\Controllers\NotificacaoController::class, 'marcarComoLida'])->name('marcar-lida');
        Route::patch('/marcar-todas-lidas', [App\Http\Controllers\NotificacaoController::class, 'marcarTodasComoLidas'])->name('marcar-todas-lidas');
        Route::delete('/{notificacao}', [App\Http\Controllers\NotificacaoController::class, 'destroy'])->name('destroy');
        Route::post('/criar-teste', [App\Http\Controllers\NotificacaoController::class, 'criarTeste'])->name('criar-teste');
        Route::get('/estatisticas', [App\Http\Controllers\NotificacaoController::class, 'estatisticas'])->name('estatisticas');
    });

    // Sistema de Exportação
    Route::prefix('exports')->name('exports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ExportController::class, 'index'])->name('index');

        // Contratos
        Route::get('/contratos/pdf', [App\Http\Controllers\ExportController::class, 'contratosPdf'])->name('contratos.pdf');
        Route::get('/contratos/excel', [App\Http\Controllers\ExportController::class, 'contratosExcel'])->name('contratos.excel');

        // Medições
        Route::get('/medicoes/pdf', [App\Http\Controllers\ExportController::class, 'medicoesPdf'])->name('medicoes.pdf');
        Route::get('/medicoes/excel', [App\Http\Controllers\ExportController::class, 'medicoesExcel'])->name('medicoes.excel');

        // Pagamentos
        Route::get('/pagamentos/pdf', [App\Http\Controllers\ExportController::class, 'pagamentosPdf'])->name('pagamentos.pdf');
        Route::get('/pagamentos/excel', [App\Http\Controllers\ExportController::class, 'pagamentosExcel'])->name('pagamentos.excel');

        // Relatório Financeiro
        Route::get('/relatorio-financeiro/pdf', [App\Http\Controllers\ExportController::class, 'relatorioFinanceiroPdf'])->name('relatorio-financeiro.pdf');
    });

    // Backup (apenas para administradores)
    Route::middleware(['auth', 'can:manage-users'])->prefix('backup')->name('backup.')->group(function () {
        Route::get('/', function () {
            return view('backup.index');
        })->name('index');

        Route::get('/list', [App\Http\Controllers\BackupController::class, 'index'])->name('list');
        Route::get('/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('download');
        Route::get('/stats', [App\Http\Controllers\BackupController::class, 'stats'])->name('stats');

        // Rotas críticas (apenas Master)
        Route::middleware('can:master')->group(function () {
            Route::post('/create', [App\Http\Controllers\BackupController::class, 'create'])->name('create');
            Route::delete('/delete/{filename}', [App\Http\Controllers\BackupController::class, 'destroy'])->name('delete');
            Route::post('/cleanup', [App\Http\Controllers\BackupController::class, 'cleanup'])->name('cleanup');
            Route::post('/schedule', [App\Http\Controllers\BackupController::class, 'schedule'])->name('schedule');
            Route::post('/restore-info', [App\Http\Controllers\BackupController::class, 'restoreInfo'])->name('restore-info');
            Route::post('/restore', [App\Http\Controllers\BackupController::class, 'restore'])->name('restore');
        });
    });

    // Busca Avançada
    Route::middleware('auth')->prefix('search')->name('search.')->group(function () {
        Route::get('/', function () {
            return view('search.index');
        })->name('index');
    });

    // Workflow de Aprovação
    Route::middleware('auth')->prefix('workflow')->name('workflow.')->group(function () {
        Route::get('/', [WorkflowController::class, 'index'])->name('index');
        Route::get('/listar', [WorkflowController::class, 'listar'])->name('listar');
        Route::get('/{id}', [WorkflowController::class, 'show'])->name('show');

        // Ações de aprovação
        Route::post('/{id}/aprovar', [WorkflowController::class, 'aprovar'])->name('aprovar');
        Route::post('/{id}/rejeitar', [WorkflowController::class, 'rejeitar'])->name('rejeitar');
        Route::post('/{id}/suspender', [WorkflowController::class, 'suspender'])->name('suspender');
        Route::post('/{id}/marcar-analise', [WorkflowController::class, 'marcarEmAnalise'])->name('marcar-analise');

        // Criar workflows
        Route::post('/criar-medicao', [WorkflowController::class, 'criarParaMedicao'])->name('criar-medicao');
        Route::post('/criar-pagamento', [WorkflowController::class, 'criarParaPagamento'])->name('criar-pagamento');

        // Estatísticas
        Route::get('/api/stats', [WorkflowController::class, 'stats'])->name('stats');
    });

    // Sistema de Autenticação de Dois Fatores
    Route::middleware('auth')->prefix('two-factor')->name('two-factor.')->group(function () {
        Route::get('/', [TwoFactorController::class, 'index'])->name('index');
        Route::post('/generate-secret', [TwoFactorController::class, 'generateSecret'])->name('generate-secret');
        Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
        Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
        Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify');
        Route::post('/regenerate-backup-codes', [TwoFactorController::class, 'regenerateBackupCodes'])->name('regenerate-backup-codes');
    });

});