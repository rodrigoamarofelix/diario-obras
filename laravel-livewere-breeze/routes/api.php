<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContratoController;
use App\Http\Controllers\Api\MedicaoController;
use App\Http\Controllers\Api\PagamentoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RelatorioController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas (sem autenticação)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
});

// Rotas protegidas (requerem autenticação)
Route::middleware('auth:sanctum')->group(function () {

    // Autenticação
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('logout-all', [AuthController::class, 'logoutAll']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('tokens', [AuthController::class, 'tokens']);
        Route::delete('tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    });

    // Contratos
    Route::apiResource('contratos', ContratoController::class);
    Route::get('contratos-stats', [ContratoController::class, 'stats']);

    // Medições
    Route::apiResource('medicoes', MedicaoController::class);
    Route::get('medicoes-stats', [MedicaoController::class, 'stats']);

    // Pagamentos
    Route::apiResource('pagamentos', PagamentoController::class);
    Route::get('pagamentos-stats', [PagamentoController::class, 'stats']);

    // Usuários (apenas para administradores)
    Route::middleware('can:manage-users')->group(function () {
        Route::apiResource('usuarios', UserController::class);
        Route::get('usuarios-stats', [UserController::class, 'stats']);
    });

    // Relatórios
    Route::prefix('relatorios')->group(function () {
        Route::get('dashboard', [RelatorioController::class, 'dashboard']);
        Route::get('financeiro', [RelatorioController::class, 'financeiro']);
        Route::get('contratos', [RelatorioController::class, 'contratos']);
        Route::get('medicoes', [RelatorioController::class, 'medicoes']);
        Route::get('pagamentos', [RelatorioController::class, 'pagamentos']);
        Route::get('usuarios', [RelatorioController::class, 'usuarios']);
    });

    // Informações do sistema
    Route::get('system/info', function () {
        return response()->json([
            'success' => true,
            'data' => [
                'name' => 'SGC - Gestão de Contratos',
                'version' => '1.0.0',
                'api_version' => 'v1',
                'timestamp' => now()->toISOString(),
                'timezone' => config('app.timezone'),
                'environment' => config('app.env'),
            ]
        ]);
    });
});

// Rate limiting para API
Route::middleware(['throttle:api'])->group(function () {
    // Rotas com limite de requisições
});




