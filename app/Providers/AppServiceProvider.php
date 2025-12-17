<?php

namespace App\Providers;

// use App\Repositories\ApplicantContractRepository; // Changed
// use App\Repositories\LoanDocumentRepository; // Changed
// use App\Repositories\Contracts\ApplicantContractRepositoryInterface;
// use App\Repositories\Contracts\LoanDocumentRepositoryInterface;
use App\Repositories\AdminRepository;
use App\Repositories\ApplicantContractRepository;
use App\Repositories\Contracts\AdminRepositoryInterface;
use App\Repositories\Contracts\ApplicantContractRepositoryInterface;
use App\Repositories\Contracts\LoanDocumentRepositoryInterface;
use App\Repositories\Contracts\LoanRepaymentRepositoryInterface;
use App\Repositories\Contracts\LoanRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\ApplicantInformationRepositoryInterface;
use App\Repositories\Contracts\ApplicantBankRepositoryInterface;
use App\Repositories\LoanDocumentRepository;
use App\Repositories\LoanRepaymentRepository;
use App\Repositories\LoanRepository; 
use App\Repositories\UserRepository; 
use App\Repositories\ApplicantInformationRepository;
use App\Repositories\ApplicantBankRepository;
use App\Services\V1\User\AuthService as UserAuthService;
use App\Services\V1\Admin\AuthService as AdminAuthService;
use App\Services\V1\User\LoanService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind services
        $this->app->bind(UserAuthService::class, function ($app) {
            return new UserAuthService(
                $app->make(UserRepositoryInterface::class)
            );
        });
        $this->app->bind(AdminAuthService::class, function ($app) {
            return new AdminAuthService(
                $app->make(AdminRepositoryInterface::class)
            );
        });
    // $this->app->bind(
    //         ApplicantInformationRepositoryInterface::class,
    //         ApplicantInformationRepository::class
    //     );
        $this->app->bind(LoanService::class, function ($app) {
            return new LoanService(
                $app->make(LoanRepositoryInterface::class),
                $app->make(LoanRepaymentRepositoryInterface::class),
                $app->make(UserRepositoryInterface::class),
                $app->make(ApplicantInformationRepositoryInterface::class),
                $app->make(ApplicantBankRepositoryInterface::class),
                $app->make(LoanDocumentRepositoryInterface::class),
                $app->make(ApplicantContractRepositoryInterface::class)
            );
        });

        // Bind repositories to interfaces
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
        $this->app->bind(LoanRepaymentRepositoryInterface::class, LoanRepaymentRepository::class);
        $this->app->bind(ApplicantInformationRepositoryInterface::class, ApplicantInformationRepository::class);
        $this->app->bind(LoanDocumentRepositoryInterface::class, LoanDocumentRepository::class);
        $this->app->bind(ApplicantBankRepositoryInterface::class, ApplicantBankRepository::class);
        $this->app->bind(ApplicantContractRepositoryInterface::class, ApplicantContractRepository::class);
        // Admin
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}