<?php

namespace App\Providers;

use App\Repositories\Interfaces\CourseRepositoryInterface;
use App\Repositories\Interfaces\MentorApplicationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Mock\CourseRepository;
use App\Repositories\Mock\MentorApplicationRepository;
use App\Repositories\Mock\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(CourseRepositoryInterface::class, CourseRepository::class);
        $this->app->singleton(MentorApplicationRepositoryInterface::class, MentorApplicationRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
