<?php
// app/Providers/RepositoryServiceProvider.php
namespace App\Providers;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository interfaces to implementations
         $this->app->bind(UserRepository::class, function ($app) {
            return new UserRepository(new User());
        });
        $this->app->bind(
            \App\Repositories\Contracts\AdminRepositoryInterface::class,
            \App\Repositories\AdminRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\LoanRepositoryInterface::class,
            \App\Repositories\LoanRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\LoanRepaymentRepositoryInterface::class,
            \App\Repositories\LoanRepaymentRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\DashboardRepositoryInterface::class,
            \App\Repositories\DashboardRepository::class
        );
        
        // Bind services
        $this->app->bind(\App\Services\V1\User\AuthService::class, function ($app) {
            return new \App\Services\V1\User\AuthService(
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });
        $this->app->bind(\App\Services\V1\Admin\AuthService::class, function ($app) {
            return new \App\Services\V1\Admin\AuthService(
                $app->make(\App\Repositories\Contracts\AdminRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\V1\User\LoanService::class, function ($app) {
            return new \App\Services\V1\User\LoanService(
                $app->make(\App\Repositories\Contracts\LoanRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\LoanRepaymentRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\V1\User\ProfileService::class, function ($app) {
            return new \App\Services\V1\User\ProfileService(
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\V1\Admin\AuthService::class, function ($app) {
            return new \App\Services\V1\Admin\AuthService(
                $app->make(\App\Repositories\Contracts\AdminRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\V1\Admin\LoanService::class, function ($app) {
            return new \App\Services\V1\Admin\LoanService(
                $app->make(\App\Repositories\Contracts\LoanRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\LoanRepaymentRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });
        
        $this->app->bind(\App\Services\V1\Admin\DashboardService::class, function ($app) {
            return new \App\Services\V1\Admin\DashboardService(
                $app->make(\App\Repositories\Contracts\DashboardRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\LoanRepositoryInterface::class),
                $app->make(\App\Repositories\Contracts\UserRepositoryInterface::class)
            );
        });
    }
}