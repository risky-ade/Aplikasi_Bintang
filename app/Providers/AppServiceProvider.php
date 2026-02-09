<?php

namespace App\Providers;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
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
        Blade::directive('rupiah', function ($expression) {
            return "<?php echo 'Rp ' . number_format($expression, 0, ',', '.'); ?>";
        });

     $helperPath = base_path('app/Support/helpers.php');
        if (file_exists($helperPath)) {
            require_once $helperPath;
        }

        Blade::if('role', function (...$roles) {
            return Auth::check() && in_array(Auth::user()->role, $roles);
        });

    }
    
}
