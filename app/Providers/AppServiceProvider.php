<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        Password::defaults(function () {
            return Password::min(8) // Mínimo 8 caracteres
                ->mixedCase()       // Obliga a tener Mayúsculas + Minúsculas
                ->numbers()         // Obliga a tener al menos un número
                ->symbols();         // Obliga a tener símbolos (@, #, $, etc.) 
        });
    }
}
