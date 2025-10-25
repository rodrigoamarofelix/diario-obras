<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Medicao;
use App\Models\Pagamento;
use App\Models\User;
use App\Observers\MedicaoObserver;
use App\Observers\PagamentoObserver;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar Observers para notificações automáticas
        Medicao::observe(MedicaoObserver::class);
        // Pagamento::observe(PagamentoObserver::class);
        // User::observe(UserObserver::class);
    }
}
