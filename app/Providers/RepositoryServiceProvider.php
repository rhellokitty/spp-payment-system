<?php

namespace App\Providers;

use App\Interfaces\BillRepositoriesInterface;
use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Interfaces\DashboardRepoositoriesInterface;
use App\Interfaces\PaymentTypeRepositoriesInterface;
use App\Interfaces\StudentRepositoriesInterface;
use App\Interfaces\TeacherRepositoriesInterface;
use App\Interfaces\TransactionRepositoriesInterface;
use App\Interfaces\UserRepositoriesInterface;
use App\Repositories\BillRepositories;
use App\Repositories\ClassRoomRepositories;
use App\Repositories\DashboardRepositories;
use App\Repositories\PaymentTypeRepositories;
use App\Repositories\StudentRepositories;
use App\Repositories\TeacherRepositories;
use App\Repositories\TransactionRepositories;
use App\Repositories\UserRepositories;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoriesInterface::class, UserRepositories::class);
        $this->app->bind(StudentRepositoriesInterface::class, StudentRepositories::class);
        $this->app->bind(BillRepositoriesInterface::class, BillRepositories::class);
        $this->app->bind(ClassRoomRepositoriesInterface::class, ClassRoomRepositories::class);
        $this->app->bind(TeacherRepositoriesInterface::class, TeacherRepositories::class);
        $this->app->bind(PaymentTypeRepositoriesInterface::class, PaymentTypeRepositories::class);
        $this->app->bind(TransactionRepositoriesInterface::class, TransactionRepositories::class);
        $this->app->bind(DashboardRepoositoriesInterface::class, DashboardRepositories::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
