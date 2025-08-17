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
        ContactMessage::class => ContactMessagePolicy::class,
        Brand::class => BrandPolicy::class,
        MainCategories::class => MainCategoriesPolicy::class,
        SubCategories::class => SubCategoriesPolicy::class,
        \App\Models\Basket::class => \App\Policies\BasketsPolicy::class,
        \App\Models\BasketProduct::class => \App\Policies\BasketProductsPolicy::class,
        \App\Models\Product::class => \App\Policies\ProductPolicy::class,
        \App\Models\ProductPrice::class => \App\Policies\ProductPricePolicy::class,
        \App\Models\Catalog::class => \App\Policies\CatalogPolicy::class,
        \App\Models\Notification::class => \App\Policies\NotificationsPolicy::class,
        \App\Models\Client::class => \App\Policies\ClientsPolicy::class,
        \App\Models\QuoteRequest::class => \App\Policies\QuoteRequestPolicy::class,
        \App\Models\QuoteAction::class => \App\Policies\QuoteActionPolicy::class,
    \App\Models\PriceUploadLog::class => \App\Policies\PriceUploadLogPolicy::class,
    \App\Models\Template::class => \App\Policies\TempletesPolicy::class,
        User::class => \App\Policies\UserPolicy::class,

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

