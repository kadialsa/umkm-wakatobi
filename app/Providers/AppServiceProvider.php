<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // Register custom Blade directive for formatting Rupiah
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo format_rupiah($expression); ?>";
        });

        // Register custom Blade directive for formatting Rupiah with symbol
        Blade::directive('rupiahSymbol', function ($expression) {
            return "<?php echo format_rupiah($expression, true); ?>";
        });
    }
}
