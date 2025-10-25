<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

Route::middleware('guest')->group(function () {
    // Rota GET para mostrar formulário de login
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');

    // Rota POST para processar login
    Route::post('login', function (Illuminate\Http\Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Verificar se o usuário foi aprovado
        $user = Auth::user();
        if ($user->isPending()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Sua solicitação de cadastro ainda está pendente de aprovação. Aguarde um administrador aprovar sua conta.',
            ]);
        }

        if ($user->isRejected()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Sua solicitação de cadastro foi rejeitada. Entre em contato com um administrador para mais informações.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    });

    // Rota GET para mostrar formulário de registro
    Route::get('register', function () {
        return view('auth.register');
    })->name('register');

    // Rota POST para processar registro
    Route::post('register', function (Illuminate\Http\Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'profile' => 'user', // Perfil padrão
            'approval_status' => 'pending', // Status pendente de aprovação
        ]);

        // Não fazer login automático - usuário fica pendente
        return redirect()->route('login')->with('status', 'Solicitação encaminhada com sucesso! Sua conta está pendente de aprovação de um administrador.');
    });

    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::post('forgot-password', function (Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    })->name('password.email');

    Route::get('reset-password/{token}', function ($token) {
        $email = request('email');
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    })->name('password.reset');

    Route::post('reset-password', function (Illuminate\Http\Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    })->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('email/verification-notification', function (Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', function () {
        return view('auth.confirm-password');
    })->name('password.confirm');

    Route::post('confirm-password', function (Illuminate\Http\Request $request) {
        if (!Hash::check($request->password, $request->user()->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended();
    })->middleware('throttle:6,1');
});
