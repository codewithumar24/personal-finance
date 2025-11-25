<?php

namespace Modules\Admin\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Admin\Contracts\Repositories\BudgetRepositoryContract;
use Modules\Admin\Contracts\Repositories\CategoryRepositoryContract;
use Modules\Admin\Contracts\Repositories\GoalRepositoryContract;
use Modules\Admin\Contracts\Repositories\TransactionRepositoryContract;
use Modules\Admin\Contracts\Repositories\WalletRepositoryContract;
use Modules\Admin\Contracts\Services\AdminRoleServiceContract;
use Modules\Admin\Contracts\Services\AdminUserServiceContract;
use Modules\Admin\Contracts\Services\BudgetServiceContract;
use Modules\Admin\Contracts\Services\CategoryServiceContract;
use Modules\Admin\Contracts\Services\GoalServiceContract;
use Modules\Admin\Contracts\Services\NotificationServiceContract;
use Modules\Admin\Contracts\Services\ReportServiceContract;
use Modules\Admin\Contracts\Services\TransactionServiceContract;
use Modules\Admin\Contracts\Services\WalletServiceContract;
use Modules\Admin\Repositories\CategoryRepository;
use Modules\Admin\Repositories\WalletRepository;
use Modules\Admin\Services\AdminRoleService;
use Modules\Admin\Services\AdminUserService;
use Modules\Admin\Services\BudgetService;
use Modules\Admin\Services\CategoryService;
use Modules\Admin\Services\GoalService;
use Modules\Admin\Services\ReportService;
use Modules\Admin\Services\WalletService;
use Modules\Admin\Repositories\BudgetRepository;
use Modules\Admin\Repositories\GoalRepository;
use Modules\Admin\Repositories\TransactionRepository;
use Modules\Admin\Services\NotificationService;
use Modules\Admin\Services\TransactionService;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class AdminServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Admin';

    protected string $nameLower = 'admin';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
        // Service Bindings
        $this->app->bind(AdminUserServiceContract::class, AdminUserService::class);
        $this->app->bind(AdminRoleServiceContract::class, AdminRoleService::class);

         // Repository Bindings
        $this->app->bind(WalletRepositoryContract::class, WalletRepository::class);
        $this->app->bind(CategoryRepositoryContract::class, CategoryRepository::class);

         $this->app->bind(BudgetRepositoryContract::class, BudgetRepository::class);
         $this->app->bind(BudgetServiceContract::class, BudgetService::class);
        $this->app->bind(GoalRepositoryContract::class, GoalRepository::class);
        $this->app->bind(GoalServiceContract::class, GoalService::class);

        // Service Bindings
        $this->app->bind(WalletServiceContract::class, WalletService::class);
        $this->app->bind(CategoryServiceContract::class, CategoryService::class);
        $this->app->bind(ReportServiceContract::class, ReportService::class);

        $this->app->bind(TransactionRepositoryContract::class, TransactionRepository::class
);

$this->app->bind(TransactionServiceContract::class, TransactionService::class);
     $this->app->bind(NotificationServiceContract::class, NotificationService::class);

    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/'.$this->nameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
            $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        Blade::componentNamespace(config('modules.namespace').'\\' . $this->name . '\\View\\Components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
