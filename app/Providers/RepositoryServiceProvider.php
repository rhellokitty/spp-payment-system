<?php

namespace App\Providers;

use App\Interfaces\ClassRoomRepositoriesInterface;
use App\Interfaces\StudentRepositoriesInterface;
use App\Interfaces\UserRepositoriesInterface;
use App\Repositories\ClassRoomRepositories;
use App\Repositories\StudentRepositories;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
