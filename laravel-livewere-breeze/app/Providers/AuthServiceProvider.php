<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Gate para gerenciar usuários (Master e Admin)
        Gate::define('manage-users', function ($user) {
            return in_array($user->profile, ['master', 'admin']);
        });

        // Gate para gerenciar backup (Master e Admin)
        Gate::define('manage-backup', function ($user) {
            return in_array($user->profile, ['master', 'admin']);
        });

        // Gate para gerenciar relatórios (Master, Admin e User)
        Gate::define('manage-reports', function ($user) {
            return in_array($user->profile, ['master', 'admin', 'user']);
        });

        // Gate para gerenciar contratos (Master, Admin e User)
        Gate::define('manage-contracts', function ($user) {
            return in_array($user->profile, ['master', 'admin', 'user']);
        });

        // Gate para gerenciar medições (Master, Admin e User)
        Gate::define('manage-measurements', function ($user) {
            return in_array($user->profile, ['master', 'admin', 'user']);
        });

        // Gate para gerenciar pagamentos (Master, Admin e User)
        Gate::define('manage-payments', function ($user) {
            return in_array($user->profile, ['master', 'admin', 'user']);
        });

        // Gate para operações críticas (apenas Master)
        Gate::define('master', function ($user) {
            return $user->profile === 'master';
        });
    }
}