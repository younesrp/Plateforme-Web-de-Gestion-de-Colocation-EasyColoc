<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Colocation;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\ColocationPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Colocation::class => ColocationPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
