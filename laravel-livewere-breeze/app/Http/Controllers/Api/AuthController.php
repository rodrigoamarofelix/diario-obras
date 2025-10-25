<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login do usuário e geração de token
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        // Verificar se o usuário está aprovado
        if ($user->approval_status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não aprovado para acesso à API'
            ], 403);
        }

        $token = $user->createToken('SGC-API-Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile,
                    'approval_status' => $user->approval_status,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toISOString()
            ]
        ]);
    }

    /**
     * Logout do usuário (revogar token)
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Revogar todos os tokens do usuário
     */
    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todos os tokens foram revogados'
        ]);
    }

    /**
     * Obter informações do usuário autenticado
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile,
                    'approval_status' => $user->approval_status,
                    'created_at' => $user->created_at->toISOString(),
                    'updated_at' => $user->updated_at->toISOString(),
                ],
                'permissions' => [
                    'can_manage_users' => $user->canManageUsers(),
                    'can_approve_users' => $user->canApproveUsers(),
                    'can_manage_contracts' => $user->canManageContracts(),
                ]
            ]
        ]);
    }

    /**
     * Renovar token
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revogar token atual
        $request->user()->currentAccessToken()->delete();

        // Criar novo token
        $token = $user->createToken('SGC-API-Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_at' => now()->addDays(30)->toISOString()
            ]
        ]);
    }

    /**
     * Listar tokens ativos do usuário
     */
    public function tokens(Request $request): JsonResponse
    {
        $tokens = $request->user()->tokens()->select([
            'id', 'name', 'abilities', 'last_used_at', 'created_at'
        ])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'tokens' => $tokens->map(function ($token) {
                    return [
                        'id' => $token->id,
                        'name' => $token->name,
                        'abilities' => $token->abilities,
                        'last_used_at' => $token->last_used_at?->toISOString(),
                        'created_at' => $token->created_at->toISOString(),
                    ];
                })
            ]
        ]);
    }

    /**
     * Revogar token específico
     */
    public function revokeToken(Request $request, $tokenId): JsonResponse
    {
        $token = $request->user()->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token não encontrado'
            ], 404);
        }

        $token->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token revogado com sucesso'
        ]);
    }
}
