<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Lista todos os usuários
     */
    public function index()
    {
        $users = User::withTrashed()->orderBy('created_at', 'desc')->get();
        $pendingUsers = User::pending()->orderBy('created_at', 'desc')->get();

        return view('users.index', compact('users', 'pendingUsers'));
    }

    /**
     * Mostra os detalhes de um usuário
     */
    public function show(User $user)
    {
        // Verificar se o usuário atual pode gerenciar outros usuários
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Você não tem permissão para visualizar usuários.');
        }

        return view('users.show', compact('user'));
    }

    /**
     * Exclui um usuário
     */
    public function destroy(User $user)
    {
        // Verificar se o usuário atual pode excluir outros usuários
        if (!Auth::user()->canDeleteUsers()) {
            abort(403, 'Você não tem permissão para excluir usuários.');
        }

        // Não permitir que o usuário exclua a si mesmo
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        // Não permitir que usuários não-master excluam usuários master
        if ($user->isMaster() && !Auth::user()->isMaster()) {
            return back()->with('error', 'Você não tem permissão para excluir usuários master.');
        }

        $user->delete();

        return back()->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Mostra o formulário para editar o perfil de um usuário
     */
    public function editProfile(User $user)
    {
        // Verificar se o usuário atual pode gerenciar outros usuários
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Você não tem permissão para gerenciar usuários.');
        }

        return view('users.edit-profile', compact('user'));
    }

    /**
     * Atualiza o perfil de um usuário
     */
    public function updateProfile(Request $request, User $user)
    {
        // Verificar se o usuário atual pode gerenciar outros usuários
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Você não tem permissão para gerenciar usuários.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $user->id,
            'profile' => 'required|in:user,admin,master'
        ]);

        // Não permitir que usuários não-master alterem perfis para master
        if ($request->profile === 'master' && !Auth::user()->isMaster()) {
            return back()->with('error', 'Você não tem permissão para definir usuários como master.');
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile' => $request->profile
        ]);

        return redirect()->route('users.index')->with('success', 'Perfil do usuário atualizado com sucesso!');
    }

    /**
     * Restaura um usuário excluído
     */
    public function restore($id)
    {
        // Verificar se o usuário atual pode gerenciar outros usuários
        if (!Auth::user()->canManageUsers()) {
            abort(403, 'Você não tem permissão para gerenciar usuários.');
        }

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return back()->with('success', 'Usuário restaurado com sucesso!');
    }

    /**
     * Exclui permanentemente um usuário
     */
    public function forceDelete($id)
    {
        // Verificar se o usuário atual pode excluir outros usuários
        if (!Auth::user()->canDeleteUsers()) {
            abort(403, 'Você não tem permissão para excluir usuários.');
        }

        $user = User::withTrashed()->findOrFail($id);

        // Não permitir que o usuário exclua a si mesmo
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Você não pode excluir sua própria conta.');
        }

        // Não permitir que usuários não-master excluam usuários master
        if ($user->isMaster() && !Auth::user()->isMaster()) {
            return back()->with('error', 'Você não tem permissão para excluir usuários master.');
        }

        $user->forceDelete();

        return back()->with('success', 'Usuário excluído permanentemente!');
    }
}
