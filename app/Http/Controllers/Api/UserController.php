<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::withCount(['medicoes', 'pagamentos']);

        if ($request->has('profile')) {
            $query->where('profile', $request->profile);
        }

        if ($request->has('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $perPage = $request->get('per_page', 15);
        $usuarios = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $usuarios->items(),
            'pagination' => [
                'current_page' => $usuarios->currentPage(),
                'last_page' => $usuarios->lastPage(),
                'per_page' => $usuarios->perPage(),
                'total' => $usuarios->total(),
            ]
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'profile' => 'required|in:master,admin,user',
            'approval_status' => 'required|in:pending,aprovado,rejeitado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'profile' => $request->profile,
                'approval_status' => $request->approval_status,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuário criado com sucesso',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile,
                    'approval_status' => $user->approval_status,
                    'created_at' => $user->created_at->toISOString(),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        $user->loadCount(['medicoes', 'pagamentos']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile' => $user->profile,
                'approval_status' => $user->approval_status,
                'created_at' => $user->created_at->toISOString(),
                'updated_at' => $user->updated_at->toISOString(),
                'medicoes_count' => $user->medicoes_count,
                'pagamentos_count' => $user->pagamentos_count,
            ]
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:6',
            'profile' => 'sometimes|required|in:master,admin,user',
            'approval_status' => 'sometimes|required|in:pending,aprovado,rejeitado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $request->only(['name', 'email', 'profile', 'approval_status']);

            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Usuário atualizado com sucesso',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile,
                    'approval_status' => $user->approval_status,
                    'updated_at' => $user->updated_at->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(User $user): JsonResponse
    {
        try {
            // Não permitir excluir o próprio usuário
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Não é possível excluir seu próprio usuário'
                ], 422);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function stats(): JsonResponse
    {
        $stats = [
            'total' => User::count(),
            'por_perfil' => User::selectRaw('profile, count(*) as total')
                ->groupBy('profile')
                ->get(),
            'por_status' => User::selectRaw('approval_status, count(*) as total')
                ->groupBy('approval_status')
                ->get(),
            'usuarios_ativos' => User::where('approval_status', 'aprovado')->count(),
            'por_mes' => User::selectRaw('DatabaseHelper::formatDateForMonthGrouping(), count(*) as total')
                ->groupBy('mes')
                ->orderBy('mes', 'desc')
                ->limit(12)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}