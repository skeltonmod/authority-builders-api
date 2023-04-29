<?php

namespace Deyji\Manage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Deyji\Manage\Classes\PermissionRegistrar;
use Deyji\Manage\Classes\UserVerification;
use Laravel\Passport\Http\Middleware\CreateFreshApiToken;
use Deyji\Manage\Http\Middleware\RoleMiddleWare;
use Deyji\Manage\Http\Middleware\LogMiddleware;
use Illuminate\Support\Arr;

class ManageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/Manage.php', 'manage');
        $this->mergeConfigFrom(__DIR__ . '/../config/auth.php', 'auth');
        $this->mergeConfigFrom(__DIR__ . '/../config/privileges.php', 'privileges');
        $this->mergeConfigFrom(__DIR__ . '/../config/services.php', 'services');
        $this->publishConfig();

        $this->loadViewsFrom(__DIR__ . '/resources/views', 'manage');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->registerRoutes();

        $registrar = app(PermissionRegistrar::class);

        $registrar->registerPermissions();

        // Register PackageRegistrar
        $this->app->singleton(PermissionRegistrar::class, function ($app) use ($registrar) {
            return $registrar;
        });

        $this->app['router']->aliasMiddleware('role', RoleMiddleWare::class);
        $this->app['router']->aliasMiddleware('token', CreateFreshApiToken::class);
        $this->app['router']->aliasMiddleware('log', LogMiddleware::class);
        // 

        // Publish the resources
        $this->publishes([
            __DIR__ . '/resources/views/publishable/views' => resource_path('/views'),
            __DIR__ . '/resources/views/publishable/js' => resource_path('/js'),
            __DIR__ . '/resources/views/publishable/sass' => resource_path('/sass'),
            __DIR__ . '/resources/views/publishable/css' => resource_path('/css'),
            __DIR__ . '/package.json' => base_path('/package.json'),
            __DIR__ . '/webpack.mix.js' => base_path('/webpack.mix.js'),
        ], 'view');
    }

    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfigs(require $path, $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    // A hack to ammend API guard not being defined
    protected function mergeConfigs(array $original, array $merging)
    {
        $array = array_merge($original, $merging);
        foreach ($original as $key => $value) {
            if (!is_array($value)) {
                continue;
            }
            if (!Arr::exists($merging, $key)) {
                continue;
            }
            if (is_numeric($key)) {
                continue;
            }
            $array[$key] = $this->mergeConfigs($value, $merging[$key]);
        }
        return $array;
    }

    /**
     * Register the package routes.
     *
     * @return void
     */
    private function registerRoutes()
    {
        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__ . '/routes/routes.php');
        });
    }

    /**
     * Get route group configuration array.
     *
     * @return array
     */
    private function routeConfiguration()
    {
        return [
            'namespace'  => "Deyji\Manage\Http\Controllers",
        ];
    }


    

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {


        // Register facade
        $this->app->singleton('manage', function () {
            return new Manage;
        });



        // Bind the Email verification facade
        $this->app->bind("user.verification", function ($app) {
            return new UserVerification();
        });
    }

    /**
     * Publish Config
     *
     * @return void
     */
    public function publishConfig()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/Manage.php' => config_path('Manage.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../config/auth.php' => config_path('auth.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../config/privileges.php' => config_path('privileges.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../config/services.php' => config_path('services.php'),
            ], 'config');
        }
    }
}
