<?php

namespace App\Providers;

use App\Http\Controllers\MainCategoriesController;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

// Models and Policies
use App\Models\ContactMessage;
use App\Policies\ContactMessagePolicy;
use App\Models\Brand;
use App\Policies\BrandPolicy;
use App\Models\MainCategories;
use App\Policies\MainCategoriesPolicy;
use App\Models\User;
use App\Models\SubCategories;
use App\Policies\SubCategoriesPolicy;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [


    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Optional: define global gates here
        // Gate::define('admin-only', fn($user) => $user->role === 'admin');
    }
}

