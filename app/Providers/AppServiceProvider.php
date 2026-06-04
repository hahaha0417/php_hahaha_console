<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        //
		if ($this->app->runningInConsole()) {
            $this->commands([
                hahaha_cache_ai_context::class,
                hahaha_cache_code_summary::class,
                hahaha_cache_project_structure::class,
                hahaha_command_db_table_enum_generate::class,
            ]);
        }
    }
}
