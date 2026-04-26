<?php

namespace App\Providers;

use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Interfaces\PaymentTypeRepositoriesInterface;
use App\Interfaces\StudentRepositoriesInterface;
use App\Interfaces\TeacherRepositoriesInterface;
use App\Interfaces\UserRepositoriesInterface;
use App\Repositories\ClassRoomRepositories;
use App\Repositories\PaymentTypeRepositories;
use App\Repositories\StudentRepositories;
use App\Repositories\TeacherRepositories;
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
        $this->app->bind(ClassRoomRepositoriesInterface::class, ClassRoomRepositories::class);
        $this->app->bind(TeacherRepositoriesInterface::class, TeacherRepositories::class);
        $this->app->bind(PaymentTypeRepositoriesInterface::class, PaymentTypeRepositories::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
