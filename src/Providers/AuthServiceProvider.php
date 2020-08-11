<?php

namespace System\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        'System\Models\Shop' => 'System\Policies\ShopPolicy',
        'System\Models\Cart' => 'System\Policies\CartPolicy',
        'System\Models\Order' => 'System\Policies\OrderPolicy',
        'System\Models\Product' => 'System\Policies\ProductPolicy',
        'System\Models\Article' => 'System\Policies\ArticlePolicy',
        'System\Models\UserBrowse' => 'System\Policies\UserBrowsePolicy',
        'System\Models\UserFavorite' => 'System\Policies\UserFavoritePolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*Gate::guessPolicyNamesUsing(function ($modelClass) {
            // return policy class name...
        });*/

        Gate::define('super_admin', function ($user) {
            return $user->isSuperAdmin() ?? false;
        });
    }
}
